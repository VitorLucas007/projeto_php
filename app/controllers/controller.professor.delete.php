<?php

include_once('../models/model.professor.class.php');

$professor = new professor(
    '',
    '',
    ''
);

$professor->excluirProfessor(
    (int) ($_GET['id'] ?? 0)
);

header(
    "Location: ../views/professor/view.professor.php"
);

exit;