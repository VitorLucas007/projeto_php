<?php

include_once('model.persistirBD.class.php');

class usuario
{
    public $login;
    public $senha;

    function __construct($login, $senha)
    {
        $this->login = $login;
        $this->senha = $senha;
    }

    function loginUsuario()
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
            u.id_usuario,
            p.nome,
            p.email,
            u.senha_hash
        FROM usuario u
        INNER JOIN pessoa p
            ON p.id_pessoa = u.fk_pessoa
        WHERE p.email = '$this->login'
        AND u.ativo = 1
        ";

        $bd->persistir($sql);

        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        if (isset($dados[0])) {

            if (password_verify($this->senha, $dados[0][3])) {

                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }

                $_SESSION['id'] = $dados[0][0];
                $_SESSION['nome'] = $dados[0][1];
                $_SESSION['email'] = $dados[0][2];

                return true;
            }
        }

        return false;
    }
}