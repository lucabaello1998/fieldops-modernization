<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `new_tecnicos` (25 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class NewTecnico extends Model
{
    protected $table = 'new_tecnicos';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'dni' => 'integer',
        'id_recurso' => 'integer',
        'ingreso' => 'date',
    ];
}
