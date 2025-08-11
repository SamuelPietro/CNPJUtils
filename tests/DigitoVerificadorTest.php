<?php

declare(strict_types=1);

use CNPJUtils\DigitoVerificador;
use PHPUnit\Framework\TestCase;

/**
 * Testes para a classe DigitoVerificador
 */
class DigitoVerificadorTest extends TestCase
{
    /**
     * Testa o cálculo de dígitos verificadores.
     */
    public function testCalcularDigitos(): void
    {
        // Teste com CNPJ numérico
        $cnpjBase = '123456789012';
        $digitos = DigitoVerificador::calcular($cnpjBase);
        $this->assertIsString($digitos);
        $this->assertMatchesRegularExpression('/^\d{2}$/', $digitos);

        // Teste com CNPJ alfanumérico
        $cnpjBase = '12ABC34501DE';
        $digitos = DigitoVerificador::calcular($cnpjBase);
        $this->assertIsString($digitos);
        $this->assertMatchesRegularExpression('/^\d{2}$/', $digitos);

        // Teste com CNPJ que resulta em DV = 0
        $cnpjBase = '000000000000';
        $digitos = DigitoVerificador::calcular($cnpjBase);
        $this->assertEquals('00', $digitos);

        // Teste com CNPJ que resulta em DV = 1
        $cnpjBase = '000000000001';
        $digitos = DigitoVerificador::calcular($cnpjBase);
        $this->assertEquals('01', $digitos);
    }

    /**
     * Testa o cálculo de dígitos verificadores com tamanho inválido.
     */
    public function testCalcularDigitosComTamanhoInvalido(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DigitoVerificador::calcular('12345678'); // Deve ter 12 caracteres
    }

    /**
     * Testa o cálculo de dígitos verificadores com caracteres inválidos.
     */
    public function testCalcularDigitosComCaracteresInvalidos(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DigitoVerificador::calcular('12345678901!'); // Caractere especial inválido
    }

    /**
     * Testa o cálculo de dígitos verificadores com letras proibidas.
     */
    public function testCalcularDigitosComLetrasProibidas(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DigitoVerificador::calcular('12345678901I'); // Letra I proibida
    }

    /**
     * Testa o cálculo de dígitos verificadores com diferentes combinações de letras permitidas.
     */
    public function testCalcularDigitosComLetrasPermitidas(): void
    {
        // Teste com letras A-E
        $digitos = DigitoVerificador::calcular('12ABC34501DE');
        $this->assertIsString($digitos);
        $this->assertMatchesRegularExpression('/^\d{2}$/', $digitos);

        // Teste com letras G-L
        $digitos = DigitoVerificador::calcular('12GHJ34501KL');
        $this->assertIsString($digitos);
        $this->assertMatchesRegularExpression('/^\d{2}$/', $digitos);

        // Teste com letras M-T
        $digitos = DigitoVerificador::calcular('12MPR34501ST');
        $this->assertIsString($digitos);
        $this->assertMatchesRegularExpression('/^\d{2}$/', $digitos);

        // Teste com letras V-Z
        $digitos = DigitoVerificador::calcular('12VWX34501YZ');
        $this->assertIsString($digitos);
        $this->assertMatchesRegularExpression('/^\d{2}$/', $digitos);
    }

    public function testCalculoDvComExemplo(): void
    {
        $cnpj = '12ABC34501DE';
        $dvs = DigitoVerificador::calcular($cnpj);
        $this->assertEquals('35', $dvs);
    }
}
