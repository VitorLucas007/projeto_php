<?php

include_once('model.persistirBD.class.php');

class unidade
{
    public $nome;
    public $endereco;
    public $telefone;

    function __construct($nome, $endereco, $telefone)
    {
        $this->nome = $nome;
        $this->endereco = $endereco;
        $this->telefone = $telefone;
    }

    /**
     * Insere a unidade usando a conexão/transação já aberta em $bd.
     * Retorna o id_unidade gerado.
     */
    function cadastrarUnidade(persistirBD $bd)
    {
        $sql = "
        INSERT INTO unidade (nome, endereco, telefone, status)
        VALUES (?, ?, ?, 1)
        ";

        $bd->persistirPreparado($sql, "sss", [
            $this->nome,
            $this->endereco,
            $this->telefone
        ]);

        return $bd->ultimoId();
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
