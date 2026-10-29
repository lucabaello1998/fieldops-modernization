<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `carga_dia` (44 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class CargaDia extends Model
{
    protected $table = 'carga_dia';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'cuando' => 'datetime',
        'fecha' => 'date',
        'inicio' => 'string',
        'fin' => 'string',
        'dos_play' => 'integer',
        'tres_play' => 'integer',
        'stb' => 'integer',
        'mudanza_interna' => 'integer',
        'baja' => 'integer',
        'garantia' => 'integer',
        'garantia_justificada' => 'integer',
        'garantia_com' => 'integer',
        'baja_tecnica' => 'integer',
        'baja_desmonte' => 'integer',
        'mtto' => 'integer',
        'mtto_externo' => 'integer',
        'reacondicionamiento' => 'integer',
    ];
}
