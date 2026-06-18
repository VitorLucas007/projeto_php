<?php

include_once('../models/model.avaliacao.class.php');

$avaliacao = new avaliacao(
    '',
    '',
    '',
    '',
    ''
);

$avaliacao->excluirAvaliacao(
    $_GET['id']
);

header(
    "Location: ../views/avaliacao/view.avaliacao.php"
);

exit;