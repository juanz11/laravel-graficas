<?php

namespace App\Http\Controllers;

use App\Imports\BaseDatosImport;
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

        $request->session()->put('imports.base_datos_path', $path);

        return redirect()->route('grafica');
    }

    public function grafica(Request $request): View
    {
        $path = $request->session()->get('imports.base_datos_path');

        if (!$path) {
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

        $diskPath = Storage::disk('local')->path($path);

        if (!File::exists($diskPath)) {
            $request->session()->forget('imports.base_datos_path');

            return view('grafica', [
                'labels' => [],
                'percentages' => [],
                'unitsByLabel' => [],
                'totalUnits' => 0,
                'selectedYear' => null,
                'selectedMonths' => [],
                'availableYears' => [],
                'availableMonths' => [],
                'error' => 'No se encontró el archivo importado. Vuelve a importar el Excel.',
            ]);
        }

        $selectedYear = $request->integer('anio') ?: null;
        $selectedMonths = $request->input('mes', []);
        if (!is_array($selectedMonths)) {
            $selectedMonths = [$selectedMonths];
        }

        $sheetKey = $this->pickBaseDatosSheetKey($diskPath);

        try {
            $import = new BaseDatosImport($sheetKey);
            Excel::import($import, $diskPath);
            $rows = $import->sheet->rows;
        } catch (SheetNotFoundException $e) {
            $names = $this->listSheetNames($diskPath);

            return view('grafica', [
                'labels' => [],
                'percentages' => [],
                'unitsByLabel' => [],
                'totalUnits' => 0,
                'selectedYear' => null,
                'selectedMonths' => [],
                'availableYears' => [],
                'availableMonths' => [],
                'error' => 'No se encontró la hoja "Base de datos". Hojas disponibles: ' . implode(', ', $names),
            ]);
        }

        $rowsNormalized = $rows->map(fn ($r) => $this->rowToArray($r));

        $availableYears = $rowsNormalized
            ->pluck('ano')
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->sort()
            ->values()
            ->all();

        $availableMonths = $rowsNormalized
            ->pluck('mes')
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->sort()
            ->values()
            ->all();

        if (
            $rowsNormalized->isEmpty()
            || ($rowsNormalized->isNotEmpty() && !array_key_exists('cliente', $rowsNormalized->first()))
        ) {
            $rowsNormalized = $this->readBaseDatosRowsFallback($diskPath, $sheetKey);
        }

        $filtered = $rowsNormalized->filter(function ($row) use ($selectedYear, $selectedMonths): bool {
            $rowArr = $this->rowToArray($row);
            $year = $rowArr['ano'] ?? null;
            $month = $rowArr['mes'] ?? null;

            if ($selectedYear !== null && (int) $year !== $selectedYear) {
                return false;
            }

            if ($selectedMonths !== [] && $month !== null) {
                $monthInt = (int) $month;
                $selected = array_map('intval', $selectedMonths);
                if (!in_array($monthInt, $selected, true)) {
                    return false;
                }
            }

            return true;
        });

        $unitsByLabel = [];
        $totalUnits = 0.0;

        foreach ($filtered as $row) {
            $rowArr = $this->rowToArray($row);

            $label = trim((string) ($rowArr['cliente'] ?? ''));
            if ($label === '') {
                $label = 'SIN_CLIENTE';
            }

            $unitsRaw = $rowArr['unidades'] ?? 0;
            $units = (float) str_replace([',', ' '], ['', ''], (string) $unitsRaw);

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
            'selectedYear' => $selectedYear,
            'selectedMonths' => array_map('intval', $selectedMonths),
            'availableYears' => $availableYears,
            'availableMonths' => $availableMonths,
            'error' => null,
        ]);
    }
}
