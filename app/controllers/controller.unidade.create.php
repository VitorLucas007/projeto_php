<?php

include_once('../models/model.persistirBD.class.php');
include_once('../models/model.unidade.class.php');
include_once('../models/model.pessoa.class.php');
include_once('../models/model.usuario.class.php');

// Campos da unidade
$nomeUnidade = trim($_POST['unidade_nome'] ?? '');
$endereco    = trim($_POST['unidade_endereco'] ?? '');
$telefone    = trim($_POST['unidade_telefone'] ?? '');

// Campos do admin da unidade
$adminNome = trim($_POST['admin_nome'] ?? '');
$adminEmail = trim($_POST['admin_email'] ?? '');
$adminSenha = $_POST['admin_senha'] ?? '';
$adminDataNascimento = $_POST['admin_data_nascimento'] ?? '';

if ($nomeUnidade === '' || $adminNome === '' || $adminEmail === '' || $adminSenha === '' || $adminDataNascimento === '') {
    header("Location: ../views/unidade/view.unidade.create.php?erro=campos");
    exit;
}

if (strlen($adminSenha) < 6) {
    header("Location: ../views/unidade/view.unidade.create.php?erro=senha");
    exit;
}

$bd = new persistirBD();
$bd->conectar();
$bd->iniciarTransacao();

try {

    $novaUnidade = new unidade($nomeUnidade, $endereco, $telefone);
    $idUnidade = $novaUnidade->cadastrarUnidade($bd);

    $pessoaAdmin = new pessoa($adminNome, 'OUTRO', $adminDataNascimento, 'Administrador', '', $adminEmail);
    $idPessoa = $pessoaAdmin->cadastrarPessoaTx($bd);

    $novoUsuario = new usuario();
    $novoUsuario->cadastrarUsuarioTx(
        $bd,
        $idPessoa,
        usuario::PERFIL_ADMIN,
        $idUnidade,
        $adminEmail,
        $adminSenha,
        'APROVADO' // o admin fundador da unidade já nasce aprovado
    );

    $bd->confirmarTransacao();

    header("Location: ../views/auth/view.login.php?unidade_criada=1");

} catch (\Throwable $e) {
    $bd->desfazerTransacao();
    error_log("Erro ao cadastrar unidade/admin: " . $e->getMessage());
    header("Location: ../views/unidade/view.unidade.create.php?erro=geral");
} finally {
    $bd->desconectar();
}

exit;
