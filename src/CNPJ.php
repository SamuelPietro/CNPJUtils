<?php

declare(strict_types=1);

namespace CNPJUtils;

use Exception;

/**
 * Classe CNPJ para geração e validação de CNPJs alfanuméricos.
 */
class CNPJ
{
    private string $cnpj;
    private string $cnpjSemDV;

    /**
     * Construtor da classe CNPJ.
     *
     * @param string $cnpj O CNPJ a ser validado ou gerado (sem DV ou com DV).
     * @throws Exception Se o formato do CNPJ não for válido.
     */
    public function __construct(string $cnpj)
    {
        if (!$this->validaFormato($cnpj)) {
            throw new Exception("CNPJ inválido! O formato esperado é aa.aaa.aaa/aaaa-dd para validação, ou aa.aaa.aaa/aaaa para geração do DV.");
        }

        $this->cnpj = $this->removePontuacao($cnpj);
    }

    /**
     * Remove pontuações do CNPJ, mantendo apenas caracteres alfanuméricos.
     *
     * @param string $cnpj O CNPJ original.
     * @return string O CNPJ sem pontuação.
     */
    private function removePontuacao(string $cnpj): string
    {
        return preg_replace('/[^\w]/', '', $cnpj);
    }

    /**
     * Remove os dígitos verificadores do CNPJ, preparando-o para o cálculo de novos DVs.
     *
     * @throws Exception Se o CNPJ tiver um tamanho inválido.
     */
    private function removeDigitos(): void
    {
        $tamanho = strlen($this->cnpj);

        if ($tamanho === 14) {
            $this->cnpjSemDV = substr($this->cnpj, 0, -2);
        } elseif ($tamanho === 12) {
            $this->cnpjSemDV = $this->cnpj;
        } else {
            throw new Exception("CNPJ com tamanho inválido!");
        }
    }

    /**
     * Verifica se o CNPJ está no formato correto.
     *
     * @param string $cnpj O CNPJ a ser validado.
     * @return bool True se o formato estiver correto, False caso contrário.
     */
    private function validaFormato(string $cnpj): bool
    {
        return (bool)preg_match('/(^([A-Z]|\d){2}\.([A-Z]|\d){3}\.([A-Z]|\d){3}\/([A-Z]|\d){4}(\-\d{2})?$)/', $cnpj);
    }

    /**
     * Gera os dois dígitos verificadores do CNPJ.
     *
     * @return string Os dígitos verificadores concatenados.
     */
    public function geraDV(): string
    {
        $this->removeDigitos();

        $dv1 = (new DigitoVerificador($this->cnpjSemDV))->calcula();
        $dv2 = (new DigitoVerificador($this->cnpjSemDV . $dv1))->calcula();

        return "{$dv1}{$dv2}";
    }

    /**
     * Gera um CNPJ alfanumérico válido aleatório no formato padrão aa.aaa.aaa/aaaa-dd.
     *
     * A função gera os 12 primeiros caracteres do CNPJ, que podem ser números ou letras maiúsculas
     * (alfanuméricos), e calcula os dois dígitos verificadores de acordo com as regras estabelecidas.
     *
     * @return string CNPJ gerado no formato aa.aaa.aaa/aaaa-dd.
     * @throws Exception Caso haja um erro na geração dos caracteres aleatórios.
     */
    public static function geraCNPJ(): string
    {
        // Definindo o conjunto de caracteres alfanuméricos permitidos (0-9, A-Z).
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $baseCNPJ = '';

        // Gerando aleatoriamente os 12 caracteres iniciais do CNPJ
        for ($i = 0; $i < 12; $i++) {
            $baseCNPJ .= $caracteres[random_int(0, strlen($caracteres) - 1)];
        }

        // Calculando o primeiro dígito verificador usando os 12 primeiros caracteres gerados
        $dv1 = (new DigitoVerificador($baseCNPJ))->calcula();

        // Calculando o segundo dígito verificador com os 12 caracteres mais o primeiro DV
        $dv2 = (new DigitoVerificador($baseCNPJ . $dv1))->calcula();

        // Formatando o CNPJ no padrão desejado
        return substr($baseCNPJ, 0, 2) . '.' . substr($baseCNPJ, 2, 3) . '.' . substr($baseCNPJ, 5, 3) . '/' . substr($baseCNPJ, 8, 4) . '-' . $dv1 . $dv2;
    }


    /**
     * Valida o CNPJ verificando seus dígitos verificadores.
     *
     * @return bool True se o CNPJ for válido, False caso contrário.
     */
    public function valida(): bool
    {
        $this->removeDigitos();
        $dvGerado = $this->geraDV();

        return $this->cnpj === "{$this->cnpjSemDV}{$dvGerado}";
    }
}
