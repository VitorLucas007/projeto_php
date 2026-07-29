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
    public $cpf;

    function __construct(
        $nome,
        $sexo,
        $data_nascimento,
        $profissao,
        $contato,
        $email,
        $cpf = null
    ) {
        $this->nome = $nome;
        $this->sexo = $sexo;
        $this->data_nascimento = $data_nascimento;
        $this->profissao = $profissao;
        $this->contato = $contato;
        $this->email = $email;
        $this->cpf = $cpf;
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
            (nome, sexo, data_nascimento, profissao, contato, email, cpf)
        VALUES
            (?, ?, ?, ?, ?, ?, ?)
        ";

        $bd->persistirPreparado($sql, "sssssss", [
            $this->nome,
            $this->sexo,
            $this->data_nascimento,
            $this->profissao,
            $this->contato,
            $this->email,
            $this->cpf
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

    /**
     * Verifica se já existe pessoa cadastrada com o CPF informado.
     */
    static function existeCpf($cpf)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "SELECT id_pessoa FROM pessoa WHERE cpf = ?";
        $bd->persistirPreparado($sql, "s", [$cpf]);

        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        return isset($dados[0]);
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
