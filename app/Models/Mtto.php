<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `mtto` (32 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Mtto extends Model
{
    protected $table = 'mtto';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'cuando' => 'datetime',
            'fecha' => 'date',
    ];
}
