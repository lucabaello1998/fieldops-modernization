<?php

namespace App\Http\Controllers;

use App\Models\Alta;
use App\Models\Material;
use App\Models\Reclamo;
use App\Models\Tarea;
use App\Models\Tecnico;
use Illuminate\Http\Request;

/**
 * Busqueda global del panel.
 *
 * Recorre tareas, altas, tecnicos, materiales y reclamos con consultas
 * parametrizadas (PDO con prepared statements via Eloquent); la salida se
 * escapa en Blade.
 */
class SearchController extends Controller
{
    /** Maximo de resultados por grupo. */
    private const LIMITE = 8;

    /** Minimo de caracteres para disparar la busqueda. */
    private const MINIMO = 2;

    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        // Sin termino suficiente: la vista se muestra vacia (nunca se consulta).
        if (mb_strlen($q) < self::MINIMO) {
            return view('buscar', [
                'q' => $q,
                'suficiente' => false,
                'total' => 0,
                'tareas' => collect(),
                'altas' => collect(),
                'tecnicos' => collect(),
                'materiales' => collect(),
                'reclamos' => collect(),
            ]);
        }

        $like = $this->patron($q);

        // ------------------------------------------------ tareas
        $tareas = Tarea::query()
            ->where(function ($w) use ($like) {
                $w->where('titulo', 'like', $like)
                    ->orWhere('a_quien', 'like', $like)
                    ->orWhere('descripcion', 'like', $like);
            })
            ->orderByDesc('cuando')
            ->limit(self::LIMITE)
            ->get();

        // ------------------------------------------------- altas
        $altas = Alta::query()
            ->where(function ($w) use ($like) {
                $w->where('ot', 'like', $like)
                    ->orWhere('cliente', 'like', $like)
                    ->orWhere('nim', 'like', $like)
                    ->orWhere('direccion', 'like', $like);
            })
            ->orderByDesc('cuando')
            ->limit(self::LIMITE)
            ->get();

        // ---------------------------------------------- tecnicos
        $tecnicos = Tecnico::query()
            ->where(function ($w) use ($like) {
                $w->where('nombre', 'like', $like)
                    ->orWhere('apellido', 'like', $like)
                    ->orWhere('tecnico', 'like', $like)
                    ->orWhere('dni', 'like', $like);
            })
            ->orderBy('apellido')
            ->limit(self::LIMITE)
            ->get();

        // --------------------------------------------- materiales
        $materiales = Material::query()
            ->where(function ($w) use ($like) {
                $w->where('material', 'like', $like)
                    ->orWhere('centro', 'like', $like);
            })
            ->orderBy('material')
            ->limit(self::LIMITE)
            ->get();

        // ----------------------------------------------- reclamos
        $reclamos = Reclamo::query()
            ->where(function ($w) use ($like) {
                $w->where('rf', 'like', $like)
                    ->orWhere('ot', 'like', $like)
                    ->orWhere('problema', 'like', $like);
            })
            ->orderByDesc('fechains')
            ->limit(self::LIMITE)
            ->get();

        $total = $tareas->count() + $altas->count() + $tecnicos->count()
            + $materiales->count() + $reclamos->count();

        return view('buscar', [
            'q' => $q,
            'suficiente' => true,
            'total' => $total,
            'tareas' => $tareas,
            'altas' => $altas,
            'tecnicos' => $tecnicos,
            'materiales' => $materiales,
            'reclamos' => $reclamos,
        ]);
    }

    /**
     * Arma el patron LIKE escapando los comodines para que la busqueda del
     * usuario sea literal. El valor viaja SIEMPRE como parametro enlazado.
     */
    private function patron(string $q): string
    {
        return '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q) . '%';
    }
}
