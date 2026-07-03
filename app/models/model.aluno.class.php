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

    function cadastrarAluno()
    {
        $bd = new persistirBD();
        $bd->conectar();
        $bd->iniciarTransacao();

        try {
            $id = $this->cadastrarAlunoTx($bd);
            $bd->confirmarTransacao();
        } catch (\Throwable $e) {
            $bd->desfazerTransacao();
            throw $e;
        } finally {
            $bd->desconectar();
        }

        return $id;
    }

    function listarAlunos()
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "
        SELECT a.id_aluno, p.nome, a.observacoes
        FROM aluno a
        INNER JOIN pessoa p ON p.id_pessoa = a.fk_pessoa
        ORDER BY p.nome
        ";

        $bd->persistir($sql);
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

    function listarPessoasDisponiveis()
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "
        SELECT id_pessoa, nome FROM pessoa
        WHERE id_pessoa NOT IN (SELECT fk_pessoa FROM aluno)
        ORDER BY nome
        ";

        $bd->persistir($sql);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();
        return $dados;
    }
}
