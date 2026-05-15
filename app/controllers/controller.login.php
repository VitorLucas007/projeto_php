<?php

include_once('../models/model.usuario.class.php');

$usuario = new usuario(
    '',
    $_POST['login_email'],
    $_POST['login_senha']
);

$login = $usuario->loginUsuario();

if ($login) {
    header("Location: ../views/home/view.home.php");
} else {
    header("Location: ../views/auth/view.login.php");
}
