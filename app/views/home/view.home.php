<?php

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
    exit; // É uma boa prática adicionar exit após um redirecionamento de header
}

include_once('../../models/model.usuario.class.php');
include_once('../../models/model.dashboard.class.php');

$perfil = (int) ($_SESSION['fk_perfil'] ?? 0);

$statsProfessor = null;
$distribuicaoImc = null;
$statsAluno = null;
$historicoAluno = [];
$statsAdmin = null;
$matriculasPorMes = null;
$pendentesAdmin = 0;
$statsRoot = null;
$pendentesRoot = 0;

if ($perfil === usuario::PERFIL_PROFESSOR) {
    include_once('../../models/model.professor.class.php');

    $fkProfessor = professor::buscarPorPessoa($_SESSION['fk_pessoa']);
    $statsProfessor = dashboard::statsProfessor($fkProfessor, $_SESSION['fk_unidade']);
    $distribuicaoImc = dashboard::distribuicaoImcPorUnidade($_SESSION['fk_unidade']);

} elseif ($perfil === usuario::PERFIL_PESSOA) {
    include_once('../../models/model.aluno.class.php');
    include_once('../../models/model.avaliacao.class.php');

    $idAluno = aluno::buscarPorPessoa($_SESSION['fk_pessoa']);
    if ($idAluno) {
        $historicoAluno = avaliacao::listarCompletoPorAluno($idAluno);
        $statsAluno = dashboard::resumoAluno($idAluno);
    }

} elseif ($perfil === usuario::PERFIL_ADMIN) {
    $statsAdmin = dashboard::statsAdmin($_SESSION['fk_unidade']);
    $matriculasPorMes = dashboard::matriculasPorMes($_SESSION['fk_unidade']);
    $pendentesAdmin = usuario::contarPendentes($_SESSION['fk_unidade']);

} elseif ($perfil === usuario::PERFIL_ROOT) {
    $statsRoot = dashboard::statsRoot();
    $pendentesRoot = usuario::contarPendentesAdmin();
}

