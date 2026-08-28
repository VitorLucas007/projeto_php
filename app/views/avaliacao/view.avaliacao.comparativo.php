<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
    exit;
}

include_once('../../models/model.usuario.class.php');

$perfil = $_SESSION['fk_perfil'] ?? null;

if (!in_array($perfil, [usuario::PERFIL_PROFESSOR, usuario::PERFIL_PESSOA], true)) {
    header("Location: ../home/view.home.php");
    exit;
}

include_once('../../models/model.avaliacao.class.php');

// Aluno pode ver só a própria comparação; professor informa o id_aluno pela URL.
if ($perfil == usuario::PERFIL_PESSOA) {
    include_once('../../models/model.aluno.class.php');
    $idAluno = aluno::buscarPorPessoa($_SESSION['fk_pessoa']);
} else {
    $idAluno = (int) ($_GET['id_aluno'] ?? 0);
}

$historico = avaliacao::listarCompletoPorAluno($idAluno);
$nomeAluno = avaliacao::buscarNomeAluno($idAluno);

// Índices escolhidos na URL (posição dentro do histórico, mais antiga = 0).
// Por padrão, compara a penúltima com a última.
$totalAvaliacoes = count($historico);
$idxAnterior = isset($_GET['anterior']) ? (int) $_GET['anterior'] : max(0, $totalAvaliacoes - 2);
$idxAtual    = isset($_GET['atual'])    ? (int) $_GET['atual']    : max(0, $totalAvaliacoes - 1);

$avAnterior = $historico[$idxAnterior] ?? null;
$avAtual    = $historico[$idxAtual] ?? null;

// Indicadores exibidos no comparativo: chave => [rótulo, unidade, grupo, "maior é melhor?"]
// contexto: pra maioria das medidas de gordura/circunferência, reduzir é "melhora";
// pra massa magra/muscular e água corporal, aumentar é "melhora".
$indicadores = [
    'peso'                     => ['Peso', 'kg', 'Bioimpedância', false],
    'percentual_gordura'       => ['% Gordura corporal', '%', 'Bioimpedância', false],
    'massa_magra'              => ['Massa magra', 'kg', 'Bioimpedância', true],
    'massa_muscular'           => ['Massa muscular', 'kg', 'Bioimpedância', true],
    'agua_corporal'            => ['Água corporal', '%', 'Bioimpedância', true],
    'imc'                      => ['IMC', '', 'Bioimpedância', false],
    'taxa_metabolica_basal'    => ['Taxa metabólica basal', 'kcal', 'Bioimpedância', true],
    'torax'                    => ['Tórax', 'cm', 'Medidas', null],
    'cintura'                  => ['Cintura', 'cm', 'Medidas', false],
    'abdominal'                => ['Abdominal', 'cm', 'Medidas', false],
    'quadril'                  => ['Quadril', 'cm', 'Medidas', null],
    'braco_relaxado_direito'   => ['Braço relaxado (D)', 'cm', 'Medidas', true],
    'braco_relaxado_esquerdo'  => ['Braço relaxado (E)', 'cm', 'Medidas', true],
    'braco_contraido_direito'  => ['Braço contraído (D)', 'cm', 'Medidas', true],
    'braco_contraido_esquerdo' => ['Braço contraído (E)', 'cm', 'Medidas', true],
    'coxa_direita'             => ['Coxa (D)', 'cm', 'Medidas', true],
    'coxa_esquerda'            => ['Coxa (E)', 'cm', 'Medidas', true],
    'panturrilha_direita'      => ['Panturrilha (D)', 'cm', 'Medidas', true],
    'panturrilha_esquerda'     => ['Panturrilha (E)', 'cm', 'Medidas', true],
];

/**
 * Monta a linha de comparação de um indicador entre duas avaliações.
 * Retorna null quando algum dos dois valores está ausente (não dá pra comparar).
 */
