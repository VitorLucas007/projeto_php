<?php

include_once('../models/model.avaliacao.class.php');

$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    avaliacao::excluirAvaliacao($id);
}

header("Location: ../views/avaliacao/view.avaliacao.php");

exit;
