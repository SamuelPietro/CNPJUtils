# Changelog

Todas as mudanças relevantes deste projeto são registradas neste arquivo.

O formato segue o [Keep a Changelog](https://keepachangelog.com/pt-BR/1.1.0/)
e o projeto adota o [Versionamento Semântico](https://semver.org/lang/pt-BR/).

## [3.0.0] - não lançada

Adequação às regras oficiais do CNPJ alfanumérico da Receita Federal
(IN RFB nº 2.229/2024). Contém mudanças incompatíveis com a série 2.x;
veja a seção [Guia de migração](#guia-de-migração-da-2x-para-a-30).

### Modificado

- **Incompatível:** todas as 26 letras são aceitas em `CNPJ::validar` e
  `DigitoVerificador::calcular`. As letras `I`, `O`, `U`, `Q` e `F` deixaram
  de ser rejeitadas, pois a regra oficial não proíbe nenhuma letra.
- **Incompatível:** `CNPJ::mascarar` lança `InvalidArgumentException` quando a
  entrada, depois de limpa, não tem 12 caracteres alfanuméricos seguidos de
  2 dígitos. Antes, devolvia a entrada limpa sem máscara.
- **Incompatível:** `CNPJ::validar` devolve `false` para o CNPJ zerado
  (`00.000.000/0000-00`), e `DigitoVerificador::calcular` lança
  `InvalidArgumentException` para a base `000000000000`.
- `CNPJ::validar` remove espaços das extremidades, converte para maiúsculas e
  retira apenas `.`, `/` e `-`. Qualquer outro caractere torna o CNPJ inválido.
- `CNPJ::gerar` usa o alfabeto completo (`0-9` e `A-Z`).
- `DigitoVerificadorInterface::calcular` documenta `InvalidArgumentException`
  no lugar de `Exception`.
- `composer.json` declara a licença `MIT`, igual ao arquivo `LICENSE`.

### Corrigido

- **Incompatível:** `DigitoVerificador::calcular('000000000001')` devolve `91`
  em vez de `01`. Com isso, `00.000.000/0001-91` passa a ser válido.
- As regex de validação usam `\z` no lugar de `$` e não aceitam mais uma
  quebra de linha no final da entrada.
- `CNPJ::mascarar` usa `[A-Z0-9]` no lugar de `\w`, que também aceitava `_`.
- Exemplo de `use CNPJUtils\DigitoVerificador;` no readme.

### Removido

- Menções a "especificação ENCAT" e a letras proibidas na documentação.

### Documentação

- Readme com as regras do CNPJ alfanumérico, exemplos de uso de cada método e
  o cálculo dos dígitos verificadores passo a passo.

### Guia de migração da 2.x para a 3.0

- Se você chama `CNPJ::mascarar` com entradas não validadas, valide antes com
  `CNPJ::validar` ou trate a `InvalidArgumentException`.
- Se você armazenou dígitos verificadores calculados para a base
  `000000000001`, recalcule: o valor correto é `91`.
- CNPJs com `I`, `O`, `U`, `Q` ou `F` que eram rejeitados agora são validados
  normalmente pelo dígito verificador.
- `'00000000000000'` deixou de ser um CNPJ válido.

## [2.3.1] - 2026-01-27

### Corrigido

- `CNPJ::validar` aceita CNPJ sem máscara.

## [2.3.0] - 2025-08-11

### Adicionado

- Configuração do PHP CS Fixer, PHPStan e PHPUnit, regras no `.editorconfig`
  e CI para testes e análise de código.
- Guia de contribuição (`CONTRIBUTING.md`).

### Modificado

- Validação com verificações de formato e tratamento de caracteres inválidos.
- Formatação e remoção de máscara com tratamento de fallback.
- Documentação das classes e interfaces.

## [2.2.0] - 2025-06-09

### Adicionado

- Validação de letras proibidas (removida na 3.0.0).
- Novos testes de validação, geração e formatação.

### Modificado

- Verificação dos dígitos na validação do CNPJ.

## [2.1.2] - 2025-04-02

### Adicionado

- `branch-alias` no `composer.json` e configuração do DeepSource.

### Corrigido

- Validação do CNPJ passa a usar a base para o cálculo dos dígitos.

## [2.1.1] - 2025-04-01

### Documentação

- Comentários de documentação nas classes e nos testes.

## [2.1.0] - 2025-02-26

### Modificado

- `CNPJ` e `DigitoVerificador` passam a expor métodos estáticos.

## [2.0.0] - 2025-02-25

### Adicionado

- Interfaces `CNPJInterface` e `DigitoVerificadorInterface`.
- Testes unitários.
- Workflow de CI para PHP.

### Modificado

- Versão mínima do PHP: 8.0.
- Métodos de validação, geração e remoção de máscara refatorados.
- Número da versão removido do `composer.json`.

## [1.0.1] - 2025-02-25

### Adicionado

- URL do Packagist no `composer.json` e workflow do OSV-Scanner.

### Modificado

- Licença alterada para MIT.

## [1.0.0] - 2024-11-24

### Adicionado

- Primeira versão: geração, validação e cálculo dos dígitos verificadores do
  CNPJ alfanumérico, distribuída como biblioteca Composer.

[3.0.0]: https://github.com/SamuelPietro/CNPJUtils/compare/v2.3.1...HEAD
[2.3.1]: https://github.com/SamuelPietro/CNPJUtils/compare/v2.3.0...v2.3.1
[2.3.0]: https://github.com/SamuelPietro/CNPJUtils/compare/v2.2.0...v2.3.0
[2.2.0]: https://github.com/SamuelPietro/CNPJUtils/compare/v2.1.2...v2.2.0
[2.1.2]: https://github.com/SamuelPietro/CNPJUtils/compare/v2.1.1...v2.1.2
[2.1.1]: https://github.com/SamuelPietro/CNPJUtils/compare/v2.1.0...v2.1.1
[2.1.0]: https://github.com/SamuelPietro/CNPJUtils/compare/v2.0.0...v2.1.0
[2.0.0]: https://github.com/SamuelPietro/CNPJUtils/compare/v1.0.1...v2.0.0
[1.0.1]: https://github.com/SamuelPietro/CNPJUtils/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/SamuelPietro/CNPJUtils/releases/tag/v1.0.0
