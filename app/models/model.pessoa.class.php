<?php

include_once('model.persistirBD.class.php');

class pessoa
{
    public $nome;
    public $sexo;
    public $data_nascimento;
    public $profissao;
    public $contato;
    public $email;

    function __construct(
        $nome,
        $sexo,
        $data_nascimento,
        $profissao,
        $contato,
        $email
    ) {
        $this->nome = $nome;
        $this->sexo = $sexo;
        $this->data_nascimento = $data_nascimento;
        $this->profissao = $profissao;
        $this->contato = $contato;
        $this->email = $email;
    }

    function cadastrarPessoa()
    {
        $bd = new persistirBD(
            "127.0.0.1",
            "root",
            "",
            "projeto_pibeu"
        );

        $bd->conectar();

        $sql = "
        INSERT INTO pessoa
        (
            nome,
            sexo,
            data_nascimento,
            profissao,
            contato,
            email
        )
        VALUES
        (
            '$this->nome',
            '$this->sexo',
            '$this->data_nascimento',
            '$this->profissao',
            '$this->contato',
            '$this->email'
        )";

        $bd->persistir($sql);

        $bd->desconectar();
    }

    function listarPessoas()
    {
        $bd = new persistirBD(
            "127.0.0.1",
            "root",
            "",
            "projeto_pibeu"
        );

        $bd->conectar();

        $sql = "SELECT * FROM pessoa ORDER BY nome";

        $bd->persistir($sql);

        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        return $dados;
    }

    function buscarPessoa($id)
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
        FROM pessoa
        WHERE id_pessoa = $id
        ";

        $bd->persistir($sql);

        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        return $dados;
    }

    function atualizarPessoa($id)
    {
        $bd = new persistirBD(
            "127.0.0.1",
            "root",
            "",
            "projeto_pibeu"
        );

        $bd->conectar();

        $sql = "
        UPDATE pessoa
        SET
            nome = '$this->nome',
            sexo = '$this->sexo',
            data_nascimento = '$this->data_nascimento',
            profissao = '$this->profissao',
            contato = '$this->contato',
            email = '$this->email'
        WHERE id_pessoa = $id
        ";

        $bd->persistir($sql);

        $bd->desconectar();
    }

    function excluirPessoa($id)
    {
        $bd = new persistirBD(
            "127.0.0.1",
            "root",
            "",
            "projeto_pibeu"
        );

        $bd->conectar();

        $sql = "
        DELETE FROM pessoa
        WHERE id_pessoa = $id
        ";

        $bd->persistir($sql);

        $bd->desconectar();
    }
}