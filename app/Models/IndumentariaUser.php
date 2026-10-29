<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `indumentaria_user` (2 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class IndumentariaUser extends Model
{
    protected $table = 'indumentaria_user';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
    ];
}
