<?php

include_once('../models/model.pessoa.class.php');

$pessoa = new pessoa(
    '',
    '',
    '',
    '',
    '',
    ''
);

$pessoa->excluirPessoa(
    $_GET['id']
);

header(
    "Location: ../views/pessoa/view.pessoa.php"
);

exit;