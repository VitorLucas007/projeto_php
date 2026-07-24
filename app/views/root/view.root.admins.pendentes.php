<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
    exit;
}

include_once('../../models/model.usuario.class.php');

if (($_SESSION['fk_perfil'] ?? null) != usuario::PERFIL_ROOT) {
    header("Location: ../home/view.home.php");
    exit;
}

$pendentes = usuario::listarPendentesAdmin();
?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header">
            <h2>Unidades / Admins pendentes de aprovação</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Admin</th>
                            <th>E-mail</th>
                            <th>Unidade</th>
                            <th>Cadastrado em</th>
                            <th width="220">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($pendentes)): ?>
                        <?php foreach ($pendentes as $linha): ?>
                            <tr>
                                <td><?= htmlspecialchars($linha[1]) ?></td>
                                <td><?= htmlspecialchars($linha[2]) ?></td>
                                <td><?= htmlspecialchars($linha[3]) ?></td>
                                <td><?= htmlspecialchars($linha[4]) ?></td>
                                <td>
                                    <a href="../../controllers/controller.usuario.aprovar.php?id=<?= (int) $linha[0] ?>&acao=aprovar"
                                       class="btn btn-success btn-sm"
                                       onclick="return confirm('Aprovar esta unidade/admin?')">Aprovar</a>
                                    <a href="../../controllers/controller.usuario.aprovar.php?id=<?= (int) $linha[0] ?>&acao=rejeitar"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Recusar esta unidade/admin?')">Recusar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">Nenhuma unidade/admin pendente.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once('../layouts/view.rodape.php'); ?>
