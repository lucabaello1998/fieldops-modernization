<?php

namespace Tests\Feature;

use App\Models\Alta;
use App\Models\Tarea;
use App\Models\Tecnico;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Render de todas las pantallas: con datos (seed completo) y en estado vacio.
 * Si una vista rompe por un `@foreach` sobre null o una variable faltante, esto falla.
 */
class PaginasTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_todas_las_pantallas_responden_con_el_seed_completo(): void
    {
        $this->seed();
        $user = User::where('role', 'Administrador')->firstOrFail();

        $paginas = [
            '/', '/tareas', '/altas', '/stock', '/tecnicos', '/usuarios',
            '/buscar?q=instalacion', '/tareas?q=a', '/tareas?prioridad=1',
            '/stock?centro=Central&nivel=critico', '/stock?orden=material',
            '/usuarios?role=Tecnico', '/usuarios?activo=1',
        ];

        foreach ($paginas as $url) {
            $r = $this->actingAs($user)->get($url);
            $r->assertOk();
            $this->assertStringNotContainsString('SQLSTATE', $r->getContent(), "Error de SQL en {$url}");
            $this->assertStringNotContainsString('Undefined variable', $r->getContent(), "Variable sin definir en {$url}");
        }
    }

    public function test_fichas_de_detalle_responden(): void
    {
        $this->seed();
        $user = User::where('role', 'Administrador')->firstOrFail();

        $alta = Alta::firstOrFail();
        $tecnico = Tecnico::firstOrFail();

        $this->actingAs($user)->get("/altas/{$alta->id}")->assertOk();
        $this->actingAs($user)->get("/tecnicos/{$tecnico->id}")->assertOk();
    }

    public function test_las_pantallas_soportan_base_vacia(): void
    {
        $user = $this->admin();

        foreach (['/', '/tareas', '/altas', '/stock', '/tecnicos', '/usuarios', '/buscar?q=zz'] as $url) {
            $this->actingAs($user)->get($url)->assertOk();
        }
    }

    public function test_detalle_inexistente_da_404(): void
    {
        $user = $this->admin();

        $this->actingAs($user)->get('/altas/999999')->assertNotFound();
        $this->actingAs($user)->get('/tecnicos/999999')->assertNotFound();
    }

    public function test_la_ficha_de_tarea_devuelve_json(): void
    {
        $this->seed();
        $user = User::where('role', 'Administrador')->firstOrFail();
        $tarea = Tarea::firstOrFail();

        $this->actingAs($user)->getJson("/tareas/{$tarea->id}")
            ->assertOk()
            ->assertJsonStructure([
                'id', 'titulo', 'tarea', 'descripcion', 'estado', 'prioridad',
                'a_quien', 'quien', 'inicio', 'fin', 'cuando', 'sub_tarea',
                'sub_estado', 'mensaje', 'token',
            ])
            ->assertJson(['id' => $tarea->id]);
    }
}
