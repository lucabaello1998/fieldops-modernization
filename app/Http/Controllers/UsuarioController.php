<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Gestion de usuarios del sistema (`App\Models\User`).
 *
 * El cast `password => hashed` convierte la contrasena a bcrypt
 * (password_hash) antes de persistirla; nunca se guarda ni se muestra en claro.
 */
class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // ---------------------------------------------- filtros
        if ($q = trim((string) $request->get('q'))) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if (in_array($request->get('role'), User::ROLES, true)) {
            $query->where('role', $request->get('role'));
        }

        // `activo` llega como '1' o '0' desde el select; null = sin filtro.
        $activo = $request->get('activo');
        if ($activo === '1' || $activo === '0') {
            $query->where('activo', $activo === '1');
        }

        $usuarios = $query
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('usuarios.index', [
            'usuarios' => $usuarios,
            'roles' => User::ROLES,
            'filtros' => $request->only(['q', 'role', 'activo']),
            'kpis' => [
                'total' => User::count(),
                'activos' => User::where('activo', true)->count(),
                'admins' => User::where('role', 'Administrador')->count(),
                'inactivos' => User::where('activo', false)->count(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(User::ROLES)],
            'zona' => ['nullable', 'string', 'max:100'],
        ], $this->mensajes());

        User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            // Se pasa en claro a proposito: el cast `hashed` aplica bcrypt solo.
            'password' => $datos['password'],
            'role' => $datos['role'],
            'zona' => $datos['zona'] ?? null,
            'activo' => true,
            'abreviatura' => $this->iniciales($datos['name']),
        ]);

        return redirect()->route('usuarios.index')->with('ok', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $usuario)
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($usuario->id)],
            'role' => ['required', Rule::in(User::ROLES)],
            'activo' => ['boolean'],
            'password' => ['nullable', 'string', 'min:8'],
        ], $this->mensajes());

        // El checkbox ausente significa "inactivo" (no se envia el campo).
        $datos['activo'] = $request->boolean('activo');

        // Si no se escribio una contrasena nueva, la actual no se toca.
        if (empty($datos['password'] ?? null)) {
            unset($datos['password']);
        }

        $usuario->update($datos);

        return redirect()->route('usuarios.index')->with('ok', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, User $usuario)
    {
        // No se puede eliminar a si mismo.
        abort_if($request->user()->id === $usuario->id, 403, 'No podes eliminar tu propio usuario.');

        // No se puede dejar el sistema sin ningun administrador activo.
        if ($usuario->role === 'Administrador' && $usuario->activo) {
            $adminsActivos = User::where('role', 'Administrador')->where('activo', true)->count();
            abort_if($adminsActivos <= 1, 403, 'No podes eliminar al ultimo administrador activo.');
        }

        $nombre = $usuario->name;
        $usuario->delete();

        return redirect()->route('usuarios.index')->with('ok', 'Usuario ' . $nombre . ' eliminado.');
    }

    /** Mensajes de validacion que explican el motivo del rechazo. */
    private function mensajes(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede superar los 120 caracteres.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo no tiene un formato valido.',
            'email.unique' => 'Ya existe un usuario registrado con ese correo.',
            'password.required' => 'La contrasena es obligatoria al crear un usuario.',
            'password.min' => 'La contrasena debe tener al menos 8 caracteres.',
            'role.required' => 'Hay que elegir un rol.',
            'role.in' => 'El rol elegido no es valido.',
            'zona.max' => 'La zona no puede superar los 100 caracteres.',
            'activo.boolean' => 'El estado activo/inactivo no es valido.',
        ];
    }

    /** Iniciales del nombre, en mayusculas (mismo criterio que User::iniciales). */
    private function iniciales(string $nombre): string
    {
        $partes = preg_split('/\s+/', trim($nombre));

        return mb_strtoupper(mb_substr($partes[0] ?? '', 0, 1) . mb_substr($partes[1] ?? '', 0, 1));
    }
}
