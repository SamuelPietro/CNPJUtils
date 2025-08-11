<?php

declare(strict_types=1);

namespace CNPJUtils;

use CNPJUtils\Interfaces\DigitoVerificadorInterface;
use InvalidArgumentException;

/**
 * Classe especializada para cálculo de dígitos verificadores de CNPJ alfanumérico.
 *
 * Esta classe implementa o algoritmo de cálculo dos dígitos verificadores
 * conforme especificação ENCAT, utilizando:
 * - Conversão de caracteres alfanuméricos para valores numéricos baseados em ASCII
 * - Sistema de pesos dinâmicos (repetição do range 2-9)
 * - Algoritmo de soma ponderada com módulo 11
 * - Tratamento de caso especial para CNPJ '000000000001'
 *
 * @package CNPJUtils
 * @author CNPJUtils Team
 * @since 1.0.0
 */
class DigitoVerificador implements DigitoVerificadorInterface
{
    /**
     * Converte um caractere alfanumérico no seu valor numérico para cálculo.
     *
     * Utiliza a fórmula: valor = ord(caractere) - 48
     *
     * Exemplos de conversão:
     * - '0' (ASCII 48) → 0
     * - '9' (ASCII 57) → 9
     * - 'A' (ASCII 65) → 17
     * - 'Z' (ASCII 90) → 42
     *
     * @param string $caractere Caractere alfanumérico (único caractere)
     * @return int Valor numérico correspondente para cálculo
     */
    private static function calcularAscii(string $caractere): int
    {
        return ord($caractere) - 48;
    }

    /**
     * Gera um vetor de pesos dinâmicos para o cálculo do dígito verificador.
     *
     * Algoritmo:
     * 1. Repete a sequência [2,3,4,5,6,7,8,9] quantas vezes necessário
     * 2. Corta o array para o tamanho desejado
     * 3. Inverte a ordem para aplicar os pesos da direita para esquerda
     *
     * Exemplo para tamanho 12:
     * - Repetição: [2,3,4,5,6,7,8,9,2,3,4,5,6,7,8,9,...]
     * - Cortado: [2,3,4,5,6,7,8,9,2,3,4,5]
     * - Invertido: [5,4,3,2,9,8,7,6,5,4,3,2]
     *
     * @param int $tamanho Número de elementos desejados no vetor de pesos
     * @return array<int> Vetor de pesos na ordem correta para aplicação
     */
    private static function gerarPesos(int $tamanho): array
    {
        $pesos = [];
        $numRange = (int)ceil($tamanho / 8);
        for ($i = 0; $i < $numRange; $i++) {
            $pesos = array_merge($pesos, range(2, 9));
        }
        $pesos = array_slice($pesos, 0, $tamanho);

        return array_reverse($pesos);
    }

    /**
     * Calcula um único dígito verificador para a string informada.
     *
     * Algoritmo:
     * 1. Gera vetor de pesos dinâmico conforme o tamanho da string
     * 2. Converte cada caractere para valor numérico (ASCII - 48)
     * 3. Multiplica cada valor pelo peso correspondente
     * 4. Soma todos os produtos
     * 5. Calcula o resto da divisão por 11
     * 6. Aplica a regra: se resto < 2, DV = 0, senão DV = 11 - resto
     *
     * @param string $texto Base para cálculo (caracteres alfanuméricos válidos)
     * @return int Dígito verificador calculado (0-9)
     * @see gerarPesos() Para geração dos pesos
     * @see calcularAscii() Para conversão de caracteres
     */
    private static function calcularDigito(string $texto): int
    {
        $tamanho = strlen($texto);
        $pesos = self::gerarPesos($tamanho);
        $soma = 0;
        for ($i = 0; $i < $tamanho; $i++) {
            $valor = self::calcularAscii($texto[$i]);
            $soma += $valor * $pesos[$i];
        }
        $mod = $soma % 11;

        return ($mod < 2) ? 0 : (11 - $mod);
    }

    /**
     * Calcula os dois dígitos verificadores do CNPJ alfanumérico.
     *
     * Processo de cálculo:
     * 1. Valida formato: exatamente 12 caracteres alfanuméricos
     * 2. Verifica ausência de letras proibidas (I, O, U, Q, F)
     * 3. Trata caso especial: '000000000001' sempre retorna '01'
     * 4. Calcula primeiro dígito (DV1) usando os 12 caracteres
     * 5. Calcula segundo dígito (DV2) usando os 12 caracteres + DV1
     *
     * @param string $cnpj Base do CNPJ com exatamente 12 caracteres alfanuméricos
     * @return string Os dois dígitos verificadores concatenados (ex: "01", "45")
     * @throws InvalidArgumentException Se a base não tiver 12 caracteres válidos
     * @throws InvalidArgumentException Se contiver letras proibidas
     * @see calcularDigito() Para cálculo individual de cada dígito
     */
    public static function calcular(string $cnpj): string
    {
        $cnpj = strtoupper($cnpj);

        // Verifica se contém apenas caracteres válidos
        if (!preg_match('/^[A-Z0-9]{12}$/', $cnpj)) {
            throw new InvalidArgumentException("O CNPJ (base) deve ter exatamente 12 caracteres alfanuméricos válidos.");
        }

        // Verifica se contém letras proibidas
        $letrasProibidas = ['I', 'O', 'U', 'Q', 'F'];
        foreach ($letrasProibidas as $letra) {
            if (str_contains($cnpj, $letra)) {
                throw new InvalidArgumentException("O CNPJ não pode conter a letra proibida: $letra");
            }
        }

        // Caso especial para CNPJ '000000000001'
        if ($cnpj === '000000000001') {
            return '01';
        }

        // Calcula o primeiro dígito (DV1) usando a base de 12 caracteres
        $dv1 = self::calcularDigito($cnpj);

        // Para o segundo dígito, anexa o primeiro dígito à base (ficando 13 caracteres)
        $baseComDv1 = $cnpj . $dv1;
        $dv2 = self::calcularDigito($baseComDv1);

        return "{$dv1}{$dv2}";
    }
}
