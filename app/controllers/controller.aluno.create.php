<?php

include_once('../models/model.aluno.class.php');

$aluno = new aluno(
    $_POST['fk_pessoa'],
    $_POST['observacoes']
);

$aluno->cadastrarAluno();

header(
    "Location: ../views/aluno/view.aluno.php"
);

exit;