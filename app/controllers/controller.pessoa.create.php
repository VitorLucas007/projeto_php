<?php

include_once('../models/model.pessoa.class.php');

$pessoa = new pessoa(
    $_POST['nome'],
    $_POST['sexo'],
    $_POST['data_nascimento'],
    $_POST['profissao'],
    $_POST['contato'],
    $_POST['email']
);

$pessoa->cadastrarPessoa();

header(
    "Location: ../views/pessoa/view.pessoa.php"
);

exit;