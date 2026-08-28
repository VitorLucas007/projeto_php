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

include_once('../../models/model.aluno.class.php');

$aluno = new aluno('', '');

$alunos = $aluno->listarAlunos($_SESSION['fk_unidade']);
?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4">

    <div class="card">

        <div class="card-header">
            <h2>Alunos</h2>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-striped table-hover align-middle">

                    <thead class="table-dark">
                        <tr>
                            <th>Nome</th>
                            <th>Observações</th>
                            <th width="260">Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (!empty($alunos)): ?>
                        <?php foreach ($alunos as $linha): ?>
                            <tr>
                                <td><?= htmlspecialchars($linha[1]) ?></td>
                                <td><?= htmlspecialchars($linha[2]) ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="view.avaliacoes.por.aluno.php?id=<?= (int) $linha[0] ?>" class="btn btn-primary btn-sm">
                                            Ver avaliações
                                        </a>
                                        <a href="../treino/view.ficha.treino.php?id=<?= (int) $linha[0] ?>" class="btn btn-info btn-sm text-white">
                                            <i class="bi bi-card-list"></i> Ver Ficha
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="text-center">Nenhum aluno cadastrado.</td></tr>
                    <?php endif; ?>
                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php include_once('../layouts/view.rodape.php'); ?>