<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `new_pedidos` (16 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class NewPedido extends Model
{
    protected $table = 'new_pedidos';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'fecha_usuario' => 'datetime',
        'fecha' => 'date',
        'sap' => 'integer',
        'descargado_cuando' => 'date',
    ];
}
