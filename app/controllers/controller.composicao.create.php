<?php

session_start();

include_once('../models/model.usuario.class.php');

if (!isset($_SESSION['id']) || ($_SESSION['fk_perfil'] ?? null) != usuario::PERFIL_PROFESSOR) {
    header("Location: ../views/auth/view.login.php");
    exit;
}

include_once('../models/model.professor.class.php');
include_once('../models/model.ficha_composicao.class.php');

$fkAvaliacao = (int) ($_POST['fk_avaliacao'] ?? 0);
$contexto = ficha_composicao::buscarContextoAvaliacao($fkAvaliacao);

if (!$contexto) {
    header("Location: ../views/avaliacao/view.avaliacao.php");
    exit;
}

// O professor responsável é sempre o da sessão, nunca o valor vindo do POST.
$_POST['fk_professor'] = professor::buscarPorPessoa($_SESSION['fk_pessoa']);

// Não são colunas da ficha, só entrada pro recalcular() achar a relação cintura-quadril.
$_POST['_cintura_avaliacao'] = $contexto['cintura'];
$_POST['_quadril_avaliacao'] = $contexto['quadril'];

$dados = ficha_composicao::recalcular($_POST);

$ficha = new ficha_composicao($dados);
$idFicha = $ficha->cadastrar();

header("Location: ../views/composicao/view.composicao.detalhe.php?id=" . $idFicha);

exit;
