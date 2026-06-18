<?php

include_once('../models/model.professor.class.php');

$professor = new professor(
    $_POST['fk_pessoa'],
    $_POST['cref'],
    $_POST['especialidade']
);

$professor->atualizarProfessor(
    $_POST['id_professor']
);

header(
    "Location: ../views/professor/view.professor.php"
);

exit;