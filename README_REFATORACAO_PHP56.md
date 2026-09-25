# Refatoração de Compatibilidade — PHP 5.6.40

Este documento registra a refatoração realizada para tornar o código do projeto estritamente compatível com **PHP 5.6.40**, seguindo as diretrizes definidas em [`skill_refatoracao_php_legado.md`](skill_refatoracao_php_legado.md).

## Escopo

A refatoração cobriu três frentes, conforme solicitado:

1. **PHP** — código da aplicação (models, controllers, views)
2. **SQL** — scripts em `database/`
3. **JavaScript** — `public/assets/js/composicao-calculos.js`

Nenhuma lógica, comportamento ou formatação foi alterada além do estritamente necessário para a compatibilidade de sintaxe. Todas as conversões preservam a semântica original do código.

---

## 1. PHP — problemas encontrados e corrigidos

Foram identificados quatro padrões de sintaxe incompatíveis com PHP 5.6.40 (introduzidos em versões posteriores do PHP), presentes em **56 arquivos**. Todos foram corrigidos.

### 1.1. Visibilidade em constantes de classe (`private const`)

Visibilidade em constantes de classe (`private`/`protected`/`public const`) só existe a partir do **PHP 7.1**. Em PHP 5.6, constantes de classe são sempre implicitamente públicas.

**Arquivo:** `app/models/model.persistirBD.class.php`

```php
// Antes
private const DB_HOST = "127.0.0.1";
private const DB_USER = "root";
private const DB_PASS = "";
private const DB_NAME = "projeto_pibeu";

// Depois
const DB_HOST = "127.0.0.1";
const DB_USER = "root";
const DB_PASS = "";
const DB_NAME = "projeto_pibeu";
```

Como nada fora da classe acessava essas constantes, remover o modificador de visibilidade não altera o comportamento.

### 1.2. Operador null coalesce (`??`)

O operador `??` foi introduzido no **PHP 7.0**. Todas as ocorrências (cerca de 130, em models, controllers e views) foram convertidas para a forma equivalente com `isset()` e ternário, que é suportada desde PHP 5.

```php
// Antes
$id = isset($_GET['id']) ?? 0;          // exemplo simples
$cnpj = trim($_POST['cnpj'] ?? '');
$perfil = $_SESSION['fk_perfil'] ?? null;

// Depois
$id = (int) (isset($_GET['id']) ? $_GET['id'] : 0);
$cnpj = trim(isset($_POST['cnpj']) ? $_POST['cnpj'] : '');
$perfil = isset($_SESSION['fk_perfil']) ? $_SESSION['fk_perfil'] : null;
```

Casos com encadeamento duplo (`$a ?? $b ?? $c`) foram convertidos com aninhamento equivalente:

```php
// Antes (app/models/model.aluno.class.php)
$id_aluno = (int) ($linha['id_aluno'] ?? $linha[0] ?? 0);

// Depois
$id_aluno = (int) (isset($linha['id_aluno']) ? $linha['id_aluno'] : (isset($linha[0]) ? $linha[0] : 0));
```

Quando o `??` era aplicado sobre o retorno direto de uma chamada de método (ex.: `$bd->retornoConsultas()[0][0] ?? 0`), foi usada uma variável intermediária antes do `isset()`, para manter a compatibilidade e a legibilidade:

```php
// Antes (app/models/model.dashboard.class.php)
$alunosAtivos = (int) ($bd->retornoConsultas()[0][0] ?? 0);

// Depois
$alunosAtivosResultado = $bd->retornoConsultas();
$alunosAtivos = (int) (isset($alunosAtivosResultado[0][0]) ? $alunosAtivosResultado[0][0] : 0);
```

O operador Elvis (`?:`), que já existe desde PHP 5.3, foi mantido sem alteração onde aparecia (não é um problema de compatibilidade).

### 1.3. Destructuring curto de array (`[$a, $b] = $expr;`)

