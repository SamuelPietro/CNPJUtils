# Contribuindo para CNPJUtils

Agradecemos seu interesse em contribuir com o projeto CNPJUtils! Este guia irá ajudá-lo a entender como contribuir de forma efetiva.

## 📋 Sumário

- [Código de Conduta](#código-de-conduta)
- [Como Contribuir](#como-contribuir)
- [Configuração do Ambiente](#configuração-do-ambiente)
- [Padrões de Código](#padrões-de-código)
- [Testes](#testes)
- [Documentação](#documentação)
- [Processo de Pull Request](#processo-de-pull-request)
- [Reportando Bugs](#reportando-bugs)
- [Sugerindo Melhorias](#sugerindo-melhorias)

## 📜 Código de Conduta

Este projeto adere a um código de conduta. Ao participar, você deve manter um comportamento respeitoso e profissional. Contribuições são bem-vindas de todos, independentemente de experiência, identidade de gênero, orientação sexual, deficiência, aparência pessoal, tamanho corporal, raça, etnia, idade, religião ou nacionalidade.

## 🤝 Como Contribuir

Existem várias formas de contribuir:

- **Reportar bugs**: Encontrou um problema? Abra uma issue!
- **Sugerir melhorias**: Tem uma ideia? Compartilhe conosco!
- **Corrigir bugs**: Veja as issues abertas e ajude a resolvê-las
- **Adicionar funcionalidades**: Implemente novas features
- **Melhorar documentação**: Ajude a manter a documentação atualizada
- **Escrever testes**: Aumente a cobertura de testes

## ⚙️ Configuração do Ambiente

### Pré-requisitos

- PHP 8.0 ou superior
- Composer
- Git

### Configuração Local

1. **Fork o repositório**
   ```bash
   # Clique em "Fork" no GitHub ou use gh cli
   gh repo fork SamuelPietro/CNPJUtils
   ```

2. **Clone seu fork**
   ```bash
   git clone https://github.com/seu-usuario/CNPJUtils.git
   cd CNPJUtils
   ```

3. **Instale as dependências**
   ```bash
   composer install
   ```

4. **Configure o upstream**
   ```bash
   git remote add upstream https://github.com/SamuelPietro/CNPJUtils.git
   ```

5. **Execute os testes para verificar se tudo está funcionando**
   ```bash
   composer test
   ```

## 📝 Padrões de Código

### Estilo de Código

- **Padrão PSR-12**: Seguimos rigorosamente o PSR-12
- **Tipos estritos**: Sempre use `declare(strict_types=1)`
- **Documentação**: Todos os métodos públicos devem ter PHPDoc completo

### Ferramentas de Qualidade

- **PHP CS Fixer**: Para formatação automática
- **PHPStan**: Para análise estática (nível 8)
- **PHPUnit**: Para testes

### Comandos Úteis

```bash
# Verificar estilo de código
composer cs-check

# Corrigir estilo automaticamente
composer cs-fix

# Executar análise estática
composer analyse

# Executar todos os checks
composer ci
```

## 🧪 Testes

### Executando Testes

```bash
# Executar todos os testes
composer test

# Executar testes com cobertura
composer test-coverage

# Executar teste específico
./vendor/bin/phpunit tests/CNPJTest.php
```

### Escrevendo Testes

- **Cobertura**: Mantenha 100% de cobertura de código
- **Casos de teste**: Inclua casos válidos, inválidos e edge cases
- **Nomenclatura**: Use nomes descritivos para métodos de teste
- **Assertivas**: Use assertivas específicas (assertEquals, assertTrue, etc.)

Exemplo de teste:
```php
public function testValidarCNPJComFormatoCorreto(): void
{
    $cnpj = '12.ABC.345/01DE-35';
    $resultado = CNPJ::validar($cnpj);
    
    $this->assertTrue($resultado, 'CNPJ válido deve passar na validação');
}
```

## 📚 Documentação

### PHPDoc

Todos os métodos públicos devem ter documentação completa:

```php
/**
 * Descrição breve do que o método faz.
 *
 * Descrição mais detalhada se necessário, incluindo:
 * - Como funciona
 * - Casos especiais
 * - Exemplos de uso
 *
 * @param string $parametro Descrição do parâmetro
 * @return bool Descrição do retorno
 * @throws InvalidArgumentException Quando o parâmetro é inválido
 * @see OutroMetodo() Para funcionalidade relacionada
 */
public function meuMetodo(string $parametro): bool
{
    // implementação
}
```

### Comentários no Código

- Use comentários para explicar **por que**, não **o que**
- Evite comentários óbvios
- Documente algoritmos complexos
- Explique decisões de design não óbvias

## 🔄 Processo de Pull Request

### Antes de Enviar

1. **Sincronize com upstream**
   ```bash
   git fetch upstream
   git rebase upstream/master
   ```

2. **Execute todos os checks**
   ```bash
   composer ci
   ```

3. **Teste manualmente** se aplicável

### Criando o Pull Request

1. **Título descritivo**: Use um título claro e conciso
2. **Descrição detalhada**: Explique as mudanças e motivação
3. **Referências**: Link issues relacionadas
4. **Testes**: Descreva como testar as mudanças

### Template de Pull Request

```markdown
## Resumo
Breve descrição das mudanças

## Mudanças
- [ ] Funcionalidade A adicionada
- [ ] Bug B corrigido
- [ ] Documentação C atualizada

## Testes
Como testar as mudanças:
1. Passo 1
2. Passo 2
3. Resultado esperado

## Checklist
- [ ] Testes passando
- [ ] Cobertura mantida
- [ ] Documentação atualizada
- [ ] CHANGELOG.md atualizado (se aplicável)
```

### Revisão

- **Seja receptivo**: Aceite feedback construtivo
- **Responda rapidamente**: Mantenha a discussão ativa
- **Faça mudanças**: Ajuste conforme solicitado
- **Teste novamente**: Após mudanças, execute os testes

## 🐛 Reportando Bugs

### Antes de Reportar

1. **Verifique issues existentes**: Pode já ter sido reportado
2. **Use a versão mais recente**: Pode já ter sido corrigido
3. **Reproduza o problema**: Confirme que não é ambiente local

### Template de Bug Report

```markdown
## Descrição
Descrição clara e concisa do bug

## Reprodução
Passos para reproduzir:
1. Execute '...'
2. Com parâmetro '...'
3. Veja o erro

## Comportamento Esperado
O que deveria acontecer

## Comportamento Atual
O que realmente acontece

## Ambiente
- PHP: versão
- CNPJUtils: versão
- OS: sistema operacional

## Informações Adicionais
Logs, screenshots, etc.
```

## 💡 Sugerindo Melhorias

### Template de Feature Request

```markdown
## Resumo
Breve descrição da funcionalidade

## Motivação
Por que esta funcionalidade é importante?

## Solução Proposta
Como você imagina que isso deveria funcionar?

## Alternativas Consideradas
Outras formas de resolver o problema

## Informações Adicionais
Contexto adicional, exemplos, etc.
```

## 📋 Diretrizes Gerais

### Commits

- **Mensagens claras**: Use mensagens descritivas
- **Commits pequenos**: Faça commits atômicos
- **Conventional Commits**: Considere usar este padrão

### Branches

- **master**: Branch principal, sempre estável
- **feature/nome-da-feature**: Para novas funcionalidades
- **fix/nome-do-bug**: Para correções
- **docs/descricao**: Para documentação

### Versionamento

Seguimos [Semantic Versioning](https://semver.org/):
- **MAJOR**: Mudanças incompatíveis na API
- **MINOR**: Novas funcionalidades compatíveis
- **PATCH**: Correções de bugs compatíveis

## 🙋‍♀️ Precisa de Ajuda?

- **Issues**: Use as issues para discussões técnicas
- **Documentação**: Consulte o README.md e o CHANGELOG.md
- **Código**: Veja exemplos nos testes

## 🎉 Reconhecimento

Todos os contribuidores são reconhecidos e valorizados! Contribuições de qualquer tamanho são bem-vindas.

Obrigado por contribuir com o CNPJUtils! 🚀
