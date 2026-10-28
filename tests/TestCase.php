<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Los tests corren siempre en `testing` y contra la base `fieldops_test`.
     *
     * Se fija antes de bootear la app (no en phpunit.xml) porque el contenedor
     * puede exportar APP_ENV/DB_DATABASE como variables de entorno reales y
     * esas tienen prioridad sobre el archivo de PHPUnit. Asi la suite nunca
     * puede tocar la base de la demo.
     */
    public function createApplication()
    {
        foreach (['APP_ENV' => 'testing', 'DB_DATABASE' => 'fieldops_test'] as $clave => $valor) {
            putenv("{$clave}={$valor}");
            $_ENV[$clave] = $valor;
            $_SERVER[$clave] = $valor;
        }

        return parent::createApplication();
    }
}
