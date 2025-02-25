# CNPJUtils
![PHP](https://img.shields.io/badge/PHP-%3E%3D%207.4-blue)
![License](https://img.shields.io/badge/license-mit-blue)

**CNPJUtils** é uma biblioteca PHP de código aberto que oferece funções úteis para trabalhar com o novo padrão de CNPJ
alfanumérico do Brasil. Esta biblioteca permite calcular dígitos verificadores e validar o formato do CNPJ conforme o
novo padrão de doze caracteres alfanuméricos e dois dígitos numéricos.

## Funcionalidades

- **Geração de CNPJ**: Cria um CNPJ aleatório que respeita o formato e as regras de validação, podendo ser usado para testes ou preenchimentos automáticos.
- **Cálculo do dígito verificador (DV)**: Gera os dois dígitos verificadores para um CNPJ alfanumérico, garantindo que o número esteja correto de acordo com as regras estabelecidas pela legislação brasileira.
- **Validação de CNPJ**: Confirma se um CNPJ segue o formato padrão e está devidamente estruturado, validando a conformidade dos caracteres e a correta formação dos dígitos verificadores.
- **Interface Web Intuitiva**: Proporciona uma interface amigável para usuários não técnicos, permitindo interações fáceis para gerar e validar CNPJs.
- **Integração com AJAX**: Permite a comunicação assíncrona entre a interface web e o backend, proporcionando uma experiência de usuário mais fluida sem recarregar a página.
- **Execução via Terminal**: Oferece uma interface de linha de comando que permite a geração e validação de CNPJs diretamente no terminal, útil para desenvolvedores e automação de processos.


## Estrutura do Projeto
A estrutura do projeto é organizada para facilitar o desenvolvimento modular e o uso via navegador, AJAX e terminal.

```plaintext
/project-root
├── index.php                 # Interface de usuário para geração e validação (HTML/CSS/JavaScript)
├── src
│   ├── CNPJ.php              # Classe principal para validação e geração de CNPJ
│   └── DigitoVerificador.php # Classe auxiliar para cálculo do dígito verificador
├── ajax
│   └── processa_cnpj.php     # Processa solicitações AJAX para validação e geração de CNPJ
├── assets
│   └── css
│       └── style.css         # Estilo da interface web
└── terminal.php              # Executa operações de geração e validação de CNPJ via terminal
```

## Requisitos
- PHP versão 7.4 ou superior
- Servidor HTTP (ex.: Apache) para rodar a interface web
- Navegador com suporte a JavaScript para uso com AJAX

## Instalação

### Via Composer

1. Para instalar a biblioteca via Composer, execute o seguinte comando:

   ```bash
   composer require samuelpietro/cnpjutils
   ```

### Manualmente

1. Clone o repositório:

   ```bash
   git clone https://github.com/seu-usuario/cnpjutils.git
   cd cnpjutils
   ```

2. Instale o projeto em um servidor HTTP, ou use o PHP embutido:

   ```bash
   php -S localhost:8000
   ```

3. Acesse `http://localhost:8000/index.php` para utilizar a interface web.

## Uso

### Interface Web

A interface web está em `index.php` e permite ao usuário:

1. **Validar um CNPJ**: Digite o CNPJ e selecione "Validar CNPJ".
2. **Gerar um CNPJ**: Selecione "Gerar CNPJ" para criar automaticamente um novo CNPJ.
3. **Gerar DV**: Calcule o DV de um CNPJ fornecido.

A comunicação com o servidor é feita através de AJAX, e o resultado é exibido na área de resposta da interface.

### Uso via Terminal

O arquivo `terminal.php` permite a execução das funcionalidades via linha de comando, ideal para integrações com scripts de automação e uso em ambientes de desenvolvimento.

#### Exemplo de Uso

1. Para gerar um CNPJ:

   ```bash
   php terminal.php -g
   ```

2. Para gerar o dígito verificador (DV) de um CNPJ fornecido:

   ```bash
   php terminal.php -dv "12.ABC.345/01DE"
   ```

3. Para validar um CNPJ existente:

   ```bash
   php terminal.php -v "12.ABC.345/01DE-35"
   ```

### Uso via Composer

Depois de instalar a biblioteca via Composer, você pode utilizá-la em seu projeto PHP. Aqui estão alguns exemplos de
uso:

#### 1. Geração de CNPJ

```php
<?php

require 'vendor/autoload.php';

use CNPJUtils\CNPJ;

// Gera um CNPJ aleatório
$cnpj = CNPJ::gerar();
echo "CNPJ Gerado: " . $cnpj;
```

#### 2. Cálculo do Dígito Verificador (DV)

```php
<?php

require 'vendor/autoload.php';

use CNPJUtils\CNPJ;

// Cálculo do DV para um CNPJ fornecido
$cnpj = "12.ABC.345/01DE";
$dv = CNPJ::calcularDV($cnpj);
echo "Dígito Verificador: " . $dv;
```

#### 3. Validação de CNPJ

```php
<?php

require 'vendor/autoload.php';

use CNPJUtils\CNPJ;

// Valida um CNPJ fornecido
$cnpj = "12.ABC.345/01DE-35";
$isValid = CNPJ::validar($cnpj);
echo $isValid ? "CNPJ Válido" : "CNPJ Inválido";
```

## Documentação Técnica

### 1. Cálculo dos Dígitos Verificadores do CNPJ Alfanumérico
1. O CNPJ é composto por doze caracteres alfanuméricos e dois dígitos verificadores (DV) calculados em duas etapas.
2. Os pesos para o cálculo são distribuídos de 2 a 9, aplicados da direita para a esquerda, reiniciando após o oitavo dígito.
3. A soma ponderada dos valores ASCII dos caracteres determina os dígitos verificadores conforme o módulo 11.


## Contribuindo

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues ou enviar pull requests para adicionar melhorias e novas funcionalidades.

Para enviar pull requests siga os passos abaixo.
1. Faça um fork do repositório
2. Crie uma branch (`git checkout -b minha-feature`)
3. Commit suas alterações (`git commit -m 'Minha nova feature'`)
4. Envie para o repositório (`git push origin minha-feature`)
5. Abra um pull request

## Licença

Este projeto está licenciado sob a Licença MIT, que permite o uso, modificação e distribuição do software para qualquer
finalidade, desde que a atribuição ao autor original, Samuel Pietro, seja mantida em todas as cópias, modificações ou
distribuições do Software.

**© 2024 Samuel Pietro / CNPJUtils**


