<?php

include_once('model.persistirBD.class.php');

/**
 * Ficha de Composição Corporal — ficha detalhada de bioimpedância (estilo
 * InBody), preenchida pelo professor e vinculada a uma avaliação física já
 * existente (fk_avaliacao). O verso impresso (anamnese + medidas) reaproveita
 * os dados já cadastrados na avaliação vinculada, então essa tabela guarda
 * apenas os dados de composição corporal em si.
 */
class ficha_composicao
{
    /**
     * Mapa central de colunas -> tipo de bind (mysqli), no mesmo padrão do
     * model de avaliação: qualquer coluna nova na tabela só precisa ser
     * adicionada aqui.
     */
    public static $campos = [
        'fk_avaliacao'                    => 'i',
        'fk_professor'                    => 'i',
        'data_teste'                      => 's',
        'hora_teste'                      => 's',
        'id_equipamento'                  => 's',

        'altura'                          => 'd',
        'sexo_referencia'                 => 's',
        'idade_referencia'                => 'i',

        'agua_corporal_total'             => 'd',
        'agua_corporal_min'               => 'd',
        'agua_corporal_max'               => 'd',

        'proteina'                        => 'd',
        'proteina_min'                    => 'd',
        'proteina_max'                    => 'd',

        'minerais'                        => 'd',
        'minerais_min'                    => 'd',
        'minerais_max'                    => 'd',

        'massa_gordura'                   => 'd',
        'massa_gordura_min'               => 'd',
        'massa_gordura_max'               => 'd',

        'peso'                            => 'd',
        'peso_min'                        => 'd',
        'peso_max'                        => 'd',

        'massa_muscular_esqueletica'      => 'd',
        'mme_min'                         => 'd',
        'mme_max'                         => 'd',

        'imc'                             => 'd',
        'imc_min'                         => 'd',
        'imc_max'                         => 'd',

        'percentual_gordura_corporal'     => 'd',
        'pgc_min'                         => 'd',
        'pgc_max'                         => 'd',

        'peso_ideal'                      => 'd',
        'controle_peso'                   => 'd',
        'controle_gordura'                => 'd',
        'controle_muscular'               => 'd',
        'pontuacao_geral'                 => 'i',

        'relacao_cintura_quadril'         => 'd',
        'nivel_gordura_visceral'          => 'i',
        'angulo_fase'                     => 'd',

        'massa_livre_gordura'             => 'd',
        'mlg_min'                         => 'd',
        'mlg_max'                         => 'd',

        'fator_atividade'                 => 'd',
        'taxa_metabolica_basal'           => 'd',
        'tmb_min'                         => 'd',
        'tmb_max'                         => 'd',

        'grau_obesidade'                  => 'd',
        'grau_obesidade_min'              => 'd',
        'grau_obesidade_max'              => 'd',

        'smi'                             => 'd',
        'ingestao_calorica_recomendada'   => 'd',

        'seg_magra_braco_esq_kg'          => 'd',
        'seg_magra_braco_esq_pct'         => 'd',
        'seg_magra_braco_dir_kg'          => 'd',
        'seg_magra_braco_dir_pct'         => 'd',
        'seg_magra_tronco_kg'             => 'd',
        'seg_magra_tronco_pct'            => 'd',
        'seg_magra_perna_esq_kg'          => 'd',
        'seg_magra_perna_esq_pct'         => 'd',
        'seg_magra_perna_dir_kg'          => 'd',
        'seg_magra_perna_dir_pct'         => 'd',

        'seg_gordura_braco_esq_kg'        => 'd',
        'seg_gordura_braco_esq_pct'       => 'd',
        'seg_gordura_braco_dir_kg'        => 'd',
        'seg_gordura_braco_dir_pct'       => 'd',
        'seg_gordura_tronco_kg'           => 'd',
        'seg_gordura_tronco_pct'          => 'd',
        'seg_gordura_perna_esq_kg'        => 'd',
        'seg_gordura_perna_esq_pct'       => 'd',
        'seg_gordura_perna_dir_kg'        => 'd',
        'seg_gordura_perna_dir_pct'       => 'd',

        'observacoes'                     => 's',
    ];

    public $dados = [];

