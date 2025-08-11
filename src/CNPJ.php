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
 * conforme a especificação ENCAT, incluindo:
 * - Geração de CNPJs válidos aleatórios
 * - Validação de CNPJs existentes (formato e dígitos verificadores)
 * - Formatação (aplicação de máscara) e remoção de máscara
 * - Suporte a caracteres alfanuméricos (0-9, A-Z exceto I, O, U, Q, F)
 *
 * @package CNPJUtils
 * @author CNPJUtils Team
 * @since 1.0.0
 */
class CNPJ implements CNPJInterface
{
    /**
     * Letras proibidas no CNPJ alfanumérico conforme especificação ENCAT.
     *
     * Estas letras são proibidas para evitar confusão visual:
     * - I: pode ser confundida com 1 (um)
     * - O: pode ser confundida com 0 (zero)
     * - U: pode ser confundida com V
     * - Q: pode ser confundida com O
     * - F: reservada para uso futuro
     *
     * @var array<string>
     */
    private const LETRAS_PROIBIDAS = ['I', 'O', 'U', 'Q', 'F'];

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
        // Definindo o conjunto de caracteres alfanuméricos permitidos (0-9, A-Z exceto letras proibidas)
        $caracteres = '0123456789ABCDEGHJKLMPRSTVWXYZ';
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
     * Verifica se o formato do CNPJ está correto.
     *
     * Valida:
     * - Formato com máscara: XX.XXX.XXX/XXXX-XX
     * - Comprimento de 14 caracteres após remoção da máscara
     * - Últimos 2 caracteres devem ser dígitos numéricos
     * - Ausência de letras proibidas nos 12 primeiros caracteres
     *
     * @param string $cnpj O CNPJ a ser validado (com ou sem máscara)
     * @return bool True se o formato estiver correto, False caso contrário
     */
    private static function validarFormato(string $cnpj): bool
    {
        $cnpj = strtoupper($cnpj);

        // Verifica se o formato com máscara está correto
        if (!preg_match('/^[A-Z0-9]{2}\.[A-Z0-9]{3}\.[A-Z0-9]{3}\/[A-Z0-9]{4}-\d{2}$/', $cnpj)) {
            return false;
        }

        $cnpjLimpo = self::removerMascara($cnpj);

        // Verifica se tem 14 caracteres
        if (strlen($cnpjLimpo) !== 14) {
            return false;
        }

        // Verifica se os dois últimos caracteres são dígitos
        if (!ctype_digit(substr($cnpjLimpo, -2))) {
            return false;
        }

        // Verifica se há letras proibidas nos 12 primeiros caracteres
        $base = substr($cnpjLimpo, 0, 12);
        foreach (self::LETRAS_PROIBIDAS as $letra) {
            if (str_contains($base, $letra)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Valida um CNPJ alfanumérico completo.
     *
     * Executa validação completa do CNPJ:
     * 1. Verifica o formato (estrutura, comprimento, caracteres permitidos)
     * 2. Calcula e verifica os dígitos verificadores
     * 3. Trata exceções durante o processo de validação
     *
     * @param string $cnpj O CNPJ a ser validado (com ou sem máscara)
     * @return bool True se o CNPJ estiver correto e válido, False caso contrário
     * @see validarFormato() Para validação apenas do formato
     * @see DigitoVerificador::calcular() Para cálculo dos dígitos verificadores
     */
    public static function validar(string $cnpj): bool
    {
        try {
            if (!self::validarFormato($cnpj)) {
                return false;
            }

            $cnpjLimpo = self::removerMascara($cnpj);
            $base = substr($cnpjLimpo, 0, 12);
            $digitos = substr($cnpjLimpo, -2);
            $digitosCalculados = DigitoVerificador::calcular($base);

            return $digitos === $digitosCalculados;
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * Formata um CNPJ alfanumérico aplicando a máscara padrão.
     *
     * Aplica a máscara no formato: XX.XXX.XXX/XXXX-XX
     * onde X representa caracteres alfanuméricos e os dois últimos são dígitos.
     *
     * Se a formatação por regex falhar, retorna o CNPJ limpo sem máscara.
     *
     * @param string $cnpj O CNPJ a ser formatado (com ou sem máscara)
     * @return string O CNPJ formatado com máscara
     * @see removerMascara() Para operação inversa
     */
    public static function mascarar(string $cnpj): string
    {
        $cnpjLimpo = self::removerMascara($cnpj);

        $resultado = preg_replace('/^(\w{2})(\w{3})(\w{3})(\w{4})(\d{2})$/', '$1.$2.$3/$4-$5', $cnpjLimpo);

        return $resultado ?? $cnpjLimpo;
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
