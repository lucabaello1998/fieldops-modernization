<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Busqueda global.
 *
 * Estos tests fijan que comillas y operadores se traten como texto literal y
 * que nada se escape del `LIKE`.
 */
class BusquedaTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    private function datos(): void
    {
        Tarea::create([
            'token' => 't1', 'quien' => 'LB', 'cuando' => now(),
            'titulo' => 'Instalacion Rivadavia', 'tarea' => 'Instalacion', 'abreviado' => 'Ins',
            'descripcion' => 'Cliente residencial', 'a_quien' => 'JPER', 'prioridad' => 1,
            'inicio' => now()->toDateString(), 'fin' => now()->addDay()->toDateString(),
            'estado' => 'Pendiente', 'sub_tarea' => '', 'sub_estado' => '',
            'color' => 'slate', 'mensaje' => '',
            'archivo_uno' => '', 'archivo_dos' => '', 'archivo_tres' => '',
            'nom_archivo_uno' => '', 'nom_archivo_dos' => '', 'nom_archivo_tres' => '',
        ]);

        Material::create([
            'material' => 'Roseta optica', 'centro' => 'Sur', 'cantidad' => 20,
            'fecha' => now()->toDateString(), 'fechaegre' => now()->toDateString(),
            'tecnico' => '', 'cantec' => 0,
        ]);
    }

    public function test_sin_termino_suficiente_no_devuelve_resultados(): void
    {
        $this->datos();

        $this->actingAs($this->admin())->get('/buscar?q=a')
            ->assertOk()
            ->assertDontSee('Instalacion Rivadavia')
            ->assertDontSee('Roseta optica');
    }

    public function test_encuentra_resultados_en_varias_tablas(): void
    {
        $this->datos();

        // La vista resalta el termino con <mark>, asi que se verifica el dato
        // que recibe la vista y no el HTML literal.
        $tareas = $this->actingAs($this->admin())->get('/buscar?q=Rivadavia')->assertOk();
        $this->assertTrue($tareas->viewData('tareas')->pluck('titulo')->contains('Instalacion Rivadavia'));

        $materiales = $this->actingAs($this->admin())->get('/buscar?q=Roseta')->assertOk();
        $this->assertTrue($materiales->viewData('materiales')->pluck('material')->contains('Roseta optica'));
    }

    public function test_un_termino_con_comillas_queda_como_texto_literal(): void
    {
        $this->datos();

        $r = $this->actingAs($this->admin())->get('/buscar?q=' . urlencode("zzz' OR 1=1 -- -"));

        $r->assertOk();
        // Si la inyeccion funcionara, veriamos todos los registros de la base.
        $r->assertDontSee('Instalacion Rivadavia');
        $r->assertDontSee('Roseta optica');
    }

    public function test_los_comodines_like_se_escapan(): void
    {
        $this->datos();

        // Sin escapar, '%%' seria LIKE '%%%' y devolveria todo.
        $this->actingAs($this->admin())->get('/buscar?q=' . urlencode('%%'))
            ->assertOk()
            ->assertDontSee('Instalacion Rivadavia')
            ->assertDontSee('Roseta optica');

        // '_' tampoco debe funcionar como comodin de un caracter.
        $this->actingAs($this->admin())->get('/buscar?q=' . urlencode('Roseta_'))
            ->assertOk()
            ->assertDontSee('Roseta optica');
    }
}
