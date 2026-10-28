<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `bajas` (19 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Baja extends Model
{
    protected $table = 'bajas';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'cuando' => 'datetime',
            'ot' => 'integer',
            'calendario' => 'date',
    ];
}
