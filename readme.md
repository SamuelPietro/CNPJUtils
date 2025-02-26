# CNPJUtils
![PHP](https://img.shields.io/badge/PHP-%3E%3D%208.0-blue)
![License](https://img.shields.io/badge/license-mit-blue)

**CNPJUtils** é uma biblioteca PHP de código aberto que oferece funções úteis para trabalhar com o novo padrão de CNPJ
alfanumérico do Brasil. Esta biblioteca permite calcular dígitos verificadores e validar o formato do CNPJ conforme o
novo padrão de doze caracteres alfanuméricos e dois dígitos numéricos.

## Funcionalidades

- **Geração de CNPJ**: Cria um CNPJ aleatório que respeita o formato e as regras de validação, podendo ser usado para testes ou preenchimentos automáticos.
- **Validação de CNPJ**: Confirma se um CNPJ segue o formato padrão e está devidamente estruturado, validando a conformidade dos caracteres e a correta formação dos dígitos verificadores.
- **Formatar/Mascarar**: Formata um CNPJ alfanumérico para um formato legível, com a máscara padrão de pontuação e separação de caracteres.
- **Remover mascara**: Remove a formatação de um CNPJ, retornando apenas os caracteres alfanuméricos.
- **Cálcular dígitos verificadores (DV)**: Gera os dois dígitos verificadores para um CNPJ alfanumérico, garantindo que o número esteja correto conforme as regras estabelecidas pela legislação brasileira.

## Estrutura do Projeto
A estrutura do projeto é organizada para facilitar o desenvolvimento modular.

```plaintext
/project-root
├── src
│   ├── Interfaces
│   │   └── CNPJInterface.php  # Interface para validação e geração de CNPJ
│   ├── CNPJ.php              # Classe principal para validação e geração de CNPJ
│   └── DigitoVerificador.php # Classe auxiliar para cálculo do dígito verificador
├── tests
│   ├── CNPJTest.php          # Testes unitários para validação e geração de CNPJ
│   └── DigitoVerificadorTest.php # Testes unitários para cálculo do dígito verificador
```

## Requisitos
- PHP versão 8.0 ou superior

## Instalação


1. Para instalar a biblioteca via Composer, execute o seguinte comando:

   ```bash
   composer require samuelpietro/cnpjutils
   ```

## Uso

1. Para gerar um CNPJ:

   ```php
   use CNPJUtils\CNPJ;
   CNPJ::gerar(); // Retorna uma ‘string’ contendo um CNPJ aleatório
   ```
2. Para validar a formatação/máscara de um CNPJ:

   ```php
   use CNPJUtils\CNPJ;
   CNPJ::validar('12ABC34501DE35'); // Retorna true se o CNPJ for válido e false se for inválido
   ```
   
3. Para mascarar um CNPJ:

   ```php
   use CNPJUtils\CNPJ;
   CNPJ::mascarar('12ABC34501DE35'); // Retorna uma ‘string’ contendo o CNPJ formatado
   ```

4. Para remover a máscara de um CNPJ:

   ```php
   use CNPJUtils\CNPJ;
   CNPJ::removerMascara('12.ABC.345/01DE-35'); // Retorna uma ‘string’ contendo o CNPJ sem máscara
   ```
   
5. Para calcular os dígitos verificadores de um CNPJ:

   ```php
    use CNPJUtils\DigitoVerificador();
    DigitoVerificador::calcular('12ABC34501DE'); // Retorna uma ‘string’ contendo os dígitos verificadores
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


