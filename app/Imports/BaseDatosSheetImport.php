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
    // NOTA: El Excel tiene Mes=2020, Año=2020, así que Mes está en columna 5 y Año en columna 6
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

            if (in_array('cliente', $rowLower) && in_array('unidades', $rowLower)) {
                $headerRowIndex = $index;
                
                // Forzar mapeo correcto según estructura del Excel base
                $this->columnMap[0] = 'codigo';
                $this->columnMap[1] = 'productos';
                $this->columnMap[2] = 'clase_terapeutica';
                $this->columnMap[3] = 'cliente';
                $this->columnMap[4] = 'clase';
                $this->columnMap[5] = 'mes';
                $this->columnMap[6] = 'ano';
                $this->columnMap[7] = 'unidades';

                // Mapear columnas adicionales dinámicamente
                foreach ($rowArr as $col => $header) {
                    if (!isset($this->columnMap[$col])) {
                        $key = $this->normalizeKey((string)($header ?? ''));
                        // Mapear 'tasa', 'valor_usd' o similares
                        if (str_contains($key, 'tasa')) $key = 'tasa';
                        elseif (str_contains($key, 'usd')) $key = 'valor_usd';
                        elseif (str_contains($key, 'bs') || str_contains($key, 'bolivare')) $key = 'valor_bs';

                        if ($key !== '') {
                            $this->columnMap[$col] = $key;
                        }
                    }
                }
                
                // Debug: Mostrar cómo se mapearon las columnas
                \Log::info('Encabezados encontrados: ', $rowArr);
                \Log::info('Mapa de columnas final: ', $this->columnMap);
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
                $value = $rowArr[$col] ?? null;
                $mapped[$key] = $value;
                
                // Debug para valores de mes
                if ($key === 'mes') {
                    \Log::info("Valor de mes encontrado: " . var_export($value, true));
                }
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
        
        // Normalizar campo de año - detectar múltiples variaciones
        if (preg_match('/(año|ano|year)/i', $key)) {
            $key = 'ano';
        }

        return $key;
    }
}
