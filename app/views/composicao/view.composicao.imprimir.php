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

include_once('../../models/model.ficha_composicao.class.php');
include_once('../../models/model.avaliacao.class.php');
include_once('../../models/model.professor.class.php');

$id = (int) ($_GET['id'] ?? 0);
$v = ficha_composicao::buscarPorId($id);

if (!$v) {
    header("Location: view.composicao.detalhe.php?id=" . $id);
    exit;
}

if ($perfil == usuario::PERFIL_PESSOA) {
    if (ficha_composicao::buscarFkPessoaAluno($id) != $_SESSION['fk_pessoa']) {
        header("Location: view.minhas.composicoes.php");
        exit;
    }
}

$contexto = ficha_composicao::buscarContextoAvaliacao($v['fk_avaliacao']);
$avaliacao = avaliacao::buscarAvaliacao($v['fk_avaliacao']);
$nomeProfessor = professor::buscarNomePorId($v['fk_professor']);
$historico = ficha_composicao::listarCompletoPorAluno($contexto['id_aluno']);

function fmtN($valor, $casas = 1)
{
    if ($valor === null || $valor === '') return '—';
    return number_format((float) $valor, $casas, ',', '.');
}

function simNaoFC($valor)
{
    return (!empty($valor) && $valor != 0) ? 'Sim' : 'Não';
}

// Posição (0-100%) do marcador na barra, com margem de 60% pra cada lado. Aproximação visual.
function barraFaixa($valor, $min, $max)
{
    if ($valor === null || $min === null || $max === null || $valor === '' || $min === '' || $max === '') {
        return null;
    }
    $valor = (float) $valor; $min = (float) $min; $max = (float) $max;
    $amplitude = max($max - $min, 0.01);
    $inicio = $min - $amplitude * 0.6;
    $fim = $max + $amplitude * 0.6;
    $largura = max($fim - $inicio, 0.01);

    $posValor = max(0, min(100, (($valor - $inicio) / $largura) * 100));
    $posMin = max(0, min(100, (($min - $inicio) / $largura) * 100));
    $posMax = max(0, min(100, (($max - $inicio) / $largura) * 100));

    return ['valor' => $posValor, 'min' => $posMin, 'max' => $posMax];
}

function renderBarra($rotulo, $valor, $unidade, $min, $max)
{
    $pos = barraFaixa($valor, $min, $max);
    ?>
    <div class="bloco-barra">
        <div class="d-flex justify-content-between">
            <strong><?= htmlspecialchars($rotulo) ?></strong>
            <strong><?= fmtN($valor, 1) ?> <?= $unidade ?></strong>
        </div>
        <?php if ($pos): ?>
            <div class="barra">
                <div class="barra-normal" style="left:<?= $pos['min'] ?>%; width:<?= max($pos['max'] - $pos['min'], 1) ?>%;"></div>
                <div class="barra-marcador" style="left:<?= $pos['valor'] ?>%;"></div>
            </div>
            <div class="d-flex justify-content-between texto-pequeno text-muted">
                <span>Abaixo</span><span>Normal (<?= fmtN($min, 1) ?>~<?= fmtN($max, 1) ?>)</span><span>Acima</span>
            </div>
        <?php else: ?>
            <div class="texto-pequeno text-muted">Sem faixa normal registrada.</div>
        <?php endif; ?>
    </div>
    <?php
}

// Gasto calórico estimado (30 min) — mesmos METs de public/assets/js/composicao-calculos.js
$metEsportes = [
    'Golfe' => 4.8, 'Gate-ball' => 4.0, 'Caminhada' => 3.5, 'Ioga' => 3.0,
    'Badminton' => 5.5, 'Tênis de mesa' => 4.0, 'Tênis' => 7.3, 'Ciclismo' => 7.5,
    'Boxe' => 7.8, 'Basquetebol' => 8.0, 'Escalada' => 8.0, 'Aeróbica' => 7.3,
    'Jogging' => 7.0, 'Futebol' => 7.0, 'Natação' => 6.0, 'Esgrima japonesa' => 6.0,
    'Raquetebol' => 7.0, 'Squash' => 12.0, 'Taekwondo' => 10.3, 'Pular corda' => 10.0,
];
$gastoCalorico = [];
if (!empty($v['peso'])) {
    foreach ($metEsportes as $esporte => $met) {
        $gastoCalorico[$esporte] = round($met * (float) $v['peso'] * 0.5);
    }
}

