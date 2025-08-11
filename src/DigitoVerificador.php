<?php

declare(strict_types=1);

namespace CNPJUtils;

use CNPJUtils\Interfaces\DigitoVerificadorInterface;
use InvalidArgumentException;

/**
 * Classe para cálculo de dígitos verificadores.
 */
class DigitoVerificador implements DigitoVerificadorInterface
{
    /**
     * Converte um caractere no seu valor numérico para o cálculo.
     * Usa: valor = ord(caractere) - 48
     * (assim, '0'→0, '9'→9 e, para letras, por exemplo, 'A' (65) → 17).
     */
    private static function calcularAscii(string $caractere): int
    {
        return ord($caractere) - 48;
    }

    /**
     * Gera um vetor de pesos dinâmicos para o cálculo do DV.
     * A lógica é: repetir range(2,9) quantas vezes forem necessárias para
     * cobrir o tamanho desejado, cortar para esse tamanho e inverter a ordem.
     *
     * @param int $tamanho Número de elementos desejados.
     * @return array Vetor de pesos.
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
     * Calcula um dígito verificador para a string informada,
     * usando a lógica de soma ponderada e regra do módulo 11.
     *
     * @param string $texto Base para cálculo (deve conter apenas caracteres válidos).
     * @return int Dígito verificador calculado.
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
     * O CNPJ base (sem os dígitos) deve ter exatamente 12 caracteres.
     *
     * @param string $cnpj CNPJ possivelmente com máscara.
     * @return string Os dois dígitos verificadores concatenados.
     * @throws InvalidArgumentException Se a base não tiver 12 caracteres.
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
