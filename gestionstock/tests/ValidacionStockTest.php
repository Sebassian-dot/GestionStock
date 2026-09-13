<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../includes/funciones.php';

class ValidacionStockTest extends TestCase
{
    public function testStockCeroEsValido(): void
    {
        $this->assertTrue(
            validarStock(0)
        );
    }

    public function testStockNegativoEsInvalido(): void
    {
        $this->assertFalse(
            validarStock(-1)
        );
    }
}