<?php

session_start();

include_once('../models/model.usuario.class.php');

if (!isset($_SESSION['id']) || ($_SESSION['fk_perfil'] ?? null) != usuario::PERFIL_PROFESSOR) {
    header("Location: ../views/auth/view.login.php");
    exit;
}

include_once('../models/model.avaliacao.class.php');

$id = (int) ($_POST['id_avaliacao'] ?? 0);

if ($id > 0) {
    $existente = avaliacao::buscarAvaliacao($id);

    if ($existente) {
        // O professor responsável nunca muda numa edição, mesmo que outro
        // professor esteja editando — evita reatribuição via POST adulterado.
        $_POST['fk_professor'] = $existente['fk_professor'];

        $avaliacao = new avaliacao($_POST);
        $avaliacao->atualizarAvaliacao($id);
    }
}

header("Location: ../views/avaliacao/view.avaliacao.php");

exit;
