<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../includes/funciones.php';

class SalidaInventarioTest extends TestCase
{
    public function testSalidaNoPuedeSuperarStock(): void
    {
        $this->assertFalse(
            validarSalidaInventario(20, 50)
        );
    }

    public function testSalidaValidaCuandoNoSuperaStock(): void
    {
        $this->assertTrue(
            validarSalidaInventario(20, 5)
        );
    }
}
