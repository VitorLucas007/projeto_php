<?php

include_once('../models/model.avaliacao.class.php');

$avaliacao = new avaliacao($_POST);

$avaliacao->cadastrarAvaliacao();

header("Location: ../views/avaliacao/view.avaliacao.php");

exit;
