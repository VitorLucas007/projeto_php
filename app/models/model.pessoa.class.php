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

    /**
     * Insere a pessoa usando a conexão/transação já aberta em $bd
     * (permite compor com usuario/professor/aluno na mesma transação).
     * Retorna o id_pessoa gerado.
     */
    function cadastrarPessoaTx(persistirBD $bd)
    {
        $sql = "
        INSERT INTO pessoa
            (nome, sexo, data_nascimento, profissao, contato, email)
        VALUES
            (?, ?, ?, ?, ?, ?)
        ";

        $bd->persistirPreparado($sql, "ssssss", [
            $this->nome,
            $this->sexo,
            $this->data_nascimento,
            $this->profissao,
            $this->contato,
            $this->email
        ]);

        return $bd->ultimoId();
    }

    function cadastrarPessoa()
    {
        $bd = new persistirBD();
        $bd->conectar();
        $id = $this->cadastrarPessoaTx($bd);
        $bd->desconectar();
        return $id;
    }

    function listarPessoas()
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "SELECT * FROM pessoa ORDER BY nome";
        $bd->persistir($sql);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();
        return $dados;
    }

    function buscarPessoa($id)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "SELECT * FROM pessoa WHERE id_pessoa = ?";
        $bd->persistirPreparado($sql, "i", [$id]);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();
        return $dados;
    }

    function atualizarPessoa($id)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "
        UPDATE pessoa
        SET nome = ?, sexo = ?, data_nascimento = ?, profissao = ?, contato = ?, email = ?
        WHERE id_pessoa = ?
        ";

        $bd->persistirPreparado($sql, "ssssssi", [
            $this->nome,
            $this->sexo,
            $this->data_nascimento,
            $this->profissao,
            $this->contato,
            $this->email,
            $id
        ]);

        $bd->desconectar();
    }

    function excluirPessoa($id)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "DELETE FROM pessoa WHERE id_pessoa = ?";
        $bd->persistirPreparado($sql, "i", [$id]);

        $bd->desconectar();
    }
}
