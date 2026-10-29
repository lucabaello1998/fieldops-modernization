<?php

namespace App\Http\Controllers;

use App\Models\Entregamaterial;
use App\Models\Material;
use App\Models\MovimientoInterno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Modulo de Stock: consulta de materiales por deposito y registro de
 * movimientos internos sobre el esquema legacy (tablas `materiales`,
 * `movimiento_interno` y `entregamaterial`, sin foreign keys).
 */
class StockController extends Controller
{
    /** Umbral por debajo del cual una linea se considera critica. */
    public const STOCK_MINIMO = 15;

    /** Umbral a partir del cual una linea se considera en nivel optimo. */
    public const STOCK_ATENCION = 40;

    /** Depositos validos del sistema. */
    public const DEPOSITOS = ['Central', 'Norte', 'Sur', 'Oeste', 'La Plata', 'Mar del Plata'];

    /** Tipos de movimiento admitidos. */
    public const TIPOS_MOVIMIENTO = [
        'Alta de material',
        'Baja de material',
        'Transferencia',
        'Devolucion',
        'Ajuste de inventario',
    ];

    /** Tipos que descuentan unidades del stock. */
    private const TIPOS_EGRESO = ['Baja de material', 'Devolucion'];

    /**
     * Listado de stock con filtros, KPIs, grafico por deposito y
     * actividad reciente (movimientos y entregas).
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $centro = (string) $request->query('centro', '');
        $nivel = (string) $request->query('nivel', '');
        $orden = (string) $request->query('orden', 'cantidad_asc');

        // ------------------------------------------------ consulta filtrada
        $filas = Material::query()
            ->when($q !== '', fn ($c) => $c->where('material', 'like', '%'.$q.'%'))
            ->when(in_array($centro, self::DEPOSITOS, true), fn ($c) => $c->where('centro', $centro))
            ->when($nivel === 'critico', fn ($c) => $c->where('cantidad', '<', self::STOCK_MINIMO))
            ->when($nivel === 'atencion', fn ($c) => $c->where('cantidad', '<', self::STOCK_ATENCION))
            ->when($nivel === 'ok', fn ($c) => $c->where('cantidad', '>=', self::STOCK_ATENCION))
            ->when($orden === 'material', fn ($c) => $c->orderBy('material'))
            ->when($orden === 'centro', fn ($c) => $c->orderBy('centro')->orderBy('material'))
            ->when(! in_array($orden, ['material', 'centro'], true),
                fn ($c) => $c->orderBy('cantidad')->orderBy('material'))
            ->paginate(25)
            ->withQueryString();

        // ----------------------------------------------------------- KPIs
        $unidadesTotales = (int) Material::sum('cantidad');
        $lineasCriticas = Material::where('cantidad', '<', self::STOCK_MINIMO)->count();
        $lineasTotales = Material::count();
        $depositosDistintos = Material::whereNotNull('centro')->distinct()->count('centro');

        // ----------------------------------------- unidades por deposito
        $porCentro = Material::query()
            ->selectRaw('centro, SUM(cantidad) as t')
            ->whereNotNull('centro')
            ->groupBy('centro')
            ->orderByDesc('t')
            ->pluck('t', 'centro');

        $depositoTop = $porCentro->keys()->first();
        $unidadesTop = (int) $porCentro->first();

        // -------------------------------------------- actividad reciente
        $movimientos = MovimientoInterno::query()
            ->orderByDesc('cuando')
            ->limit(12)
            ->get();

        $entregasRecientes = Entregamaterial::query()
            ->orderByDesc('fechaegre')
            ->limit(10)
            ->get();

        // Materiales sugeridos para el datalist del formulario.
        $materiales = Material::query()
            ->whereNotNull('material')
            ->distinct()
            ->orderBy('material')
            ->limit(300)
            ->pluck('material');

        return view('stock.index', [
            'filas' => $filas,
            'kpis' => [
                'unidades' => $unidadesTotales,
                'criticas' => $lineasCriticas,
                'lineas' => $lineasTotales,
                'depositos' => $depositosDistintos,
                'unidadesTop' => $unidadesTop,
                'depositoTop' => $depositoTop ?? '—',
            ],
            'porCentro' => $porCentro,
            'movimientos' => $movimientos,
            'entregasRecientes' => $entregasRecientes,
            'materiales' => $materiales,
            'depositos' => self::DEPOSITOS,
            'tipos' => self::TIPOS_MOVIMIENTO,
            'filtros' => [
                'q' => $q,
                'centro' => $centro,
                'nivel' => $nivel,
                'orden' => $orden,
            ],
        ]);
    }

    /**
     * Registra un movimiento interno y ajusta el stock del deposito.
     */
    public function movimiento(Request $request)
    {
        $datos = $request->validate([
            'centro' => ['required', 'string', 'in:'.implode(',', self::DEPOSITOS)],
            'tipo' => ['required', 'string', 'in:'.implode(',', self::TIPOS_MOVIMIENTO)],
            'material' => ['required', 'string', 'max:255'],
            'cantidad' => ['required', 'integer', 'min:1', 'max:1000'],
            'obs' => ['nullable', 'string', 'max:500'],
        ]);

        $cantidad = (int) $datos['cantidad'];
        $esEgreso = in_array($datos['tipo'], self::TIPOS_EGRESO, true);

        DB::transaction(function () use ($datos, $cantidad, $esEgreso) {
            // ------------------------------------- bitacora del movimiento
            MovimientoInterno::create([
                'token' => bin2hex(random_bytes(6)),
                'quien' => auth()->user()->abreviatura ?? auth()->user()->email,
                'cuando' => now(),
                'movimiento' => $datos['tipo'],
                'pag' => '1',
                'inicio' => now()->toDateString(),
                'dia' => now()->locale('es')->isoFormat('dddd'),
                'tipo' => 'Interno',
                'zona' => $datos['centro'],
                'obs' => $datos['obs'] ?? '',
                'latitud' => '',
                'longitud' => '',
            ]);

            // ------------------------------------ ajuste del stock real
            $linea = Material::query()
                ->where('centro', $datos['centro'])
                ->where('material', $datos['material'])
                ->first();

            if ($linea) {
                $linea->cantidad = $esEgreso
                    ? max(0, (int) $linea->cantidad - $cantidad)
                    : (int) $linea->cantidad + $cantidad;
                $linea->save();
            } elseif ($datos['tipo'] === 'Alta de material') {
                // Solo el alta genera una linea nueva cuando no existe.
                Material::create([
                    'material' => $datos['material'],
                    'centro' => $datos['centro'],
                    'cantidad' => $cantidad,
                    'fecha' => now()->toDateString(),
                    'fechaegre' => now()->toDateString(),
                    'tecnico' => '',
                    'cantec' => 0,
                ]);
            }
        });

        return back()->with('ok', 'Movimiento registrado correctamente.');
    }
}
