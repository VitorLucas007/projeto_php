<?php
session_start();

// CORREÇÃO: Usando $_SESSION['id'] em vez de id_usuario para bater com o seu sistema
if (!isset($_SESSION['id']) || !isset($_SESSION['fk_perfil']) || !in_array($_SESSION['fk_perfil'], [1, 2])) {
    header("Location: ../views/auth/view.login.php");
    exit;
}

include_once('../models/model.persistirBD.class.php');
include_once('../models/model.professor.class.php');

// Resgata o id_professor vinculado à pessoa logada (usando o método do seu model)
// Confirme se a sua sessão de login salva o 'fk_pessoa'. Se salvar com outro nome, altere aqui.
$fk_pessoa_logada = $_SESSION['fk_pessoa'] ?? 0;
$fk_professor = professor::buscarPorPessoa($fk_pessoa_logada);

if (!$fk_professor) {
    die("Erro: Você não possui um perfil de professor ativo para criar treinos. Fk_pessoa: " . $fk_pessoa_logada);
}

// Dados do cabeçalho da Ficha
$fk_aluno = (int) ($_POST['id_aluno'] ?? 0);
$nome_treino = trim($_POST['nome_treino'] ?? '');
$data_validade = !empty($_POST['data_validade']) ? $_POST['data_validade'] : null;
$observacoes = trim($_POST['observacoes'] ?? '');
$data_criacao = date('Y-m-d');

if ($fk_aluno === 0 || $nome_treino === '') {
    header("Location: ../views/aluno/view.ficha_treino.create.php?id_aluno=$fk_aluno&erro=campos");
    exit;
}

// Arrays dos exercícios preenchidos no formulário
$nomes_maquina = $_POST['exercicio'] ?? [];
$series = $_POST['series'] ?? [];
$repeticoes = $_POST['repeticoes'] ?? [];
$cargas = $_POST['carga'] ?? [];
$descansos = $_POST['descanso'] ?? [];

$bd = new persistirBD();
$bd->conectar();
$bd->iniciarTransacao();

try {
    // 1. Grava o cabeçalho do treino na tabela ficha_treino
    $sqlFicha = "INSERT INTO ficha_treino (fk_aluno, fk_professor, nome_treino, data_criacao, data_validade, observacoes) VALUES (?, ?, ?, ?, ?, ?)";
    $bd->persistirPreparado($sqlFicha, "iissss", [$fk_aluno, $fk_professor, $nome_treino, $data_criacao, $data_validade, $observacoes]);
    
    $id_ficha = $bd->ultimoId();

    // 2. Faz um loop para gravar todos os exercícios
    if (!empty($nomes_maquina)) {
        $sqlExercicio = "INSERT INTO exercicio_treino (fk_ficha, nome_maquina_exercicio, series, repeticoes, carga, tempo_descanso) VALUES (?, ?, ?, ?, ?, ?)";
        
        for ($i = 0; $i < count($nomes_maquina); $i++) {
            $nomeEx = trim($nomes_maquina[$i]);
            
            if ($nomeEx !== '') {
                $serie = (int) ($series[$i] ?? 0);
                $rep = trim($repeticoes[$i] ?? '');
                $carg = trim($cargas[$i] ?? '');
                $descanso = trim($descansos[$i] ?? '');

                $bd->persistirPreparado($sqlExercicio, "isisss", [$id_ficha, $nomeEx, $serie, $rep, $carg, $descanso]);
            }
        }
    }

    $bd->confirmarTransacao();
    
    // Redireciona de volta para a ficha recém-criada (ajustado para a pasta "aluno")
    header("Location: ../views/aluno/view.ficha_treino.php?id=$fk_aluno&sucesso=1");

} catch (\Throwable $e) {
    $bd->desfazerTransacao();
    error_log("Erro ao salvar ficha de treino: " . $e->getMessage());
    header("Location: ../views/aluno/view.ficha_treino.create.php?id_aluno=$fk_aluno&erro=geral");
} finally {
    $bd->desconectar();
}
exit;