$medidas = [
    'torax' => 'Tórax', 'cintura' => 'Cintura', 'abdominal' => 'Abdominal', 'quadril' => 'Quadril',
    'braco_relaxado_direito' => 'Braço relaxado (D)', 'braco_relaxado_esquerdo' => 'Braço relaxado (E)',
    'braco_contraido_direito' => 'Braço contraído (D)', 'braco_contraido_esquerdo' => 'Braço contraído (E)',
    'coxa_direita' => 'Coxa (D)', 'coxa_esquerda' => 'Coxa (E)',
    'panturrilha_direita' => 'Panturrilha (D)', 'panturrilha_esquerda' => 'Panturrilha (E)',
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Composição Corporal #<?= (int) $v['id_ficha'] ?> — Impressão</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.4/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-size: 14px; color: #212529; background: #eef1f4; }
        .barra-acoes { max-width: 900px; margin: 16px auto; }
        .folha {
            max-width: 900px;
            margin: 0 auto 24px;
            padding: 24px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
        }
        .texto-pequeno { font-size: 11px; }
        .cabecalho-marca { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #212529; padding-bottom: 10px; margin-bottom: 12px; }
        .cabecalho-marca h1 { font-size: 1.6rem; font-weight: 800; margin: 0; }
        .caixa { border: 1px solid #dee2e6; border-radius: 6px; padding: 10px 14px; margin-bottom: 12px; }
        .caixa h6 { text-transform: uppercase; font-size: .72rem; letter-spacing: .05em; color: #495057; margin-bottom: 8px; font-weight: 700; }
        .grid-2 { display: grid; grid-template-columns: 2fr 1fr; gap: 16px; }
        .bloco-barra { margin-bottom: 14px; }
        .barra { position: relative; height: 10px; background: #e9ecef; border-radius: 5px; margin: 6px 0 2px; }
        .barra-normal { position: absolute; top: 0; height: 100%; background: #b6d7f7; border-radius: 5px; }
        .barra-marcador { position: absolute; top: -3px; width: 3px; height: 16px; background: #212529; transform: translateX(-50%); }
        .score-caixa { background: #212529; color: #fff; border-radius: 6px; padding: 14px; text-align: center; margin-bottom: 12px; }
        .score-caixa .numero { font-size: 2.4rem; font-weight: 800; }
        .segmento-caixa { border: 1px solid #dee2e6; border-radius: 6px; padding: 8px; text-align: center; }
        .segmento-caixa .kg { font-weight: 700; }
        .tabela-esportes td, .tabela-esportes th { padding: 2px 6px; font-size: 11.5px; }
        table.ficha td, table.ficha th { padding: 4px 8px; font-size: 13px; }
        .folha h4 { border-bottom: 2px solid #212529; padding-bottom: 4px; margin-top: 20px; }

        @media (max-width: 700px) {
            .grid-2 { grid-template-columns: 1fr; }
        }

        @media print {
            body { background: #fff; margin: 0; }
            .barra-acoes { display: none !important; }
            .folha { box-shadow: none; border-radius: 0; margin: 0; padding: 0; max-width: 100%; page-break-after: always; }
            .folha:last-child { page-break-after: auto; }
            @page { size: A4; margin: 12mm; }
        }
    </style>
</head>
<body>

    <div class="barra-acoes d-flex justify-content-between">
        <a href="view.composicao.detalhe.php?id=<?= (int) $v['id_ficha'] ?>" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            <i class="bi bi-printer"></i> Imprimir / Salvar como PDF (frente e verso)
        </button>
    </div>

    <!-- ===================== FRENTE — Composição Corporal ===================== -->
    <div class="folha">
        <div class="cabecalho-marca">
            <div>
                <h1>Ficha de Composição Corporal</h1>
                <div class="text-muted">Relatório de Composição Corporal — Aluno: <strong><?= htmlspecialchars($contexto['nome'] ?? '—') ?></strong></div>
            </div>
            <div class="text-end texto-pequeno">
                <div>Ficha nº <?= (int) $v['id_ficha'] ?></div>
                <div>ID equipamento: <?= htmlspecialchars($v['id_equipamento'] ?: '—') ?></div>
                <div>Altura: <?= fmtN($v['altura'], 1) ?> cm</div>
                <div>Idade ref.: <?= $v['idade_referencia'] ?: '—' ?> anos</div>
                <div>Sexo ref.: <?= $v['sexo_referencia'] == 'F' ? 'Feminino' : ($v['sexo_referencia'] == 'M' ? 'Masculino' : '—') ?></div>
                <div>Data/Hora: <?= date('d/m/Y', strtotime($v['data_teste'])) ?><?= $v['hora_teste'] ? ' ' . substr($v['hora_teste'], 0, 5) : '' ?></div>
            </div>
        </div>

        <div class="grid-2">
            <div>
                <div class="caixa">
                    <h6>Análise da Composição Corporal</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <thead><tr><th>Componentes</th><th class="text-end">Valor</th><th class="text-end">Faixa normal</th></tr></thead>
                        <tbody>
                            <tr><td>Água corporal total (L)</td><td class="text-end"><?= fmtN($v['agua_corporal_total'], 1) ?></td><td class="text-end texto-pequeno"><?= fmtN($v['agua_corporal_min'],1) ?> ~ <?= fmtN($v['agua_corporal_max'],1) ?></td></tr>
                            <tr><td>Proteína (kg)</td><td class="text-end"><?= fmtN($v['proteina'], 1) ?></td><td class="text-end texto-pequeno"><?= fmtN($v['proteina_min'],1) ?> ~ <?= fmtN($v['proteina_max'],1) ?></td></tr>
                            <tr><td>Minerais (kg)</td><td class="text-end"><?= fmtN($v['minerais'], 2) ?></td><td class="text-end texto-pequeno"><?= fmtN($v['minerais_min'],2) ?> ~ <?= fmtN($v['minerais_max'],2) ?></td></tr>
                            <tr><td>Massa de gordura (kg)</td><td class="text-end"><?= fmtN($v['massa_gordura'], 1) ?></td><td class="text-end texto-pequeno"><?= fmtN($v['massa_gordura_min'],1) ?> ~ <?= fmtN($v['massa_gordura_max'],1) ?></td></tr>
                            <tr><td><strong>Peso (kg)</strong></td><td class="text-end"><strong><?= fmtN($v['peso'], 1) ?></strong></td><td class="text-end texto-pequeno"><?= fmtN($v['peso_min'],1) ?> ~ <?= fmtN($v['peso_max'],1) ?></td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="caixa">
                    <h6>Análise Músculo-Gordura</h6>
                    <?php renderBarra('Peso (kg)', $v['peso'], 'kg', $v['peso_min'], $v['peso_max']); ?>
                    <?php renderBarra('Massa Muscular Esquelética (kg)', $v['massa_muscular_esqueletica'], 'kg', $v['mme_min'], $v['mme_max']); ?>
                    <?php renderBarra('Massa de Gordura (kg)', $v['massa_gordura'], 'kg', $v['massa_gordura_min'], $v['massa_gordura_max']); ?>
                </div>

                <div class="caixa">
                    <h6>Análise de Obesidade</h6>
                    <?php renderBarra('IMC (kg/m²)', $v['imc'], '', $v['imc_min'], $v['imc_max']); ?>
                    <?php renderBarra('PGC — % Gordura Corporal', $v['percentual_gordura_corporal'], '%', $v['pgc_min'], $v['pgc_max']); ?>
                </div>
            </div>

            <div>
                <?php if ($v['pontuacao_geral'] !== null): ?>
                    <div class="score-caixa">
                        <div class="texto-pequeno text-uppercase">Pontuação Geral</div>
                        <div class="numero"><?= (int) $v['pontuacao_geral'] ?><span style="font-size:1rem">/100</span></div>
                    </div>
                <?php endif; ?>

                <div class="caixa">
                    <h6>Controle de Peso</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td>Peso ideal</td><td class="text-end"><strong><?= fmtN($v['peso_ideal'],1) ?> kg</strong></td></tr>
                        <tr><td>Controle de peso</td><td class="text-end"><?= fmtN($v['controle_peso'],1) ?> kg</td></tr>
                        <tr><td>Controle de gordura</td><td class="text-end"><?= fmtN($v['controle_gordura'],1) ?> kg</td></tr>
                        <tr><td>Controle muscular</td><td class="text-end"><?= fmtN($v['controle_muscular'],1) ?> kg</td></tr>
                    </table>
                </div>

                <div class="caixa">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td>Relação Cintura-Quadril</td><td class="text-end"><strong><?= fmtN($v['relacao_cintura_quadril'],2) ?></strong></td></tr>
                        <tr><td>Nível de Gordura Visceral</td><td class="text-end"><strong><?= $v['nivel_gordura_visceral'] ?: '—' ?></strong></td></tr>
                        <tr><td>Ângulo de Fase (50kHz)</td><td class="text-end"><strong><?= fmtN($v['angulo_fase'],1) ?>°</strong></td></tr>
                    </table>
                </div>

                <div class="caixa">
                    <h6>Dados Adicionais</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td>Massa Livre de Gordura</td><td class="text-end"><?= fmtN($v['massa_livre_gordura'],1) ?> kg</td></tr>
                        <tr><td>Taxa Metabólica Basal</td><td class="text-end"><?= fmtN($v['taxa_metabolica_basal'],0) ?> kcal</td></tr>
                        <tr><td>Grau de Obesidade</td><td class="text-end"><?= fmtN($v['grau_obesidade'],1) ?>%</td></tr>
                        <tr><td>SMI</td><td class="text-end"><?= fmtN($v['smi'],1) ?> kg/m²</td></tr>
                        <tr><td>Ingestão calórica recomendada</td><td class="text-end"><?= fmtN($v['ingestao_calorica_recomendada'],0) ?> kcal</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <h4>Análise de Massa Magra Segmentar</h4>
        <div class="row row-cols-2 row-cols-md-5 g-2">
            <?php
            $segMagra = [
                'Braço Esq.' => ['seg_magra_braco_esq_kg', 'seg_magra_braco_esq_pct'],
                'Braço Dir.' => ['seg_magra_braco_dir_kg', 'seg_magra_braco_dir_pct'],
                'Tronco'     => ['seg_magra_tronco_kg', 'seg_magra_tronco_pct'],
                'Perna Esq.' => ['seg_magra_perna_esq_kg', 'seg_magra_perna_esq_pct'],
                'Perna Dir.' => ['seg_magra_perna_dir_kg', 'seg_magra_perna_dir_pct'],
            ];
            foreach ($segMagra as $rotulo => [$kg, $pct]):
            ?>
                <div class="col">
                    <div class="segmento-caixa">
                        <div class="texto-pequeno text-muted"><?= $rotulo ?></div>
                        <div class="kg"><?= fmtN($v[$kg], 2) ?> kg</div>
                        <div class="texto-pequeno"><?= fmtN($v[$pct], 1) ?>%</div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <h4>Análise de Gordura Segmentar</h4>
        <div class="row row-cols-2 row-cols-md-5 g-2">
            <?php
            $segGordura = [
                'Braço Esq.' => ['seg_gordura_braco_esq_kg', 'seg_gordura_braco_esq_pct'],
                'Braço Dir.' => ['seg_gordura_braco_dir_kg', 'seg_gordura_braco_dir_pct'],
                'Tronco'     => ['seg_gordura_tronco_kg', 'seg_gordura_tronco_pct'],
                'Perna Esq.' => ['seg_gordura_perna_esq_kg', 'seg_gordura_perna_esq_pct'],
                'Perna Dir.' => ['seg_gordura_perna_dir_kg', 'seg_gordura_perna_dir_pct'],
            ];
            foreach ($segGordura as $rotulo => [$kg, $pct]):
            ?>
                <div class="col">
                    <div class="segmento-caixa">
                        <div class="texto-pequeno text-muted"><?= $rotulo ?></div>
                        <div class="kg"><?= fmtN($v[$kg], 2) ?> kg</div>
                        <div class="texto-pequeno"><?= fmtN($v[$pct], 1) ?>%</div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($gastoCalorico)): ?>
            <h4>Perdas de Calorias do Exercício (30 min) <span class="texto-pequeno text-muted fw-normal">— estimativa por MET x peso</span></h4>
            <table class="table table-sm tabela-esportes mb-0">
                <?php $paresEsportes = array_chunk($gastoCalorico, 4, true); ?>
                <?php foreach ($paresEsportes as $linha): ?>
                    <tr>
                        <?php foreach ($linha as $esporte => $kcal): ?>
                            <td><?= htmlspecialchars($esporte) ?></td><td class="text-end fw-bold"><?= $kcal ?> kcal</td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <?php if (count($historico) >= 2): ?>
            <?php
            $atual = end($historico);
            $anterior = $historico[count($historico) - 2];
            ?>
            <h4>Histórico da Composição Corporal</h4>
            <table class="table table-sm table-bordered ficha mb-0">
                <tr>
                    <th>Data do exame</th>
                    <td><?= date('d/m/y', strtotime($anterior['data_teste'])) ?></td>
                    <td><strong><?= date('d/m/y', strtotime($atual['data_teste'])) ?></strong></td>
                    <th>Progresso</th>
                </tr>
                <?php foreach (['peso' => 'Peso (kg)', 'massa_muscular_esqueletica' => 'MME (kg)', 'percentual_gordura_corporal' => 'PGC (%)'] as $chave => $rotulo): ?>
                    <?php
                    $vAnterior = $anterior[$chave] !== null ? (float) $anterior[$chave] : null;
                    $vAtual = $atual[$chave] !== null ? (float) $atual[$chave] : null;
                    $diff = ($vAnterior !== null && $vAtual !== null) ? $vAtual - $vAnterior : null;
                    ?>
                    <tr>
                        <th><?= $rotulo ?></th>
                        <td><?= fmtN($vAnterior, 1) ?></td>
                        <td><strong><?= fmtN($vAtual, 1) ?></strong></td>
                        <td class="<?= $diff > 0 ? 'text-primary' : ($diff < 0 ? 'text-danger' : '') ?>">
                            <?= $diff !== null ? (($diff > 0 ? '+' : '') . fmtN($diff, 1)) : '—' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <?php if (!empty($v['observacoes'])): ?>
            <h4>Observações</h4>
            <p><?= nl2br(htmlspecialchars($v['observacoes'])) ?></p>
        <?php endif; ?>

        <div class="mt-4 pt-3 border-top d-flex justify-content-between texto-pequeno">
            <div>Professor avaliador: <strong><?= htmlspecialchars($nomeProfessor ?? '—') ?></strong></div>
            <div>Assinatura do avaliador: ______________________________</div>
        </div>
    </div>

    <!-- ===================== VERSO — Anamnese e Medidas ===================== -->
    <div class="folha">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h3 class="mb-0">Ficha de Composição Corporal — Verso</h3>
                <div class="text-muted">Aluno: <strong><?= htmlspecialchars($contexto['nome'] ?? '—') ?></strong></div>
            </div>
            <div class="text-end texto-pequeno">
                <div>Ficha nº <?= (int) $v['id_ficha'] ?> — referente à Avaliação nº <?= (int) $v['fk_avaliacao'] ?></div>
                <div>Data: <?= date('d/m/Y', strtotime($avaliacao['data_avaliacao'])) ?></div>
                <div>Professor: <?= htmlspecialchars($nomeProfessor ?? '—') ?></div>
            </div>
        </div>

        <h4>Sinais vitais e estilo de vida</h4>
        <table class="table table-sm table-bordered ficha">
            <tr><th style="width:40%">Frequência cardíaca</th><td><?= htmlspecialchars($avaliacao['frequencia_cardiaca'] ?: '—') ?></td></tr>
            <tr><th>Pressão arterial</th><td><?= htmlspecialchars($avaliacao['pressao_arterial'] ?: '—') ?></td></tr>
            <tr><th>Sedentário</th><td><?= simNaoFC($avaliacao['sedentario']) ?></td></tr>
            <tr><th>Atividade física praticada</th><td><?= htmlspecialchars($avaliacao['atividade_fisica'] ?: '—') ?></td></tr>
            <tr><th>Tabagismo</th><td><?= simNaoFC($avaliacao['tabagismo']) ?></td></tr>
            <tr><th>Consome álcool</th><td><?= simNaoFC($avaliacao['alcool']) ?></td></tr>
        </table>

        <h4>Anamnese clínica</h4>
        <table class="table table-sm table-bordered ficha">
            <tr><th style="width:40%">Medicação controlada</th><td><?= simNaoFC($avaliacao['medicacao_controlada']) ?> — <?= htmlspecialchars($avaliacao['medicamentos_descricao'] ?: '—') ?></td></tr>
            <tr><th>Problema osteoarticular</th><td><?= simNaoFC($avaliacao['problema_osteoarticular']) ?> — <?= htmlspecialchars($avaliacao['osteoarticular_descricao'] ?: '—') ?></td></tr>
            <tr><th>Problema neuromuscular</th><td><?= simNaoFC($avaliacao['problema_neuromuscular']) ?> — <?= htmlspecialchars($avaliacao['neuromuscular_descricao'] ?: '—') ?></td></tr>
            <tr><th>Problema coronário</th><td><?= simNaoFC($avaliacao['problema_coronario']) ?> — <?= htmlspecialchars($avaliacao['coronario_descricao'] ?: '—') ?></td></tr>
            <tr><th>Problema vascular</th><td><?= simNaoFC($avaliacao['problema_vascular']) ?></td></tr>
            <tr><th>Hospitalização (5 anos)</th><td><?= simNaoFC($avaliacao['hospitalizacao_5_anos']) ?> — <?= htmlspecialchars($avaliacao['hospitalizacao_descricao'] ?: '—') ?></td></tr>
            <tr><th>Cirurgia (5 anos)</th><td><?= simNaoFC($avaliacao['cirurgia_5_anos']) ?> — <?= htmlspecialchars($avaliacao['cirurgia_descricao'] ?: '—') ?></td></tr>
        </table>

        <h4>Medidas antropométricas</h4>
        <table class="table table-sm table-bordered ficha">
            <?php $pares = array_chunk($medidas, 2, true); ?>
            <?php foreach ($pares as $par): ?>
                <tr>
                    <?php foreach ($par as $chave => $rotulo): ?>
                        <th style="width:25%"><?= $rotulo ?></th>
                        <td style="width:25%"><?= fmtN($avaliacao[$chave] ?? null, 2) ?> cm</td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>

        <p class="text-muted mt-4 texto-pequeno">
            Este verso reaproveita a anamnese e as medidas cadastradas na Avaliação Física nº <?= (int) $v['fk_avaliacao'] ?>.
        </p>
    </div>

</body>
</html>
