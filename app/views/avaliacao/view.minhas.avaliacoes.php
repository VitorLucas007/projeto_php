<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
    exit;
}

include_once('../../models/model.usuario.class.php');

// Página exclusiva do aluno: cada um só pode ver as próprias avaliações
if (($_SESSION['fk_perfil'] ?? null) != usuario::PERFIL_PESSOA) {
    header("Location: ../home/view.home.php");
    exit;
}

include_once('../../models/model.avaliacao.class.php');

$dados = avaliacao::listarAvaliacoesPorPessoa($_SESSION['fk_pessoa']);
?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>Minhas Avaliações Físicas</h2>
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
                            <tr>
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
