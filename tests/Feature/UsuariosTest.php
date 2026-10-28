<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Usuarios: alta, edicion y baja con las reglas de seguridad del sistema
 * (password hasheada, no borrarse a si mismo, no quedarse sin administrador).
 */
class UsuariosTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_crea_un_usuario_con_password_hasheada(): void
    {
        $this->actingAs($this->admin())
            ->post('/usuarios', [
                'name' => 'Nadia Ruiz',
                'email' => 'nadia@fieldops.test',
                'password' => 'plano-123456',
                'role' => 'Supervisor',
                'zona' => 'CABA Norte',
            ])
            ->assertRedirect(route('usuarios.index'))
            ->assertSessionHas('ok');

        $nuevo = User::where('email', 'nadia@fieldops.test')->firstOrFail();

        $this->assertNotSame('plano-123456', $nuevo->password);
        $this->assertTrue(Hash::check('plano-123456', $nuevo->password));
        $this->assertTrue($nuevo->activo);
        $this->assertSame('NR', $nuevo->abreviatura);
    }

    public function test_rechaza_email_duplicado_y_password_corta(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/usuarios', [
            'name' => 'Otro',
            'email' => $admin->email,
            'password' => 'corta',
            'role' => 'Tecnico',
        ])->assertSessionHasErrors(['email', 'password']);

        $this->assertSame(1, User::count());
    }

    public function test_rechaza_un_rol_que_no_existe(): void
    {
        $this->actingAs($this->admin())->post('/usuarios', [
            'name' => 'Invitado',
            'email' => 'invitado@fieldops.test',
            'password' => '12345678',
            'role' => 'Root',
        ])->assertSessionHasErrors('role');
    }

    public function test_actualiza_rol_y_estado_sin_tocar_la_password(): void
    {
        $admin = $this->admin();
        $user = User::factory()->create(['role' => 'Tecnico', 'activo' => true]);
        $hashOriginal = $user->password;

        $this->actingAs($admin)
            ->patch("/usuarios/{$user->id}", [
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'Despacho',
                // `activo` ausente = checkbox desmarcado = inactivo.
            ])
            ->assertRedirect(route('usuarios.index'))
            ->assertSessionHas('ok');

        $user->refresh();
        $this->assertSame('Despacho', $user->role);
        $this->assertFalse($user->activo);
        $this->assertSame($hashOriginal, $user->password);
    }

    public function test_actualiza_la_password_cuando_se_escribe_una_nueva(): void
    {
        $admin = $this->admin();
        $user = User::factory()->create();

        $this->actingAs($admin)->patch("/usuarios/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'activo' => '1',
            'password' => 'nueva-clave-99',
        ])->assertRedirect(route('usuarios.index'));

        $this->assertTrue(Hash::check('nueva-clave-99', $user->fresh()->password));
        $this->assertTrue($user->fresh()->activo);
    }

    public function test_elimina_un_usuario(): void
    {
        $admin = $this->admin();
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->delete("/usuarios/{$user->id}")
            ->assertRedirect(route('usuarios.index'))
            ->assertSessionHas('ok');

        $this->assertNull(User::find($user->id));
    }

    public function test_no_puede_eliminarse_a_si_mismo(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->delete("/usuarios/{$admin->id}")
            ->assertForbidden();

        $this->assertNotNull(User::find($admin->id));
    }

    public function test_no_puede_quedarse_sin_administrador_activo(): void
    {
        $admin = $this->admin();
        $otroAdmin = User::factory()->admin()->create();

        // Con dos administradores se puede borrar uno.
        $this->actingAs($admin)->delete("/usuarios/{$otroAdmin->id}")->assertRedirect();

        // Queda uno solo: el ultimo administrador activo no se puede borrar.
        $this->actingAs($admin)->delete("/usuarios/{$admin->id}")->assertForbidden();
    }

    public function test_el_listado_filtra_por_rol_y_estado(): void
    {
        $admin = $this->admin();
        User::factory()->create(['name' => 'Tecnico Uno', 'role' => 'Tecnico']);
        User::factory()->create(['name' => 'Inactivo Dos', 'role' => 'Tecnico', 'activo' => false]);

        $this->actingAs($admin)->get('/usuarios?role=Tecnico&activo=1')
            ->assertOk()
            ->assertSee('Tecnico Uno')
            ->assertDontSee('Inactivo Dos');
    }
}
