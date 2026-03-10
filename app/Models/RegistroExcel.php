<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroExcel extends Model
{
    protected $table = 'registros_excel';

    protected $fillable = [
        'importacion_id',
        'codigo',
        'productos',
        'clase_terapeutica',
        'cliente',
        'clase',
        'mes',
        'ano',
        'unidades',
    ];

    protected $casts = [
        'unidades' => 'decimal:3',
    ];

    public function importacion(): BelongsTo
    {
        return $this->belongsTo(Importacion::class);
    }
}
