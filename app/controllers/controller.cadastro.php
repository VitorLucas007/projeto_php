<?php

include_once('../models/model.usuario.class.php');

$novo_usuario = new usuario(
    $_POST['cad_nome'],
    $_POST['cad_email'],
    $_POST['cad_senha']
);

$novo_usuario->persistirUsuario();

header("Location: ../views/auth/view.login.php");
