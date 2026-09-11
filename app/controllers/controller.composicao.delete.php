<?php

session_start();

include_once('../models/model.usuario.class.php');

if (!isset($_SESSION['id']) || ($_SESSION['fk_perfil'] ?? null) != usuario::PERFIL_PROFESSOR) {
    header("Location: ../views/auth/view.login.php");
    exit;
}

include_once('../models/model.ficha_composicao.class.php');

$id = (int) ($_GET['id'] ?? 0);
$idAluno = (int) ($_GET['id_aluno'] ?? 0);

if ($id > 0) {
    ficha_composicao::excluir($id);
}

$destino = $idAluno > 0
    ? "../views/composicao/view.composicoes.por.aluno.php?id=" . $idAluno
    : "../views/avaliacao/view.avaliacao.php";

header("Location: " . $destino);

exit;
