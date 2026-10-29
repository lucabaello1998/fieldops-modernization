<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `no_conformidades` (11 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class NoConformidad extends Model
{
    protected $table = 'no_conformidades';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'cuando' => 'datetime',
        'fecha' => 'date',
    ];
}