    /**
     * @param array $dadosPost Array associativo (tipicamente $_POST), já
     * passado por self::recalcular() para garantir que os campos calculados
     * fiquem consistentes mesmo que o JS do formulário não tenha rodado.
     */
    function __construct(array $dadosPost)
    {
        foreach (self::$campos as $campo => $tipo) {

            $valor = $dadosPost[$campo] ?? null;

            if ($tipo === 'd' || $tipo === 'i') {
                $this->dados[$campo] = ($valor === null || $valor === '') ? null : $valor;
                continue;
            }

            $this->dados[$campo] = ($valor === null) ? '' : $valor;
        }

        if ($this->dados['hora_teste'] === '') {
            $this->dados['hora_teste'] = null;
        }
        if ($this->dados['sexo_referencia'] === '') {
            $this->dados['sexo_referencia'] = null;
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

    function cadastrar()
    {
        [$colunas, $tipos, $valores] = $this->montarListasBind();

        $sql = "INSERT INTO ficha_composicao_corporal (" . implode(', ', $colunas) . ")
                VALUES (" . implode(', ', array_fill(0, count($colunas), '?')) . ")";

        $bd = new persistirBD();
        $bd->conectar();
        $bd->persistirPreparado($sql, $tipos, $valores);
        $id = $bd->ultimoId();
        $bd->desconectar();

        return $id;
    }

    function atualizar($id)
    {
        [$colunas, $tipos, $valores] = $this->montarListasBind();

        $sets = implode(', ', array_map(fn($c) => "$c = ?", $colunas));

        $sql = "UPDATE ficha_composicao_corporal SET $sets WHERE id_ficha = ?";

        $bd = new persistirBD();
        $bd->conectar();
        $bd->persistirPreparado($sql, $tipos . "i", [...$valores, $id]);
        $bd->desconectar();
    }

    /**
     * Retorna a ficha como array associativo, pronta pra popular o
     * formulário de edição ou a impressão.
     */
    static function buscarPorId($id)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "SELECT * FROM ficha_composicao_corporal WHERE id_ficha = ?";
        $bd->persistirPreparado($sql, "i", [$id]);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        if (!isset($dados[0])) {
            return null;
        }

        $colunas = array_merge(['id_ficha'], array_keys(self::$campos), ['created_at']);

        return array_combine($colunas, $dados[0]);
    }

    static function excluir($id)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "DELETE FROM ficha_composicao_corporal WHERE id_ficha = ?";
        $bd->persistirPreparado($sql, "i", [$id]);

        $bd->desconectar();
    }

