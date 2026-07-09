<?php

include_once('model.persistirBD.class.php');

class unidade
{
    public $nome;
    public $cnpj;
    public $endereco;
    public $telefone;

    function __construct($nome, $endereco, $telefone, $cnpj = null)
    {
        $this->nome = $nome;
        $this->endereco = $endereco;
        $this->telefone = $telefone;
        $this->cnpj = $cnpj;
    }

    /**
     * Insere a unidade usando a conexão/transação já aberta em $bd.
     * Retorna o id_unidade gerado.
     */
    function cadastrarUnidade(persistirBD $bd)
    {
        $sql = "
        INSERT INTO unidade (nome, cnpj, endereco, telefone, status)
        VALUES (?, ?, ?, ?, 1)
        ";

        $bd->persistirPreparado($sql, "ssss", [
            $this->nome,
            $this->cnpj,
            $this->endereco,
            $this->telefone
        ]);

        return $bd->ultimoId();
    }

    /**
     * Busca uma unidade ativa pelo CNPJ (sem formatação).
     * Retorna a linha [id_unidade, nome, cnpj] ou array vazio se não encontrada.
     */
    static function buscarPorCnpj($cnpj)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "SELECT id_unidade, nome, cnpj FROM unidade WHERE cnpj = ? AND status = 1";
        $bd->persistirPreparado($sql, "s", [$cnpj]);

        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        return $dados;
    }

    /**
     * Verifica se já existe unidade cadastrada com o CNPJ informado.
     */
    static function existeCnpj($cnpj)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "SELECT id_unidade FROM unidade WHERE cnpj = ?";
        $bd->persistirPreparado($sql, "s", [$cnpj]);

        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        return isset($dados[0]);
    }

    static function listarUnidadesAtivas()
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "SELECT id_unidade, nome FROM unidade WHERE status = 1 ORDER BY nome";

        $bd->persistir($sql);

        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        return $dados;
    }
}
