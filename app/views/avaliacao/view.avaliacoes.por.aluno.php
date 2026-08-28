<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
    exit;
}

include_once('../../models/model.usuario.class.php');

if (($_SESSION['fk_perfil'] ?? null) != usuario::PERFIL_PROFESSOR) {
    header("Location: ../home/view.home.php");
    exit;
}

include_once('../../models/model.avaliacao.class.php');

$idAluno = (int) ($_GET['id'] ?? 0);
$dados = avaliacao::listarPorAluno($idAluno);
?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="mb-0">Histórico de Avaliações</h2>
            <div class="d-flex gap-2">
                <a href="view.avaliacao.create.php" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle"></i> Nova avaliação
                </a>
                <?php if (count($dados) >= 2): ?>
                    <a href="view.avaliacao.comparativo.php?id_aluno=<?= $idAluno ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-bar-chart-line"></i> Comparar avaliações
                    </a>
                <?php endif; ?>
                <a href="view.avaliacao.alunos.php" class="btn btn-secondary btn-sm">Voltar</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Professor Avaliador</th>
                            <th>Data Avaliação</th>
                            <th>F. Cardíaca</th>
                            <th>P. Arterial</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($dados)): ?>
                        <?php foreach ($dados as $linha): ?>
                            <tr class="cursor-pointer" onclick="window.location='view.avaliacao.detalhe.php?id=<?= (int) $linha[0] ?>'" style="cursor:pointer">
                                <td><?= (int) $linha[0] ?></td>
                                <td><?= htmlspecialchars($linha[2]) ?></td>
                                <td><?= date('d/m/Y', strtotime($linha[3])) ?></td>
                                <td><?= htmlspecialchars($linha[4]) ?></td>
                                <td><?= htmlspecialchars($linha[5]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">Nenhuma avaliação cadastrada ainda.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once('../layouts/view.rodape.php'); ?>
