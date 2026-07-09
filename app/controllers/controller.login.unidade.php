<?php

include_once('../models/model.validacoes.class.php');
include_once('../models/model.unidade.class.php');
include_once('../models/model.usuario.class.php');

$cnpj = trim($_POST['login_cnpj'] ?? '');
$senha = $_POST['login_senha'] ?? '';

if ($cnpj === '' || $senha === '' || !validacoes::validar_cnpj($cnpj)) {
    header("Location: ../views/auth/view.login.php?erro_unidade=1");
    exit;
}

$cnpjLimpo = validacoes::remover_formatacao_cnpj($cnpj);
$unidade = unidade::buscarPorCnpj($cnpjLimpo);

if (!isset($unidade[0])) {
    header("Location: ../views/auth/view.login.php?erro_unidade=1");
    exit;
}

$resultado = usuario::loginUsuarioPorUnidade($unidade[0][0], $senha);

switch ($resultado) {

    case "OK":
        header("Location: ../views/home/view.home.php");
        break;

    case "PENDENTE":
        header("Location: ../views/auth/view.login.php?erro=pendente");
        break;

    default:
        // "NAO_ENCONTRADO" e "SENHA_INCORRETA" retornam a mesma mensagem
        // de propósito, pra não revelar se o CNPJ existe ou não.
        header("Location: ../views/auth/view.login.php?erro_unidade=1");
        break;
}

exit;
