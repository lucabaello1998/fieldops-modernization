<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `desarrollo` (13 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Desarrollo extends Model
{
    protected $table = 'desarrollo';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'cuando' => 'datetime',
            'fecha' => 'date',
            'fase' => 'integer',
    ];
}
