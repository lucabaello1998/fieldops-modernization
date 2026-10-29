<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `precios` (25 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Precio extends Model
{
    protected $table = 'precios';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'dosplay' => 'integer',
        'tresplay' => 'integer',
        'stb' => 'integer',
        'mudainter' => 'integer',
        'bajas' => 'integer',
        'garanjus' => 'integer',
        'garaninterv' => 'integer',
        'garancomp' => 'integer',
        'sab' => 'integer',
        'fer' => 'integer',
        'sabmtto' => 'integer',
        'fermtto' => 'integer',
        'bajatec' => 'integer',
        'bajadesmont' => 'integer',
        'mttointer' => 'integer',
        'mttoexter' => 'integer',
        'desplani' => 'integer',
        'desepp' => 'integer',
        'desinst' => 'integer',
        'descalidad' => 'integer',
        'destoa' => 'integer',
        'destotal' => 'integer',
        'desbaja' => 'integer',
        'mttoreacond' => 'integer',
    ];
}
