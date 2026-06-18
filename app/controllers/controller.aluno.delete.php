<?php

include_once('../models/model.aluno.class.php');

$aluno = new aluno(
    '',
    ''
);

$aluno->excluirAluno(
    $_GET['id']
);

header(
    "Location: ../views/aluno/view.aluno.php"
);

exit;