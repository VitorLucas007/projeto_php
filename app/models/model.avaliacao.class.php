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

        'peso'                      => 'd',
        'percentual_gordura'        => 'd',
        'massa_magra'               => 'd',
        'massa_muscular'            => 'd',
        'agua_corporal'             => 'd',
        'imc'                       => 'd',
        'taxa_metabolica_basal'     => 'd',
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

    /**
     * Lista somente as avaliações do aluno logado (via fk_pessoa da sessão).
     */
    static function listarAvaliacoesPorPessoa($fk_pessoa)
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
        INNER JOIN aluno al ON al.id_aluno = pr.fk_aluno
        INNER JOIN professor pf ON pf.id_professor = a.fk_professor
        INNER JOIN pessoa pe ON pe.id_pessoa = pf.fk_pessoa
        WHERE al.fk_pessoa = ?
        ORDER BY a.id_avaliacao DESC
        ";

        $bd->persistirPreparado($sql, "i", [$fk_pessoa]);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();
        return $dados;
    }

    /**
     * Histórico de avaliações de um aluno, de qualquer professor (sem
     * filtrar por fk_professor), ordenado por data.
     */
    static function listarPorAluno($id_aluno)
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
        WHERE pr.fk_aluno = ?
        ORDER BY a.data_avaliacao DESC
        ";

        $bd->persistirPreparado($sql, "i", [$id_aluno]);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();
        return $dados;
    }

    /**
     * Histórico completo (todos os campos, não só o resumo de listagem) das
     * avaliações de um aluno, já como array associativo cada uma — usado no
     * dashboard comparativo. Ordenado da mais antiga pra mais nova.
     */
    static function listarCompletoPorAluno($id_aluno)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "
        SELECT a.*
        FROM avaliacao a
        INNER JOIN prontuario pr ON pr.id_prontuario = a.fk_prontuario
        WHERE pr.fk_aluno = ?
        ORDER BY a.data_avaliacao ASC, a.id_avaliacao ASC
        ";

        $bd->persistirPreparado($sql, "i", [$id_aluno]);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        if (empty($dados)) {
            return [];
        }

        $colunas = array_merge(['id_avaliacao'], array_keys(self::$campos), ['created_at']);

        return array_map(fn($linha) => array_combine($colunas, $linha), $dados);
    }

    /**
     * Resolve o id_pessoa dono (aluno) de uma avaliação, pra checagem de
     * posse na tela de detalhe somente-leitura.
     */
    static function buscarFkPessoaAluno($id_avaliacao)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "
        SELECT pe.id_pessoa
        FROM avaliacao a
        INNER JOIN prontuario pr ON pr.id_prontuario = a.fk_prontuario
        INNER JOIN aluno al ON al.id_aluno = pr.fk_aluno
        INNER JOIN pessoa pe ON pe.id_pessoa = al.fk_pessoa
        WHERE a.id_avaliacao = ?
        ";

        $bd->persistirPreparado($sql, "i", [$id_avaliacao]);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        return isset($dados[0][0]) ? (int) $dados[0][0] : null;
    }

    /**
     * Nome do aluno (pessoa) a partir do id_aluno — usado nos cabeçalhos
     * do histórico, comparativo e impressão.
     */
    static function buscarNomeAluno($id_aluno)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "
        SELECT pe.nome
        FROM aluno al
        INNER JOIN pessoa pe ON pe.id_pessoa = al.fk_pessoa
        WHERE al.id_aluno = ?
        ";

        $bd->persistirPreparado($sql, "i", [$id_aluno]);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        return $dados[0][0] ?? null;
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
}
