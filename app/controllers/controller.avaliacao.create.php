<?php

session_start();

include_once('../models/model.usuario.class.php');

if (!isset($_SESSION['id']) || ($_SESSION['fk_perfil'] ?? null) != usuario::PERFIL_PROFESSOR) {
    header("Location: ../views/auth/view.login.php");
    exit;
}

include_once('../models/model.professor.class.php');
include_once('../models/model.avaliacao.class.php');

// O professor responsável é sempre o da sessão, nunca o valor vindo do POST.
$_POST['fk_professor'] = professor::buscarPorPessoa($_SESSION['fk_pessoa']);

$avaliacao = new avaliacao($_POST);

$avaliacao->cadastrarAvaliacao();

header("Location: ../views/avaliacao/view.avaliacao.php");

exit;
