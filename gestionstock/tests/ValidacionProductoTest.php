<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../includes/funciones.php';

class ValidacionProductoTest extends TestCase
{
    public function testPrecioPositivoEsValido(): void
    {
        $this->assertTrue(
            validarPrecio(45000)
        );
    }

    public function testPrecioNegativoEsInvalido(): void
    {
        $this->assertFalse(
            validarPrecio(-5000)
        );
    }
}