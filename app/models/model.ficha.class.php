<?php
include_once('model.persistirBD.class.php');

class ficha
{
    // Busca a ficha mais recente de um aluno específico
    static function buscarFichaPorAluno($fk_aluno)
    {
        $bd = new persistirBD();
        $bd->conectar();
        
        $sql = "SELECT * FROM ficha_treino WHERE fk_aluno = ? ORDER BY data_criacao DESC LIMIT 1";
        $bd->persistirPreparado($sql, "i", [$fk_aluno]);
        $dados = $bd->retornoConsultas();
        
        $bd->desconectar();
        
        // Retorna a primeira linha encontrada ou nulo se não houver
        return isset($dados[0]) ? $dados[0] : null; 
    }

    // Busca todos os exercícios vinculados a uma ficha
    static function buscarExerciciosDaFicha($fk_ficha)
    {
        $bd = new persistirBD();
        $bd->conectar();
        
        $sql = "SELECT * FROM exercicio_treino WHERE fk_ficha = ?";
        $bd->persistirPreparado($sql, "i", [$fk_ficha]);
        $dados = $bd->retornoConsultas();
        
        $bd->desconectar();
        return $dados;
    }
    
    public static function listarFichasPorAluno($id_aluno) {
    $bd = new persistirBD();
    $bd->conectar();
    
    $sql = "SELECT * FROM ficha_treino WHERE fk_aluno = ?";
    $bd->persistirPreparado($sql, "i", [$id_aluno]);
    $todasAsFichas = $bd->retornoConsultas();
    
    $bd->desconectar();
    return $todasAsFichas ?: [];
}
}
?>