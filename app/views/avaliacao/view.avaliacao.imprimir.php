<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
    exit;
}

include_once('../../models/model.usuario.class.php');

$perfil = $_SESSION['fk_perfil'] ?? null;

if (!in_array($perfil, [usuario::PERFIL_PROFESSOR, usuario::PERFIL_PESSOA], true)) {
    header("Location: ../home/view.home.php");
    exit;
}

include_once('../../models/model.avaliacao.class.php');
include_once('../../models/model.professor.class.php');

$id = (int) ($_GET['id'] ?? 0);
$v = avaliacao::buscarAvaliacao($id);

if (!$v) {
    header("Location: view.avaliacao.php");
    exit;
}

// Aluno só pode imprimir a própria avaliação.
if ($perfil == usuario::PERFIL_PESSOA) {
    if (avaliacao::buscarFkPessoaAluno($id) != $_SESSION['fk_pessoa']) {
        header("Location: view.minhas.avaliacoes.php");
        exit;
    }
}

$nomeProfessorResponsavel = professor::buscarNomePorId($v['fk_professor']);
$nomeAluno = avaliacao::buscarNomeAluno(
    // resolve id_aluno a partir do fk_prontuario indiretamente via fk_pessoa já checado acima
    // quando professor, buscamos pelo prontuário mesmo
    (int) ($_GET['id_aluno'] ?? 0)
);

// Fallback: se não veio id_aluno na URL, descobre pelo próprio registro.
if (!$nomeAluno) {
    include_once('../../models/model.persistirBD.class.php');
    $bd = new persistirBD();
    $bd->conectar();
    $bd->persistirPreparado(
        "SELECT pe.nome, al.id_aluno FROM avaliacao a
         INNER JOIN prontuario pr ON pr.id_prontuario = a.fk_prontuario
         INNER JOIN aluno al ON al.id_aluno = pr.fk_aluno
         INNER JOIN pessoa pe ON pe.id_pessoa = al.fk_pessoa
         WHERE a.id_avaliacao = ?",
        "i",
        [$id]
    );
    $resultado = $bd->retornoConsultas();
    $bd->desconectar();
    $nomeAluno = $resultado[0][0] ?? '—';
}

function fmt($valor, $unidade = '')
{
    if ($valor === null || $valor === '') {
        return '—';
    }
    return number_format((float) $valor, 2, ',', '.') . ($unidade ? ' ' . $unidade : '');
}

function simNao($valor)
{
    return (!empty($valor) && $valor != 0) ? 'Sim' : 'Não';
}

$medidas = [
    'torax' => 'Tórax', 'cintura' => 'Cintura', 'abdominal' => 'Abdominal', 'quadril' => 'Quadril',
    'braco_relaxado_direito' => 'Braço relaxado (D)', 'braco_relaxado_esquerdo' => 'Braço relaxado (E)',
    'braco_contraido_direito' => 'Braço contraído (D)', 'braco_contraido_esquerdo' => 'Braço contraído (E)',
    'coxa_direita' => 'Coxa (D)', 'coxa_esquerda' => 'Coxa (E)',
    'panturrilha_direita' => 'Panturrilha (D)', 'panturrilha_esquerda' => 'Panturrilha (E)',
];

