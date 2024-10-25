<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validação e Geração de CNPJ</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">
    <h2>Validação e Geração de CNPJ</h2>

    <form id="cnpjForm">
        <div class="form-group">
            <label for="cnpj">CNPJ (Formato: aa.aaa.aaa/aaaa ou aa.aaa.aaa/aaaa-dd)</label>
            <input type="text" name="cnpj" id="cnpj">
        </div>

        <div class="form-group">
            <label>Escolha a ação:</label>
            <div class="row-group">
                <input type="radio" name="acao" value="validar" id="validar" checked>
                <label for="validar">Validar CNPJ</label>
            </div>
            <div class="row-group">
                <input type="radio" name="acao" value="gerarDV" id="gerarDV">
                <label for="gerarDV">Gerar DV</label>
            </div>
            <div class="row-group">
                <input type="radio" name="acao" value="gerarCNPJ" id="gerarCNPJ">
                <label for="gerarCNPJ">Gerar CNPJ</label>
            </div>
        </div>

        <button type="submit">Executar</button>
    </form>

    <div id="resultado" class="resultado"></div>
</div>

<script>
    document.getElementById('cnpjForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);
        const resultadoDiv = document.getElementById('resultado');
        const cnpjInput = document.getElementById('cnpj');
        const acao = formData.get('acao'); // Obtém a ação selecionada

        fetch('ajax/processa_cnpj.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    if (acao === 'gerarCNPJ' && data.cnpj) {
                        // Atualiza o input com o CNPJ gerado
                        cnpjInput.value = data.cnpj;
                        resultadoDiv.innerText = `CNPJ Gerado: ${data.cnpj}`;
                    } else if (acao === 'gerarDV' && data.dv) {
                        // Atualiza o input com o CNPJ e DV gerados
                        cnpjInput.value = cnpjInput.value.replace(/[-\d]{2}$/, '') + '-' + data.dv;
                        resultadoDiv.innerText = `Dígitos Verificadores Gerados: ${data.dv}`;
                    } else if (acao === 'validar' && typeof data.valid !== 'undefined') {
                        // Mantém o input com o valor original para validação
                        resultadoDiv.innerText = data.message;
                    }
                } else {
                    resultadoDiv.innerText = `Erro: ${data.message}`;
                }
            })
            .catch(error => {
                resultadoDiv.innerText = 'Ocorreu um erro ao processar a solicitação.';
                console.error('Erro:', error);
            });
    });
</script>


</body>
</html>
