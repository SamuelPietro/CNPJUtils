<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use CNPJUtils\DigitoVerificador;

class DigitoVerificadorTest extends TestCase
{
    private DigitoVerificador $dv;

    /**
     * Configura o ambiente de teste.
     */
    protected function setUp(): void
    {
        $this->dv = new DigitoVerificador();
    }

    public function testCalcularDigitos(): void
    {
        $cnpjBase = '123456789012';
        $digitos = $this->dv->calcularDigitos($cnpjBase);
        $this->assertMatchesRegularExpression('/^\d{2}$/', $digitos);
    }

    public function testCalcularDigitosComTamanhoInvalido(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->dv->calcularDigitos('12345678'); // Deve ter 12 caracteres
    }
}
