<?php
session_start();

if (!isset($_SESSION['id']) || !in_array($_SESSION['fk_perfil'], [1, 2])) {
    header("Location: ../views/auth/view.login.php");
    exit;
}

include_once('../models/model.persistirBD.class.php');

$id_ficha = (int) (isset($_POST['id_ficha']) ? $_POST['id_ficha'] : 0);
$id_aluno = (int) (isset($_POST['id_aluno']) ? $_POST['id_aluno'] : 0);
$nome_treino = trim(isset($_POST['nome_treino']) ? $_POST['nome_treino'] : '');
$data_validade = !empty($_POST['data_validade']) ? $_POST['data_validade'] : null;
$observacoes = trim(isset($_POST['observacoes']) ? $_POST['observacoes'] : '');

$nomes_maquina = isset($_POST['exercicio']) ? $_POST['exercicio'] : [];
$series = isset($_POST['series']) ? $_POST['series'] : [];
$repeticoes = isset($_POST['repeticoes']) ? $_POST['repeticoes'] : [];
$cargas = isset($_POST['carga']) ? $_POST['carga'] : [];
$descansos = isset($_POST['descanso']) ? $_POST['descanso'] : [];

if ($id_ficha > 0 && $id_aluno > 0) {
    $bd = new persistirBD();
    $bd->conectar();
    $bd->iniciarTransacao();

    try {
        // Atualiza a tabela principal usando a coluna id_ficha
        $sqlFicha = "UPDATE ficha_treino SET nome_treino = ?, data_validade = ?, observacoes = ? WHERE id_ficha = ?";
        $bd->persistirPreparado($sqlFicha, "sssi", [$nome_treino, $data_validade, $observacoes, $id_ficha]);

        // Limpa os exercícios antigos
        $bd->persistirPreparado("DELETE FROM exercicio_treino WHERE fk_ficha = ?", "i", [$id_ficha]);

        // Insere os exercícios atualizados
        if (!empty($nomes_maquina)) {
            $sqlExercicio = "INSERT INTO exercicio_treino (fk_ficha, nome_maquina_exercicio, series, repeticoes, carga, tempo_descanso) VALUES (?, ?, ?, ?, ?, ?)";
            
            for ($i = 0; $i < count($nomes_maquina); $i++) {
                $nomeEx = trim($nomes_maquina[$i]);
                if ($nomeEx !== '') {
                    $serie = (int) (isset($series[$i]) ? $series[$i] : 0);
                    $rep = trim(isset($repeticoes[$i]) ? $repeticoes[$i] : '');
                    $carg = trim(isset($cargas[$i]) ? $cargas[$i] : '');
                    $descanso = trim(isset($descansos[$i]) ? $descansos[$i] : '');

                    $bd->persistirPreparado($sqlExercicio, "isisss", [$id_ficha, $nomeEx, $serie, $rep, $carg, $descanso]);
                }
            }
        }
        $bd->confirmarTransacao();
    } catch (\Throwable $e) {
        $bd->desfazerTransacao();
        error_log("Erro ao editar ficha: " . $e->getMessage());
    } finally {
        $bd->desconectar();
    }
}

header("Location: ../views/treino/view.ficha.treino.php?id=$id_aluno");
exit;