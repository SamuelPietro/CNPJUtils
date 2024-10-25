<?php

require_once '../src/CNPJ.php';
require_once '../src/DigitoVerificador.php';

use CNPJUtils\CNPJ;

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Ação inválida'];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $acao = $_POST['acao'] ?? '';
        if ($acao === 'gerarCNPJ') {
            // Gera um CNPJ válido aleatório
            $response = [
                'status' => 'success',
                'cnpj' => CNPJ::geraCNPJ()
            ];
        } elseif ($acao === 'validar' || $acao === 'gerarDV') {
            $cnpjInput = $_POST['cnpj'] ?? '';
            $cnpj = new CNPJ($cnpjInput);

            if ($acao === 'validar') {
                // Verifica a validade do CNPJ
                $response = [
                    'status' => 'success',
                    'valid' => $cnpj->valida(),
                    'message' => $cnpj->valida() ? 'CNPJ válido' : 'CNPJ inválido'
                ];
            } elseif ($acao === 'gerarDV') {
                // Calcula o dígito verificador do CNPJ fornecido
                $response = [
                    'status' => 'success',
                    'dv' => $cnpj->geraDV()
                ];
            }
        }
    }
} catch (Exception $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
