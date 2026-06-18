<?php

include_once('model.persistirBD.class.php');

class avaliacao
{
    public $fk_prontuario;
    public $fk_professor;
    public $data_avaliacao;
    public $frequencia_cardiaca;
    public $pressao_arterial;

    function __construct(
        $fk_prontuario,
        $fk_professor,
        $data_avaliacao,
        $frequencia_cardiaca,
        $pressao_arterial
    ){
        $this->fk_prontuario = $fk_prontuario;
        $this->fk_professor = $fk_professor;
        $this->data_avaliacao = $data_avaliacao;
        $this->frequencia_cardiaca = $frequencia_cardiaca;
        $this->pressao_arterial = $pressao_arterial;
    }

    function cadastrarAvaliacao()
    {
        $bd = new persistirBD(
            "127.0.0.1",
            "root",
            "",
            "projeto_pibeu"
        );

        $bd->conectar();

        $sql = "
        INSERT INTO avaliacao
        (
            fk_prontuario,
            fk_professor,
            data_avaliacao,
            frequencia_cardiaca,
            pressao_arterial
        )
        VALUES
        (
            '$this->fk_prontuario',
            '$this->fk_professor',
            '$this->data_avaliacao',
            '$this->frequencia_cardiaca',
            '$this->pressao_arterial'
        )
        ";

        $bd->persistir($sql);

        $bd->desconectar();
    }

    function listarAvaliacoes()
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
            a.id_avaliacao,
            pr.id_prontuario,
            pe.nome,
            a.data_avaliacao,
            a.frequencia_cardiaca,
            a.pressao_arterial
        FROM avaliacao a
        INNER JOIN prontuario pr
            ON pr.id_prontuario = a.fk_prontuario
        INNER JOIN professor pf
            ON pf.id_professor = a.fk_professor
        INNER JOIN pessoa pe
            ON pe.id_pessoa = pf.fk_pessoa
        ORDER BY a.id_avaliacao DESC
        ";

        $bd->persistir($sql);

        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        return $dados;
    }

    function buscarAvaliacao($id)
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
        FROM avaliacao
        WHERE id_avaliacao = $id
        ";

        $bd->persistir($sql);

        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        return $dados;
    }

    function atualizarAvaliacao($id)
    {
        $bd = new persistirBD(
            "127.0.0.1",
            "root",
            "",
            "projeto_pibeu"
        );

        $bd->conectar();

        $sql = "
        UPDATE avaliacao
        SET
            fk_prontuario = '$this->fk_prontuario',
            fk_professor = '$this->fk_professor',
            data_avaliacao = '$this->data_avaliacao',
            frequencia_cardiaca = '$this->frequencia_cardiaca',
            pressao_arterial = '$this->pressao_arterial'
        WHERE id_avaliacao = $id
        ";

        $bd->persistir($sql);

        $bd->desconectar();
    }

    function excluirAvaliacao($id)
    {
        $bd = new persistirBD(
            "127.0.0.1",
            "root",
            "",
            "projeto_pibeu"
        );

        $bd->conectar();

        $sql = "
        DELETE FROM avaliacao
        WHERE id_avaliacao = $id
        ";

        $bd->persistir($sql);

        $bd->desconectar();
    }

    function listarProntuarios()
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
            id_prontuario
        FROM prontuario
        ORDER BY id_prontuario
        ";

        $bd->persistir($sql);

        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        return $dados;
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
            pf.id_professor,
            pe.nome
        FROM professor pf
        INNER JOIN pessoa pe
            ON pe.id_pessoa = pf.fk_pessoa
        ORDER BY pe.nome
        ";

        $bd->persistir($sql);

        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        return $dados;
    }
}