<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `corpo` (10 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Corpo extends Model
{
    protected $table = 'corpo';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'ID';

    protected $casts = [
            'ID' => 'integer',
            'CT' => 'integer',
            'FECHA' => 'date',
    ];
}
