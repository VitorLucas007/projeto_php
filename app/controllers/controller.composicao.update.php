<?php

session_start();

include_once('../models/model.usuario.class.php');

if (!isset($_SESSION['id']) || ($_SESSION['fk_perfil'] ?? null) != usuario::PERFIL_PROFESSOR) {
    header("Location: ../views/auth/view.login.php");
    exit;
}

include_once('../models/model.professor.class.php');
include_once('../models/model.ficha_composicao.class.php');

$id = (int) ($_POST['id_ficha'] ?? 0);
$fkAvaliacao = (int) ($_POST['fk_avaliacao'] ?? 0);
$contexto = ficha_composicao::buscarContextoAvaliacao($fkAvaliacao);

if (!$id || !$contexto) {
    header("Location: ../views/avaliacao/view.avaliacao.php");
    exit;
}

$_POST['fk_professor'] = professor::buscarPorPessoa($_SESSION['fk_pessoa']);
$_POST['_cintura_avaliacao'] = $contexto['cintura'];
$_POST['_quadril_avaliacao'] = $contexto['quadril'];

$dados = ficha_composicao::recalcular($_POST);

$ficha = new ficha_composicao($dados);
$ficha->atualizar($id);

header("Location: ../views/composicao/view.composicao.detalhe.php?id=" . $id);

exit;
