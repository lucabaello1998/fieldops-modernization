<?php

namespace Tests\Feature;

use App\Models\Tarea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Kanban: alta, movimiento entre columnas, notas y borrado.
 * Se valida rol, estado y se deja trazabilidad en la bitacora.
 */
class TareasTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create(['abreviatura' => 'LB']);
    }

    private function datosValidos(array $extra = []): array
    {
        return array_merge([
            'titulo' => 'Instalar ONT en Av. Rivadavia 1234',
            'tarea' => 'Instalacion',
            'descripcion' => 'Cliente nuevo, fibra disponible en el palier.',
            'a_quien' => 'JPER',
            'prioridad' => 1,
            'inicio' => now()->toDateString(),
            'fin' => now()->addDays(3)->toDateString(),
        ], $extra);
    }

    /** Inserta una tarea cruda respetando las columnas NOT NULL del esquema real. */
    private function tarea(array $extra = []): Tarea
    {
        return Tarea::create(array_merge([
            'token' => 'tok'.random_int(1000, 9999),
            'quien' => 'LB',
            'cuando' => now(),
            'titulo' => 'Tarea de prueba',
            'tarea' => 'Revision',
            'abreviado' => 'Rev',
            'descripcion' => '',
            'a_quien' => 'JPER',
            'prioridad' => 2,
            'inicio' => now()->toDateString(),
            'fin' => now()->addDays(2)->toDateString(),
            'estado' => 'Pendiente',
            'sub_tarea' => '',
            'sub_estado' => '',
            'color' => 'slate',
            'mensaje' => '',
            'archivo_uno' => '', 'archivo_dos' => '', 'archivo_tres' => '',
            'nom_archivo_uno' => '', 'nom_archivo_dos' => '', 'nom_archivo_tres' => '',
        ], $extra));
    }

    public function test_crea_una_tarea_completa_con_las_columnas_not_null_del_legacy(): void
    {
        $this->actingAs($this->admin())
            ->post('/tareas', $this->datosValidos())
            ->assertRedirect()
            ->assertSessionHas('ok');

        $tarea = Tarea::firstOrFail();

        $this->assertSame('Instalar ONT en Av. Rivadavia 1234', $tarea->titulo);
        $this->assertSame('Pendiente', $tarea->estado);
        $this->assertSame('LB', $tarea->quien);
        $this->assertSame('Ins', $tarea->abreviado);

        // Si alguna de estas columnas NOT NULL faltara, MySQL devuelve error 500.
        foreach (['archivo_uno', 'archivo_dos', 'archivo_tres',
            'nom_archivo_uno', 'nom_archivo_dos', 'nom_archivo_tres',
            'sub_tarea', 'sub_estado', 'mensaje'] as $col) {
            $this->assertSame('', $tarea->{$col}, "La columna {$col} quedo NULL");
        }

        $this->assertSame('slate', $tarea->color);

        $this->assertNotEmpty($tarea->token);
    }

    public function test_la_tarea_creada_aparece_en_el_tablero(): void
    {
        $user = $this->admin();
        $this->actingAs($user)->post('/tareas', $this->datosValidos());

        $this->actingAs($user)->get('/tareas')
            ->assertOk()
            ->assertSee('Instalar ONT en Av. Rivadavia 1234');
    }

    public function test_rechaza_alta_sin_campos_obligatorios(): void
    {
        $this->actingAs($this->admin())
            ->post('/tareas', [])
            ->assertSessionHasErrors(['titulo', 'tarea', 'a_quien', 'prioridad']);

        $this->assertSame(0, Tarea::count());
    }

    public function test_rechaza_prioridad_invalida_y_fechas_invertidas(): void
    {
        $this->actingAs($this->admin())
            ->post('/tareas', $this->datosValidos([
                'prioridad' => 9,
                'inicio' => now()->toDateString(),
                'fin' => now()->subDays(5)->toDateString(),
            ]))
            ->assertSessionHasErrors(['prioridad', 'fin']);
    }

    public function test_mover_la_tarea_cambia_estado_color_y_deja_bitacora(): void
    {
        $tarea = $this->tarea();

        $this->actingAs($this->admin())
            ->patchJson("/tareas/{$tarea->id}/estado", ['estado' => 'En curso'])
            ->assertOk()
            ->assertJson(['ok' => true, 'estado' => 'En curso']);

        $tarea->refresh();
        $this->assertSame('En curso', $tarea->estado);
        $this->assertSame('indigo', $tarea->color);
        $this->assertStringContainsString('movio la tarea de "Pendiente" a "En curso"', $tarea->mensaje);
        $this->assertStringContainsString('LB', $tarea->mensaje);
    }

    public function test_finalizar_marca_el_color_y_conserva_la_fecha_de_fin(): void
    {
        // En el esquema real `fin` es NOT NULL, asi que siempre viene con fecha.
        $tarea = $this->tarea(['estado' => 'En revision', 'color' => 'sky']);

        $this->actingAs($this->admin())->patchJson("/tareas/{$tarea->id}/estado", ['estado' => 'Finalizado'])
            ->assertOk();

        $this->assertSame('Finalizado', $tarea->fresh()->estado);
        $this->assertSame('emerald', $tarea->fresh()->color);
        $this->assertNotNull($tarea->fresh()->fin);
    }

    public function test_rechaza_un_estado_que_no_existe(): void
    {
        $tarea = $this->tarea();

        $this->actingAs($this->admin())
            ->patchJson("/tareas/{$tarea->id}/estado", ['estado' => 'Archivada'])
            ->assertStatus(422);
    }

    public function test_agregar_una_nota_queda_firmada(): void
    {
        $tarea = $this->tarea();

        $this->actingAs($this->admin())
            ->post("/tareas/{$tarea->id}/nota", ['mensaje' => 'Cliente ausente, reprogramar.'])
            ->assertRedirect()
            ->assertSessionHas('ok');

        $this->assertStringContainsString('Cliente ausente, reprogramar.', $tarea->fresh()->mensaje);
        $this->assertStringContainsString('LB:', $tarea->fresh()->mensaje);
    }

    public function test_rechaza_una_nota_vacia_o_demasiado_larga(): void
    {
        $tarea = $this->tarea();

        $this->actingAs($this->admin())
            ->post("/tareas/{$tarea->id}/nota", ['mensaje' => ''])
            ->assertSessionHasErrors('mensaje');

        $this->actingAs($this->admin())
            ->post("/tareas/{$tarea->id}/nota", ['mensaje' => str_repeat('x', 1001)])
            ->assertSessionHasErrors('mensaje');
    }

    public function test_eliminar_una_tarea(): void
    {
        $tarea = $this->tarea(['titulo' => 'Borrar']);

        $this->actingAs($this->admin())
            ->delete("/tareas/{$tarea->id}")
            ->assertRedirect()
            ->assertSessionHas('ok');

        $this->assertNull(Tarea::find($tarea->id));
    }
}
