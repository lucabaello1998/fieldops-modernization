<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `atcmapas` (12 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Atcmapa extends Model
{
    protected $table = 'atcmapas';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'fecha' => 'date',
        'orden' => 'integer',
    ];
}
