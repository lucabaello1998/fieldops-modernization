<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `basemateriales` (3 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Basematerial extends Model
{
    protected $table = 'basemateriales';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'sap' => 'integer',
    ];
}
