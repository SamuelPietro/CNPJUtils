<?php

declare(strict_types=1);

namespace CNPJUtils\Interfaces;

/**
 * Interface para operações com CNPJ alfanumérico.
 *
 * Define o contrato para todas as operações essenciais com CNPJs alfanuméricos:
 * geração, validação, formatação e remoção de máscara.
 *
 * @package CNPJUtils\Interfaces
 * @author CNPJUtils Team
 * @since 1.0.0
 */
interface CNPJInterface
{
    /**
     * Gera um CNPJ alfanumérico válido aleatório.
     *
     * Gera um CNPJ com:
     * - 12 caracteres alfanuméricos aleatórios (0-9, A-Z exceto I,O,U,Q,F)
     * - 2 dígitos verificadores calculados automaticamente
     * - Formatação com máscara aplicada (XX.XXX.XXX/XXXX-XX)
     *
     * @return string CNPJ gerado e formatado
     * @throws \Exception Se houver erro na geração de números aleatórios
     */
    public static function gerar(): string;


    /**
     * Verifica se um CNPJ é válido.
     *
     * Executa validação completa:
     * - Formato e estrutura
     * - Caracteres permitidos
     * - Cálculo e verificação dos dígitos verificadores
     *
     * @param string $cnpj O CNPJ a ser validado (com ou sem máscara)
     * @return bool True se o CNPJ for válido, False caso contrário
     */
    public static function validar(string $cnpj): bool;


    /**
     * Aplica máscara de formatação ao CNPJ.
     *
     * Formata o CNPJ no padrão: XX.XXX.XXX/XXXX-XX
     *
     * @param string $cnpj O CNPJ a ser formatado (com ou sem máscara)
     * @return string O CNPJ formatado com máscara
     */
    public static function mascarar(string $cnpj): string;

    /**
     * Remove a máscara do CNPJ, mantendo apenas caracteres alfanuméricos.
     *
     * Remove pontos, barras, hífens e outros caracteres especiais,
     * mantendo apenas dígitos (0-9) e letras maiúsculas (A-Z).
     *
     * @param string $cnpj O CNPJ a ser limpo (com ou sem máscara)
     * @return string O CNPJ sem caracteres especiais
     */
    public static function removerMascara(string $cnpj): string;
}