function montarComparacao($chave, $meta, $anterior, $atual)
{
    [$rotulo, $unidade, $grupo, $maiorEhMelhor] = $meta;

    $valorAnterior = $anterior[$chave] ?? null;
    $valorAtual = $atual[$chave] ?? null;

    if ($valorAnterior === null || $valorAnterior === '' || $valorAtual === null || $valorAtual === '') {
        return null;
    }

    $valorAnterior = (float) $valorAnterior;
    $valorAtual = (float) $valorAtual;
    $diferenca = $valorAtual - $valorAnterior;
    $percentual = ($valorAnterior != 0) ? ($diferenca / $valorAnterior) * 100 : null;

    $situacao = 'neutro';
    if ($maiorEhMelhor !== null && abs($diferenca) > 0.001) {
        $melhorou = $maiorEhMelhor ? ($diferenca > 0) : ($diferenca < 0);
        $situacao = $melhorou ? 'melhora' : 'piora';
    }

    return [
        'chave' => $chave,
        'rotulo' => $rotulo,
        'unidade' => $unidade,
        'grupo' => $grupo,
        'anterior' => $valorAnterior,
        'atual' => $valorAtual,
        'diferenca' => $diferenca,
        'percentual' => $percentual,
        'situacao' => $situacao,
    ];
}

