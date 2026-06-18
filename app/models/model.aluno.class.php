<?php

include_once('model.persistirBD.class.php');

class aluno
{
    public $fk_pessoa;
    public $observacoes;

    function __construct(
        $fk_pessoa,
        $observacoes
    ){
        $this->fk_pessoa = $fk_pessoa;
        $this->observacoes = $observacoes;
    }

    function cadastrarAluno()
    {
        $bd = new persistirBD(
            "127.0.0.1",
            "root",
            "",
            "projeto_pibeu"
        );

        $bd->conectar();

        $sql = "
        INSERT INTO aluno
        (
            fk_pessoa,
            observacoes
        )
        VALUES
        (
            '$this->fk_pessoa',
            '$this->observacoes'
        )
        ";

        $bd->persistir($sql);

        $sql = "SELECT MAX(id_aluno) FROM aluno";

        $bd->persistir($sql);

        $dados = $bd->retornoConsultas();

        $idAluno = $dados[0][0];

        $sql = "
        INSERT INTO prontuario
        (
            fk_aluno,
            data_abertura,
            observacoes_gerais
        )
        VALUES
        (
            '$idAluno',
            CURDATE(),
            ''
        )
        ";

        $bd->persistir($sql);

        $bd->desconectar();
    }

    function listarAlunos()
    {
        $bd = new persistirBD(
            "127.0.0.1",
            "root",
            "",
            "projeto_pibeu"
        );

        $bd->conectar();

        $sql = "
        SELECT
            a.id_aluno,
            p.nome,
            a.observacoes
        FROM aluno a
        INNER JOIN pessoa p
            ON p.id_pessoa = a.fk_pessoa
        ORDER BY p.nome
        ";

        $bd->persistir($sql);

        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        return $dados;
    }

    function buscarAluno($id)
    {
        $bd = new persistirBD(
            "127.0.0.1",
            "root",
            "",
            "projeto_pibeu"
        );

        $bd->conectar();

        $sql = "
        SELECT *
        FROM aluno
        WHERE id_aluno = $id
        ";

        $bd->persistir($sql);

        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        return $dados;
    }

    function atualizarAluno($id)
    {
        $bd = new persistirBD(
            "127.0.0.1",
            "root",
            "",
            "projeto_pibeu"
        );

        $bd->conectar();

        $sql = "
        UPDATE aluno
        SET
            fk_pessoa = '$this->fk_pessoa',
            observacoes = '$this->observacoes'
        WHERE id_aluno = $id
        ";

        $bd->persistir($sql);

        $bd->desconectar();
    }

    function excluirAluno($id)
    {
        $bd = new persistirBD(
            "127.0.0.1",
            "root",
            "",
            "projeto_pibeu"
        );

        $bd->conectar();

        $sql = "
        DELETE FROM prontuario
        WHERE fk_aluno = $id
        ";

        $bd->persistir($sql);

        $sql = "
        DELETE FROM aluno
        WHERE id_aluno = $id
        ";

        $bd->persistir($sql);

        $bd->desconectar();
    }

    function listarPessoasDisponiveis()
    {
        $bd = new persistirBD(
            "127.0.0.1",
            "root",
            "",
            "projeto_pibeu"
        );

        $bd->conectar();

        $sql = "
        SELECT
            id_pessoa,
            nome
        FROM pessoa
        WHERE id_pessoa NOT IN
        (
            SELECT fk_pessoa
            FROM aluno
        )
        ORDER BY nome
        ";

        $bd->persistir($sql);

        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        return $dados;
    }
}