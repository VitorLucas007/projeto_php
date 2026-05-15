<?php

include_once('../models/model.usuario.class.php');

$usuario = new usuario('', '', '');

$usuario->excluirUsuario($_GET['id']);

header("Location: ../views/crud/view.read.php");
