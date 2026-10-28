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

    public function test_un_egreso_mayor_al_stock_deja_cero_y_nunca_negativo(): void
    {
        $linea = $this->material(cantidad: 5);

        $this->actingAs($this->admin())
            ->post('/stock/movimiento', $this->movimiento(['tipo' => 'Devolucion', 'cantidad' => 50]));

        $this->assertSame(0, (int) $linea->fresh()->cantidad);
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

    public function test_un_egreso_de_un_material_inexistente_no_crea_la_linea(): void
    {
        $this->actingAs($this->admin())
            ->post('/stock/movimiento', $this->movimiento([
                'material' => 'No existe',
                'tipo' => 'Baja de material',
            ]));

        $this->assertNull(Material::where('material', 'No existe')->first());
        // El movimiento igual queda registrado en la bitacora.
        $this->assertSame(1, MovimientoInterno::count());
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
