<?php

include_once('model.persistirBD.class.php');

class aluno
{
    public $fk_pessoa;
    public $observacoes;

    function __construct($fk_pessoa, $observacoes)
    {
        $this->fk_pessoa = $fk_pessoa;
        $this->observacoes = $observacoes;
    }

    /**
     * Cadastra aluno + abre o prontuário, dentro da transação recebida.
     */
    function cadastrarAlunoTx(persistirBD $bd)
    {
        $sql = "INSERT INTO aluno (fk_pessoa, observacoes) VALUES (?, ?)";
        $bd->persistirPreparado($sql, "is", [$this->fk_pessoa, $this->observacoes]);
        $idAluno = $bd->ultimoId();

        $sql = "INSERT INTO prontuario (fk_aluno, data_abertura, observacoes_gerais) VALUES (?, CURDATE(), '')";
        $bd->persistirPreparado($sql, "i", [$idAluno]);

        return $idAluno;
    }

    function listarAlunos($fk_unidade)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "
        SELECT a.id_aluno, p.nome, a.observacoes
        FROM aluno a
        INNER JOIN pessoa p ON p.id_pessoa = a.fk_pessoa
        INNER JOIN usuario u ON u.fk_pessoa = p.id_pessoa
        WHERE u.status_aprovacao = 'APROVADO' AND u.fk_unidade = ?
        ORDER BY p.nome
        ";

        $bd->persistirPreparado($sql, "i", [$fk_unidade]);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();
        return $dados;
    }

    function buscarAluno($id)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "SELECT * FROM aluno WHERE id_aluno = ?";
        $bd->persistirPreparado($sql, "i", [$id]);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();
        return $dados;
    }

    function atualizarAluno($id)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "UPDATE aluno SET fk_pessoa = ?, observacoes = ? WHERE id_aluno = ?";
        $bd->persistirPreparado($sql, "isi", [$this->fk_pessoa, $this->observacoes, $id]);

        $bd->desconectar();
    }

    function excluirAluno($id)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "DELETE FROM prontuario WHERE fk_aluno = ?";
        $bd->persistirPreparado($sql, "i", [$id]);

        $sql = "DELETE FROM aluno WHERE id_aluno = ?";
        $bd->persistirPreparado($sql, "i", [$id]);

        $bd->desconectar();
    }
}
