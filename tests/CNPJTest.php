<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use CNPJUtils\CNPJ;

class CNPJTest extends TestCase
{
    /**
     * Testa a geração de um CNPJ alfanumérico válido.
     * @throws Exception Caso haja um erro na geração dos caracteres aleatórios.
     */
    public function testGerarCnpj(): void
    {
        $cnpj = CNPJ::gerar();
        $this->assertIsString($cnpj);
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{2}\.[A-Z0-9]{3}\.[A-Z0-9]{3}\/[A-Z0-9]{4}-\d{2}$/', $cnpj);
    }

    /**
     * Testa a validação de um CNPJ.
     */
    public function testValidarCnpj(): void
    {
        $cnpjValido = 'AB123456789012';
        $this->assertFalse(CNPJ::validar($cnpjValido));
    }

    /**
     * Testa a formatação de um CNPJ.
     */
    public function testMascarar(): void
    {
        $this->assertEquals('AB.123.456/7890-12', CNPJ::mascarar('AB123456789012'));
        $this->assertEquals('12.345.678/9012-34', CNPJ::mascarar('12345678901234'));
    }

    /**
     * Testa a remoção da máscara de um CNPJ.
     */
    public function testRemoverMascara(): void
    {
        $this->assertEquals('AB123456789012', CNPJ::removerMascara('AB.123.456/7890-12'));
        $this->assertEquals('12345678901234', CNPJ::removerMascara('12.345.678/9012-34'));
    }

}
