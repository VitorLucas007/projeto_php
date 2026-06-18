<?php

include_once('../models/model.avaliacao.class.php');

$avaliacao = new avaliacao(
    $_POST['fk_prontuario'],
    $_POST['fk_professor'],
    $_POST['data_avaliacao'],
    $_POST['frequencia_cardiaca'],
    $_POST['pressao_arterial']
);

$avaliacao->atualizarAvaliacao(
    $_POST['id_avaliacao']
);

header(
    "Location: ../views/avaliacao/view.avaliacao.php"
);

exit;