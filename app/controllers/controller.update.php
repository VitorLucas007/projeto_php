<?php

include_once('../models/model.users.class.php');

$users = new users('', '', '');

$users->atualizarusers(
    $_POST['id'],
    $_POST['nome'],
    $_POST['email']
);

header("Location: ../views/crud/view.read.php");
