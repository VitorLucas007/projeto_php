<?php

header('Content-Type: application/json');

include_once('../models/model.validacoes.class.php');
include_once('../models/model.pessoa.class.php');

$cpf = trim($_POST['cpf'] ?? '');

$valido = $cpf !== '' && validacoes::validar_cpf($cpf);
$existe = false;

if ($valido) {
    $cpfLimpo = validacoes::remover_formatacao_cpf($cpf);
    $existe = pessoa::existeCpf($cpfLimpo);
}

echo json_encode([
    'valido' => $valido,
    'existe' => $existe
]);
exit;
