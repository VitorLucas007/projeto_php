<?php

include_once('../models/model.users.class.php');

$novo_users = new users(
    $_POST['cad_nome'],
    $_POST['cad_email'],
    $_POST['cad_senha']
);

$novo_users->persistirusers();

header("Location: ../views/auth/view.login.php");
