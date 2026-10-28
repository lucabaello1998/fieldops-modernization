<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `track` (7 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Track extends Model
{
    protected $table = 'track';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
    ];
}