    /**
     * Fichas já cadastradas para uma avaliação específica (normalmente 0 ou 1).
     */
    static function listarPorAvaliacao($fk_avaliacao)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "SELECT id_ficha, data_teste, peso, percentual_gordura_corporal
                FROM ficha_composicao_corporal
                WHERE fk_avaliacao = ?
                ORDER BY data_teste DESC, id_ficha DESC";
        $bd->persistirPreparado($sql, "i", [$fk_avaliacao]);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();
        return $dados;
    }

    /**
     * Listagem resumida das fichas de um aluno (todas as avaliações dele),
     * mais recente primeiro — usada nas telas de listagem.
     */
    static function listarPorAluno($id_aluno)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "
        SELECT
            f.id_ficha, f.data_teste, f.peso, f.percentual_gordura_corporal,
            f.imc, pe.nome AS nome_professor
        FROM ficha_composicao_corporal f
        INNER JOIN avaliacao a ON a.id_avaliacao = f.fk_avaliacao
        INNER JOIN prontuario pr ON pr.id_prontuario = a.fk_prontuario
        INNER JOIN professor pf ON pf.id_professor = f.fk_professor
        INNER JOIN pessoa pe ON pe.id_pessoa = pf.fk_pessoa
        WHERE pr.fk_aluno = ?
        ORDER BY f.data_teste DESC, f.id_ficha DESC
        ";

        $bd->persistirPreparado($sql, "i", [$id_aluno]);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();
        return $dados;
    }

    /**
     * Histórico completo (todos os campos) das fichas de um aluno, da mais
     * antiga pra mais nova — usado no gráfico de evolução.
     */
    static function listarCompletoPorAluno($id_aluno)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "
        SELECT f.*
        FROM ficha_composicao_corporal f
        INNER JOIN avaliacao a ON a.id_avaliacao = f.fk_avaliacao
        INNER JOIN prontuario pr ON pr.id_prontuario = a.fk_prontuario
        WHERE pr.fk_aluno = ?
        ORDER BY f.data_teste ASC, f.id_ficha ASC
        ";

        $bd->persistirPreparado($sql, "i", [$id_aluno]);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        if (empty($dados)) {
            return [];
        }

        $colunas = array_merge(['id_ficha'], array_keys(self::$campos), ['created_at']);

        return array_map(fn($linha) => array_combine($colunas, $linha), $dados);
    }

    /**
     * Resolve o id_pessoa dono (aluno) de uma ficha, pra checagem de posse
     * nas telas de detalhe/impressão restritas ao próprio aluno.
     */
    static function buscarFkPessoaAluno($id_ficha)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "
        SELECT pe.id_pessoa
        FROM ficha_composicao_corporal f
        INNER JOIN avaliacao a ON a.id_avaliacao = f.fk_avaliacao
        INNER JOIN prontuario pr ON pr.id_prontuario = a.fk_prontuario
        INNER JOIN aluno al ON al.id_aluno = pr.fk_aluno
        INNER JOIN pessoa pe ON pe.id_pessoa = al.fk_pessoa
        WHERE f.id_ficha = ?
        ";

        $bd->persistirPreparado($sql, "i", [$id_ficha]);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        return isset($dados[0][0]) ? (int) $dados[0][0] : null;
    }

    /**
     * Dados base da avaliação vinculada, usados pra pré-preencher o
     * formulário (nome do aluno, sexo/data de nascimento pra idade e TMB,
     * cintura/quadril já medidos pra sugerir a relação cintura-quadril).
     */
    static function buscarContextoAvaliacao($fk_avaliacao)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "
        SELECT
            pe.nome, pe.sexo, pe.data_nascimento,
            a.data_avaliacao, a.cintura, a.quadril,
            al.id_aluno
        FROM avaliacao a
        INNER JOIN prontuario pr ON pr.id_prontuario = a.fk_prontuario
        INNER JOIN aluno al ON al.id_aluno = pr.fk_aluno
        INNER JOIN pessoa pe ON pe.id_pessoa = al.fk_pessoa
        WHERE a.id_avaliacao = ?
        ";

        $bd->persistirPreparado($sql, "i", [$fk_avaliacao]);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        if (!isset($dados[0])) {
            return null;
        }

        return [
            'nome'            => $dados[0][0],
            'sexo'            => $dados[0][1],
            'data_nascimento' => $dados[0][2],
            'data_avaliacao'  => $dados[0][3],
            'cintura'         => $dados[0][4],
            'quadril'         => $dados[0][5],
            'id_aluno'        => $dados[0][6],
        ];
    }

    /**
     * Recalcula, no servidor, todos os campos derivados a partir dos dados
     * brutos recebidos — mesma lógica de public/assets/js/composicao-calculos.js,
     * espelhada em PHP. Isso garante que a ficha salva no banco fique
     * consistente mesmo que o JavaScript do formulário não tenha rodado
     * (client desatualizado, POST manual, futura importação automática da
     * balança etc). Valores calculados vindos do POST são sempre
     * sobrescritos; os campos que não têm fórmula pública confiável
     * (água/proteína/minerais, score geral, ângulo de fase, nível de
     * gordura visceral e todas as faixas normais) continuam sendo os que o
     * professor digitou.
     */
    static function recalcular(array $dados)
    {
        $peso = self::numOuNull($dados['peso'] ?? null);
        $altura = self::numOuNull($dados['altura'] ?? null);
        $massaGordura = self::numOuNull($dados['massa_gordura'] ?? null);
        $mme = self::numOuNull($dados['massa_muscular_esqueletica'] ?? null);
        $idade = self::numOuNull($dados['idade_referencia'] ?? null);
        $sexo = $dados['sexo_referencia'] ?? null;
        $fatorAtividade = self::numOuNull($dados['fator_atividade'] ?? null) ?: 1.55;
        $cintura = self::numOuNull($dados['_cintura_avaliacao'] ?? null);
        $quadril = self::numOuNull($dados['_quadril_avaliacao'] ?? null);

        $alturaM = $altura ? $altura / 100 : null;

        // IMC
        if ($peso && $alturaM) {
            $dados['imc'] = round($peso / ($alturaM ** 2), 2);
        }

        // Peso ideal (IMC-alvo 22, meio da faixa saudável da OMS)
        $pesoIdeal = null;
        if ($alturaM) {
            $pesoIdeal = 22 * ($alturaM ** 2);
            $dados['peso_ideal'] = round($pesoIdeal, 2);
        }

        // Massa livre de gordura
        if ($peso !== null && $massaGordura !== null) {
            $dados['massa_livre_gordura'] = round($peso - $massaGordura, 2);
        }

        // Grau de obesidade
        if ($peso && $pesoIdeal) {
            $dados['grau_obesidade'] = round(($peso / $pesoIdeal) * 100, 2);
        }

        // SMI
        if ($mme && $alturaM) {
            $dados['smi'] = round($mme / ($alturaM ** 2), 2);
        }

        // Relação cintura-quadril (só recalcula se a avaliação vinculada trouxe as medidas)
        if ($cintura && $quadril) {
            $dados['relacao_cintura_quadril'] = round($cintura / $quadril, 2);
        }

        // TMB — Mifflin-St Jeor
        $tmb = null;
        if ($peso && $altura && $idade) {
            $base = 10 * $peso + 6.25 * $altura - 5 * $idade;
            $tmb = ($sexo === 'F') ? $base - 161 : $base + 5;
            $dados['taxa_metabolica_basal'] = round($tmb, 2);
        }

        // Ingestão calórica recomendada
        if ($tmb) {
            $dados['ingestao_calorica_recomendada'] = round($tmb * $fatorAtividade, 2);
        }

        // Controles de peso/gordura/muscular (meta de %gordura pelo sexo, meio da faixa ACE)
        if ($peso !== null && $pesoIdeal) {
            $controlePeso = $pesoIdeal - $peso;
            $dados['controle_peso'] = round($controlePeso, 2);

            if ($massaGordura !== null) {
                $pgcAlvo = ($sexo === 'F') ? 23 : 15;
                $massaGorduraIdeal = $pesoIdeal * ($pgcAlvo / 100);
                $controleGordura = $massaGorduraIdeal - $massaGordura;
                $dados['controle_gordura'] = round($controleGordura, 2);
                $dados['controle_muscular'] = round($controlePeso - $controleGordura, 2);
            }
        }

        unset($dados['_cintura_avaliacao'], $dados['_quadril_avaliacao']);

        return $dados;
    }

    private static function numOuNull($v)
    {
        if ($v === null || $v === '') return null;
        return is_numeric($v) ? (float) $v : null;
    }
}
