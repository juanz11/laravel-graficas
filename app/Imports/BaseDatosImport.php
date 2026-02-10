<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BaseDatosImport implements WithMultipleSheets
{
    public BaseDatosSheetImport $sheet;

    /** @var int|string */
    private $sheetKey;

    /** @param int|string $sheetKey */
    public function __construct($sheetKey = 0)
    {
        $this->sheet = new BaseDatosSheetImport();
        $this->sheetKey = $sheetKey;
    }

    public function sheets(): array
    {
        return [
            $this->sheetKey => $this->sheet,
        ];
    }
}
