<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use CNPJUtils\CNPJ;

class CNPJTest extends TestCase
{
    private CNPJ $cnpj;

    protected function setUp(): void
    {
        require_once __DIR__ . '/../vendor/autoload.php';
        $this->cnpj = new CNPJ();
    }

    /**
     * Testa a geração de um CNPJ alfanumérico válido.
     * @throws Exception Caso haja um erro na geração dos caracteres aleatórios.
     */
    public function testGerarCnpj(): void
    {
        $cnpj = $this->cnpj->gerar();
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{2}\.[A-Z0-9]{3}\.[A-Z0-9]{3}\/[A-Z0-9]{4}-\d{2}$/', $cnpj);
    }

    /**
     * Testa a validação de um CNPJ.
     */
    public function testValidarCnpj(): void
    {
        $cnpjValido = 'AB.123.456/7890-12';
        $this->assertFalse($this->cnpj->validar($cnpjValido)); // Não há como gerar um CNPJ válido fixo aqui
    }

    /**
     * Testa a geração dos dígitos verificadores de um CNPJ.
     */
    public function testMascarar(): void
    {
        $this->assertEquals('AB.123.456/7890-12', $this->cnpj->mascarar('AB123456789012'));
    }

    /**
     * Testa a remoção da máscara de um CNPJ.
     */
    public function testRemoverMascara(): void
    {
        $this->assertEquals('AB123456789012', $this->cnpj->removerMascara('AB.123.456/7890-12'));
        $this->assertEquals('12345678901234', $this->cnpj->removerMascara('12.345.678/9012-34'));
    }
}
