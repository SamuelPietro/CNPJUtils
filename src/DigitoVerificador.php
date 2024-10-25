<?php

declare(strict_types=1);

namespace CNPJUtils;

/**
 * Classe para calcular os dígitos verificadores do CNPJ alfanumérico.
 */
class DigitoVerificador
{
    private string $cnpj;
    private array $pesos = [];

    /**
     * Construtor da classe DigitoVerificador.
     *
     * @param string $cnpj O CNPJ sem os dígitos verificadores.
     */
    public function __construct(string $cnpj)
    {
        $this->cnpj = strtoupper($cnpj);
    }

    /**
     * Converte um caractere alfanumérico em valor numérico para cálculo do DV.
     *
     * @param string $caracter Um caractere alfanumérico do CNPJ.
     * @return int Valor numérico do caractere.
     */
    private function calculaAscii(string $caracter): int
    {
        return ord($caracter) - 48;
    }

    /**
     * Calcula a soma dos produtos entre valores alfanuméricos e pesos, para o cálculo do DV.
     *
     * @return int A soma dos produtos.
     */
    private function calculaSoma(): int
    {
        $tamanhoRange = strlen($this->cnpj);
        $numRange = (int)ceil($tamanhoRange / 8);

        // Preenche a lista de pesos, de 2 a 9, conforme necessário para o tamanho do CNPJ
        for ($i = 0; $i < $numRange; $i++) {
            $this->pesos = array_merge($this->pesos, range(2, 9));
        }

        $this->pesos = array_slice($this->pesos, 0, $tamanhoRange);
        $this->pesos = array_reverse($this->pesos);

        // Calcula o produto dos valores com os pesos e retorna a soma
        $somaProdutos = array_sum(
            array_map(
                fn($char, $peso) => $this->calculaAscii($char) * $peso,
                str_split($this->cnpj),
                $this->pesos
            )
        );

        return $somaProdutos;
    }

    /**
     * Calcula o dígito verificador com base na soma dos produtos.
     *
     * @return int O dígito verificador (0-9).
     */
    public function calcula(): int
    {
        $modSoma = $this->calculaSoma() % 11;
        return $modSoma < 2 ? 0 : 11 - $modSoma;
    }
}