$bioimpedancia = [
    'peso' => ['Peso', 'kg'], 'percentual_gordura' => ['% Gordura corporal', '%'],
    'massa_magra' => ['Massa magra', 'kg'], 'massa_muscular' => ['Massa muscular', 'kg'],
    'agua_corporal' => ['Água corporal', '%'], 'imc' => ['IMC', ''],
    'taxa_metabolica_basal' => ['Taxa metabólica basal', 'kcal'],
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Ficha de Avaliação #<?= (int) $v['id_avaliacao'] ?> — Impressão</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 14px; color: #212529; }
        .folha { max-width: 800px; margin: 0 auto; padding: 24px; }
        .folha h4 { border-bottom: 2px solid #212529; padding-bottom: 4px; margin-top: 24px; }
        table.ficha td, table.ficha th { padding: 4px 8px; font-size: 13px; }
        .barra-acoes { max-width: 800px; margin: 16px auto 0; }
        .verso-marca { font-size: 11px; color: #6c757d; text-align: right; }

        @media print {
            .barra-acoes { display: none !important; }
            .folha { padding: 0; }
            body { margin: 0; }
            @page { size: A4; margin: 15mm; }
        }
    </style>
</head>
<body>

    <div class="barra-acoes d-flex justify-content-between">
        <a href="view.avaliacao.detalhe.php?id=<?= (int) $v['id_avaliacao'] ?>" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            <i class="bi bi-printer"></i> Imprimir / Salvar como PDF
        </button>
    </div>

    <div class="folha">
        <div class="verso-marca">Verso — Anamnese e Medidas</div>
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h3 class="mb-0">Ficha de Avaliação Física</h3>
                <div class="text-muted">Aluno: <strong><?= htmlspecialchars($nomeAluno) ?></strong></div>
            </div>
            <div class="text-end">
                <div>Avaliação nº <?= (int) $v['id_avaliacao'] ?></div>
                <div>Data: <?= date('d/m/Y', strtotime($v['data_avaliacao'])) ?></div>
                <div>Professor: <?= htmlspecialchars($nomeProfessorResponsavel ?? '—') ?></div>
            </div>
        </div>

        <h4>Sinais vitais e estilo de vida</h4>
        <table class="table table-sm table-bordered ficha">
            <tr><th style="width:40%">Frequência cardíaca</th><td><?= htmlspecialchars($v['frequencia_cardiaca'] ?: '—') ?></td></tr>
            <tr><th>Pressão arterial</th><td><?= htmlspecialchars($v['pressao_arterial'] ?: '—') ?></td></tr>
            <tr><th>Sedentário</th><td><?= simNao($v['sedentario']) ?></td></tr>
            <tr><th>Atividade física praticada</th><td><?= htmlspecialchars($v['atividade_fisica'] ?: '—') ?></td></tr>
            <tr><th>Tabagismo</th><td><?= simNao($v['tabagismo']) ?></td></tr>
            <tr><th>Consome álcool</th><td><?= simNao($v['alcool']) ?></td></tr>
        </table>

        <h4>Anamnese clínica</h4>
        <table class="table table-sm table-bordered ficha">
            <tr><th style="width:40%">Medicação controlada</th><td><?= simNao($v['medicacao_controlada']) ?> — <?= htmlspecialchars($v['medicamentos_descricao'] ?: '—') ?></td></tr>
            <tr><th>Problema osteoarticular</th><td><?= simNao($v['problema_osteoarticular']) ?> — <?= htmlspecialchars($v['osteoarticular_descricao'] ?: '—') ?></td></tr>
            <tr><th>Problema neuromuscular</th><td><?= simNao($v['problema_neuromuscular']) ?> — <?= htmlspecialchars($v['neuromuscular_descricao'] ?: '—') ?></td></tr>
            <tr><th>Problema coronário</th><td><?= simNao($v['problema_coronario']) ?> — <?= htmlspecialchars($v['coronario_descricao'] ?: '—') ?></td></tr>
            <tr><th>Problema vascular</th><td><?= simNao($v['problema_vascular']) ?></td></tr>
            <tr><th>Hospitalização (5 anos)</th><td><?= simNao($v['hospitalizacao_5_anos']) ?> — <?= htmlspecialchars($v['hospitalizacao_descricao'] ?: '—') ?></td></tr>
            <tr><th>Cirurgia (5 anos)</th><td><?= simNao($v['cirurgia_5_anos']) ?> — <?= htmlspecialchars($v['cirurgia_descricao'] ?: '—') ?></td></tr>
        </table>

        <h4>Medidas antropométricas</h4>
        <table class="table table-sm table-bordered ficha">
            <?php $pares = array_chunk($medidas, 2, true); ?>
            <?php foreach ($pares as $par): ?>
                <tr>
                    <?php foreach ($par as $chave => $rotulo): ?>
                        <th style="width:25%"><?= $rotulo ?></th>
                        <td style="width:25%"><?= fmt($v[$chave] ?? null, 'cm') ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>

        <h4>Bioimpedância</h4>
        <table class="table table-sm table-bordered ficha">
            <?php $paresBio = array_chunk($bioimpedancia, 2, true); ?>
            <?php foreach ($paresBio as $par): ?>
                <tr>
                    <?php foreach ($par as $chave => [$rotulo, $unidade]): ?>
                        <th style="width:25%"><?= $rotulo ?></th>
                        <td style="width:25%"><?= fmt($v[$chave] ?? null, $unidade) ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>

        <p class="text-muted mt-4" style="font-size:12px">
            Esta página corresponde ao verso da ficha (anamnese e medidas). A frente (ficha de biopendância) é impressa separadamente.
        </p>
    </div>

</body>
</html>
