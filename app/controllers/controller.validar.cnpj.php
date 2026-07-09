<?php

header('Content-Type: application/json');

include_once('../models/model.validacoes.class.php');
include_once('../models/model.unidade.class.php');

$cnpj = trim($_POST['cnpj'] ?? '');

$valido = $cnpj !== '' && validacoes::validar_cnpj($cnpj);
$existe = false;

if ($valido) {
    $cnpjLimpo = validacoes::remover_formatacao_cnpj($cnpj);
    $existe = unidade::existeCnpj($cnpjLimpo);
}

echo json_encode([
    'valido' => $valido,
    'existe' => $existe
]);
exit;
