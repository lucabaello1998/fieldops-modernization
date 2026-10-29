<?php

namespace App\Http\Controllers;

use App\Models\Alta;
use App\Models\Entregamaterial;
use App\Models\Produccion;
use App\Models\Tecnico;
use App\Models\Vehiculo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TecnicoController extends Controller
{
    /**
     * Plantel de tecnicos con filtros y desempeno de los ultimos 30 dias.
     */
    public function index(Request $request)
    {
        $tecnicos = $this->consulta($request)
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->paginate(24)
            ->withQueryString();

        // Desempeno de los tecnicos de la pagina en UNA sola consulta agregada (evita N+1).
        $handles = $tecnicos->pluck('tecnico')->filter()->values()->all();

        $desempeno = Produccion::query()
            ->selectRaw('tecnico,
                SUM(COALESCE(dosplay, 0) + COALESCE(tresplay, 0)) as instalaciones,
                SUM(COALESCE(mtto_int, 0) + COALESCE(mtto_ext, 0)) as mantenimiento,
                SUM(COALESCE(bajas, 0)) as bajas')
            ->whereIn('tecnico', $handles)
            ->where('fecha', '>=', now()->subDays(30))
            ->groupBy('tecnico')
            ->get()
            ->keyBy('tecnico');

        // Instalaciones de los ultimos 30 dias de todo el plantel.
        $fila = Produccion::query()
            ->where('fecha', '>=', now()->subDays(30))
            ->selectRaw('SUM(COALESCE(dosplay, 0) + COALESCE(tresplay, 0)) as t')
            ->first();

        return view('tecnicos.index', [
            'tecnicos' => $tecnicos,
            'desempeno' => $desempeno,
            'zonas' => Tecnico::query()->distinct()->orderBy('zona')->pluck('zona')->filter()->values(),
            'tipos' => Tecnico::query()->distinct()->orderBy('tipo')->pluck('tipo')->filter()->values(),
            'depositos' => Tecnico::query()->distinct()->orderBy('deposito')->pluck('deposito')->filter()->values(),
            'kpis' => [
                'total' => Tecnico::count(),
                'activos' => Tecnico::query()->where('activo', 'Si')->count(),
                'zonas' => Tecnico::query()->distinct()->count('zona'),
                'instalaciones30' => (int) ($fila?->t ?? 0),
            ],
        ]);
    }

    /**
     * Ficha del tecnico: contacto, vehiculo, produccion reciente y entregas de material.
     */
    public function show(Tecnico $tecnico)
    {
        $produccion = Produccion::query()
            ->where('tecnico', $tecnico->tecnico)
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $entregas = Entregamaterial::query()
            ->where('tecnico', $tecnico->tecnico)
            ->orderByDesc('fechaegre')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        // El vinculo con las altas es por texto (handle), no hay foreign key.
        $altas = Alta::query()->where('tecnico', $tecnico->tecnico)->count();

        $vehiculo = $tecnico->patente
            ? Vehiculo::query()->where('patente', $tecnico->patente)->first()
            : null;

        return view('tecnicos.show', [
            'tecnico' => $tecnico,
            'vehiculo' => $vehiculo,
            'produccion' => $produccion,
            'entregas' => $entregas,
            'altas' => $altas,
        ]);
    }

    /**
     * Query base del plantel con los filtros del querystring aplicados.
     */
    private function consulta(Request $request): Builder
    {
        $busqueda = trim((string) $request->input('q', ''));

        return Tecnico::query()
            ->when($busqueda !== '', function (Builder $query) use ($busqueda) {
                $termino = '%'.$busqueda.'%';

                $query->where(function (Builder $sub) use ($termino) {
                    $sub->where('nombre', 'like', $termino)
                        ->orWhere('apellido', 'like', $termino)
                        ->orWhere('tecnico', 'like', $termino)
                        ->orWhere('dni', 'like', $termino);
                });
            })
            ->when($request->filled('zona'), fn (Builder $q) => $q->where('zona', $request->input('zona')))
            ->when($request->filled('tipo'), fn (Builder $q) => $q->where('tipo', $request->input('tipo')))
            ->when($request->filled('activo'), fn (Builder $q) => $q->where('activo', $request->input('activo')))
            ->when($request->filled('deposito'), fn (Builder $q) => $q->where('deposito', $request->input('deposito')));
    }
}
