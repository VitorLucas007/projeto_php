<?php

include_once('../models/model.persistirBD.class.php');
include_once('../models/model.validacoes.class.php');
include_once('../models/model.unidade.class.php');
include_once('../models/model.pessoa.class.php');
include_once('../models/model.usuario.class.php');

// Campos da unidade
$nomeUnidade = trim($_POST['unidade_nome'] ?? '');
$cnpj        = trim($_POST['unidade_cnpj'] ?? '');
$endereco    = trim($_POST['unidade_endereco'] ?? '');
$telefone    = trim($_POST['unidade_telefone'] ?? '');

// Campos do admin da unidade
$adminNome = trim($_POST['admin_nome'] ?? '');
$adminEmail = trim($_POST['admin_email'] ?? '');
$adminSenha = $_POST['admin_senha'] ?? '';
$adminSenhaConfirma = $_POST['admin_senha_confirma'] ?? '';
$adminDataNascimento = $_POST['admin_data_nascimento'] ?? '';

if ($nomeUnidade === '' || $cnpj === '' || $adminNome === '' || $adminEmail === '' || $adminSenha === '' || $adminDataNascimento === '') {
    header("Location: ../views/unidade/view.unidade.create.php?erro=campos");
    exit;
}

if (!validacoes::validar_cnpj($cnpj)) {
    header("Location: ../views/unidade/view.unidade.create.php?erro=cnpj");
    exit;
}

$cnpjLimpo = validacoes::remover_formatacao_cnpj($cnpj);

if (unidade::existeCnpj($cnpjLimpo)) {
    header("Location: ../views/unidade/view.unidade.create.php?erro=cnpj_existe");
    exit;
}

if ($adminSenha !== $adminSenhaConfirma) {
    header("Location: ../views/unidade/view.unidade.create.php?erro=senha");
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

    $novaUnidade = new unidade($nomeUnidade, $endereco, $telefone, $cnpjLimpo);
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
        'PENDENTE' // o admin fundador da unidade agora depende de aprovação do Root
    );

    $bd->confirmarTransacao();

    header("Location: ../views/auth/view.login.php?unidade_pendente=1");

} catch (\Throwable $e) {
    $bd->desfazerTransacao();
    error_log("Erro ao cadastrar unidade/admin: " . $e->getMessage());
    header("Location: ../views/unidade/view.unidade.create.php?erro=geral");
} finally {
    $bd->desconectar();
}

exit;