<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class BaseDatosSheetImport implements ToCollection
{
    public Collection $rows;

    // Mapa de columnas: índice numérico => nombre normalizado
    // Basado en el Excel real: Código(0), Productos(1), Clase Terapéutica(2),
    // Cliente(3), Clase(4), Mes(5), Año(6), Unidades(7)
    private array $columnMap = [];

    public function __construct()
    {
        $this->rows = collect();
    }

    public function collection(Collection $rows): void
    {
        $headerRowIndex = null;

        // Buscar la fila de encabezados escaneando las primeras 20 filas
        foreach ($rows as $index => $row) {
            $rowArr = $row->toArray();
            $rowLower = array_map(fn($v) => mb_strtolower(trim((string)($v ?? ''))), $rowArr);

            // Detectar si esta fila contiene los encabezados clave
            if (in_array('cliente', $rowLower) && in_array('unidades', $rowLower)) {
                $headerRowIndex = $index;
                // Construir mapa de columnas
                foreach ($rowArr as $col => $header) {
                    $key = $this->normalizeKey((string)($header ?? ''));
                    if ($key !== '') {
                        $this->columnMap[$col] = $key;
                    }
                }
                break;
            }

            if ($index >= 20) break;
        }

        if ($headerRowIndex === null) {
            $this->rows = collect();
            return;
        }

        // Tomar solo las filas de datos (después del encabezado)
        $this->rows = $rows->slice($headerRowIndex + 1)->values()->map(function ($row) {
            $rowArr = $row->toArray();
            $mapped = [];
            foreach ($this->columnMap as $col => $key) {
                $mapped[$key] = $rowArr[$col] ?? null;
            }
            return $mapped;
        })->filter(function ($row) {
            // Filtrar filas completamente vacías
            $cliente = trim((string)($row['cliente'] ?? ''));
            $unidades = trim((string)($row['unidades'] ?? ''));
            return $cliente !== '' || $unidades !== '';
        })->values();
    }

    private function normalizeKey(string $key): string
    {
        $key = trim(mb_strtolower($key));
        $key = preg_replace('/\s+/', ' ', $key) ?? $key;

        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $key);
        if (is_string($ascii) && $ascii !== '') {
            $key = $ascii;
        }

        $key = str_replace(['.', ':'], '', $key);
        $key = str_replace(' ', '_', $key);
        $key = str_replace('ano', 'ano', $key);

        return $key;
    }
}
