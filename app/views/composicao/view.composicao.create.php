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

$fkAvaliacao = (int) ($_GET['fk_avaliacao'] ?? 0);
$contexto = ficha_composicao::buscarContextoAvaliacao($fkAvaliacao);

if (!$contexto) {
    header("Location: ../avaliacao/view.avaliacao.php");
    exit;
}

$contexto['id_avaliacao'] = $fkAvaliacao;
$v = [];
$nomeProfessorResponsavel = $_SESSION['nome'];
?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4 mb-5">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="mb-0">Nova Ficha de Composição Corporal</h2>
            <a href="../avaliacao/view.avaliacao.detalhe.php?id=<?= $fkAvaliacao ?>" class="btn btn-secondary btn-sm">Voltar</a>
        </div>
        <div class="card-body">
            <form method="POST" action="../../controllers/controller.composicao.create.php">
                <?php include('partial.campos.composicao.php'); ?>
                <button type="submit" class="btn btn-success w-100 mt-3">Salvar Ficha</button>
            </form>
        </div>
    </div>
</div>

<?php include_once('../layouts/view.rodape.php'); ?>
