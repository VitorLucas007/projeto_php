<?php

session_start();

include_once('../models/model.usuario.class.php');

if (!isset($_SESSION['id']) || ($_SESSION['fk_perfil'] ?? null) != usuario::PERFIL_ROOT) {
    header("Location: ../views/auth/view.login.php");
    exit;
}

include_once('../models/model.pessoa.class.php');

$pessoa = new pessoa(
    $_POST['nome'],
    $_POST['sexo'],
    $_POST['data_nascimento'],
    $_POST['profissao'],
    $_POST['contato'],
    $_POST['email']
);

$pessoa->atualizarPessoa(
    $_POST['id_pessoa']
);

header(
    "Location: ../views/pessoa/view.pessoa.php"
);

exit;