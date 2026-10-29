<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `reportevl` (15 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Reportevl extends Model
{
    protected $table = 'reportevl';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'fecha' => 'date',
        'cant_drop' => 'integer',
    ];
}
