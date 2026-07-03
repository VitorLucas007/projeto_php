<?php

include_once('model.persistirBD.class.php');

class avaliacao
{
    /**
     * Mapa central de colunas -> tipo de bind (mysqli). Usado para montar
     * INSERT/UPDATE dinamicamente e pra mapear o SELECT * de volta em array
     * associativo. Qualquer coluna nova na tabela `avaliacao` só precisa
     * ser adicionada aqui.
     */
    public static $campos = [
        'fk_prontuario'             => 'i',
        'fk_professor'              => 'i',
        'data_avaliacao'            => 's',
        'frequencia_cardiaca'       => 's',
        'pressao_arterial'          => 's',
        'sedentario'                => 'i',
        'atividade_fisica'          => 's',
        'tabagismo'                 => 'i',
        'alcool'                    => 'i',
        'medicacao_controlada'      => 'i',
        'medicamentos_descricao'    => 's',
        'problema_osteoarticular'   => 'i',
        'osteoarticular_descricao'  => 's',
        'problema_neuromuscular'    => 'i',
        'neuromuscular_descricao'   => 's',
        'problema_coronario'        => 'i',
        'coronario_descricao'       => 's',
        'problema_vascular'         => 'i',
        'hospitalizacao_5_anos'     => 'i',
        'hospitalizacao_descricao'  => 's',
        'cirurgia_5_anos'           => 'i',
        'cirurgia_descricao'        => 's',
        'torax'                     => 'd',
        'cintura'                   => 'd',
        'abdominal'                 => 'd',
        'quadril'                   => 'd',
        'braco_relaxado_direito'    => 'd',
        'braco_relaxado_esquerdo'   => 'd',
        'braco_contraido_direito'   => 'd',
        'braco_contraido_esquerdo'  => 'd',
        'coxa_direita'              => 'd',
        'coxa_esquerda'             => 'd',
        'panturrilha_direita'       => 'd',
        'panturrilha_esquerda'      => 'd',
    ];

    // Campos que são checkbox (boolean) — marcam 0 quando ausentes no POST,
    // ao invés de virar NULL (que é o comportamento certo pros outros tipos).
    private static $booleanos = [
        'sedentario', 'tabagismo', 'alcool', 'medicacao_controlada',
        'problema_osteoarticular', 'problema_neuromuscular', 'problema_coronario',
        'problema_vascular', 'hospitalizacao_5_anos', 'cirurgia_5_anos',
    ];

    public $dados = [];

    /**
     * @param array $dadosPost Array associativo (tipicamente $_POST) com as
     * mesmas chaves dos nomes de coluna listados em self::$campos.
     */
    function __construct(array $dadosPost)
    {
        foreach (self::$campos as $campo => $tipo) {

            $valor = $dadosPost[$campo] ?? null;

            if (in_array($campo, self::$booleanos, true)) {
                $this->dados[$campo] = (!empty($valor)) ? 1 : 0;
                continue;
            }

            if ($tipo === 'd' || $tipo === 'i') {
                $this->dados[$campo] = ($valor === null || $valor === '') ? null : $valor;
                continue;
            }

            // string: mantém vazio como vazio, não força null
            $this->dados[$campo] = ($valor === null) ? '' : $valor;
        }
    }

    private function montarListasBind()
    {
        $colunas = array_keys(self::$campos);
        $tipos = implode('', array_values(self::$campos));
        $valores = [];

        foreach ($colunas as $campo) {
            $valores[] = $this->dados[$campo];
        }

        return [$colunas, $tipos, $valores];
    }

    function cadastrarAvaliacao()
    {
        [$colunas, $tipos, $valores] = $this->montarListasBind();

        $sql = "INSERT INTO avaliacao (" . implode(', ', $colunas) . ")
                VALUES (" . implode(', ', array_fill(0, count($colunas), '?')) . ")";

        $bd = new persistirBD();
        $bd->conectar();
        $bd->persistirPreparado($sql, $tipos, $valores);
        $id = $bd->ultimoId();
        $bd->desconectar();

        return $id;
    }

    function atualizarAvaliacao($id)
    {
        [$colunas, $tipos, $valores] = $this->montarListasBind();

        $sets = implode(', ', array_map(fn($c) => "$c = ?", $colunas));

        $sql = "UPDATE avaliacao SET $sets WHERE id_avaliacao = ?";

        $bd = new persistirBD();
        $bd->conectar();
        $bd->persistirPreparado($sql, $tipos . "i", [...$valores, $id]);
        $bd->desconectar();
    }

    static function listarAvaliacoes()
    {
        $bd = new persistirBD();
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
        INNER JOIN prontuario pr ON pr.id_prontuario = a.fk_prontuario
        INNER JOIN professor pf ON pf.id_professor = a.fk_professor
        INNER JOIN pessoa pe ON pe.id_pessoa = pf.fk_pessoa
        ORDER BY a.id_avaliacao DESC
        ";

        $bd->persistir($sql);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();
        return $dados;
    }

    /**
     * Retorna a avaliação como array associativo (nome_coluna => valor),
     * já pronto pra popular o formulário de edição.
     */
    static function buscarAvaliacao($id)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "SELECT * FROM avaliacao WHERE id_avaliacao = ?";
        $bd->persistirPreparado($sql, "i", [$id]);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        if (!isset($dados[0])) {
            return null;
        }

        $colunas = array_merge(['id_avaliacao'], array_keys(self::$campos), ['created_at']);

        return array_combine($colunas, $dados[0]);
    }

    static function excluirAvaliacao($id)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "DELETE FROM avaliacao WHERE id_avaliacao = ?";
        $bd->persistirPreparado($sql, "i", [$id]);

        $bd->desconectar();
    }

    static function listarProntuarios()
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "
        SELECT pr.id_prontuario, pe.nome
        FROM prontuario pr
        INNER JOIN aluno al ON al.id_aluno = pr.fk_aluno
        INNER JOIN pessoa pe ON pe.id_pessoa = al.fk_pessoa
        ORDER BY pe.nome
        ";

        $bd->persistir($sql);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();
        return $dados;
    }

    static function listarProfessores()
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "
        SELECT pf.id_professor, pe.nome
        FROM professor pf
        INNER JOIN pessoa pe ON pe.id_pessoa = pf.fk_pessoa
        ORDER BY pe.nome
        ";

        $bd->persistir($sql);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();
        return $dados;
    }
}