function mesAbreviado($anoMes)
{
    $meses = ['01'=>'Jan','02'=>'Fev','03'=>'Mar','04'=>'Abr','05'=>'Mai','06'=>'Jun','07'=>'Jul','08'=>'Ago','09'=>'Set','10'=>'Out','11'=>'Nov','12'=>'Dez'];
    [$ano, $mes] = explode('-', $anoMes);
    return $meses[$mes] . '/' . substr($ano, 2);
}
?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4 mb-5">

    <h2 class="mb-4">Bem-vindo, <?= htmlspecialchars($_SESSION['nome']) ?></h2>

    <?php if ($perfil === usuario::PERFIL_PROFESSOR): ?>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted text-uppercase texto-pequeno" style="font-size:.75rem">Alunos ativos</div>
                        <div class="fs-2 fw-bold"><?= (int) $statsProfessor['alunos_ativos'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted text-uppercase texto-pequeno" style="font-size:.75rem">Avaliações feitas este mês</div>
                        <div class="fs-2 fw-bold"><?= (int) $statsProfessor['avaliacoes_no_mes'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm h-100 <?= count($statsProfessor['sem_avaliacao_recente']) > 0 ? 'border-warning' : '' ?>">
                    <div class="card-body">
                        <div class="text-muted text-uppercase texto-pequeno" style="font-size:.75rem">Alunos sem avaliação há +60 dias</div>
                        <div class="fs-2 fw-bold <?= count($statsProfessor['sem_avaliacao_recente']) > 0 ? 'text-warning' : '' ?>">
                            <?= count($statsProfessor['sem_avaliacao_recente']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-7">
                <div class="card shadow-sm h-100">
                    <div class="card-header">Distribuição de IMC da carteira (última avaliação de cada aluno)</div>
                    <div class="card-body">
                        <?php if (empty($distribuicaoImc)): ?>
                            <p class="text-muted mb-0">Nenhum aluno com IMC registrado ainda.</p>
                        <?php else: ?>
                            <canvas id="graficoImcCarteira" height="110"></canvas>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card shadow-sm h-100">
                    <div class="card-header">Precisam de reavaliação</div>
                    <div class="card-body p-0">
                        <?php if (empty($statsProfessor['sem_avaliacao_recente'])): ?>
                            <p class="text-muted p-3 mb-0">Todos os alunos ativos foram avaliados nos últimos 60 dias. 🎉</p>
                        <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($statsProfessor['sem_avaliacao_recente'] as $linha): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><?= htmlspecialchars($linha[0]) ?></span>
                                        <span class="badge bg-warning text-dark">
                                            <?= $linha[2] ? 'Última: ' . date('d/m/Y', strtotime($linha[2])) : 'Nunca avaliado' ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($distribuicaoImc)): ?>
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
            <script>
                const dadosImc = <?= json_encode($distribuicaoImc) ?>;
                new Chart(document.getElementById('graficoImcCarteira'), {
                    type: 'bar',
                    data: {
                        labels: dadosImc.map(d => d[0]),
                        datasets: [{ label: 'IMC', data: dadosImc.map(d => d[1]), backgroundColor: '#0d6efd' }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: false, suggestedMin: 15, suggestedMax: 35 } }
                    }
                });
            </script>
        <?php endif; ?>

    <?php elseif ($perfil === usuario::PERFIL_PESSOA): ?>

        <?php
        $ultima = !empty($historicoAluno) ? end($historicoAluno) : null;
        $penultima = count($historicoAluno) >= 2 ? $historicoAluno[count($historicoAluno) - 2] : null;
        $ficha = $statsAluno['ficha_treino_ativa'] ?? null;
        ?>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted text-uppercase texto-pequeno" style="font-size:.75rem">Peso atual</div>
                        <div class="fs-2 fw-bold"><?= $ultima && $ultima['peso'] !== null ? number_format($ultima['peso'], 1, ',', '.') . ' kg' : '—' ?></div>
                        <?php if ($ultima && $penultima && $ultima['peso'] !== null && $penultima['peso'] !== null): ?>
                            <?php $diff = $ultima['peso'] - $penultima['peso']; ?>
                            <div class="texto-pequeno <?= $diff < 0 ? 'text-success' : ($diff > 0 ? 'text-danger' : 'text-muted') ?>">
                                <?= $diff > 0 ? '+' : '' ?><?= number_format($diff, 1, ',', '.') ?> kg desde a última avaliação
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted text-uppercase texto-pequeno" style="font-size:.75rem">IMC atual</div>
                        <div class="fs-2 fw-bold"><?= $ultima && $ultima['imc'] !== null ? number_format($ultima['imc'], 1, ',', '.') : '—' ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted text-uppercase texto-pequeno" style="font-size:.75rem">Ficha de treino ativa</div>
                        <?php if ($ficha): ?>
                            <div class="fw-bold"><?= htmlspecialchars($ficha[1]) ?></div>
                            <div class="texto-pequeno text-muted">Válida até <?= $ficha[3] ? date('d/m/Y', strtotime($ficha[3])) : '—' ?></div>
                        <?php else: ?>
                            <div class="text-muted">Nenhuma ficha ativa</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">Minha evolução</div>
            <div class="card-body">
                <?php if (count($historicoAluno) < 2): ?>
                    <p class="text-muted mb-0">Você ainda não tem avaliações suficientes para ver um gráfico de evolução (mínimo de 2).</p>
                <?php else: ?>
                    <canvas id="graficoEvolucaoAluno" height="90"></canvas>
                    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
                    <script>
                        const historico = <?= json_encode(array_map(fn($h) => [
                            'data' => date('d/m/y', strtotime($h['data_avaliacao'])),
                            'peso' => $h['peso'],
                            'pgc' => $h['percentual_gordura'],
                            'imc' => $h['imc'],
                        ], $historicoAluno)) ?>;

                        new Chart(document.getElementById('graficoEvolucaoAluno'), {
                            type: 'line',
                            data: {
                                labels: historico.map(h => h.data),
                                datasets: [
                                    { label: 'Peso (kg)', data: historico.map(h => h.peso), borderColor: '#0d6efd', tension: 0.2 },
                                    { label: '% Gordura', data: historico.map(h => h.pgc), borderColor: '#dc3545', tension: 0.2 },
                                    { label: 'IMC', data: historico.map(h => h.imc), borderColor: '#198754', tension: 0.2 },
                                ]
                            },
                            options: { responsive: true, plugins: { legend: { position: 'top' } } }
                        });
                    </script>
                <?php endif; ?>
            </div>
        </div>

    <?php elseif ($perfil === usuario::PERFIL_ADMIN): ?>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted text-uppercase texto-pequeno" style="font-size:.75rem">Alunos ativos</div>
                        <div class="fs-2 fw-bold"><?= (int) $statsAdmin['alunos'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted text-uppercase texto-pequeno" style="font-size:.75rem">Professores ativos</div>
                        <div class="fs-2 fw-bold"><?= (int) $statsAdmin['professores'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm h-100 <?= $pendentesAdmin > 0 ? 'border-warning' : '' ?>">
                    <div class="card-body">
                        <div class="text-muted text-uppercase texto-pequeno" style="font-size:.75rem">Cadastros pendentes</div>
                        <div class="fs-2 fw-bold <?= $pendentesAdmin > 0 ? 'text-warning' : '' ?>"><?= $pendentesAdmin ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">Novas matrículas de alunos (últimos 6 meses)</div>
            <div class="card-body">
                <canvas id="graficoMatriculas" height="90"></canvas>
                <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
                <script>
                    const matriculas = <?= json_encode(array_map(fn($m) => ['mes' => mesAbreviado($m['mes']), 'total' => $m['total']], $matriculasPorMes)) ?>;
                    new Chart(document.getElementById('graficoMatriculas'), {
                        type: 'bar',
                        data: {
                            labels: matriculas.map(m => m.mes),
                            datasets: [{ label: 'Novos alunos', data: matriculas.map(m => m.total), backgroundColor: '#198754' }]
                        },
                        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
                    });
                </script>
            </div>
        </div>

    <?php elseif ($perfil === usuario::PERFIL_ROOT): ?>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted text-uppercase texto-pequeno" style="font-size:.75rem">Unidades ativas</div>
                        <div class="fs-2 fw-bold"><?= (int) $statsRoot['unidades'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm h-100 <?= $pendentesRoot > 0 ? 'border-warning' : '' ?>">
                    <div class="card-body">
                        <div class="text-muted text-uppercase texto-pequeno" style="font-size:.75rem">Admins pendentes de aprovação</div>
                        <div class="fs-2 fw-bold <?= $pendentesRoot > 0 ? 'text-warning' : '' ?>"><?= $pendentesRoot ?></div>
                        <?php if ($pendentesRoot > 0): ?>
                            <a href="../root/view.root.admins.pendentes.php" class="btn btn-sm btn-outline-warning mt-2">Revisar pendentes</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="card">
            <div class="card-body text-center">
                <p class="text-muted">Abra o menu para acessar as seções.</p>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php include_once('../layouts/view.rodape.php'); ?>
