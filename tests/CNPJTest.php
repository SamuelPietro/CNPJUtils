<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use CNPJUtils\CNPJ;

/**
 * Testes para a classe CNPJ
 */
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
        $this->assertTrue(CNPJ::validar($cnpj));
    }

    /**
     * Testa a validação de CNPJs válidos.
     */
    public function testValidarCnpjValidos(): void
    {
        // CNPJ numérico válido
        $this->assertTrue(CNPJ::validar('51.090.626/0001-77'));
        $this->assertTrue(CNPJ::validar('05.475.103/0001-21'));
        $this->assertTrue(CNPJ::validar('20.971.057/0001-45'));

        
        // CNPJ alfanumérico válido
        $this->assertTrue(CNPJ::validar('12.ABC.345/01DE-35'));
        $this->assertTrue(CNPJ::validar('12.GHI.345/01JK-03'));

        // CNPJs com diferentes combinações de letras permitidas
        $this->assertTrue(CNPJ::validar('12.ABC.345/01DE-35')); // A, B, C, D, E
        $this->assertTrue(CNPJ::validar('12.GHJ.345/01KL-35')); // G, H, J, K, L
        $this->assertTrue(CNPJ::validar('12.MPR.345/01ST-35')); // M, P, R, S, T
        $this->assertTrue(CNPJ::validar('12.VWX.345/01YZ-35')); // V, W, X, Y, Z
    }

    /**
     * Testa a validação de CNPJs inválidos.
     */
    public function testValidarCnpjInvalidos(): void
    {
        // CNPJ com letras proibidas
        $this->assertFalse(CNPJ::validar('12.ABC.345/01IF-35')); // Contém I e F
        $this->assertFalse(CNPJ::validar('12.ABC.345/01OQ-35')); // Contém O e Q
        $this->assertFalse(CNPJ::validar('12.ABC.345/01UQ-35')); // Contém U e Q

        // CNPJ com formato inválido
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE-3')); // Faltando um dígito
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE-35X')); // Dígito extra
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE-XX')); // Dígitos não numéricos
        
        // Mais casos de formato inválido
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE')); // Sem dígitos verificadores
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE-3')); // Apenas um dígito verificador
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE-3X')); // Segundo dígito não numérico
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE-X3')); // Primeiro dígito não numérico
        
        // CNPJs com caracteres especiais inválidos
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE-35!')); // Caractere especial no final
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE-35 ')); // Espaço no final
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE-35.')); // Ponto no final
        
        // CNPJs com dígitos verificadores incorretos
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE-34')); // Dígitos verificadores errados
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE-36')); // Dígitos verificadores errados
    }

    /**
     * Testa a formatação de um CNPJ.
     */
    public function testMascarar(): void
    {
        $this->assertEquals('AB.123.456/7890-12', CNPJ::mascarar('AB123456789012'));
        $this->assertEquals('12.345.678/9012-34', CNPJ::mascarar('12345678901234'));
        $this->assertEquals('12.ABC.345/01DE-35', CNPJ::mascarar('12ABC34501DE35'));
        
        // Testes com CNPJs já mascarados
        $this->assertEquals('AB.123.456/7890-12', CNPJ::mascarar('AB.123.456/7890-12'));
        $this->assertEquals('12.345.678/9012-34', CNPJ::mascarar('12.345.678/9012-34'));
        
        // Testes com CNPJs em diferentes formatos
        $this->assertEquals('AB.123.456/7890-12', CNPJ::mascarar('AB123456789012'));
        $this->assertEquals('AB.123.456/7890-12', CNPJ::mascarar('AB.123.456.7890.12'));
        $this->assertEquals('AB.123.456/7890-12', CNPJ::mascarar('AB-123-456-7890-12'));
    }

    /**
     * Testa a remoção da máscara de um CNPJ.
     */
    public function testRemoverMascara(): void
    {
        $this->assertEquals('AB123456789012', CNPJ::removerMascara('AB.123.456/7890-12'));
        $this->assertEquals('12345678901234', CNPJ::removerMascara('12.345.678/9012-34'));
        $this->assertEquals('12ABC34501DE35', CNPJ::removerMascara('12.ABC.345/01DE-35'));
        
        // Testes com CNPJs sem máscara
        $this->assertEquals('AB123456789012', CNPJ::removerMascara('AB123456789012'));
        $this->assertEquals('12345678901234', CNPJ::removerMascara('12345678901234'));
        
        // Testes com CNPJs em diferentes formatos
        $this->assertEquals('AB123456789012', CNPJ::removerMascara('AB.123.456.7890.12'));
        $this->assertEquals('AB123456789012', CNPJ::removerMascara('AB-123-456-7890-12'));
        $this->assertEquals('AB123456789012', CNPJ::removerMascara('AB 123 456 7890 12'));
    }

    /**
     * Testa a geração de múltiplos CNPJs.
     */
    public function testGerarMultiplosCnpjs(): void
    {
        $cnpjs = [];
        for ($i = 0; $i < 100; $i++) {
            $cnpj = CNPJ::gerar();
            $this->assertIsString($cnpj);
            $this->assertMatchesRegularExpression('/^[A-Z0-9]{2}\.[A-Z0-9]{3}\.[A-Z0-9]{3}\/[A-Z0-9]{4}-\d{2}$/', $cnpj);
            $this->assertTrue(CNPJ::validar($cnpj));
            $cnpjs[] = $cnpj;
        }
        
        // Verifica se todos os CNPJs gerados são únicos
        $this->assertCount(100, array_unique($cnpjs), 'Todos os CNPJs gerados devem ser únicos');
    }
}
