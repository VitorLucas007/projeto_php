<?php

include_once('../models/model.professor.class.php');

$professor = new professor(
    '',
    '',
    ''
);

$professor->excluirProfessor(
    $_GET['id']
);

header(
    "Location: ../views/professor/view.professor.php"
);

exit;