<?php

include_once('model.persistirBD.class.php');

class professor
{
    public $fk_pessoa;
    public $cref;
    public $especialidade;

    function __construct(
        $fk_pessoa,
        $cref,
        $especialidade
    ){
        $this->fk_pessoa = $fk_pessoa;
        $this->cref = $cref;
        $this->especialidade = $especialidade;
    }

    function cadastrarProfessor()
    {
        $bd = new persistirBD(
            "127.0.0.1",
            "root",
            "",
            "projeto_pibeu"
        );

        $bd->conectar();

        $sql = "
        INSERT INTO professor
        (
            fk_pessoa,
            cref,
            especialidade
        )
        VALUES
        (
            '$this->fk_pessoa',
            '$this->cref',
            '$this->especialidade'
        )";

        $bd->persistir($sql);

        $bd->desconectar();
    }

    function listarProfessores()
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
            pr.id_professor,
            pe.nome,
            pr.cref,
            pr.especialidade
        FROM professor pr
        INNER JOIN pessoa pe
            ON pe.id_pessoa = pr.fk_pessoa
        ORDER BY pe.nome
        ";

        $bd->persistir($sql);

        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        return $dados;
    }

    function buscarProfessor($id)
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
        FROM professor
        WHERE id_professor = $id
        ";

        $bd->persistir($sql);

        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        return $dados;
    }

    function atualizarProfessor($id)
    {
        $bd = new persistirBD(
            "127.0.0.1",
            "root",
            "",
            "projeto_pibeu"
        );

        $bd->conectar();

        $sql = "
        UPDATE professor
        SET
            fk_pessoa = '$this->fk_pessoa',
            cref = '$this->cref',
            especialidade = '$this->especialidade'
        WHERE id_professor = $id
        ";

        $bd->persistir($sql);

        $bd->desconectar();
    }

    function excluirProfessor($id)
    {
        $bd = new persistirBD(
            "127.0.0.1",
            "root",
            "",
            "projeto_pibeu"
        );

        $bd->conectar();

        $sql = "
        DELETE FROM professor
        WHERE id_professor = $id
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
            FROM professor
        )
        ORDER BY nome
        ";

        $bd->persistir($sql);

        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        return $dados;
    }
}