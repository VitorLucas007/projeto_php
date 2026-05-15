<?php

include_once('model.persistirBD.class.php');

class usuario
{

    public $id;
    public $nome;
    public $email;
    public $senha;

    function __construct($vnome, $vemail, $vsenha)
    {
        $this->nome = $vnome;
        $this->email = $vemail;
        $this->senha = $vsenha;
    }

    function persistirUsuario()
    {

        $senhaHash = password_hash($this->senha, PASSWORD_DEFAULT);

        $bdusuario = new persistirBD("127.0.0.1", "root", "", "projeto_php");
        $bdusuario->conectar();

        $sql = "INSERT INTO users(nome,email,senha)
        VALUES(
        '" . $this->nome . "',
        '" . $this->email . "',
        '" . $senhaHash . "')";

        $bdusuario->persistir($sql);
        $bdusuario->desconectar();
    }

    function loginUsuario()
    {

        $bdusuario = new persistirBD("127.0.0.1", "root", "", "projeto_php");
        $bdusuario->conectar();

        $sql = "SELECT * FROM users WHERE email='" . $this->email . "'";

        $bdusuario->persistir($sql);

        $dados = $bdusuario->retornoConsultas();

        $bdusuario->desconectar();

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

    function listarUsuarios()
    {

        $bdusuario = new persistirBD("127.0.0.1", "root", "", "projeto_php");
        $bdusuario->conectar();

        $sql = "SELECT * FROM users";

        $bdusuario->persistir($sql);

        $dados = $bdusuario->retornoConsultas();

        $bdusuario->desconectar();

        return $dados;
    }

    function buscarUsuario($id)
    {

        $bdusuario = new persistirBD("127.0.0.1", "root", "", "projeto_php");
        $bdusuario->conectar();

        $sql = "SELECT * FROM users WHERE id=" . $id;

        $bdusuario->persistir($sql);

        $dados = $bdusuario->retornoConsultas();

        $bdusuario->desconectar();

        return $dados;
    }

    function atualizarUsuario($id, $nome, $email)
    {

        $bdusuario = new persistirBD("127.0.0.1", "root", "", "projeto_php");
        $bdusuario->conectar();

        $sql = "UPDATE users
        SET nome='" . $nome . "', email='" . $email . "'
        WHERE id=" . $id;

        $bdusuario->persistir($sql);

        $bdusuario->desconectar();
    }

    function excluirUsuario($id)
    {

        $bdusuario = new persistirBD("127.0.0.1", "root", "", "projeto_php");
        $bdusuario->conectar();

        $sql = "DELETE FROM users WHERE id=" . $id;

        $bdusuario->persistir($sql);

        $bdusuario->desconectar();
    }
}
