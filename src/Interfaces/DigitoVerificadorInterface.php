<?php

declare(strict_types=1);

namespace CNPJUtils\Interfaces;

use Exception;

/**
 * Interface para cálculo de dígitos verificadores de CNPJ alfanumérico.
 *
 * Define o contrato para implementação do algoritmo de cálculo dos dígitos
 * verificadores conforme especificação ENCAT.
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
     * @return string Os dois dígitos verificadores concatenados (ex: "01", "45")
     * @throws Exception Caso haja erro no cálculo ou formato inválido
     */
    public static function calcular(string $cnpj): string;
}