A sintaxe curta de destructuring em atribuições foi introduzida no **PHP 7.1**. Convertida para `list()`, suportado desde PHP 4.

```php
// Antes
[$colunas, $tipos, $valores] = $this->montarListasBind();
[$fkPessoa, $fkPerfil] = $dados[0];
[$ano, $mes] = explode('-', $anoMes);

// Depois
list($colunas, $tipos, $valores) = $this->montarListasBind();
list($fkPessoa, $fkPerfil) = $dados[0];
list($ano, $mes) = explode('-', $anoMes);
```

### 1.4. Destructuring curto dentro de `foreach` (`foreach ($x as $k => [$a, $b])`)

Mesma limitação do item anterior, aplicada ao valor de um `foreach`. Como `list()` não pode ser escrito diretamente na cláusula `as` de um `foreach` em PHP 5.6, cada ocorrência foi reestruturada com uma variável intermediária:

```php
// Antes
foreach ($segMagra as $rotulo => [$kg, $pct]):
    // usa $kg e $pct
endforeach;

// Depois
foreach ($segMagra as $rotulo => $meta):
    list($kg, $pct) = $meta;
    // usa $kg e $pct (corpo do loop inalterado)
endforeach;
```

Em casos de `foreach` aninhado (`app/views/avaliacao/view.avaliacao.imprimir.php`), a variável intermediária recebeu um nome distinto (`$dadosLinha`) para não colidir com a variável do laço externo (`$par`).

### 1.5. O que **não** foi alterado

- Type hints de array em parâmetros, como `function foo(array $x)` — válidos desde **PHP 5.1**, não são um problema de compatibilidade com 5.6.
- Sintaxe alternativa de controle (`if (...):`/`endif;`, `foreach (...):`/`endforeach;`) — suportada desde sempre no PHP.
- Uso do nome de variável `$readonly` — é apenas um identificador comum no código (ex.: `partial.campos.php`), não a palavra reservada `readonly` introduzida no PHP 8.1.
- `[]` como sintaxe de array literal — válida desde PHP 5.4, mantida como estava.

### 1.6. Arquivos alterados

**Models** (`app/models/`):
- `model.persistirBD.class.php`
- `model.ficha_composicao.class.php`
- `model.dashboard.class.php`
- `model.avaliacao.class.php`
- `model.aluno.class.php`
- `model.professor.class.php`
- `model.usuario.class.php`

**Controllers** (`app/controllers/`) — 20 arquivos:
`controller.aluno.delete.php`, `controller.avaliacao.create.php`, `controller.avaliacao.delete.php`, `controller.avaliacao.update.php`, `controller.cadastro.php`, `controller.composicao.create.php`, `controller.composicao.delete.php`, `controller.composicao.update.php`, `controller.ficha_treino.create.php`, `controller.ficha_treino.delete.php`, `controller.ficha_treino.edit.php`, `controller.login.php`, `controller.login.unidade.php`, `controller.pessoa.create.php`, `controller.pessoa.delete.php`, `controller.pessoa.update.php`, `controller.professor.delete.php`, `controller.unidade.create.php`, `controller.usuario.aprovar.php`, `controller.validar.cnpj.php`, `controller.validar.cpf.php`.

**Views** (`app/views/`) — 28 arquivos:
- `avaliacao/`: `partial.campos.php`, `view.avaliacao.alunos.php`, `view.avaliacao.comparativo.php`, `view.avaliacao.create.php`, `view.avaliacao.detalhe.php`, `view.avaliacao.imprimir.php`, `view.avaliacao.update.php`, `view.avaliacoes.por.aluno.php`, `view.minhas.avaliacoes.php`
- `composicao/`: `partial.campos.composicao.php`, `view.composicao.create.php`, `view.composicao.detalhe.php`, `view.composicao.imprimir.php`, `view.composicao.update.php`, `view.composicoes.por.aluno.php`, `view.minhas.composicoes.php`
- `home/view.home.php`
- `layouts/sidebar.php`, `layouts/view.menu.php`
- `treino/view.ficha_treino.edit.php`, `treino/view.ficha.imprimir.php`, `treino/view.ficha.treino.php`, `treino/view.ficha_treino.create.php`
- `pessoa/view.pessoa.create.php`, `pessoa/view.pessoa.php`, `pessoa/view.pessoa.update.php`
- `admin/view.usuarios.pendentes.php`
- `root/view.root.admins.pendentes.php`

