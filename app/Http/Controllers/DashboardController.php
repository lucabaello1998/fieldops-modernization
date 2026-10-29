<?php

namespace App\Http\Controllers;

use App\Models\Alta;
use App\Models\Baja;
use App\Models\CargaDia;
use App\Models\Material;
use App\Models\NoConformidad;
use App\Models\Produccion;
use App\Models\Reclamo;
use App\Models\Tarea;
use App\Models\Tecnico;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /** Umbral por debajo del cual un material se considera en nivel critico. */
    public const STOCK_MINIMO = 15;

    public function index()
    {
        $meses = collect(range(11, 0))->map(fn (int $i) => now()->subMonths($i)->format('Y-m'));
        $etiquetas = $meses->map(fn (string $m) => Carbon::createFromFormat('Y-m', $m)->locale('es')->isoFormat('MMM YY'));
        $desde = now()->subMonths(11)->startOfMonth();

        // -------------------------------------------- series mensuales
        $altasMes = Alta::query()
            ->selectRaw("DATE_FORMAT(calendario, '%Y-%m') as m, COUNT(*) as t")
            ->where('calendario', '>=', $desde)
            ->groupBy('m')->pluck('t', 'm');

        $bajasMes = Baja::query()
            ->selectRaw("DATE_FORMAT(calendario, '%Y-%m') as m, COUNT(*) as t")
            ->where('calendario', '>=', $desde)
            ->groupBy('m')->pluck('t', 'm');

        $produccion = Produccion::query()
            ->selectRaw("DATE_FORMAT(fecha, '%Y-%m') as m,
                         SUM(COALESCE(dosplay,0) + COALESCE(tresplay,0)) as instalaciones,
                         SUM(COALESCE(mtto_int,0) + COALESCE(mtto_ext,0)) as mtto,
                         SUM(COALESCE(bajas,0) + COALESCE(baja_desmonte,0)) as bajas")
            ->where('fecha', '>=', $desde)
            ->groupBy('m')->get()->keyBy('m');

        // -------------------------------------------- KPIs
        $altasActual = Alta::whereBetween('calendario', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $altasPrevio = Alta::whereBetween('calendario', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->count();

        $tareasActivas = Tarea::whereIn('estado', ['Pendiente', 'En curso', 'En revision'])->count();
        $enRevision = Tarea::where('estado', 'En revision')->count();

        $stockCritico = Material::where('cantidad', '<', self::STOCK_MINIMO)->count();
        $materiales = Material::count();

        $tecnicosActivos = Tecnico::where('activo', 'Si')->count();
        $zonas = Tecnico::where('activo', 'Si')->distinct()->count('zona');

        // En el esquema real `solucion` es NOT NULL: un reclamo abierto queda con la
        // cadena vacia (o NULL en la base productiva). Se cuentan las dos formas.
        $reclamosAbiertos = Reclamo::where(function ($q) {
            $q->whereNull('solucion')->orWhere('solucion', '');
        })->count();
        $sinCerrar = CargaDia::whereBetween('fecha', [now()->subDays(30), now()])
            ->where('estado', '!=', 'Completada')->count();
        $noConformidades = NoConformidad::where('fecha', '>=', now()->subMonths(3))->count();

        return view('dashboard', [
            'etiquetas' => $etiquetas,
            'series' => [
                'instalaciones' => $meses->map(fn ($m) => (int) ($produccion[$m]->instalaciones ?? 0)),
                'mtto' => $meses->map(fn ($m) => (int) ($produccion[$m]->mtto ?? 0)),
                'bajasProd' => $meses->map(fn ($m) => (int) ($produccion[$m]->bajas ?? 0)),
                'altas' => $meses->map(fn ($m) => (int) ($altasMes[$m] ?? 0)),
                'bajas' => $meses->map(fn ($m) => (int) ($bajasMes[$m] ?? 0)),
            ],
            'tareasPorEstado' => Tarea::selectRaw('estado, COUNT(*) as t')->groupBy('estado')->pluck('t', 'estado'),
            'stockPorDeposito' => Material::selectRaw('centro, SUM(cantidad) as t')->groupBy('centro')->orderByDesc('t')->pluck('t', 'centro'),
            'topTecnicos' => Produccion::query()
                ->selectRaw('tecnico,
                    SUM(COALESCE(dosplay,0) + COALESCE(tresplay,0)) as instalaciones,
                    SUM(COALESCE(mtto_int,0) + COALESCE(mtto_ext,0)) as mtto,
                    SUM(COALESCE(bajas,0)) as bajas')
                ->where('fecha', '>=', now()->subDays(30))
                ->groupBy('tecnico')->orderByDesc('instalaciones')->limit(6)->get(),
            'ultimasAltas' => Alta::orderByDesc('cuando')->limit(7)->get(),
            'kpis' => [
                'altas' => $altasActual,
                'altasPrevio' => $altasPrevio,
                'altasDelta' => $altasPrevio > 0 ? (($altasActual - $altasPrevio) / $altasPrevio) * 100 : 0,
                'tareas' => $tareasActivas,
                'enRevision' => $enRevision,
                'stockCritico' => $stockCritico,
                'materiales' => $materiales,
                'tecnicos' => $tecnicosActivos,
                'zonas' => $zonas,
                'reclamos' => $reclamosAbiertos,
                'sinCerrar' => $sinCerrar,
                'noConformidades' => $noConformidades,
            ],
        ]);
    }
}
