<?php

namespace App\Http\Controllers;

use App\Models\Alta;
use App\Models\Entregamaterial;
use App\Models\Tecnico;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AltaController extends Controller
{
    /**
     * Listado de altas con filtros por texto, zona, deposito, estado y rango de fechas.
     */
    public function index(Request $request)
    {
        $altas = $this->consulta($request)
            ->orderByDesc('calendario')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        // -------------------------------------------- KPIs del filtro actual
        $total = $this->consulta($request)->count();
        $completas = $this->consulta($request)->where('completo', 'Si')->count();
        $mesActual = $this->consulta($request)
            ->whereBetween('calendario', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
        $zonasDistintas = $this->consulta($request)->distinct()->count('zona');

        // -------------------------------------------- serie de los ultimos 12 meses
        $meses = collect(range(11, 0))->map(fn (int $i) => now()->subMonths($i)->format('Y-m'));
        $etiquetas = $meses->map(fn (string $m) => Carbon::createFromFormat('Y-m', $m)->locale('es')->isoFormat('MMM YY'));

        $conteoMes = $this->consulta($request)
            ->selectRaw("DATE_FORMAT(calendario, '%Y-%m') as m, COUNT(*) as t")
            ->where('calendario', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('m')
            ->pluck('t', 'm');

        $porMes = $meses->map(fn (string $m) => (int) ($conteoMes[$m] ?? 0))->values();

        // -------------------------------------------- top de zonas
        $porZona = $this->consulta($request)
            ->selectRaw('zona, COUNT(*) as t')
            ->whereNotNull('zona')
            ->where('zona', '!=', '')
            ->groupBy('zona')
            ->orderByDesc('t')
            ->limit(8)
            ->get();

        return view('altas.index', [
            'altas' => $altas,
            'zonas' => Alta::query()->distinct()->orderBy('zona')->pluck('zona')->filter()->values(),
            'depositos' => Alta::query()->distinct()->orderBy('deposito')->pluck('deposito')->filter()->values(),
            'etiquetas' => $etiquetas,
            'porMes' => $porMes,
            'porZona' => $porZona,
            'kpis' => [
                'total' => $total,
                'completas' => $completas,
                'pctCompletas' => $total > 0 ? round($completas / $total * 100, 1) : 0,
                'mesActual' => $mesActual,
                'zonas' => $zonasDistintas,
            ],
        ]);
    }

    /**
     * Ficha de una instalacion: domicilio, equipos, tecnico responsable y trazabilidad.
     */
    public function show(Alta $alta)
    {
        // El vinculo con el tecnico es por texto (handle); puede no existir la ficha.
        $tecnico = Tecnico::query()->where('tecnico', $alta->tecnico)->first();

        $entregas = Entregamaterial::query()
            ->where('tecnico', $alta->tecnico)
            ->orderByDesc('fechaegre')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('altas.show', [
            'alta' => $alta,
            'tecnico' => $tecnico,
            'entregas' => $entregas,
        ]);
    }

    /**
     * Query base del listado con los filtros del querystring aplicados.
     * Se invoca varias veces (listado, KPIs, graficos): cada llamada devuelve una query nueva.
     */
    private function consulta(Request $request): Builder
    {
        $busqueda = trim((string) $request->input('q', ''));

        return Alta::query()
            ->when($busqueda !== '', function (Builder $query) use ($busqueda) {
                $termino = '%'.$busqueda.'%';

                $query->where(function (Builder $sub) use ($termino) {
                    $sub->where('cliente', 'like', $termino)
                        ->orWhere('ot', 'like', $termino)
                        ->orWhere('nim', 'like', $termino)
                        ->orWhere('direccion', 'like', $termino);
                });
            })
            ->when($request->filled('zona'), fn (Builder $q) => $q->where('zona', $request->input('zona')))
            ->when($request->filled('deposito'), fn (Builder $q) => $q->where('deposito', $request->input('deposito')))
            ->when($request->filled('completo'), fn (Builder $q) => $q->where('completo', $request->input('completo')))
            ->when($request->filled('desde'), fn (Builder $q) => $q->where('calendario', '>=', $request->input('desde')))
            ->when($request->filled('hasta'), fn (Builder $q) => $q->where('calendario', '<=', $request->input('hasta')));
    }
}
