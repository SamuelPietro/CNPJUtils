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
    }

    /**
     * Testa os exemplos oficiais da Receita Federal (IN RFB 2.229/2024).
     */
    public function testCalcularDigitosExemplosOficiais(): void
    {
        $this->assertSame('35', DigitoVerificador::calcular('12ABC34501DE'));
        $this->assertSame('12', DigitoVerificador::calcular('00000000E08G'));
        $this->assertSame('81', DigitoVerificador::calcular('112223330001'));
    }

    /**
     * A base '000000000001' segue o cálculo normal (00.000.000/0001-91).
     */
    public function testCalcularDigitosBaseUm(): void
    {
        $this->assertSame('91', DigitoVerificador::calcular('000000000001'));
    }

    /**
     * A base zerada não é um CNPJ válido.
     */
    public function testCalcularDigitosComBaseZerada(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DigitoVerificador::calcular('000000000000');
    }

    /**
     * Quebra de linha no final da base não pode ser aceita pela regex.
     */
    public function testCalcularDigitosComQuebraDeLinhaNoFinal(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DigitoVerificador::calcular("12ABC34501DE\n");
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
     * Todas as 26 letras são válidas, inclusive I, O, U, Q e F.
     */
    public function testCalcularDigitosComLetrasIOUQF(): void
    {
        $this->assertSame('11', DigitoVerificador::calcular('12345678901I'));
        $this->assertSame('25', DigitoVerificador::calcular('IOUQF34501DE'));
        $this->assertSame('25', DigitoVerificador::calcular('iouqf34501de'));
    }

    /**
     * Testa o cálculo de dígitos verificadores com diferentes combinações de letras.
     */
    public function testCalcularDigitosComDiferentesLetras(): void
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
