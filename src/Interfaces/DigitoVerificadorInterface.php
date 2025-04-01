<?php

declare(strict_types=1);

namespace CNPJUtils\Interfaces;

use Exception;

interface DigitoVerificadorInterface
{
    /**
     * Calcula os dígitos verificadores de um CNPJ.
     *
     * @param string $cnpj Base do CNPJ, com 12 dígitos, sem caracteres especiais, a ter os dígitos verificadores calculados.
     * @return string Os dígitos verificadores calculados.
     * @throws Exception Caso haja um erro no cálculo dos dígitos verificadores.
     */
    public static function calcular(string $cnpj): string;
}
