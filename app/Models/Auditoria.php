<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `auditoria` (58 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Auditoria extends Model
{
    protected $table = 'auditoria';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'fecha_relevo' => 'datetime',
        'fecha' => 'date',
    ];
}
