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

include_once('../../models/model.ficha_composicao.class.php');

$id = (int) ($_GET['id'] ?? 0);
$v = ficha_composicao::buscarPorId($id);

if (!$v) {
    header("Location: ../avaliacao/view.avaliacao.php");
    exit;
}

$contexto = ficha_composicao::buscarContextoAvaliacao($v['fk_avaliacao']);
$contexto['id_avaliacao'] = $v['fk_avaliacao'];
$nomeProfessorResponsavel = $_SESSION['nome'];
?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4 mb-5">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="mb-0">Editar Ficha de Composição Corporal #<?= (int) $v['id_ficha'] ?></h2>
            <a href="view.composicao.detalhe.php?id=<?= (int) $v['id_ficha'] ?>" class="btn btn-secondary btn-sm">Voltar</a>
        </div>
        <div class="card-body">
            <form method="POST" action="../../controllers/controller.composicao.update.php">
                <?php include('partial.campos.composicao.php'); ?>
                <button type="submit" class="btn btn-success w-100 mt-3">Salvar Alterações</button>
            </form>
        </div>
    </div>
</div>

<?php include_once('../layouts/view.rodape.php'); ?>
