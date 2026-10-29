<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `numeroSap` (6 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class NumeroSap extends Model
{
    protected $table = 'numeroSap';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = null;

    public $incrementing = false;

    protected $casts = [
        'Material Sinergia' => 'integer',
    ];
}
