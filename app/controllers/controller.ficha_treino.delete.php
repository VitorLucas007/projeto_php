<?php
session_start();

if (!isset($_SESSION['id']) || !in_array($_SESSION['fk_perfil'], [1, 2])) {
    header("Location: ../views/auth/view.login.php");
    exit;
}

include_once('../models/model.persistirBD.class.php');

$id_ficha = (int) ($_GET['id_ficha'] ?? 0);
$id_aluno = (int) ($_GET['id_aluno'] ?? 0);

if ($id_ficha > 0) {
    $bd = new persistirBD();
    $bd->conectar();
    $bd->iniciarTransacao();

    try {
        // Exclui os filhos primeiro
        $bd->persistirPreparado("DELETE FROM exercicio_treino WHERE fk_ficha = ?", "i", [$id_ficha]);
        
        // Exclui a ficha usando a coluna correta
        $bd->persistirPreparado("DELETE FROM ficha_treino WHERE id_ficha = ?", "i", [$id_ficha]);
        
        $bd->confirmarTransacao();
    } catch (\Throwable $e) {
        $bd->desfazerTransacao();
        error_log("Erro ao excluir ficha: " . $e->getMessage());
    } finally {
        $bd->desconectar();
    }
}

// Redireciona de volta para a visualização, recarregando a página
header("Location: ../views/treino/view.ficha.treino.php?id=$id_aluno");
exit;