<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `garantias` (20 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Garantia extends Model
{
    protected $table = 'garantias';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'ot' => 'integer',
            'fechaint' => 'date',
            'fecharep' => 'date',
            'cuando' => 'datetime',
    ];
}
