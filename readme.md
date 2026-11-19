# CNPJUtils
![PHP](https://img.shields.io/badge/PHP-%3E%3D%208.0-blue)
![License](https://img.shields.io/badge/license-mit-blue)

**CNPJUtils** é uma biblioteca PHP de código aberto que oferece funções úteis para trabalhar com o CNPJ alfanumérico
do Brasil. Ela gera, valida, formata e calcula os dígitos verificadores conforme as regras oficiais da Receita Federal
(IN RFB nº 2.229/2024, em vigor desde julho de 2026): doze caracteres alfanuméricos seguidos de dois dígitos numéricos.

Os CNPJs exclusivamente numéricos continuam válidos e são tratados pelo mesmo algoritmo.

## Funcionalidades

- **Geração de CNPJ**: Cria um CNPJ alfanumérico aleatório e válido, útil para testes ou preenchimentos automáticos.
- **Validação de CNPJ**: Confirma se um CNPJ (numérico ou alfanumérico, com ou sem máscara) tem o formato correto e os dígitos verificadores corretos.
- **Formatar/Mascarar**: Aplica a máscara padrão `XX.XXX.XXX/XXXX-XX`.
- **Remover máscara**: Remove a formatação de um CNPJ, retornando apenas os caracteres alfanuméricos.
- **Calcular dígitos verificadores (DV)**: Gera os dois dígitos verificadores para uma base de doze caracteres.

## Regras do CNPJ alfanumérico

| Posições | Conteúdo                      | Caracteres aceitos |
|----------|-------------------------------|--------------------|
| 1 a 8    | Raiz                          | `0-9` e `A-Z`      |
| 9 a 12   | Ordem do estabelecimento      | `0-9` e `A-Z`      |
| 13 e 14  | Dígitos verificadores (DV)    | `0-9`              |

- **Todas as 26 letras são válidas**, inclusive `I`, `O`, `U`, `Q` e `F`.
- As letras são sempre maiúsculas; a biblioteca converte minúsculas automaticamente.
- Os dígitos verificadores são sempre numéricos.
- O CNPJ zerado (`00.000.000/0000-00`) é inválido.

## Estrutura do Projeto
A estrutura do projeto é organizada para facilitar o desenvolvimento modular.

```plaintext
/project-root
├── src
│   ├── Interfaces
│   │   ├── CNPJInterface.php              # Contrato para geração, validação e formatação
│   │   └── DigitoVerificadorInterface.php # Contrato para cálculo dos dígitos verificadores
│   ├── CNPJ.php                           # Classe principal para validação e geração de CNPJ
│   └── DigitoVerificador.php              # Classe auxiliar para cálculo do dígito verificador
├── tests
│   ├── CNPJTest.php                       # Testes unitários para validação e geração de CNPJ
│   └── DigitoVerificadorTest.php          # Testes unitários para cálculo do dígito verificador
```

## Requisitos
- PHP versão 8.0 ou superior

## Instalação

Para instalar a biblioteca via Composer, execute o seguinte comando:

```bash
composer require samuelpietro/cnpjutils
```

## Uso

### Gerar um CNPJ

```php
use CNPJUtils\CNPJ;

CNPJ::gerar(); // Ex.: 'K7.Q2F.9IO/01UZ-48' (aleatório, já com máscara e DV válido)
```

### Validar um CNPJ

```php
use CNPJUtils\CNPJ;

CNPJ::validar('12.ABC.345/01DE-35');   // true  (alfanumérico com máscara)
CNPJ::validar('12ABC34501DE35');       // true  (sem máscara)
CNPJ::validar('12abc34501de35');       // true  (minúsculas são convertidas)
CNPJ::validar(' 12.ABC.345/01DE-35 '); // true  (espaços nas extremidades são ignorados)
CNPJ::validar('00.000.000/E08G-12');   // true  (primeiro CNPJ alfanumérico emitido)
CNPJ::validar('11.222.333/0001-81');   // true  (CNPJ numérico)
CNPJ::validar('00.000.000/0001-91');   // true

CNPJ::validar('12.ABC.345/01DE-36');   // false (dígito verificador incorreto)
CNPJ::validar('00.000.000/0000-00');   // false (CNPJ zerado)
CNPJ::validar('12 ABC 345 01DE 35');   // false (separador não permitido)
CNPJ::validar("12ABC34501DE35\n");     // false (quebra de linha)
```

Antes de validar, a entrada tem os espaços das extremidades removidos, é convertida para maiúsculas e perde apenas
os caracteres da máscara (`.`, `/` e `-`). Qualquer outro caractere torna o CNPJ inválido. `validar` nunca lança
exceção: sempre devolve `true` ou `false`.

### Mascarar um CNPJ

```php
use CNPJUtils\CNPJ;

CNPJ::mascarar('12ABC34501DE35'); // '12.ABC.345/01DE-35'
CNPJ::mascarar('12abc34501de35'); // '12.ABC.345/01DE-35'
CNPJ::mascarar('12ABC34501DE');   // lança InvalidArgumentException
```

`mascarar` apenas formata, sem conferir os dígitos verificadores. Ele lança `InvalidArgumentException` quando a
entrada, depois de limpa, não tem doze caracteres alfanuméricos seguidos de dois dígitos.

### Remover a máscara de um CNPJ

```php
use CNPJUtils\CNPJ;

CNPJ::removerMascara('12.ABC.345/01DE-35'); // '12ABC34501DE35'
```

### Calcular os dígitos verificadores

```php
use CNPJUtils\DigitoVerificador;

DigitoVerificador::calcular('12ABC34501DE'); // '35'
DigitoVerificador::calcular('00000000E08G'); // '12'
DigitoVerificador::calcular('000000000001'); // '91'
DigitoVerificador::calcular('000000000000'); // lança InvalidArgumentException (base zerada)
DigitoVerificador::calcular('12ABC345');     // lança InvalidArgumentException (tamanho inválido)
```

A base deve ter exatamente doze caracteres alfanuméricos, sem máscara.

## Documentação Técnica

### Cálculo dos Dígitos Verificadores

1. Cada caractere é convertido em um valor numérico: **código ASCII − 48**. Assim, `0` a `9` valem 0 a 9, `A` vale 17,
   `B` vale 18 e assim por diante, até `Z`, que vale 42.
2. Cada valor é multiplicado por um peso. Os pesos vão de 2 a 9, aplicados **da direita para a esquerda**, e voltam
   a 2 depois do 9.
3. Somam-se os produtos e calcula-se o resto da divisão da soma por 11.
4. Se o resto for menor que 2, o dígito é **0**; caso contrário, é **11 − resto**.
5. O segundo dígito é calculado da mesma forma, sobre os doze caracteres acrescidos do primeiro dígito.

#### Exemplo: base `12ABC34501DE`

Primeiro dígito:

| Caractere | 1 | 2 | A  | B  | C   | 3  | 4  | 5  | 0 | 1 | D  | E  |
|-----------|---|---|----|----|-----|----|----|----|---|---|----|----|
| Valor     | 1 | 2 | 17 | 18 | 19  | 3  | 4  | 5  | 0 | 1 | 20 | 21 |
| Peso      | 5 | 4 | 3  | 2  | 9   | 8  | 7  | 6  | 5 | 4 | 3  | 2  |
| Produto   | 5 | 8 | 51 | 36 | 171 | 24 | 28 | 30 | 0 | 4 | 60 | 42 |

Soma = 459; 459 mod 11 = 8; primeiro dígito = 11 − 8 = **3**.

Segundo dígito (base acrescida do primeiro dígito, `12ABC34501DE3`):

| Caractere | 1 | 2  | A  | B  | C  | 3  | 4  | 5  | 0 | 1 | D  | E  | 3 |
|-----------|---|----|----|----|----|----|----|----|---|---|----|----|---|
| Valor     | 1 | 2  | 17 | 18 | 19 | 3  | 4  | 5  | 0 | 1 | 20 | 21 | 3 |
| Peso      | 6 | 5  | 4  | 3  | 2  | 9  | 8  | 7  | 6 | 5 | 4  | 3  | 2 |
| Produto   | 6 | 10 | 68 | 54 | 38 | 27 | 32 | 35 | 0 | 5 | 80 | 63 | 6 |

Soma = 424; 424 mod 11 = 6; segundo dígito = 11 − 6 = **5**.

Resultado: `12.ABC.345/01DE-35`.

## Desenvolvimento

```bash
composer install
composer ci      # cs-check, phpstan e phpunit
composer test    # apenas os testes
composer cs-fix  # corrige o estilo do código
```

## Contribuindo

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues ou enviar pull requests para adicionar melhorias e novas funcionalidades.

Para enviar pull requests siga os passos abaixo.
1. Faça um fork do repositório
2. Crie uma branch (`git checkout -b minha-feature`)
3. Commit as suas alterações (`git commit -m 'Minha nova feature'`)
4. Envie para o repositório (`git push origin minha-feature`)
5. Abra um pull request

## Licença

Este projeto está licenciado sob a Licença MIT, que permite o uso, modificação e distribuição do ‘software’ para qualquer
finalidade, desde que a atribuição ao autor original, Samuel Pietro, seja mantida em todas as cópias, modificações ou
distribuições do ‘Software’.

**© 2024 Samuel Pietro / CNPJUtils**
