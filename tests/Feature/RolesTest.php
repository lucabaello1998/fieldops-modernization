<?php

namespace Tests\Feature;

use App\Models\Tarea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Control de acceso por rol: el middleware `role:Administrador` y las reglas
 * de negocio (solo Administrador/Supervisor eliminan tareas).
 */
class RolesTest extends TestCase
{
    use RefreshDatabase;

    private function tarea(): Tarea
    {
        return Tarea::create([
            'token' => 'tok', 'quien' => 'LB', 'cuando' => now(),
            'titulo' => 'T', 'tarea' => 'T', 'abreviado' => 'T',
            'descripcion' => '', 'a_quien' => 'JPER', 'prioridad' => 2,
            'inicio' => now()->toDateString(), 'fin' => now()->toDateString(),
            'estado' => 'Pendiente', 'sub_tarea' => '', 'sub_estado' => '',
            'color' => 'slate', 'mensaje' => '',
            'archivo_uno' => '', 'archivo_dos' => '', 'archivo_tres' => '',
            'nom_archivo_uno' => '', 'nom_archivo_dos' => '', 'nom_archivo_tres' => '',
        ]);
    }

    public function test_un_tecnico_no_entra_a_usuarios(): void
    {
        $tecnico = User::factory()->create(['role' => 'Tecnico']);

        $this->actingAs($tecnico)->get('/usuarios')->assertForbidden();
        $this->actingAs($tecnico)->post('/usuarios', [])->assertForbidden();
    }

    public function test_un_supervisor_tampoco_administra_usuarios(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $this->actingAs($supervisor)->get('/usuarios')->assertForbidden();
    }

    public function test_un_tecnico_no_puede_eliminar_tareas_pero_si_moverlas(): void
    {
        $tecnico = User::factory()->create(['role' => 'Tecnico']);
        $tarea = $this->tarea();

        $this->actingAs($tecnico)->delete("/tareas/{$tarea->id}")->assertForbidden();
        $this->assertNotNull(Tarea::find($tarea->id));

        $this->actingAs($tecnico)
            ->patchJson("/tareas/{$tarea->id}/estado", ['estado' => 'En curso'])
            ->assertOk();
    }

    public function test_un_supervisor_si_puede_eliminar_tareas(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        $tarea = $this->tarea();

        $this->actingAs($supervisor)->delete("/tareas/{$tarea->id}")->assertRedirect();
        $this->assertNull(Tarea::find($tarea->id));
    }

    public function test_todos_los_roles_entran_al_panel_operativo(): void
    {
        $tarea = $this->tarea();

        foreach (['Administrador', 'Supervisor', 'Tecnico', 'Deposito', 'Despacho'] as $rol) {
            $user = User::factory()->create(['role' => $rol]);

            foreach (['/', '/tareas', '/altas', '/stock', '/tecnicos', '/buscar?q=ab'] as $url) {
                $this->actingAs($user)->get($url)->assertOk();
            }
        }

        $this->assertNotNull(Tarea::find($tarea->id));
    }
}
