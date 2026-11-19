<?php

declare(strict_types=1);

namespace CNPJUtils\Interfaces;

use InvalidArgumentException;

/**
 * Interface para cálculo de dígitos verificadores de CNPJ alfanumérico.
 *
 * Define o contrato para implementação do algoritmo de cálculo dos dígitos
 * verificadores conforme as regras da Receita Federal (IN RFB 2.229/2024).
 *
 * @package CNPJUtils\Interfaces
 * @author CNPJUtils Team
 * @since 1.0.0
 */
interface DigitoVerificadorInterface
{
    /**
     * Calcula os dois dígitos verificadores de um CNPJ alfanumérico.
     *
     * Implementa o algoritmo de cálculo dos dígitos verificadores utilizando:
     * - Conversão de caracteres alfanuméricos para valores numéricos
     * - Sistema de pesos dinâmicos
     * - Soma ponderada com módulo 11
     *
     * @param string $cnpj Base do CNPJ com exatamente 12 caracteres alfanuméricos
     * @return string Os dois dígitos verificadores concatenados (ex: "91", "35")
     * @throws InvalidArgumentException Se a base não tiver 12 caracteres alfanuméricos ou for zerada
     */
    public static function calcular(string $cnpj): string;
}
