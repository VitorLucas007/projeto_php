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

include_once('../../models/model.ficha_composicao.class.php');

$id = (int) ($_GET['id'] ?? 0);
$v = ficha_composicao::buscarPorId($id);

if (!$v) {
    header("Location: ../avaliacao/view.avaliacao.php");
    exit;
}

if ($perfil == usuario::PERFIL_PESSOA) {
    if (ficha_composicao::buscarFkPessoaAluno($id) != $_SESSION['fk_pessoa']) {
        header("Location: view.minhas.composicoes.php");
        exit;
    }
}

$contexto = ficha_composicao::buscarContextoAvaliacao($v['fk_avaliacao']);
$contexto['id_avaliacao'] = $v['fk_avaliacao'];
$readonly = true;

$historico = ficha_composicao::listarCompletoPorAluno($contexto['id_aluno']);
?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4 mb-5">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="mb-0">Ficha de Composição Corporal #<?= (int) $v['id_ficha'] ?> (somente leitura)</h2>
            <div class="d-flex gap-2 flex-wrap">
                <a href="view.composicao.imprimir.php?id=<?= (int) $v['id_ficha'] ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                    <i class="bi bi-printer"></i> Imprimir / Exportar PDF
                </a>
                <?php if ($perfil == usuario::PERFIL_PROFESSOR): ?>
                    <a href="view.composicao.update.php?id=<?= (int) $v['id_ficha'] ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <a href="../../controllers/controller.composicao.delete.php?id=<?= (int) $v['id_ficha'] ?>&id_aluno=<?= (int) $contexto['id_aluno'] ?>"
                       class="btn btn-outline-danger btn-sm"
                       onclick="return confirm('Excluir esta ficha de composição corporal?');">
                        <i class="bi bi-trash"></i> Excluir
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <?php include('partial.campos.composicao.php'); ?>

            <?php if (count($historico) >= 2): ?>
                <h5 class="mt-4">Evolução</h5>
                <hr class="mt-1">
                <canvas id="graficoEvolucaoComposicao" height="90"></canvas>

                <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
                <script>
                    const historico = <?= json_encode(array_map(fn($h) => [
                        'data' => date('d/m/y', strtotime($h['data_teste'])),
                        'peso' => $h['peso'],
                        'pgc' => $h['percentual_gordura_corporal'],
                        'imc' => $h['imc'],
                    ], $historico)) ?>;

                    new Chart(document.getElementById('graficoEvolucaoComposicao'), {
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
</div>

<?php include_once('../layouts/view.rodape.php'); ?>
