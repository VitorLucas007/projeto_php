<?php

include_once('model.persistirBD.class.php');

class usuario
{
    public $login;
    public $senha;

    // Perfis fixos, carregados pelo script SQL
    const PERFIL_ADMIN = 1;
    const PERFIL_PROFESSOR = 2;
    const PERFIL_PESSOA = 3; // usado aqui como "ALUNO"

    function __construct($login = null, $senha = null)
    {
        $this->login = $login;
        $this->senha = $senha;
    }

    function loginUsuario()
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "
        SELECT
            u.id_usuario,
            p.nome,
            p.email,
            u.senha_hash,
            u.fk_perfil,
            u.fk_unidade,
            u.status_aprovacao
        FROM usuario u
        INNER JOIN pessoa p ON p.id_pessoa = u.fk_pessoa
        WHERE p.email = ?
        AND u.ativo = 1
        ";

        $bd->persistirPreparado($sql, "s", [$this->login]);

        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        if (!isset($dados[0])) {
            return "NAO_ENCONTRADO";
        }

        if (!password_verify($this->senha, $dados[0][3])) {
            return "SENHA_INCORRETA";
        }

        if ($dados[0][6] !== 'APROVADO') {
            return "PENDENTE";
        }

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['id'] = $dados[0][0];
        $_SESSION['nome'] = $dados[0][1];
        $_SESSION['email'] = $dados[0][2];
        $_SESSION['fk_perfil'] = (int) $dados[0][4];
        $_SESSION['fk_unidade'] = (int) $dados[0][5];

        return "OK";
    }

    /**
     * Cria o usuário dentro de uma transação já aberta em $bd, vinculado
     * a uma pessoa que já foi inserida na mesma transação.
     * O hash da senha é gerado aqui dentro, nunca fora do model.
     */
    function cadastrarUsuarioTx(persistirBD $bd, $fk_pessoa, $fk_perfil, $fk_unidade, $login, $senhaPlana, $statusAprovacao)
    {
        $hash = password_hash($senhaPlana, PASSWORD_DEFAULT);
        $ativo = ($statusAprovacao === 'APROVADO') ? 1 : 0;

        $sql = "
        INSERT INTO usuario
            (fk_pessoa, fk_perfil, fk_unidade, login, senha_hash, status_aprovacao, ativo)
        VALUES
            (?, ?, ?, ?, ?, ?, ?)
        ";

        $bd->persistirPreparado($sql, "iiisssi", [
            $fk_pessoa,
            $fk_perfil,
            $fk_unidade,
            $login,
            $hash,
            $statusAprovacao,
            $ativo
        ]);

        return $bd->ultimoId();
    }

    /**
     * Lista usuários pendentes de aprovação de uma unidade específica.
     */
    static function listarPendentes($fk_unidade)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "
        SELECT
            u.id_usuario,
            p.nome,
            p.email,
            pf.nome_perfil,
            u.created_at
        FROM usuario u
        INNER JOIN pessoa p ON p.id_pessoa = u.fk_pessoa
        INNER JOIN perfil pf ON pf.id_perfil = u.fk_perfil
        WHERE u.status_aprovacao = 'PENDENTE'
        AND u.fk_unidade = ?
        ORDER BY u.created_at
        ";

        $bd->persistirPreparado($sql, "i", [$fk_unidade]);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();
        return $dados;
    }

    static function aprovar($id_usuario)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "UPDATE usuario SET status_aprovacao = 'APROVADO', ativo = 1 WHERE id_usuario = ?";
        $bd->persistirPreparado($sql, "i", [$id_usuario]);

        $bd->desconectar();
    }

    static function rejeitar($id_usuario)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "UPDATE usuario SET status_aprovacao = 'REJEITADO', ativo = 0 WHERE id_usuario = ?";
        $bd->persistirPreparado($sql, "i", [$id_usuario]);

        $bd->desconectar();
    }

    static function existeAdmin()
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "SELECT COUNT(*) FROM usuario WHERE fk_perfil = " . self::PERFIL_ADMIN;
        $bd->persistir($sql);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        return isset($dados[0][0]) && $dados[0][0] > 0;
    }
}
