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
include_once('../../models/model.professor.class.php');

$id = (int) ($_GET['id'] ?? 0);
$v = avaliacao::buscarAvaliacao($id);

if (!$v) {
    header("Location: ../avaliacao/view.avaliacao.php");
    exit;
}

$prontuarios = avaliacao::listarProntuarios();
$nomeProfessorResponsavel = professor::buscarNomePorId($v['fk_professor']);
?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4 mb-5">
    <div class="card shadow">
        <div class="card-header">
            <h2>Editar Avaliação Física #<?= (int) $v['id_avaliacao'] ?></h2>
        </div>
        <div class="card-body">
            <form method="POST" action="../../controllers/controller.avaliacao.update.php">
                <input type="hidden" name="id_avaliacao" value="<?= (int) $v['id_avaliacao'] ?>">
                <?php include('partial.campos.php'); ?>
                <button type="submit" class="btn btn-warning w-100 mt-3">Atualizar Avaliação</button>
            </form>
        </div>
    </div>
</div>

<?php include_once('../layouts/view.rodape.php'); ?>
