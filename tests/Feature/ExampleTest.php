<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Smoke test del arranque: la raiz exige sesion y el healthcheck responde.
 */
class ExampleTest extends TestCase
{
    public function test_la_raiz_pide_login_a_un_invitado(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_el_healthcheck_responde(): void
    {
        $this->get('/up')->assertOk();
    }
}
