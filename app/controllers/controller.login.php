<?php

include_once('../models/model.usuario.class.php');

$usuario = new usuario(
    trim($_POST['login_email'] ?? ''),
    $_POST['login_senha'] ?? ''
);

$resultado = $usuario->loginUsuario();

switch ($resultado) {

    case "OK":
        header("Location: ../views/home/view.home.php");
        break;

    case "PENDENTE":
        header("Location: ../views/auth/view.login.php?erro=pendente");
        break;

    default:
        // "NAO_ENCONTRADO" e "SENHA_INCORRETA" retornam a mesma mensagem
        // de propósito, pra não revelar se o e-mail existe ou não.
        header("Location: ../views/auth/view.login.php?erro=1");
        break;
}

exit;
