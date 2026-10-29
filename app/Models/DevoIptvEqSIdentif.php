<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `devo_iptv_eq_s_identif` (18 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class DevoIptvEqSIdentif extends Model
{
    protected $table = 'devo_iptv_eq_s_identif';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = null;

    public $incrementing = false;

    protected $casts = [
        'id' => 'integer',
        'N° REMITO' => 'integer',
        'MATERIAL SAP' => 'integer',
        'CANTIDAD' => 'integer',
        'Nº CAJA' => 'integer',
    ];
}
