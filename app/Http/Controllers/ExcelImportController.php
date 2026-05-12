<?php

namespace App\Http\Controllers;

use App\Imports\BaseDatosImport;
use App\Models\Importacion;
use App\Models\RegistroExcel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Exceptions\SheetNotFoundException;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Collection;

class ExcelImportController extends Controller
{
    private function normalizeSheetName(string $name): string
    {
        $name = trim(mb_strtolower($name));
        $name = preg_replace('/\s+/', ' ', $name) ?? $name;

        return $name;
    }

    private function normalizeKey(string $key): string
    {
        $key = trim(mb_strtolower($key));
        $key = str_replace(['\n', "\r"], ' ', $key);
        $key = preg_replace('/\s+/', ' ', $key) ?? $key;

        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $key);
        if (is_string($ascii) && $ascii !== '') {
            $key = $ascii;
        }

        $key = str_replace(['.', ':'], '', $key);
        $key = str_replace(' ', '_', $key);
        $key = str_replace('año', 'ano', $key);

        return $key;
    }

    /** @return int|string */
    private function pickBaseDatosSheetKey(string $diskPath)
    {
        $spreadsheet = IOFactory::load($diskPath);
        $names = $spreadsheet->getSheetNames();

        $target = $this->normalizeSheetName('Base de datos');
        foreach ($names as $name) {
            if ($this->normalizeSheetName($name) === $target) {
                return $name;
            }
        }

        foreach ($names as $name) {
            $n = $this->normalizeSheetName($name);
            if (str_contains($n, 'base') && (str_contains($n, 'dato') || str_contains($n, 'datos'))) {
                return $name;
            }
        }

        if (count($names) >= 3) {
            return 2;
        }

        return 0;
    }

    /** @return string[] */
    private function listSheetNames(string $diskPath): array
    {
        $spreadsheet = IOFactory::load($diskPath);

        return $spreadsheet->getSheetNames();
    }

    private function rowToArray($row): array
    {
        $arr = $row instanceof Collection ? $row->toArray() : (array) $row;
        $normalized = [];

        foreach ($arr as $k => $v) {
            $key = is_string($k) ? $this->normalizeKey($k) : (string) $k;
            $normalized[$key] = $v;
        }

        return $normalized;
    }

    private function readBaseDatosRowsFallback(string $diskPath, $sheetKey): Collection
    {
        $spreadsheet = IOFactory::load($diskPath);
        $sheet = is_int($sheetKey)
            ? $spreadsheet->getSheet($sheetKey)
            : $spreadsheet->getSheetByName((string) $sheetKey);

        if (!$sheet) {
            return collect();
        }

        $highestRow = $sheet->getHighestRow();
        $highestCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn());

        $headerRow = null;
        $colMap = [];

        $scanLimit = min($highestRow, 80);
        for ($r = 1; $r <= $scanLimit; $r++) {
            $found = [];

            for ($c = 1; $c <= $highestCol; $c++) {
                $raw = (string) $sheet->getCellByColumnAndRow($c, $r)->getValue();
                $k = $this->normalizeKey($raw);
                if ($k === 'cliente' || $k === 'unidades' || $k === 'mes' || $k === 'ano') {
                    $found[$k] = $c;
                }
            }

            if (isset($found['cliente'], $found['unidades'])) {
                $headerRow = $r;
                $colMap = $found;
                break;
            }
        }

        if ($headerRow === null) {
            return collect();
        }

        $rows = collect();
        $emptyStreak = 0;

        for ($r = $headerRow + 1; $r <= $highestRow; $r++) {
            $cliente = isset($colMap['cliente']) ? $sheet->getCellByColumnAndRow($colMap['cliente'], $r)->getValue() : null;
            $unidades = isset($colMap['unidades']) ? $sheet->getCellByColumnAndRow($colMap['unidades'], $r)->getValue() : null;
            $mes = isset($colMap['mes']) ? $sheet->getCellByColumnAndRow($colMap['mes'], $r)->getValue() : null;
            $ano = isset($colMap['ano']) ? $sheet->getCellByColumnAndRow($colMap['ano'], $r)->getValue() : null;

            $clienteStr = trim((string) ($cliente ?? ''));
            $unidadesStr = trim((string) ($unidades ?? ''));

            if ($clienteStr === '' && $unidadesStr === '') {
                $emptyStreak++;
                if ($emptyStreak >= 25) {
                    break;
                }
                continue;
            }

            $emptyStreak = 0;

            $rows->push([
                'cliente' => $clienteStr,
                'unidades' => $unidades,
                'mes' => $mes,
                'ano' => $ano,
            ]);
        }

        return $rows;
    }

    public function showImportForm(Request $request): View
    {
        return view('import');
    }

    public function import(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'excel' => ['required', 'file', 'mimes:xls,xlsx,xlsm'],
        ]);

        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $validated['excel'];

        Storage::disk('local')->makeDirectory('imports');

        $path = $file->storeAs(
            'imports',
            'base_datos_' . time() . '.' . $file->getClientOriginalExtension(),
            'local'
        );

        $diskPath = Storage::disk('local')->path($path);
        $sheetKey = $this->pickBaseDatosSheetKey($diskPath);

        try {
            $import = new BaseDatosImport($sheetKey);
            Excel::import($import, $diskPath);
            $rows = $import->sheet->rows;
        } catch (\Exception $e) {
            return redirect()->route('import')->withErrors(['excel' => 'Error al procesar el archivo: ' . $e->getMessage()]);
        }

        $rowsNormalized = $rows; // Ya vienen mapeadas desde BaseDatosSheetImport

        $importacion = Importacion::create([
            'archivo_nombre' => $file->getClientOriginalName(),
            'archivo_path' => $path,
            'fecha_importacion' => now(),
        ]);

        foreach ($rowsNormalized as $row) {
            $rowArr = is_array($row) ? $row : (method_exists($row, 'toArray') ? $row->toArray() : (array)$row);

            $unitsRaw = $rowArr['unidades'] ?? 0;
            $units = (float) str_replace([',', ' '], ['', ''], (string) $unitsRaw);

            RegistroExcel::create([
                'importacion_id'    => $importacion->id,
                'codigo'            => $rowArr['codigo'] ?? null,
                'productos'         => $rowArr['productos'] ?? null,
                'clase_terapeutica' => $rowArr['clase_terapeutica'] ?? null,
                'cliente'           => $rowArr['cliente'] ?? null,
                'clase'             => $rowArr['clase'] ?? null,
                'mes'               => isset($rowArr['mes']) ? (int) $rowArr['mes'] : null,
                'ano'               => isset($rowArr['ano']) ? (int) $rowArr['ano'] : null,
                'unidades'          => $units,
            ]);
        }

        $request->session()->put('imports.base_datos_importacion_id', $importacion->id);

        return redirect()->route('grafica')->with('success', 'Archivo importado y guardado correctamente.');
    }

    public function grafica(Request $request): View
    {
        $importacionId = $request->session()->get('imports.base_datos_importacion_id');

        if (!$importacionId) {
            $ultimaImportacion = Importacion::latest('fecha_importacion')->first();
            if ($ultimaImportacion) {
                $importacionId = $ultimaImportacion->id;
                $request->session()->put('imports.base_datos_importacion_id', $importacionId);
            }
        }

        if (!$importacionId) {
            return view('grafica', [
                'labels' => [],
                'percentages' => [],
                'unitsByLabel' => [],
                'totalUnits' => 0,
                'selectedYear' => null,
                'selectedMonths' => [],
                'availableYears' => [],
                'availableMonths' => [],
                'error' => 'Primero debes importar el Excel.',
            ]);
        }

        $importacion = Importacion::find($importacionId);
        if (!$importacion) {
            return view('grafica', [
                'labels' => [],
                'percentages' => [],
                'unitsByLabel' => [],
                'totalUnits' => 0,
                'selectedYear' => null,
                'selectedMonths' => [],
                'availableYears' => [],
                'availableMonths' => [],
                'error' => 'No se encontró la importación.',
            ]);
        }

        $selectedYear = $request->integer('anio') ?: null;
        $selectedMonths = $request->input('mes', []);
        if (!is_array($selectedMonths)) {
            $selectedMonths = [$selectedMonths];
        }
        $selectedProductos = $request->input('producto', []);
        if (!is_array($selectedProductos)) {
            $selectedProductos = [$selectedProductos];
        }

        $selectedClientes = $request->input('cliente', []);
        if (!is_array($selectedClientes)) {
            $selectedClientes = [$selectedClientes];
        }

        $selectedClases = $request->input('clase', []);
        if (!is_array($selectedClases)) {
            $selectedClases = [$selectedClases];
        }

        $query = RegistroExcel::where('importacion_id', $importacionId);

        if ($selectedYear !== null) {
            $query->where('ano', $selectedYear);
        }

        if ($selectedMonths !== []) {
            $query->whereIn('mes', array_map('intval', $selectedMonths));
        }

        if ($selectedClientes !== []) {
            $query->whereIn('cliente', $selectedClientes);
        }

        if ($selectedClases !== []) {
            $query->whereIn('clase_terapeutica', $selectedClases);
        }

        if ($selectedProductos !== []) {
            $query->whereIn('productos', $selectedProductos);
        }

        $registros = $query->get();

        $availableYears = RegistroExcel::where('importacion_id', $importacionId)
            ->whereNotNull('ano')
            ->distinct()
            ->pluck('ano')
            ->sort()
            ->values()
            ->all();

        $availableMonths = RegistroExcel::where('importacion_id', $importacionId)
            ->whereNotNull('mes')
            ->distinct()
            ->pluck('mes')
            ->sort()
            ->values()
            ->all();

        $availableClientes = RegistroExcel::where('importacion_id', $importacionId)
            ->whereNotNull('cliente')
            ->distinct()
            ->orderBy('cliente')
            ->pluck('cliente')
            ->all();

        $availableClases = RegistroExcel::where('importacion_id', $importacionId)
            ->whereNotNull('clase_terapeutica')
            ->distinct()
            ->orderBy('clase_terapeutica')
            ->pluck('clase_terapeutica')
            ->all();

        $availableProductos = RegistroExcel::where('importacion_id', $importacionId)
            ->whereNotNull('productos')
            ->distinct()
            ->orderBy('productos')
            ->pluck('productos')
            ->all();

        $vista = $request->input('vista', 'cliente');
        $unitsByLabel = [];
        $totalUnits = 0.0;

        foreach ($registros as $registro) {
            if ($vista === 'producto') {
                $label = trim($registro->productos ?? '');
                if ($label === '') {
                    $label = 'SIN_PRODUCTO';
                }
            } else {
                // Vista por cliente (por defecto)
                $label = trim($registro->cliente ?? '');
                if ($label === '') {
                    $label = 'SIN_CLIENTE';
                }
            }

            $units = (float) $registro->unidades;
            $unitsByLabel[$label] = ($unitsByLabel[$label] ?? 0) + $units;
            $totalUnits += $units;
        }

        arsort($unitsByLabel);

        $labels = array_keys($unitsByLabel);
        $percentages = array_map(function (float $units) use ($totalUnits): float {
            if ($totalUnits <= 0) {
                return 0.0;
            }
            return round(($units / $totalUnits) * 100, 2);
        }, array_values($unitsByLabel));

        return view('grafica', [
            'labels' => $labels,
            'percentages' => $percentages,
            'unitsByLabel' => $unitsByLabel,
            'totalUnits' => $totalUnits,
            'vista' => $vista,
            'selectedYear' => $selectedYear,
            'selectedMonths' => array_map('intval', $selectedMonths),
            'selectedClientes' => $selectedClientes,
            'selectedClases' => $selectedClases,
            'selectedProductos' => $selectedProductos,
            'availableYears' => $availableYears,
            'availableMonths' => $availableMonths,
            'availableClientes' => $availableClientes,
            'availableClases' => $availableClases,
            'availableProductos' => $availableProductos,
            'error' => null,
        ]);
    }

    public function reprocesar(Request $request): \Illuminate\Http\JsonResponse
    {
        $importaciones = Importacion::all();
        $total = 0;

        foreach ($importaciones as $importacion) {
            $diskPath = Storage::disk('local')->path($importacion->archivo_path);

            if (!\Illuminate\Support\Facades\File::exists($diskPath)) {
                continue;
            }

            $sheetKey = $this->pickBaseDatosSheetKey($diskPath);
            $import = new BaseDatosImport($sheetKey);
            Excel::import($import, $diskPath);
            $rows = $import->sheet->rows;

            // Obtener registros de esta importación ordenados por id
            $registros = \App\Models\RegistroExcel::where('importacion_id', $importacion->id)
                ->orderBy('id')
                ->get();

            foreach ($rows as $i => $row) {
                $rowArr = is_array($row) ? $row : $row->toArray();

                if (!isset($registros[$i])) continue;

                $registros[$i]->update([
                    'codigo'            => $rowArr['codigo'] ?? null,
                    'productos'         => $rowArr['productos'] ?? null,
                    'clase_terapeutica' => $rowArr['clase_terapeutica'] ?? null,
                    'clase'             => $rowArr['clase'] ?? null,
                ]);
                $total++;
            }
        }

        return response()->json([
            'ok' => true,
            'registros_actualizados' => $total,
            'muestra_productos' => \App\Models\RegistroExcel::whereNotNull('productos')->distinct()->limit(5)->pluck('productos'),
        ]);
    }

    public function debugKeys(Request $request): \Illuminate\Http\JsonResponse
    {
        $importacion = Importacion::latest('fecha_importacion')->first();
        if (!$importacion) {
            return response()->json(['error' => 'No hay importaciones']);
        }

        $diskPath = Storage::disk('local')->path($importacion->archivo_path);
        $sheetKey = $this->pickBaseDatosSheetKey($diskPath);

        $import = new BaseDatosImport($sheetKey);
        Excel::import($import, $diskPath);
        $rows = $import->sheet->rows;

        $firstRow = $rows->first();
        $keys = $firstRow ? array_keys(is_array($firstRow) ? $firstRow : $firstRow->toArray()) : [];

        return response()->json([
            'total_registros_bd'       => \App\Models\RegistroExcel::count(),
            'registros_con_productos'  => \App\Models\RegistroExcel::whereNotNull('productos')->count(),
            'registros_sin_productos'  => \App\Models\RegistroExcel::whereNull('productos')->count(),
            'sample_row_keys'          => $keys,
            'sample_row_values'        => $firstRow,
            'importacion_activa_id'    => $importacion->id,
        ]);
    }

    public function historial(Request $request): View
    {
        $importaciones = Importacion::withCount('registros')
            ->latest('fecha_importacion')
            ->paginate(10);

        $importacionActualId = $request->session()->get('imports.base_datos_importacion_id');

        return view('historial', [
            'importaciones' => $importaciones,
            'importacionActualId' => $importacionActualId,
        ]);
    }

    public function seleccionarImportacion(Request $request, int $id): RedirectResponse
    {
        $importacion = Importacion::findOrFail($id);
        
        $request->session()->put('imports.base_datos_importacion_id', $importacion->id);

        return redirect()->route('grafica')->with('success', 'Importación seleccionada correctamente.');
    }
}
