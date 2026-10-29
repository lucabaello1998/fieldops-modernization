<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `new_lista_materiales` (4 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class NewListaMaterial extends Model
{
    protected $table = 'new_lista_materiales';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'sap' => 'integer',
    ];
}
