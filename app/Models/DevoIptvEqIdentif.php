<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `devo_iptv_eq_identif` (18 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class DevoIptvEqIdentif extends Model
{
    protected $table = 'devo_iptv_eq_identif';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'N° REMITO' => 'integer',
        'MATERIAL SAP' => 'integer',
        'CANTIDAD' => 'integer',
        'Nº CAJA' => 'integer',
    ];
}
