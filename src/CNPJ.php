<?php

declare(strict_types=1);

namespace CNPJUtils;

use Exception;
use CNPJUtils\Interfaces\CNPJInterface;

/**
 * Classe CNPJ para geração e validação de CNPJs alfanuméricos.
 */
class CNPJ implements CNPJInterface
{

    /**
     * Gera um CNPJ alfanumérico válido aleatório no formato padrão aa.aaa.aaa/aaaa-dd.
     *
     * A função gera os 12 primeiros caracteres do CNPJ, que podem ser números ou letras maiúsculas
     * (alfanuméricos), e calcula os dois dígitos verificadores conforme as regras estabelecidas.
     *
     * @return string CNPJ gerado no formato aa.aaa.aaa/aaaa-dd.
     * @throws Exception Caso haja um erro na geração dos caracteres aleatórios.
     */
    public static function gerar(): string
    {
        // Definindo o conjunto de caracteres alfanuméricos permitidos (0-9, A-Z).
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $cnpj = '';

        // Gerando aleatoriamente os 12 caracteres iniciais do CNPJ
        for ($i = 0; $i < 12; $i++) {
            $cnpj .= $caracteres[random_int(0, strlen($caracteres) - 1)];
        }

        $digitos = DigitoVerificador::calcular($cnpj);
        $cnpj = $cnpj . $digitos;

        return self::mascarar($cnpj);
    }

    /**
     * Verifica se o Formato do CNPJ está correto.
     *
     * @param string $cnpj O CNPJ a ser validado.
     * @return bool True se o formato estiver correto, False caso contrário.
     */
    private static function validarFormato(string $cnpj): bool
    {
        $cnpjLimpo = self::removerMascara($cnpj);
        if (strlen($cnpjLimpo) !== 14) {
            return false;
        }

        return $cnpj === self::mascarar($cnpjLimpo);

    }

    /**
     * Formata um CNPJ alfanumérico no formato padrão aa.aaa.aaa/aaaa-dd.
     *
     * @param string $cnpj O CNPJ a ser formatado.
     * @return bool True se o CNPJ estiver correto e valido, False caso contrário.
     */
    public static function validar(string $cnpj): bool
    {
        if (!self::validarFormato($cnpj)) {
            return false;
        }
        $cnpjLimpo = self::removerMascara($cnpj);
        $base = substr($cnpjLimpo, 0, -2);
        $digitos = DigitoVerificador::calcular($base);

        return $cnpjLimpo === "$base$digitos";

    }


    /**
     * Formata um CNPJ alfanumérico no formato padrão aa.aaa.aaa/aaaa-dd.
     *
     * @param string $cnpj O CNPJ a ser formatado.
     * @return string O CNPJ formatado.
     */
    public static function mascarar(string $cnpj): string
    {
        $cnpjLimpo = self::removerMascara($cnpj);
        return preg_replace('/^(\w{2})(\w{3})(\w{3})(\w{4})(\d{2})$/', '$1.$2.$3/$4-$5', $cnpjLimpo);
    }


    /**
     * Remove todos os caracteres que não sejam letras ou dígitos.
     *
     * @param string $cnpj CNPJ possivelmente com máscara.
     * @return string CNPJ sem máscara.
     */
    public static function removerMascara(string $cnpj): string
    {
        return preg_replace('/[^A-Z0-9]/', '', strtoupper($cnpj));
    }
}