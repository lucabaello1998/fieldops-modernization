<?php

namespace Tests\Feature;

use App\Models\Alta;
use App\Models\Tarea;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * El contenedor de produccion arranca con RUN_SEED=1 y Fly apaga/arranca la maquina:
 * sembrar dos veces no puede duplicar la demo.
 */
class SembradoTest extends TestCase
{
    use RefreshDatabase;

    public function test_sembrar_dos_veces_no_duplica_los_datos(): void
    {
        $this->seed();

        $tareas = Tarea::query()->count();
        $altas = Alta::query()->count();

        $this->assertGreaterThan(0, $tareas);

        $this->seed();

        $this->assertSame($tareas, Tarea::query()->count());
        $this->assertSame($altas, Alta::query()->count());
    }
}
