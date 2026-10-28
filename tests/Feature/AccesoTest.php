<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Autenticacion: acceso, credenciales, throttle y logout.
 */
class AccesoTest extends TestCase
{
    use RefreshDatabase;

    public function test_invitado_es_redirigido_al_login_en_todas_las_pantallas(): void
    {
        foreach (['/', '/tareas', '/altas', '/stock', '/tecnicos', '/usuarios', '/buscar'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }
    }

    public function test_pantalla_de_login_responde_y_muestra_la_marca(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('FieldOps')
            ->assertSee('Entrar');
    }

    public function test_login_con_credenciales_validas_entra_al_panel(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@fieldops.test',
            'password' => 'secreto123',
            'role' => 'Administrador',
            'activo' => true,
        ]);

        $this->post('/login', [
            'email' => 'admin@fieldops.test',
            'password' => 'secreto123',
        ])->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_actualiza_el_ultimo_acceso(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@fieldops.test',
            'password' => 'secreto123',
            'ultimo_acceso' => null,
        ]);

        $this->post('/login', ['email' => 'admin@fieldops.test', 'password' => 'secreto123']);

        $this->assertNotNull($user->fresh()->ultimo_acceso);
    }

    public function test_password_incorrecta_no_autentica_y_no_revela_si_el_usuario_existe(): void
    {
        User::factory()->create(['email' => 'admin@fieldops.test', 'password' => 'secreto123']);

        $this->post('/login', ['email' => 'admin@fieldops.test', 'password' => 'incorrecta'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_usuario_inactivo_no_puede_entrar(): void
    {
        User::factory()->create([
            'email' => 'baja@fieldops.test',
            'password' => 'secreto123',
            'activo' => false,
        ]);

        $this->post('/login', ['email' => 'baja@fieldops.test', 'password' => 'secreto123'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_email_invalido_falla_la_validacion(): void
    {
        $this->post('/login', ['email' => 'no-es-un-mail', 'password' => 'x'])
            ->assertSessionHasErrors('email');
    }

    public function test_el_throttle_corta_los_intentos_brutos(): void
    {
        // IP propia: el limitador es por IP y el resto de los tests comparten la default.
        $this->withServerVariables(['REMOTE_ADDR' => '10.9.9.9']);

        for ($i = 0; $i < 6; $i++) {
            $this->post('/login', ['email' => 'x@y.com', 'password' => 'mal']);
        }

        $this->post('/login', ['email' => 'x@y.com', 'password' => 'mal'])
            ->assertStatus(429);
    }

    public function test_logout_cierra_la_sesion(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }
}
