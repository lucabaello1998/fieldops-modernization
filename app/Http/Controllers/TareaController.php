<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\Tecnico;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * Tablero de tareas (Kanban).
 *
 * Cada movimiento y cada baja pasan por `auth` + CSRF + validacion de estado:
 * no hay escrituras directas a la base desde el navegador.
 */
class TareaController extends Controller
{
    public const ESTADOS = ['Pendiente', 'En curso', 'En revision', 'Finalizado'];

    public const PRIORIDADES = [1 => 'Alta', 2 => 'Media', 3 => 'Baja'];

    public function index(Request $request)
    {
        $query = Tarea::query();

        if ($q = trim((string) $request->get('q'))) {
            $query->where(function ($w) use ($q) {
                $w->where('titulo', 'like', "%{$q}%")
                    ->orWhere('a_quien', 'like', "%{$q}%")
                    ->orWhere('tarea', 'like', "%{$q}%")
                    ->orWhere('descripcion', 'like', "%{$q}%");
            });
        }

        if ($tecnico = $request->get('tecnico')) {
            $query->where('a_quien', $tecnico);
        }

        if ($prioridad = $request->get('prioridad')) {
            $query->where('prioridad', (int) $prioridad);
        }

        $tareas = $query
            ->orderBy('prioridad') // 1 = Alta: primero lo urgente
            ->orderByDesc('cuando')
            ->get();

        $columnas = collect(self::ESTADOS)
            ->mapWithKeys(fn (string $estado) => [$estado => $tareas->where('estado', $estado)->values()]);

        $hoy = Carbon::today();
        $activas = $tareas->whereIn('estado', ['Pendiente', 'En curso', 'En revision']);

        return view('tareas.index', [
            'columnas' => $columnas,
            'tecnicos' => Tecnico::where('activo', 'Si')->orderBy('apellido')->pluck('tecnico'),
            'tipos' => \App\Support\DemoCatalog::TIPOS_TAREA,
            'filtros' => $request->only(['q', 'tecnico', 'prioridad']),
            'kpis' => [
                'total' => $tareas->count(),
                'activas' => $activas->count(),
                'vencidas' => $activas->filter(fn (Tarea $t) => $t->fin && Carbon::parse($t->fin)->lt($hoy))->count(),
                'finalizadasMes' => $tareas
                    ->where('estado', 'Finalizado')
                    ->filter(fn (Tarea $t) => $t->fin && Carbon::parse($t->fin)->isCurrentMonth())
                    ->count(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'tarea' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'a_quien' => ['required', 'string', 'max:255'],
            'prioridad' => ['required', 'integer', 'in:1,2,3'],
            'inicio' => ['nullable', 'date'],
            'fin' => ['nullable', 'date', 'after_or_equal:inicio'],
        ]);

        $user = $request->user();

        Tarea::create([
            'token' => bin2hex(random_bytes(6)),
            'quien' => $user->abreviatura ?: $user->email,
            'cuando' => now(),
            'titulo' => $datos['titulo'],
            'tarea' => $datos['tarea'],
            'abreviado' => mb_substr($datos['tarea'], 0, 3),
            'descripcion' => $datos['descripcion'] ?? '',
            'a_quien' => $datos['a_quien'],
            'prioridad' => $datos['prioridad'],
            'inicio' => $datos['inicio'] ?? now()->toDateString(),
            'fin' => $datos['fin'] ?? now()->addDays(3)->toDateString(),
            'estado' => 'Pendiente',
            'sub_tarea' => '',
            'sub_estado' => '',
            'color' => 'slate',
            'mensaje' => '',
            // Columnas NOT NULL del esquema legacy para adjuntos (no se usan en v2).
            'archivo_uno' => '', 'archivo_dos' => '', 'archivo_tres' => '',
            'nom_archivo_uno' => '', 'nom_archivo_dos' => '', 'nom_archivo_tres' => '',
        ]);

        return back()->with('ok', 'Tarea creada y asignada a ' . $datos['a_quien'] . '.');
    }

    public function show(Tarea $tarea)
    {
        return response()->json([
            'id' => $tarea->id,
            'titulo' => $tarea->titulo,
            'tarea' => $tarea->tarea,
            'descripcion' => $tarea->descripcion,
            'estado' => $tarea->estado,
            'prioridad' => self::PRIORIDADES[$tarea->prioridad] ?? 'Media',
            'a_quien' => $tarea->a_quien,
            'quien' => $tarea->quien,
            'inicio' => $tarea->inicio ? Carbon::parse($tarea->inicio)->format('d/m/Y') : null,
            'fin' => $tarea->fin ? Carbon::parse($tarea->fin)->format('d/m/Y') : null,
            'cuando' => $tarea->cuando ? Carbon::parse($tarea->cuando)->locale('es')->isoFormat('D MMM YYYY HH:mm') : null,
            'sub_tarea' => $tarea->sub_tarea,
            'sub_estado' => $tarea->sub_estado,
            'mensaje' => $tarea->mensaje,
            'token' => $tarea->token,
        ]);
    }

    /** Movimiento desde el tablero (drag and drop). */
    public function updateEstado(Request $request, Tarea $tarea)
    {
        $datos = $request->validate([
            'estado' => ['required', Rule::in(self::ESTADOS)],
        ]);

        $anterior = $tarea->estado;

        $tarea->estado = $datos['estado'];
        $tarea->color = match ($datos['estado']) {
            'En curso' => 'indigo',
            'En revision' => 'sky',
            'Finalizado' => 'emerald',
            default => 'slate',
        };

        if ($datos['estado'] === 'Finalizado') {
            $tarea->fin = $tarea->fin ?: now()->toDateString();
        }

        // Trazabilidad: queda registrado quien movio la tarea y cuando.
        $autor = $request->user()->abreviatura ?: $request->user()->email;
        $sello = sprintf('[%s] %s movio la tarea de "%s" a "%s".', now()->format('d/m/Y H:i'), $autor, $anterior, $datos['estado']);
        $tarea->mensaje = trim(($tarea->mensaje ? $tarea->mensaje . "\n" : '') . $sello);
        $tarea->save();

        return response()->json([
            'ok' => true,
            'id' => $tarea->id,
            'estado' => $tarea->estado,
        ]);
    }

    public function comentar(Request $request, Tarea $tarea)
    {
        $datos = $request->validate([
            'mensaje' => ['required', 'string', 'max:1000'],
        ]);

        $autor = $request->user()->abreviatura ?: $request->user()->email;
        $nota = sprintf('[%s] %s: %s', now()->format('d/m/Y H:i'), $autor, $datos['mensaje']);
        $tarea->mensaje = trim(($tarea->mensaje ? $tarea->mensaje . "\n" : '') . $nota);
        $tarea->save();

        return back()->with('ok', 'Nota agregada a la tarea #' . $tarea->id . '.');
    }

    public function destroy(Request $request, Tarea $tarea)
    {
        // Solo Administrador y Supervisor pueden eliminar.
        abort_unless($request->user()->puedeEditar(), 403, 'No tenes permisos para eliminar tareas.');

        $id = $tarea->id;
        $tarea->delete();

        return back()->with('ok', 'Tarea #' . $id . ' eliminada.');
    }
}