$comparacoes = [];
if ($avAnterior && $avAtual) {
    foreach ($indicadores as $chave => $meta) {
        $linha = montarComparacao($chave, $meta, $avAnterior, $avAtual);
        if ($linha !== null) {
            $comparacoes[] = $linha;
        }
    }
}
?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4 mb-5">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="mb-0">Comparativo de Avaliações <?= $nomeAluno ? '— ' . htmlspecialchars($nomeAluno) : '' ?></h2>
            <div class="d-flex gap-2">
                <a href="view.avaliacao.create.php?fk_prontuario=<?= $avAtual['fk_prontuario'] ?? '' ?>" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle"></i> Nova avaliação
                </a>
                <a href="view.avaliacoes.por.aluno.php?id=<?= (int) $idAluno ?>" class="btn btn-secondary btn-sm">Voltar</a>
            </div>
        </div>
        <div class="card-body">

            <?php if ($totalAvaliacoes < 2): ?>
                <div class="alert alert-info">
                    Este aluno ainda não tem avaliações suficientes para comparar (mínimo de 2 avaliações cadastradas).
                </div>
            <?php else: ?>

                <!-- Seletor de quais avaliações comparar -->
                <form method="get" class="row g-3 align-items-end mb-4">
                    <?php if ($perfil != usuario::PERFIL_PESSOA): ?>
                        <input type="hidden" name="id_aluno" value="<?= (int) $idAluno ?>">
                    <?php endif; ?>
                    <div class="col-md-5">
                        <label class="form-label">Avaliação base (anterior)</label>
                        <select name="anterior" class="form-select" onchange="this.form.submit()">
                            <?php foreach ($historico as $i => $av): ?>
                                <option value="<?= $i ?>" <?= $i == $idxAnterior ? 'selected' : '' ?>>
                                    #<?= (int) $av['id_avaliacao'] ?> — <?= date('d/m/Y', strtotime($av['data_avaliacao'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Avaliação atual</label>
                        <select name="atual" class="form-select" onchange="this.form.submit()">
                            <?php foreach ($historico as $i => $av): ?>
                                <option value="<?= $i ?>" <?= $i == $idxAtual ? 'selected' : '' ?>>
                                    #<?= (int) $av['id_avaliacao'] ?> — <?= date('d/m/Y', strtotime($av['data_avaliacao'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Comparar</button>
                    </div>
                </form>

                <?php if ($idxAnterior === $idxAtual): ?>
                    <div class="alert alert-warning">Selecione duas avaliações diferentes para comparar.</div>
                <?php elseif (empty($comparacoes)): ?>
                    <div class="alert alert-warning">Nenhum indicador em comum preenchido nas duas avaliações escolhidas.</div>
                <?php else: ?>

                    <!-- Filtro por grupo -->
                    <ul class="nav nav-pills mb-3" id="filtroGrupo">
                        <li class="nav-item">
                            <button class="nav-link active" data-grupo="todos">Todos</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-grupo="Bioimpedância">Bioimpedância</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-grupo="Medidas">Medidas (cm)</button>
                        </li>
                    </ul>

                    <!-- Gráfico -->
                    <canvas id="graficoComparativo" height="90"></canvas>

                    <!-- Tabela detalhada -->
                    <div class="table-responsive mt-4">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Indicador</th>
                                    <th>Anterior</th>
                                    <th>Atual</th>
                                    <th>Diferença</th>
                                    <th>% Variação</th>
                                    <th>Situação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($comparacoes as $c): ?>
                                    <tr data-grupo="<?= htmlspecialchars($c['grupo']) ?>">
                                        <td><?= htmlspecialchars($c['rotulo']) ?></td>
                                        <td><?= number_format($c['anterior'], 2, ',', '.') ?> <?= $c['unidade'] ?></td>
                                        <td><?= number_format($c['atual'], 2, ',', '.') ?> <?= $c['unidade'] ?></td>
                                        <td class="<?= $c['diferenca'] > 0 ? 'text-primary' : ($c['diferenca'] < 0 ? 'text-danger' : '') ?>">
                                            <?= $c['diferenca'] > 0 ? '+' : '' ?><?= number_format($c['diferenca'], 2, ',', '.') ?> <?= $c['unidade'] ?>
                                        </td>
                                        <td>
                                            <?php if ($c['percentual'] !== null): ?>
                                                <?= $c['percentual'] > 0 ? '+' : '' ?><?= number_format($c['percentual'], 1, ',', '.') ?>%
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($c['situacao'] === 'melhora'): ?>
                                                <span class="badge bg-success"><i class="bi bi-arrow-up-circle"></i> Melhora</span>
                                            <?php elseif ($c['situacao'] === 'piora'): ?>
                                                <span class="badge bg-danger"><i class="bi bi-arrow-down-circle"></i> Piora</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Sem variação</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
                    <script>
                        const dadosComparacao = <?= json_encode($comparacoes) ?>;

                        function renderizarGrafico(grupo) {
                            const filtrados = grupo === 'todos'
                                ? dadosComparacao
                                : dadosComparacao.filter(d => d.grupo === grupo);

                            const labels = filtrados.map(d => d.rotulo + (d.unidade ? ` (${d.unidade})` : ''));
                            const anteriores = filtrados.map(d => d.anterior);
                            const atuais = filtrados.map(d => d.atual);

                            if (window._graficoComparativo) {
                                window._graficoComparativo.destroy();
                            }

                            const ctx = document.getElementById('graficoComparativo');
                            window._graficoComparativo = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: [
                                        { label: 'Avaliação anterior', data: anteriores, backgroundColor: '#adb5bd' },
                                        { label: 'Avaliação atual', data: atuais, backgroundColor: '#0d6efd' },
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    plugins: { legend: { position: 'top' } },
                                    scales: { y: { beginAtZero: false } }
                                }
                            });
                        }

                        renderizarGrafico('todos');

                        document.querySelectorAll('#filtroGrupo .nav-link').forEach(btn => {
                            btn.addEventListener('click', () => {
                                document.querySelectorAll('#filtroGrupo .nav-link').forEach(b => b.classList.remove('active'));
                                btn.classList.add('active');
                                const grupo = btn.dataset.grupo;
                                renderizarGrafico(grupo);

                                document.querySelectorAll('tbody tr[data-grupo]').forEach(tr => {
                                    tr.style.display = (grupo === 'todos' || tr.dataset.grupo === grupo) ? '' : 'none';
                                });
                            });
                        });
                    </script>

                <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php include_once('../layouts/view.rodape.php'); ?>
