<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use CNPJUtils\DigitoVerificador;

class DigitoVerificadorTest extends TestCase
{
    public function testCalcularDigitos(): void
    {
        $cnpjBase = '123456789012';
        $digitos = DigitoVerificador::calcular($cnpjBase);
        $this->assertIsString($digitos);
        $this->assertMatchesRegularExpression('/^\d{2}$/', $digitos);
    }

    public function testCalcularDigitosComTamanhoInvalido(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DigitoVerificador::calcular('12345678'); // Deve ter 12 caracteres
    }
}
