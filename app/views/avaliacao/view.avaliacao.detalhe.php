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

include_once('../../models/model.avaliacao.class.php');
include_once('../../models/model.professor.class.php');

$id = (int) ($_GET['id'] ?? 0);
$v = avaliacao::buscarAvaliacao($id);

if (!$v) {
    header("Location: view.avaliacao.php");
    exit;
}

// Aluno só pode ver a própria avaliação, nunca a de outro aluno pela URL.
if ($perfil == usuario::PERFIL_PESSOA) {
    if (avaliacao::buscarFkPessoaAluno($id) != $_SESSION['fk_pessoa']) {
        header("Location: view.minhas.avaliacoes.php");
        exit;
    }
}

$prontuarios = avaliacao::listarProntuarios();
$nomeProfessorResponsavel = professor::buscarNomePorId($v['fk_professor']);
$readonly = true;
?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4 mb-5">
    <div class="card shadow">
        <div class="card-header">
            <h2>Avaliação Física #<?= (int) $v['id_avaliacao'] ?> (somente leitura)</h2>
        </div>
        <div class="card-body">
            <?php include('partial.campos.php'); ?>
        </div>
    </div>
</div>

<?php include_once('../layouts/view.rodape.php'); ?>
