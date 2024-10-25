<?php
require_once 'src/CNPJ.php';

use CNPJUtils\CNPJ;

if ($argc < 2) {
    echo "Uso: php terminal.php [-g|-v|-dv] [CNPJ opcional]\n";
    echo "-g        : Gerar CNPJ completo\n";
    echo "-dv [CNPJ]: Gerar dígito verificador para o CNPJ fornecido\n";
    echo "-v [CNPJ] : Validar um CNPJ completo\n";
    exit;
}

$option = $argv[1];
$cnpjInput = $argv[2] ?? null;

try {
    switch ($option) {
        case '-g':
            echo "CNPJ Gerado: " . CNPJ::geraCNPJ() . "\n";
            break;
        case '-dv':
            if ($cnpjInput) {
                $cnpj = new CNPJ($cnpjInput);
                echo "Dígito Verificador: " . $cnpj->geraDV() . "\n";
            } else {
                echo "Erro: Informe um CNPJ parcial para gerar o DV\n";
            }
            break;
        case '-v':
            if ($cnpjInput) {
                $cnpj = new CNPJ($cnpjInput);
                echo $cnpj->valida() ? "CNPJ Válido\n" : "CNPJ Inválido\n";
            } else {
                echo "Erro: Informe um CNPJ para validar\n";
            }
            break;
        default:
            echo "Opção inválida. Use -g, -dv ou -v.\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
