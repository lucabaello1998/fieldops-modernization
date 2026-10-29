<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Usuario del sistema.
 *
 * `password` se guarda con bcrypt via el cast `hashed`.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLES = ['Administrador', 'Supervisor', 'Tecnico', 'Deposito', 'Despacho'];

    protected $fillable = [
        'name', 'email', 'password', 'role', 'activo', 'zona', 'abreviatura', 'ultimo_acceso',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo' => 'boolean',
            'ultimo_acceso' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'Administrador';
    }

    public function puedeEditar(): bool
    {
        return in_array($this->role, ['Administrador', 'Supervisor'], true);
    }

    public function iniciales(): string
    {
        $partes = preg_split('/\s+/', trim((string) $this->name));

        return mb_strtoupper(mb_substr($partes[0] ?? '', 0, 1).mb_substr($partes[1] ?? '', 0, 1));
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
