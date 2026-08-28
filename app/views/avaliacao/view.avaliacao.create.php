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

$prontuarios = avaliacao::listarProntuarios();
$v = [
    'fk_professor' => professor::buscarPorPessoa($_SESSION['fk_pessoa']),
    'fk_prontuario' => $_GET['fk_prontuario'] ?? '',
];
$nomeProfessorResponsavel = $_SESSION['nome'];
?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4 mb-5">
    <div class="card shadow">
        <div class="card-header">
            <h2>Nova Avaliação Física</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="../../controllers/controller.avaliacao.create.php">
                <?php include('partial.campos.php'); ?>
                <button type="submit" class="btn btn-success w-100 mt-3">Salvar Avaliação</button>
            </form>
        </div>
    </div>
</div>

<?php include_once('../layouts/view.rodape.php'); ?>
