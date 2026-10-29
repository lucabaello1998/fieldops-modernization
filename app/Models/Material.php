<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `materiales` (8 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Material extends Model
{
    protected $table = 'materiales';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'cantidad' => 'integer',
        'fecha' => 'date',
        'fechaegre' => 'date',
        'cantec' => 'integer',
    ];
}
