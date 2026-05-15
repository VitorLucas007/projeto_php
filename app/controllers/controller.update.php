<?php

include_once('../models/model.usuario.class.php');

$usuario = new usuario('', '', '');

$usuario->atualizarUsuario(
    $_POST['id'],
    $_POST['nome'],
    $_POST['email']
);

header("Location: ../views/crud/view.read.php");
