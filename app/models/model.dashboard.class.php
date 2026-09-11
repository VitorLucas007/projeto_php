<?php

include_once('model.persistirBD.class.php');

// Consultas agregadas dos cards/gráficos da home. Ficam aqui, fora dos
// models de cada entidade, por serem específicas dessa tela.
class dashboard
{
    // Alunos ativos, avaliações do mês e alunos sem avaliação há $diasSemAvaliacao dias.
    static function statsProfessor($fk_professor, $fk_unidade, $diasSemAvaliacao = 60)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $bd->persistirPreparado(
            "SELECT COUNT(*) FROM aluno al
             INNER JOIN pessoa pe ON pe.id_pessoa = al.fk_pessoa
             INNER JOIN usuario u ON u.fk_pessoa = pe.id_pessoa
             WHERE u.fk_unidade = ? AND u.status_aprovacao = 'APROVADO' AND u.ativo = 1",
            "i",
            [$fk_unidade]
        );
        $alunosAtivos = (int) ($bd->retornoConsultas()[0][0] ?? 0);

        $bd->persistirPreparado(
            "SELECT COUNT(*) FROM avaliacao
             WHERE fk_professor = ?
             AND MONTH(data_avaliacao) = MONTH(CURDATE()) AND YEAR(data_avaliacao) = YEAR(CURDATE())",
            "i",
            [$fk_professor]
        );
        $avaliacoesNoMes = (int) ($bd->retornoConsultas()[0][0] ?? 0);

        $bd->persistirPreparado(
            "SELECT pe.nome, al.id_aluno, MAX(a.data_avaliacao) AS ultima
             FROM aluno al
             INNER JOIN pessoa pe ON pe.id_pessoa = al.fk_pessoa
             INNER JOIN usuario u ON u.fk_pessoa = pe.id_pessoa
             INNER JOIN prontuario pr ON pr.fk_aluno = al.id_aluno
             LEFT JOIN avaliacao a ON a.fk_prontuario = pr.id_prontuario
             WHERE u.fk_unidade = ? AND u.status_aprovacao = 'APROVADO' AND u.ativo = 1
             GROUP BY al.id_aluno, pe.nome
             HAVING MAX(a.data_avaliacao) IS NULL OR MAX(a.data_avaliacao) < DATE_SUB(CURDATE(), INTERVAL ? DAY)
             ORDER BY MAX(a.data_avaliacao) IS NULL DESC, MAX(a.data_avaliacao) ASC",
            "ii",
            [$fk_unidade, $diasSemAvaliacao]
        );
        $semAvaliacaoRecente = $bd->retornoConsultas();

        $bd->desconectar();

        return [
            'alunos_ativos' => $alunosAtivos,
            'avaliacoes_no_mes' => $avaliacoesNoMes,
            'sem_avaliacao_recente' => $semAvaliacaoRecente,
        ];
    }

    // IMC e %gordura da última avaliação de cada aluno ativo da unidade.
    static function distribuicaoImcPorUnidade($fk_unidade)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "
        SELECT pe.nome, a.imc, a.percentual_gordura
        FROM aluno al
        INNER JOIN pessoa pe ON pe.id_pessoa = al.fk_pessoa
        INNER JOIN usuario u ON u.fk_pessoa = pe.id_pessoa
        INNER JOIN prontuario pr ON pr.fk_aluno = al.id_aluno
        INNER JOIN avaliacao a ON a.fk_prontuario = pr.id_prontuario
        WHERE u.fk_unidade = ? AND u.status_aprovacao = 'APROVADO' AND u.ativo = 1
        AND a.id_avaliacao = (
            SELECT a2.id_avaliacao FROM avaliacao a2
            WHERE a2.fk_prontuario = pr.id_prontuario
            ORDER BY a2.data_avaliacao DESC, a2.id_avaliacao DESC LIMIT 1
        )
        AND a.imc IS NOT NULL
        ORDER BY pe.nome
        ";

        $bd->persistirPreparado($sql, "i", [$fk_unidade]);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();
        return $dados;
    }

    // Alunos e professores ativos da unidade.
    static function statsAdmin($fk_unidade)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $bd->persistirPreparado(
            "SELECT COUNT(*) FROM aluno al
             INNER JOIN pessoa pe ON pe.id_pessoa = al.fk_pessoa
             INNER JOIN usuario u ON u.fk_pessoa = pe.id_pessoa
             WHERE u.fk_unidade = ? AND u.status_aprovacao = 'APROVADO' AND u.ativo = 1",
            "i",
            [$fk_unidade]
        );
        $alunos = (int) ($bd->retornoConsultas()[0][0] ?? 0);

        $bd->persistirPreparado(
            "SELECT COUNT(*) FROM professor pf
             INNER JOIN pessoa pe ON pe.id_pessoa = pf.fk_pessoa
             INNER JOIN usuario u ON u.fk_pessoa = pe.id_pessoa
             WHERE u.fk_unidade = ? AND u.status_aprovacao = 'APROVADO' AND u.ativo = 1",
            "i",
            [$fk_unidade]
        );
        $professores = (int) ($bd->retornoConsultas()[0][0] ?? 0);

        $bd->desconectar();

        return [
            'alunos' => $alunos,
            'professores' => $professores,
        ];
    }

    // Novas matrículas por mês nos últimos $meses meses (meses sem matrícula entram com 0).
    static function matriculasPorMes($fk_unidade, $meses = 6)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $sql = "
        SELECT DATE_FORMAT(u.created_at, '%Y-%m') AS ano_mes, COUNT(*) AS total
        FROM usuario u
        WHERE u.fk_unidade = ? AND u.fk_perfil = 3
        AND u.created_at >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
        GROUP BY ano_mes
        ";

        $bd->persistirPreparado($sql, "ii", [$fk_unidade, $meses]);
        $dados = $bd->retornoConsultas();

        $bd->desconectar();

        $porMes = [];
        foreach ($dados as $linha) {
            $porMes[$linha[0]] = (int) $linha[1];
        }

        $resultado = [];
        for ($i = $meses - 1; $i >= 0; $i--) {
            $chave = date('Y-m', strtotime("-$i months"));
            $resultado[] = ['mes' => $chave, 'total' => $porMes[$chave] ?? 0];
        }

        return $resultado;
    }

    // Total de unidades reais (exclui a unidade dummy do Root) e admins pendentes.
    static function statsRoot()
    {
        include_once('model.unidade.class.php');
        include_once('model.usuario.class.php');

        return [
            'unidades' => count(unidade::listarUnidadesAtivas()),
            'admins_pendentes' => usuario::contarPendentesAdmin(),
        ];
    }

    // Ficha de treino ativa do aluno (mais recente ainda dentro da validade).
    static function resumoAluno($id_aluno)
    {
        $bd = new persistirBD();
        $bd->conectar();

        $bd->persistirPreparado(
            "SELECT id_ficha, nome_treino, data_criacao, data_validade
             FROM ficha_treino
             WHERE fk_aluno = ? AND (data_validade IS NULL OR data_validade >= CURDATE())
             ORDER BY data_criacao DESC LIMIT 1",
            "i",
            [$id_aluno]
        );
        $fichaTreino = $bd->retornoConsultas()[0] ?? null;

        $bd->desconectar();

        return [
            'ficha_treino_ativa' => $fichaTreino,
        ];
    }
}
