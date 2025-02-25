<?php

declare(strict_types=1);

namespace CNPJUtils\Interfaces;

interface CNPJInterface
{

    /**
     * Gera um CNPJ válido.
     *
     * @return string O CNPJ gerado.
     */
    public function gerar(): string;


    /**
     * Verifica se um CNPJ é válido.
     *
     * @param string $cnpj O CNPJ a ser validado.
     * @return bool True se o CNPJ for válido, False caso contrário.
     */
    public function validar(string $cnpj): bool;


    /**
     * Formata um CNPJ.
     *
     * @param string $cnpj O CNPJ a ser formatado.
     * @return string O CNPJ formatado.
     */
    public function mascarar(string $cnpj): string;

    /**
     * Remove caracteres especiais de um CNPJ.
     *
     * @param string $cnpj O CNPJ a ser limpo.
     * @return string O CNPJ sem caracteres especiais.
     */
    public function removerMascara(string $cnpj): string;
}