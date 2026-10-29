<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `ingresomaterial` (17 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Ingresomaterial extends Model
{
    protected $table = 'ingresomaterial';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'fecha_usuario' => 'datetime',
        'fecha' => 'date',
        'sap' => 'integer',
        'descargado_cuando' => 'date',
    ];
}
