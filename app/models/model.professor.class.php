<?php

include_once('model.persistirBD.class.php');

class professor
{
    public $fk_pessoa;
    public $cref;
    public $especialidade;

    function __construct($fk_pessoa, $cref, $especialidade)
    {
        $this->fk_pessoa = $fk_pessoa;
        $this->cref = $cref;
        $this->especialidade = $especialidade;
    }

    function cadastrarProfessorTx(persistirBD $bd)
    {
        $sql = "INSERT INTO professor (fk_pessoa, cref, especialidade) VALUES (?, ?, ?)";
        $bd->persistirPreparado($sql, "iss", [$this->fk_pessoa, $this->cref, $this->especialidade]);
        return $bd->ultimoId();
    }

    function cadastrarProfessor()
    {
        $bd = new persistirBD();
        $bd->conectar();
        $id = $this->cadastrarProfessorTx($bd);
        $bd->desconectar();
        return $id;
    }

    function listarProfessores($fk_unidade)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "
        SELECT pr.id_professor, pe.nome, pr.cref, pr.especialidade
        FROM professor pr
        INNER JOIN pessoa pe ON pe.id_pessoa = pr.fk_pessoa
        INNER JOIN usuario u ON u.fk_pessoa = pe.id_pessoa
        WHERE u.status_aprovacao = 'APROVADO' AND u.fk_unidade = ?
        ORDER BY pe.nome
        ";

        $bd->persistirPreparado($sql, "i", [$fk_unidade]);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();
        return $dados;
    }

    /**
     * Busca o id_professor vinculado a uma pessoa (ex: professor logado na sessão).
     */
    static function buscarPorPessoa($fk_pessoa)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "SELECT id_professor FROM professor WHERE fk_pessoa = ?";
        $bd->persistirPreparado($sql, "i", [$fk_pessoa]);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        return isset($dados[0][0]) ? (int) $dados[0][0] : null;
    }

    /**
     * Retorna o nome da pessoa vinculada a um professor, para exibição
     * somente-leitura (ex: professor responsável já fixado numa avaliação).
     */
    static function buscarNomePorId($id_professor)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "
        SELECT pe.nome
        FROM professor pr
        INNER JOIN pessoa pe ON pe.id_pessoa = pr.fk_pessoa
        WHERE pr.id_professor = ?
        ";
        $bd->persistirPreparado($sql, "i", [$id_professor]);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        return $dados[0][0] ?? null;
    }

    function buscarProfessor($id)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "SELECT * FROM professor WHERE id_professor = ?";
        $bd->persistirPreparado($sql, "i", [$id]);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();
        return $dados;
    }

    function atualizarProfessor($id)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "UPDATE professor SET fk_pessoa = ?, cref = ?, especialidade = ? WHERE id_professor = ?";
        $bd->persistirPreparado($sql, "issi", [$this->fk_pessoa, $this->cref, $this->especialidade, $id]);

        $bd->desconectar();
    }

    function excluirProfessor($id)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "DELETE FROM professor WHERE id_professor = ?";
        $bd->persistirPreparado($sql, "i", [$id]);

        $bd->desconectar();
    }

    function listarPessoasDisponiveis()
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "
        SELECT id_pessoa, nome FROM pessoa
        WHERE id_pessoa NOT IN (SELECT fk_pessoa FROM professor)
        ORDER BY nome
        ";

        $bd->persistir($sql);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();
        return $dados;
    }
}
