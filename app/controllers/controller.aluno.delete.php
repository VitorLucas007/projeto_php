<?php

include_once('../models/model.aluno.class.php');

$aluno = new aluno(
    '',
    ''
);

$aluno->excluirAluno(
    (int) ($_GET['id'] ?? 0)
);

header(
    "Location: ../views/aluno/view.aluno.php"
);

exit;