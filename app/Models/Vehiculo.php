<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `vehiculos` (9 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Vehiculo extends Model
{
    protected $table = 'vehiculos';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'vtv' => 'date',
            'seguro' => 'date',
    ];
}
