<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\MovimientoInterno;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Stock: movimientos internos que ajustan el stock real por deposito.
 */
class StockTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create(['abreviatura' => 'LB']);
    }

    private function material(string $nombre = 'ONT Huawei', string $centro = 'Central', int $cantidad = 100): Material
    {
        return Material::create([
            'material' => $nombre,
            'centro' => $centro,
            'cantidad' => $cantidad,
            'fecha' => now()->toDateString(),
            'fechaegre' => now()->toDateString(),
            'tecnico' => '',
            'cantec' => 0,
        ]);
    }

    private function movimiento(array $extra = []): array
    {
        return array_merge([
            'centro' => 'Central',
            'tipo' => 'Alta de material',
            'material' => 'ONT Huawei',
            'cantidad' => 10,
            'obs' => 'Ingreso de proveedor',
        ], $extra);
    }

    public function test_un_alta_de_material_suma_al_stock_existente(): void
    {
        $linea = $this->material(cantidad: 100);

        $this->actingAs($this->admin())
            ->post('/stock/movimiento', $this->movimiento())
            ->assertRedirect()
            ->assertSessionHas('ok');

        $this->assertSame(110, (int) $linea->fresh()->cantidad);
        $this->assertSame(1, MovimientoInterno::count());
    }

    public function test_un_egreso_resta_unidades(): void
    {
        $linea = $this->material(cantidad: 100);

        $this->actingAs($this->admin())
            ->post('/stock/movimiento', $this->movimiento(['tipo' => 'Baja de material', 'cantidad' => 30]));

        $this->assertSame(70, (int) $linea->fresh()->cantidad);
    }

    public function test_un_egreso_mayor_al_stock_es_un_error_y_no_deja_negativo(): void
    {
        $linea = $this->material(cantidad: 5);

        $this->actingAs($this->admin())
            ->post('/stock/movimiento', $this->movimiento(['tipo' => 'Devolucion', 'cantidad' => 50]))
            ->assertSessionHasErrors('cantidad');

        // Antes se recortaba a cero en silencio; ahora el movimiento no se registra
        // y el stock queda como estaba (una diferencia de inventario se ve).
        $this->assertSame(5, (int) $linea->fresh()->cantidad);
        $this->assertSame(0, MovimientoInterno::count());
    }

    public function test_el_alta_crea_la_linea_si_el_material_no_existe_en_ese_deposito(): void
    {
        $this->actingAs($this->admin())
            ->post('/stock/movimiento', $this->movimiento([
                'material' => 'Roseta optica',
                'centro' => 'Sur',
                'cantidad' => 25,
            ]));

        $linea = Material::where('material', 'Roseta optica')->where('centro', 'Sur')->first();

        $this->assertNotNull($linea);
        $this->assertSame(25, (int) $linea->cantidad);
    }

    public function test_un_egreso_de_un_material_inexistente_no_crea_la_linea_y_avisa(): void
    {
        $this->actingAs($this->admin())
            ->post('/stock/movimiento', $this->movimiento([
                'material' => 'No existe',
                'tipo' => 'Baja de material',
            ]))
            ->assertSessionHasErrors('material');

        $this->assertNull(Material::where('material', 'No existe')->first());
        // No se registra un movimiento que no movió nada.
        $this->assertSame(0, MovimientoInterno::count());
    }

    public function test_el_movimiento_guarda_quien_lo_hizo_y_el_deposito(): void
    {
        $this->material();

        $this->actingAs($this->admin())->post('/stock/movimiento', $this->movimiento());

        $mov = MovimientoInterno::firstOrFail();
        $this->assertSame('LB', $mov->quien);
        $this->assertSame('Central', $mov->zona);
        $this->assertSame('Interno', $mov->tipo);
        $this->assertSame('Alta de material', $mov->movimiento);
        $this->assertSame('Ingreso de proveedor', $mov->obs);
    }

    public function test_rechaza_cantidades_y_tipos_invalidos(): void
    {
        $this->actingAs($this->admin())
            ->post('/stock/movimiento', $this->movimiento(['cantidad' => 0]))
            ->assertSessionHasErrors('cantidad');

        $this->actingAs($this->admin())
            ->post('/stock/movimiento', $this->movimiento(['cantidad' => 5000]))
            ->assertSessionHasErrors('cantidad');

        $this->actingAs($this->admin())
            ->post('/stock/movimiento', $this->movimiento(['tipo' => 'Robo']))
            ->assertSessionHasErrors('tipo');

        $this->actingAs($this->admin())
            ->post('/stock/movimiento', $this->movimiento(['centro' => 'Marte']))
            ->assertSessionHasErrors('centro');

        $this->assertSame(0, MovimientoInterno::count());
    }

    public function test_una_transferencia_mueve_las_unidades_sin_inflar_el_total(): void
    {
        $this->material('ONT Huawei', 'Central', 100);
        $this->material('ONT Huawei', 'Norte', 20);

        $this->actingAs($this->admin())
            ->post('/stock/movimiento', $this->movimiento([
                'tipo' => 'Transferencia',
                'centro_destino' => 'Norte',
                'cantidad' => 30,
            ]))
            ->assertRedirect()
            ->assertSessionHas('ok');

        $origen = Material::where('centro', 'Central')->firstOrFail();
        $destino = Material::where('centro', 'Norte')->firstOrFail();

        $this->assertSame(70, (int) $origen->cantidad);
        $this->assertSame(50, (int) $destino->cantidad);
        $this->assertSame(120, (int) Material::sum('cantidad'));
    }

    public function test_una_transferencia_a_un_deposito_sin_la_linea_la_crea(): void
    {
        $this->material('ONT Huawei', 'Central', 10);

        $this->actingAs($this->admin())
            ->post('/stock/movimiento', $this->movimiento([
                'tipo' => 'Transferencia',
                'centro_destino' => 'Sur',
                'cantidad' => 4,
            ]))
            ->assertSessionHas('ok');

        $this->assertSame(6, (int) Material::where('centro', 'Central')->value('cantidad'));
        $this->assertSame(4, (int) Material::where('centro', 'Sur')->value('cantidad'));
    }

    public function test_una_transferencia_sin_destino_es_invalida(): void
    {
        $this->material('ONT Huawei', 'Central', 10);

        $this->actingAs($this->admin())
            ->post('/stock/movimiento', $this->movimiento([
                'tipo' => 'Transferencia',
                'cantidad' => 4,
            ]))
            ->assertSessionHasErrors('centro_destino');

        $this->assertSame(10, (int) Material::where('centro', 'Central')->value('cantidad'));
        $this->assertSame(0, MovimientoInterno::count());
    }

    public function test_una_transferencia_al_mismo_deposito_es_invalida(): void
    {
        $this->material('ONT Huawei', 'Central', 10);

        $this->actingAs($this->admin())
            ->post('/stock/movimiento', $this->movimiento([
                'tipo' => 'Transferencia',
                'centro_destino' => 'Central',
                'cantidad' => 4,
            ]))
            ->assertSessionHasErrors('centro_destino');
    }

    public function test_no_se_puede_transferir_mas_de_lo_que_hay(): void
    {
        $this->material('ONT Huawei', 'Central', 3);

        $this->actingAs($this->admin())
            ->post('/stock/movimiento', $this->movimiento([
                'tipo' => 'Transferencia',
                'centro_destino' => 'Norte',
                'cantidad' => 5,
            ]))
            ->assertSessionHasErrors('cantidad');

        $this->assertSame(3, (int) Material::where('centro', 'Central')->value('cantidad'));
        $this->assertSame(0, Material::where('centro', 'Norte')->count());
    }

    public function test_el_listado_filtra_por_deposito_y_nivel_critico(): void
    {
        $user = $this->admin();
        $this->material('ONT Huawei', 'Central', 5);
        $this->material('Roseta optica', 'Sur', 500);

        // Se mira el dato que recibe la vista: el <datalist> del formulario
        // lista todos los materiales y ensuciaria un assertSee/assertDontSee.
        $porCentro = $this->actingAs($user)->get('/stock?centro=Sur')->assertOk();
        $this->assertSame(['Roseta optica'], $porCentro->viewData('filas')->pluck('material')->all());

        $criticos = $this->actingAs($user)->get('/stock?nivel=critico')->assertOk();
        $this->assertSame(['ONT Huawei'], $criticos->viewData('filas')->pluck('material')->all());
    }
}