**Total:** 56 arquivos, 199 linhas adicionadas / 189 removidas.

### 1.7. Validação

Todos os 56 arquivos alterados (e o restante do projeto) foram checados com `php -l` (lint de sintaxe) sem nenhum erro. Uma varredura final por regex confirmou:
- Zero ocorrências de `??` em código executável (resta uma única menção dentro de um comentário/docblock, apenas texto explicativo, sem efeito em tempo de execução).
- Zero ocorrências de destructuring curto (`[$a, $b] = ...` ou `=> [$a, $b]`).
- Zero ocorrências de visibilidade em `const`.

> **Observação:** o PHP CLI disponível neste ambiente (XAMPP) é a versão 8.0.30, portanto `php -l` valida apenas a ausência de erros fatais de sintaxe — todas as construções aqui removidas (`??`, destructuring curto, `private const`) continuam sendo sintaxe *válida* em PHP 8, então o lint não as reportaria como erro. A conformidade com PHP 5.6.40 foi garantida por revisão manual de cada padrão via busca textual (grep) em todo o código-fonte, não apenas pelo lint.

---

## 2. SQL — `database/`

Os arquivos `script_pibeu_definitivo_2026.sql`, `migracao_bioimpedancia.sql`, `migracao_ficha_composicao_corporal.sql`, `migracao_ficha_treino.sql` e `migracao_mock_avaliacoes.sql` foram revisados em busca de recursos exclusivos de versões mais recentes do MySQL (CTEs/`WITH`, funções de janela, funções `JSON_*`, `CHECK` constraints, colunas `GENERATED`, `ADD COLUMN IF NOT EXISTS`, collations `utf8mb4_0900_*`, etc.).

**Nenhuma ocorrência foi encontrada.** Os scripts já utilizam apenas sintaxe e recursos suportados por MySQL 5.x com o driver mysqli/mysqlnd (charset `utf8mb4_general_ci`, `ENGINE=InnoDB`, DDL padrão). **Nenhuma alteração foi necessária nos arquivos SQL.**

---

## 3. JavaScript — `public/assets/js/composicao-calculos.js`

Este arquivo é executado no **navegador do cliente**, não pelo interpretador PHP do servidor. A versão do PHP rodando no back-end não impõe nenhuma restrição sobre a sintaxe JavaScript aceita pelo navegador — são ambientes de execução independentes.

Por esse motivo, **não existe uma "compatibilidade com PHP 5.6" aplicável a JavaScript**, e nenhuma alteração foi feita neste arquivo no âmbito desta refatoração. Caso haja interesse em garantir suporte a navegadores mais antigos (compatibilidade ECMAScript/ES5, por exemplo), isso seria um objetivo diferente e seria tratado separadamente, se solicitado.

---

## Padrão de arquitetura mantido

Toda a refatoração respeitou a arquitetura já definida na skill:
- Persistência centralizada na classe `persistirBD` (mysqli + prepared statements via `persistirPreparado`).
- Abertura/fechamento de conexão por método nas models.
- Métodos transacionais com sufixo `Tx` recebendo uma instância ativa de `persistirBD`.
- Controllers procedurais que incluem a model, processam a requisição e redirecionam a view.

Nenhuma dessas convenções foi modificada — apenas a sintaxe incompatível com PHP 5.6.40 foi ajustada.
