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
    (int) ($_GET['id'] ?? 0)
);

header(
    "Location: ../views/pessoa/view.pessoa.php"
);

exit;