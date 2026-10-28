<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `calendario` (21 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Calendario extends Model
{
    protected $table = 'calendario';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'cuando' => 'datetime',
            'inicio' => 'date',
            'fin' => 'date',
    ];
}
