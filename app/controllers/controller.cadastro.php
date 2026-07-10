<?php

include_once('../models/model.persistirBD.class.php');
include_once('../models/model.pessoa.class.php');
include_once('../models/model.usuario.class.php');
include_once('../models/model.professor.class.php');
include_once('../models/model.aluno.class.php');

$nome = trim($_POST['cad_nome'] ?? '');
$email = trim($_POST['cad_email'] ?? '');
$senha = $_POST['cad_senha'] ?? '';
$sexo = $_POST['cad_sexo'] ?? '';
$dataNascimento = $_POST['cad_data_nascimento'] ?? '';
$contato = trim($_POST['cad_contato'] ?? '');
$fkUnidade = (int) ($_POST['cad_unidade'] ?? 0);
$tipoPerfil = $_POST['cad_tipo'] ?? ''; // "PROFESSOR" ou "ALUNO"

// Campos específicos
$cref = trim($_POST['cad_cref'] ?? '');
$especialidade = trim($_POST['cad_especialidade'] ?? '');
$observacoes = trim($_POST['cad_observacoes'] ?? '');

if ($nome === '' || $email === '' || $senha === '' || $sexo === '' || $dataNascimento === '' || $fkUnidade <= 0) {
    header("Location: ../views/auth/view.cadastro.php?erro=campos");
    exit;
}

if (strlen($senha) < 6) {
    header("Location: ../views/auth/view.cadastro.php?erro=senha");
    exit;
}

if (!in_array($tipoPerfil, ['PROFESSOR', 'ALUNO'], true)) {
    header("Location: ../views/auth/view.cadastro.php?erro=tipo");
    exit;
}

$fkPerfil = ($tipoPerfil === 'PROFESSOR') ? usuario::PERFIL_PROFESSOR : usuario::PERFIL_PESSOA;

$bd = new persistirBD();
$bd->conectar();
$bd->iniciarTransacao();

try {

    $novaPessoa = new pessoa($nome, $sexo, $dataNascimento, '', $contato, $email);
    $idPessoa = $novaPessoa->cadastrarPessoaTx($bd);

    $novoUsuario = new usuario();
    $novoUsuario->cadastrarUsuarioTx(
        $bd,
        $idPessoa,
        $fkPerfil,
        $fkUnidade,
        $email,
        $senha,
        'PENDENTE' // só entra depois que o admin da unidade aprovar
    );

    if ($tipoPerfil === 'PROFESSOR') {
        $novoProfessor = new professor($idPessoa, $cref, $especialidade);
        $novoProfessor->cadastrarProfessorTx($bd);
    } else {
        $novoAluno = new aluno($idPessoa, $observacoes);
        $novoAluno->cadastrarAlunoTx($bd);
    }

    $bd->confirmarTransacao();

    header("Location: ../views/auth/view.login.php?cadastro=pendente");

} catch (\Throwable $e) {
    $bd->desfazerTransacao();
    error_log("Erro ao cadastrar usuário: " . $e->getMessage());
    header("Location: ../views/auth/view.cadastro.php?erro=geral");
} finally {
    $bd->desconectar();
}

exit;