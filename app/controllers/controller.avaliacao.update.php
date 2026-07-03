<?php

include_once('../models/model.avaliacao.class.php');

$id = (int) ($_POST['id_avaliacao'] ?? 0);

if ($id > 0) {
    $avaliacao = new avaliacao($_POST);
    $avaliacao->atualizarAvaliacao($id);
}

header("Location: ../views/avaliacao/view.avaliacao.php");

exit;
