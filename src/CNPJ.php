<?php

declare(strict_types=1);

namespace CNPJUtils;

use CNPJUtils\Interfaces\CNPJInterface;
use Exception;
use InvalidArgumentException;

/**
 * Classe CNPJ para geração e validação de CNPJs alfanuméricos.
 *
 * Esta classe implementa a funcionalidade completa para trabalhar com CNPJs alfanuméricos
 * conforme as regras da Receita Federal (IN RFB 2.229/2024), incluindo:
 * - Geração de CNPJs válidos aleatórios
 * - Validação de CNPJs existentes (formato e dígitos verificadores)
 * - Formatação (aplicação de máscara) e remoção de máscara
 * - Suporte a caracteres alfanuméricos (0-9, A-Z)
 *
 * @package CNPJUtils
 * @author CNPJUtils Team
 * @since 1.0.0
 */
class CNPJ implements CNPJInterface
{
    /**
     * Conjunto de caracteres permitidos nas 12 primeiras posições do CNPJ.
     */
    private const CARACTERES = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * Base zerada, que não corresponde a um CNPJ válido.
     */
    private const BASE_ZERADA = '000000000000';

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
        $ultimoIndice = strlen(self::CARACTERES) - 1;

        // Gerando aleatoriamente os 12 caracteres iniciais do CNPJ (a base zerada é inválida)
        do {
            $cnpj = '';
            for ($i = 0; $i < 12; $i++) {
                $cnpj .= self::CARACTERES[random_int(0, $ultimoIndice)];
            }
        } while ($cnpj === self::BASE_ZERADA);

        $digitos = DigitoVerificador::calcular($cnpj);
        $cnpj = $cnpj . $digitos;

        return self::mascarar($cnpj);
    }

    /**
     * Normaliza a entrada para validação.
     *
     * Remove espaços das extremidades, converte para maiúsculas e remove
     * apenas os caracteres da máscara ('.', '/' e '-').
     *
     * @param string $cnpj O CNPJ informado (com ou sem máscara)
     * @return string O CNPJ normalizado
     */
    private static function normalizar(string $cnpj): string
    {
        return str_replace(['.', '/', '-'], '', strtoupper(trim($cnpj, ' ')));
    }

    /**
     * Verifica se o formato do CNPJ (já normalizado) está correto.
     *
     * Valida:
     * - 12 caracteres alfanuméricos (0-9, A-Z)
     * - Seguidos de 2 dígitos numéricos (dígitos verificadores)
     *
     * @param string $cnpjLimpo O CNPJ normalizado, sem máscara
     * @return bool True se o formato estiver correto, False caso contrário
     */
    private static function validarFormato(string $cnpjLimpo): bool
    {
        return preg_match('/^[A-Z0-9]{12}\d{2}\z/', $cnpjLimpo) === 1;
    }

    /**
     * Valida um CNPJ alfanumérico completo.
     *
     * Executa validação completa do CNPJ:
     * 1. Normaliza a entrada (trim de espaços, maiúsculas, remoção de '.', '/' e '-')
     * 2. Verifica o formato (12 alfanuméricos + 2 dígitos); qualquer outro caractere invalida
     * 3. Calcula e verifica os dígitos verificadores
     *
     * O CNPJ zerado (00.000.000/0000-00) é sempre inválido.
     *
     * @param string $cnpj O CNPJ a ser validado (com ou sem máscara)
     * @return bool True se o CNPJ estiver correto e válido, False caso contrário
     * @see validarFormato() Para validação apenas do formato
     * @see DigitoVerificador::calcular() Para cálculo dos dígitos verificadores
     */
    public static function validar(string $cnpj): bool
    {
        $cnpjLimpo = self::normalizar($cnpj);

        if (!self::validarFormato($cnpjLimpo)) {
            return false;
        }

        try {
            $digitosCalculados = DigitoVerificador::calcular(substr($cnpjLimpo, 0, 12));
        } catch (InvalidArgumentException $e) {
            return false;
        }

        return substr($cnpjLimpo, -2) === $digitosCalculados;
    }

    /**
     * Formata um CNPJ alfanumérico aplicando a máscara padrão.
     *
     * Aplica a máscara no formato: XX.XXX.XXX/XXXX-XX
     * onde X representa caracteres alfanuméricos e os dois últimos são dígitos.
     *
     * @param string $cnpj O CNPJ a ser formatado (com ou sem máscara)
     * @return string O CNPJ formatado com máscara
     * @throws InvalidArgumentException Se a entrada, depois de limpa, não tiver
     *                                  12 caracteres alfanuméricos seguidos de 2 dígitos
     * @see removerMascara() Para operação inversa
     */
    public static function mascarar(string $cnpj): string
    {
        $cnpjLimpo = self::removerMascara($cnpj);

        if (!preg_match('/^([A-Z0-9]{2})([A-Z0-9]{3})([A-Z0-9]{3})([A-Z0-9]{4})(\d{2})\z/', $cnpjLimpo, $partes)) {
            throw new InvalidArgumentException(
                'O CNPJ deve ter 12 caracteres alfanuméricos seguidos de 2 dígitos numéricos.'
            );
        }

        return "{$partes[1]}.{$partes[2]}.{$partes[3]}/{$partes[4]}-{$partes[5]}";
    }

    /**
     * Remove a máscara do CNPJ, mantendo apenas caracteres alfanuméricos.
     *
     * Remove todos os caracteres que não sejam:
     * - Dígitos (0-9)
     * - Letras maiúsculas (A-Z)
     *
     * Converte automaticamente letras minúsculas para maiúsculas.
     * Se a remoção por regex falhar, retorna string vazia.
     *
     * @param string $cnpj CNPJ possivelmente com máscara
     * @return string CNPJ sem máscara (apenas caracteres alfanuméricos)
     * @see mascarar() Para operação inversa
     */
    public static function removerMascara(string $cnpj): string
    {
        $resultado = preg_replace('/[^A-Z0-9]/', '', strtoupper($cnpj));

        return $resultado ?? '';
    }
}
