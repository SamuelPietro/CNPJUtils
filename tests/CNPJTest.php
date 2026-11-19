<?php

declare(strict_types=1);

use CNPJUtils\CNPJ;
use PHPUnit\Framework\TestCase;

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
        // CNPJ numérico válido (com máscara)
        $this->assertTrue(CNPJ::validar('51.090.626/0001-77'));
        $this->assertTrue(CNPJ::validar('05.475.103/0001-21'));
        $this->assertTrue(CNPJ::validar('20.971.057/0001-45'));

        // CNPJ numérico válido (sem máscara)
        $this->assertTrue(CNPJ::validar('51090626000177'));
        $this->assertTrue(CNPJ::validar('05475103000121'));
        $this->assertTrue(CNPJ::validar('20971057000145'));

        // CNPJ alfanumérico válido (com máscara)
        $this->assertTrue(CNPJ::validar('12.ABC.345/01DE-35'));
        $this->assertTrue(CNPJ::validar('12.GHJ.345/01KL-72'));

        // CNPJ alfanumérico válido (sem máscara)
        $this->assertTrue(CNPJ::validar('12ABC34501DE35'));
        $this->assertTrue(CNPJ::validar('12GHJ34501KL72'));

        // CNPJs com diferentes combinações de letras
        $this->assertTrue(CNPJ::validar('12.ABC.345/01DE-35')); // A, B, C, D, E
        $this->assertTrue(CNPJ::validar('12.GHJ.345/01KL-72')); // G, H, J, K, L
        $this->assertTrue(CNPJ::validar('12.MPR.345/01ST-49')); // M, P, R, S, T
        $this->assertTrue(CNPJ::validar('12.VWX.345/01YZ-05')); // V, W, X, Y, Z
    }

    /**
     * Testa os exemplos oficiais da Receita Federal (IN RFB 2.229/2024).
     */
    public function testValidarExemplosOficiais(): void
    {
        $this->assertTrue(CNPJ::validar('12.ABC.345/01DE-35')); // Exemplo oficial
        $this->assertTrue(CNPJ::validar('12abc34501de35')); // Minúsculas
        $this->assertTrue(CNPJ::validar('00.000.000/E08G-12')); // Primeiro CNPJ alfanumérico emitido
        $this->assertTrue(CNPJ::validar('00.000.000/0001-91'));
        $this->assertTrue(CNPJ::validar('11.222.333/0001-81'));
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE-36'));
    }

    /**
     * Todas as 26 letras são válidas, inclusive I, O, U, Q e F.
     */
    public function testValidarAceitaLetrasIOUQF(): void
    {
        $this->assertTrue(CNPJ::validar('IO.UQF.345/01DE-25'));
        $this->assertTrue(CNPJ::validar('IOUQF34501DE25'));
        $this->assertFalse(CNPJ::validar('IOUQF34501DE26'));
    }

    /**
     * O CNPJ zerado é inválido, com ou sem máscara.
     */
    public function testValidarRejeitaCnpjZerado(): void
    {
        $this->assertFalse(CNPJ::validar('00.000.000/0000-00'));
        $this->assertFalse(CNPJ::validar('00000000000000'));
    }

    /**
     * Quebra de linha no final não pode ser aceita pela regex.
     */
    public function testValidarRejeitaQuebraDeLinhaNoFinal(): void
    {
        $this->assertFalse(CNPJ::validar("12ABC34501DE35\n"));
        $this->assertFalse(CNPJ::validar("12.ABC.345/01DE-35\n"));
    }

    /**
     * Espaços nas extremidades são removidos antes da validação.
     */
    public function testValidarAplicaTrim(): void
    {
        $this->assertTrue(CNPJ::validar(' 12.ABC.345/01DE-35 '));
        $this->assertTrue(CNPJ::validar(' 12ABC34501DE35 '));
    }

    /**
     * Apenas '.', '/' e '-' são removidos; qualquer outro caractere invalida.
     */
    public function testValidarRejeitaOutrosCaracteres(): void
    {
        $this->assertFalse(CNPJ::validar('12 ABC 345 01DE 35')); // Espaços internos
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE 35')); // Espaço no lugar do hífen
        $this->assertFalse(CNPJ::validar('12,ABC,345/01DE-35')); // Vírgulas
        $this->assertFalse(CNPJ::validar('12_ABC_345/01DE-35')); // Sublinhados
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE-35!')); // Caractere especial no final
        $this->assertFalse(CNPJ::validar(''));
    }

    /**
     * Testa a validação de CNPJs inválidos.
     */
    public function testValidarCnpjInvalidos(): void
    {
        // CNPJ com formato inválido
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE-3')); // Faltando um dígito
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE-35X')); // Dígito extra
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE-XX')); // Dígitos não numéricos

        // Mais casos de formato inválido
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE')); // Sem dígitos verificadores
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE-3')); // Apenas um dígito verificador
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE-3X')); // Segundo dígito não numérico
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE-X3')); // Primeiro dígito não numérico

        // CNPJs com dígitos verificadores incorretos
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE-34')); // Dígitos verificadores errados
        $this->assertFalse(CNPJ::validar('12.ABC.345/01DE-36')); // Dígitos verificadores errados

        // CNPJs inválidos sem máscara
        $this->assertFalse(CNPJ::validar('12ABC34501DE34')); // Dígitos verificadores errados
        $this->assertFalse(CNPJ::validar('51090626000178')); // Dígitos verificadores errados
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
     * mascarar aceita todas as letras e normaliza para maiúsculas.
     */
    public function testMascararAceitaTodasAsLetras(): void
    {
        $this->assertEquals('IO.UQF.345/01DE-25', CNPJ::mascarar('iouqf34501de25'));
    }

    /**
     * @dataProvider entradasInvalidasParaMascarar
     */
    public function testMascararLancaExcecaoParaEntradaInvalida(string $entrada): void
    {
        $this->expectException(InvalidArgumentException::class);
        CNPJ::mascarar($entrada);
    }

    /**
     * @return array<string, array{string}>
     */
    public function entradasInvalidasParaMascarar(): array
    {
        return [
            'vazio' => [''],
            'curto' => ['12ABC34501DE3'],
            'longo' => ['12ABC34501DE355'],
            'dv não numérico' => ['12ABC34501DEXX'],
            'apenas separadores' => ['../-'],
        ];
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

    /**
     * Todo CNPJ gerado deve passar na validação.
     */
    public function testGerarSempreProduzCnpjValido(): void
    {
        for ($i = 0; $i < 200; $i++) {
            $cnpj = CNPJ::gerar();
            $this->assertTrue(CNPJ::validar($cnpj), "CNPJ gerado inválido: $cnpj");
        }
    }

    /**
     * A geração usa o alfabeto completo (0-9 e A-Z), incluindo I, O, U, Q e F.
     */
    public function testGerarUsaAlfabetoCompleto(): void
    {
        $vistos = [];
        for ($i = 0; $i < 2000; $i++) {
            foreach (str_split(substr(CNPJ::removerMascara(CNPJ::gerar()), 0, 12)) as $caractere) {
                $vistos[$caractere] = true;
            }
        }

        foreach (['I', 'O', 'U', 'Q', 'F'] as $letra) {
            $this->assertArrayHasKey($letra, $vistos, "A letra $letra nunca foi gerada");
        }
    }
}
