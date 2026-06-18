<?php

include_once('../models/model.usuario.class.php');

$usuario = new usuario(
    $_POST['login_email'],
    $_POST['login_senha']
);

if ($usuario->loginUsuario()) {

    header("Location: ../views/home/view.home.php");

} else {

    header("Location: ../views/auth/view.login.php?erro=1");

}