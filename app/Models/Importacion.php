<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Importacion extends Model
{
    protected $table = 'importaciones';

    protected $fillable = [
        'archivo_nombre',
        'archivo_path',
        'fecha_importacion',
    ];

    protected $casts = [
        'fecha_importacion' => 'datetime',
    ];

    public function registros(): HasMany
    {
        return $this->hasMany(RegistroExcel::class);
    }
}
