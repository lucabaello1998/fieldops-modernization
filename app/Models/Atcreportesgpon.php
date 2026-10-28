<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `atcreportesgpon` (25 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Atcreportesgpon extends Model
{
    protected $table = 'atcreportesgpon';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'fecha' => 'date',
    ];
}
