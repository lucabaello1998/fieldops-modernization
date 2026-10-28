<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `precontacto` (24 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Precontacto extends Model
{
    protected $table = 'precontacto';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'orden' => 'integer',
            'cuando' => 'datetime',
    ];
}
