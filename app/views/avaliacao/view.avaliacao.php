<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
    exit;
}

include_once('../../models/model.avaliacao.class.php');

$dados = avaliacao::listarAvaliacoes();
?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>Histórico de Avaliações Físicas</h2>
            <a href="view.avaliacao.create.php" class="btn btn-success btn-sm">Nova Avaliação</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Prontuário</th>
                            <th>Professor Avaliador</th>
                            <th>Data Avaliação</th>
                            <th>F. Cardíaca</th>
                            <th>P. Arterial</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($dados)): ?>
                        <?php foreach ($dados as $linha): ?>
                            <tr>
                                <td><?= (int) $linha[0] ?></td>
                                <td>Prontuário #<?= (int) $linha[1] ?></td>
                                <td><?= htmlspecialchars($linha[2]) ?></td>
                                <td><?= date('d/m/Y', strtotime($linha[3])) ?></td>
                                <td><?= htmlspecialchars($linha[4]) ?></td>
                                <td><?= htmlspecialchars($linha[5]) ?></td>
                                <td>
                                    <a href="view.avaliacao.update.php?id=<?= (int) $linha[0] ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="../../controllers/controller.avaliacao.delete.php?id=<?= (int) $linha[0] ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Deseja realmente excluir esta avaliação?')">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center">Nenhuma avaliação cadastrada.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once('../layouts/view.rodape.php'); ?>
