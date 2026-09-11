<?php
/**
 * Partial com os campos do formulário de Ficha de Composição Corporal.
 * Espera no escopo de quem inclui:
 *   $v         => array associativo com valores atuais (vazio para create)
 *   $contexto  => array de ficha_composicao::buscarContextoAvaliacao() (aluno, sexo, data_nascimento, cintura, quadril)
 *   $nomeProfessorResponsavel => nome exibido no campo travado de professor
 *   $readonly  => opcional; quando true, todos os campos ficam desabilitados
 */
function campoFC($v, $chave) {
    return htmlspecialchars($v[$chave] ?? '');
}

$readonly = $readonly ?? false;
$dis = $readonly ? 'disabled' : '';
$sexoPessoa = $contexto['sexo'] === 'F' ? 'F' : 'M'; // OUTRO cai em M por padrão; professor pode trocar a referência
?>

<input type="hidden" name="fk_avaliacao" value="<?= (int) ($contexto['id_avaliacao'] ?? $v['fk_avaliacao'] ?? 0) ?>">
<?php if (!empty($v['id_ficha'])): ?>
    <input type="hidden" name="id_ficha" value="<?= (int) $v['id_ficha'] ?>">
<?php endif; ?>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Aluno</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($contexto['nome'] ?? '') ?>" disabled>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Professor responsável</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($nomeProfessorResponsavel ?? '') ?>" disabled>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-3">
        <label class="form-label">Data do teste</label>
        <input type="date" id="data_teste" name="data_teste" class="form-control" value="<?= campoFC($v, 'data_teste') ?: ($contexto['data_avaliacao'] ?? '') ?>" required <?= $dis ?>>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Hora do teste</label>
        <input type="time" name="hora_teste" class="form-control" value="<?= campoFC($v, 'hora_teste') ?>" <?= $dis ?>>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">ID do equipamento / teste</label>
        <input type="text" name="id_equipamento" class="form-control" placeholder="ex: nº de série InBody" value="<?= campoFC($v, 'id_equipamento') ?>" <?= $dis ?>>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Pontuação geral (0-100)</label>
        <input type="number" min="0" max="100" name="pontuacao_geral" class="form-control" value="<?= campoFC($v, 'pontuacao_geral') ?>" <?= $dis ?>>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-3">
        <label class="form-label">Altura (cm)</label>
        <input type="number" step="0.1" id="altura" name="altura" class="form-control" value="<?= campoFC($v, 'altura') ?>" required <?= $dis ?>>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Sexo de referência (fórmulas)</label>
        <select id="sexo_referencia" name="sexo_referencia" class="form-select" <?= $dis ?>>
            <option value="M" <?= (campoFC($v, 'sexo_referencia') ?: $sexoPessoa) === 'M' ? 'selected' : '' ?>>Masculino</option>
            <option value="F" <?= (campoFC($v, 'sexo_referencia') ?: $sexoPessoa) === 'F' ? 'selected' : '' ?>>Feminino</option>
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Idade de referência (anos)</label>
        <input type="number" id="idade_referencia" name="idade_referencia" class="form-control" value="<?= campoFC($v, 'idade_referencia') ?>" <?= $dis ?>>
        <div class="form-text">Calculada a partir da data de nascimento; pode ajustar.</div>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Fator de atividade</label>
        <select id="fator_atividade" name="fator_atividade" class="form-select" <?= $dis ?>>
            <?php
            $fatores = [
                '1.2'   => 'Sedentário',
                '1.375' => 'Leve (1-3x/semana)',
                '1.55'  => 'Moderado (3-5x/semana)',
                '1.725' => 'Intenso (6-7x/semana)',
                '1.9'   => 'Muito intenso (atleta)',
            ];
            $atual = campoFC($v, 'fator_atividade') ?: '1.55';
            foreach ($fatores as $val => $rotulo):
            ?>
                <option value="<?= $val ?>" <?= (abs((float)$atual - (float)$val) < 0.001) ? 'selected' : '' ?>><?= $rotulo ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<h5 class="mt-4">Análise da Composição Corporal</h5>
<hr class="mt-1">
<div class="row">
    <?php
    $composicao = [
        'agua_corporal_total' => ['Água corporal total (L)', true],
        'proteina'            => ['Proteína (kg)', true],
        'minerais'            => ['Minerais (kg)', true],
        'massa_gordura'       => ['Massa de gordura (kg)', true],
        'peso'                => ['Peso (kg)', true],
    ];
    foreach ($composicao as $chave => [$rotulo, $temFaixa]):
    ?>
        <div class="col-md-4 col-lg-2 mb-3">
            <label class="form-label"><?= $rotulo ?></label>
            <input type="number" step="0.01" id="<?= $chave ?>" name="<?= $chave ?>" class="form-control campo-calc-origem" value="<?= campoFC($v, $chave) ?>" <?= $dis ?>>
            <?php if ($temFaixa): ?>
                <div class="input-group input-group-sm mt-1">
                    <span class="input-group-text">Normal</span>
                    <input type="number" step="0.01" name="<?= $chave ?>_min" placeholder="mín" class="form-control" value="<?= campoFC($v, $chave . '_min') ?>" <?= $dis ?>>
                    <input type="number" step="0.01" name="<?= $chave ?>_max" placeholder="máx" class="form-control" value="<?= campoFC($v, $chave . '_max') ?>" <?= $dis ?>>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<h5 class="mt-4">Análise Músculo-Gordura</h5>
<hr class="mt-1">
<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Massa muscular esquelética — MME (kg)</label>
        <input type="number" step="0.01" id="massa_muscular_esqueletica" name="massa_muscular_esqueletica" class="form-control campo-calc-origem" value="<?= campoFC($v, 'massa_muscular_esqueletica') ?>" <?= $dis ?>>
        <div class="input-group input-group-sm mt-1">
            <span class="input-group-text">Normal</span>
            <input type="number" step="0.01" name="mme_min" placeholder="mín" class="form-control" value="<?= campoFC($v, 'mme_min') ?>" <?= $dis ?>>
            <input type="number" step="0.01" name="mme_max" placeholder="máx" class="form-control" value="<?= campoFC($v, 'mme_max') ?>" <?= $dis ?>>
        </div>
    </div>
</div>

<h5 class="mt-4">Análise de Obesidade</h5>
<hr class="mt-1">
<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">IMC (kg/m²) <span class="badge bg-secondary">calculado</span></label>
        <input type="number" step="0.01" id="imc" name="imc" class="form-control" value="<?= campoFC($v, 'imc') ?>" <?= $dis ?>>
        <div class="input-group input-group-sm mt-1">
            <span class="input-group-text">Normal</span>
            <input type="number" step="0.01" id="imc_min" name="imc_min" placeholder="mín" class="form-control" value="<?= campoFC($v, 'imc_min') ?: '18.5' ?>" <?= $dis ?>>
            <input type="number" step="0.01" id="imc_max" name="imc_max" placeholder="máx" class="form-control" value="<?= campoFC($v, 'imc_max') ?: '24.9' ?>" <?= $dis ?>>
        </div>
        <div class="form-text">Faixa padrão OMS (editável).</div>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">PGC — % Gordura corporal</label>
        <input type="number" step="0.01" id="percentual_gordura_corporal" name="percentual_gordura_corporal" class="form-control campo-calc-origem" value="<?= campoFC($v, 'percentual_gordura_corporal') ?>" <?= $dis ?>>
        <div class="input-group input-group-sm mt-1">
            <span class="input-group-text">Normal</span>
            <input type="number" step="0.01" id="pgc_min" name="pgc_min" placeholder="mín" class="form-control" value="<?= campoFC($v, 'pgc_min') ?>" <?= $dis ?>>
            <input type="number" step="0.01" id="pgc_max" name="pgc_max" placeholder="máx" class="form-control" value="<?= campoFC($v, 'pgc_max') ?>" <?= $dis ?>>
        </div>
        <div class="form-text">Sugestão ACE por sexo — ajustada automaticamente, editável.</div>
    </div>
</div>

<h5 class="mt-4">Controle de Peso</h5>
<hr class="mt-1">
<div class="row">
    <div class="col-md-3 mb-3">
        <label class="form-label">Peso ideal (kg) <span class="badge bg-secondary">calculado</span></label>
        <input type="number" step="0.01" id="peso_ideal" name="peso_ideal" class="form-control" value="<?= campoFC($v, 'peso_ideal') ?>" <?= $dis ?>>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Controle de peso (kg) <span class="badge bg-secondary">calculado</span></label>
        <input type="number" step="0.01" id="controle_peso" name="controle_peso" class="form-control" value="<?= campoFC($v, 'controle_peso') ?>" <?= $dis ?>>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Controle de gordura (kg) <span class="badge bg-secondary">calculado</span></label>
        <input type="number" step="0.01" id="controle_gordura" name="controle_gordura" class="form-control" value="<?= campoFC($v, 'controle_gordura') ?>" <?= $dis ?>>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Controle muscular (kg) <span class="badge bg-secondary">calculado</span></label>
        <input type="number" step="0.01" id="controle_muscular" name="controle_muscular" class="form-control" value="<?= campoFC($v, 'controle_muscular') ?>" <?= $dis ?>>
    </div>
</div>

<h5 class="mt-4">Indicadores adicionais</h5>
<hr class="mt-1">
<div class="row">
    <div class="col-md-3 mb-3">
        <label class="form-label">Relação cintura-quadril <span class="badge bg-secondary">calculado</span></label>
        <input type="number" step="0.01" id="relacao_cintura_quadril" name="relacao_cintura_quadril" class="form-control" value="<?= campoFC($v, 'relacao_cintura_quadril') ?>" <?= $dis ?>>
        <div class="form-text">A partir da cintura/quadril da avaliação vinculada.</div>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Nível de gordura visceral (1-20)</label>
        <input type="number" min="1" max="20" name="nivel_gordura_visceral" class="form-control" value="<?= campoFC($v, 'nivel_gordura_visceral') ?>" <?= $dis ?>>
        <div class="form-text">Fornecido pelo equipamento — sem fórmula pública.</div>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Ângulo de fase (°) — 50kHz</label>
        <input type="number" step="0.1" name="angulo_fase" class="form-control" value="<?= campoFC($v, 'angulo_fase') ?>" <?= $dis ?>>
        <div class="form-text">Fornecido pelo equipamento — sem fórmula pública.</div>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Massa livre de gordura (kg) <span class="badge bg-secondary">calculado</span></label>
        <input type="number" step="0.01" id="massa_livre_gordura" name="massa_livre_gordura" class="form-control" value="<?= campoFC($v, 'massa_livre_gordura') ?>" <?= $dis ?>>
        <div class="input-group input-group-sm mt-1">
            <span class="input-group-text">Normal</span>
            <input type="number" step="0.01" name="mlg_min" placeholder="mín" class="form-control" value="<?= campoFC($v, 'mlg_min') ?>" <?= $dis ?>>
            <input type="number" step="0.01" name="mlg_max" placeholder="máx" class="form-control" value="<?= campoFC($v, 'mlg_max') ?>" <?= $dis ?>>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-3 mb-3">
        <label class="form-label">Taxa metabólica basal (kcal) <span class="badge bg-secondary">calculado</span></label>
        <input type="number" step="0.01" id="taxa_metabolica_basal" name="taxa_metabolica_basal" class="form-control" value="<?= campoFC($v, 'taxa_metabolica_basal') ?>" <?= $dis ?>>
        <div class="input-group input-group-sm mt-1">
            <span class="input-group-text">Normal</span>
            <input type="number" step="0.01" name="tmb_min" placeholder="mín" class="form-control" value="<?= campoFC($v, 'tmb_min') ?>" <?= $dis ?>>
            <input type="number" step="0.01" name="tmb_max" placeholder="máx" class="form-control" value="<?= campoFC($v, 'tmb_max') ?>" <?= $dis ?>>
        </div>
        <div class="form-text">Fórmula de Mifflin-St Jeor.</div>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Grau de obesidade (%) <span class="badge bg-secondary">calculado</span></label>
        <input type="number" step="0.01" id="grau_obesidade" name="grau_obesidade" class="form-control" value="<?= campoFC($v, 'grau_obesidade') ?>" <?= $dis ?>>
        <div class="input-group input-group-sm mt-1">
            <span class="input-group-text">Normal</span>
            <input type="number" step="0.01" name="grau_obesidade_min" placeholder="mín" class="form-control" value="<?= campoFC($v, 'grau_obesidade_min') ?: '90' ?>" <?= $dis ?>>
            <input type="number" step="0.01" name="grau_obesidade_max" placeholder="máx" class="form-control" value="<?= campoFC($v, 'grau_obesidade_max') ?: '110' ?>" <?= $dis ?>>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">SMI (kg/m²) <span class="badge bg-secondary">calculado</span></label>
        <input type="number" step="0.01" id="smi" name="smi" class="form-control" value="<?= campoFC($v, 'smi') ?>" <?= $dis ?>>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Ingestão calórica recomendada (kcal) <span class="badge bg-secondary">calculado</span></label>
        <input type="number" step="0.01" id="ingestao_calorica_recomendada" name="ingestao_calorica_recomendada" class="form-control" value="<?= campoFC($v, 'ingestao_calorica_recomendada') ?>" <?= $dis ?>>
    </div>
</div>

<h5 class="mt-4">Análise de Massa Magra Segmentar</h5>
<hr class="mt-1">
<div class="row">
    <?php
    $segmentosMagra = [
        'seg_magra_braco_esq' => 'Braço esquerdo',
        'seg_magra_braco_dir' => 'Braço direito',
        'seg_magra_tronco'    => 'Tronco',
        'seg_magra_perna_esq' => 'Perna esquerda',
        'seg_magra_perna_dir' => 'Perna direita',
    ];
    foreach ($segmentosMagra as $chave => $rotulo):
    ?>
        <div class="col-md-4 col-lg-2 mb-3">
            <label class="form-label"><?= $rotulo ?></label>
            <div class="input-group input-group-sm">
                <span class="input-group-text">kg</span>
                <input type="number" step="0.01" name="<?= $chave ?>_kg" class="form-control" value="<?= campoFC($v, $chave . '_kg') ?>" <?= $dis ?>>
            </div>
            <div class="input-group input-group-sm mt-1">
                <span class="input-group-text">%</span>
                <input type="number" step="0.1" name="<?= $chave ?>_pct" class="form-control" value="<?= campoFC($v, $chave . '_pct') ?>" <?= $dis ?>>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<h5 class="mt-4">Análise de Gordura Segmentar</h5>
<hr class="mt-1">
<div class="row">
    <?php
    $segmentosGordura = [
        'seg_gordura_braco_esq' => 'Braço esquerdo',
        'seg_gordura_braco_dir' => 'Braço direito',
        'seg_gordura_tronco'    => 'Tronco',
        'seg_gordura_perna_esq' => 'Perna esquerda',
        'seg_gordura_perna_dir' => 'Perna direita',
    ];
    foreach ($segmentosGordura as $chave => $rotulo):
    ?>
        <div class="col-md-4 col-lg-2 mb-3">
            <label class="form-label"><?= $rotulo ?></label>
            <div class="input-group input-group-sm">
                <span class="input-group-text">kg</span>
                <input type="number" step="0.01" name="<?= $chave ?>_kg" class="form-control" value="<?= campoFC($v, $chave . '_kg') ?>" <?= $dis ?>>
            </div>
            <div class="input-group input-group-sm mt-1">
                <span class="input-group-text">%</span>
                <input type="number" step="0.1" name="<?= $chave ?>_pct" class="form-control" value="<?= campoFC($v, $chave . '_pct') ?>" <?= $dis ?>>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="mb-3 mt-4">
    <label class="form-label">Observações</label>
    <textarea name="observacoes" class="form-control" rows="3" <?= $dis ?>><?= campoFC($v, 'observacoes') ?></textarea>
</div>

<?php if (!$readonly): ?>
<script src="../../../public/assets/js/composicao-calculos.js"></script>
<script>
(function () {
    const dataNascimento = <?= json_encode($contexto['data_nascimento'] ?? null) ?>;
    const cinturaAvaliacao = <?= json_encode($contexto['cintura'] ?? null) ?>;
    const quadrilAvaliacao = <?= json_encode($contexto['quadril'] ?? null) ?>;

    const $ = (id) => document.getElementById(id);

    function valor(id) {
        return ComposicaoCalc.paraNumero($(id) ? $(id).value : null);
    }

    function definir(id, resultado) {
        const el = $(id);
        if (el && resultado !== null && resultado !== undefined && !Number.isNaN(resultado)) {
            el.value = Math.round(resultado * 100) / 100;
        }
    }

    function recalcularIdade() {
        if (!dataNascimento) return;
        const dataTeste = $('data_teste') ? $('data_teste').value : null;
        const idade = ComposicaoCalc.idade(dataNascimento, dataTeste);
        if (idade !== null && $('idade_referencia')) {
            $('idade_referencia').value = idade;
        }
    }

    function recalcularPgcPadrao() {
        const sexo = $('sexo_referencia') ? $('sexo_referencia').value : 'M';
        const faixa = ComposicaoCalc.FAIXAS_PADRAO.pgc[sexo] || ComposicaoCalc.FAIXAS_PADRAO.pgc.M;
        if ($('pgc_min') && !$('pgc_min').value) $('pgc_min').value = faixa.min;
        if ($('pgc_max') && !$('pgc_max').value) $('pgc_max').value = faixa.max;
    }

    function recalcularTudo() {
        const peso = valor('peso');
        const altura = valor('altura');
        const massaGordura = valor('massa_gordura');
        const mme = valor('massa_muscular_esqueletica');
        const idade = valor('idade_referencia');
        const sexo = $('sexo_referencia') ? $('sexo_referencia').value : 'M';
        const fatorAtividade = valor('fator_atividade');

        definir('imc', ComposicaoCalc.imc(peso, altura));

        const pesoIdeal = ComposicaoCalc.pesoIdeal(altura, 22);
        definir('peso_ideal', pesoIdeal);

        definir('massa_livre_gordura', ComposicaoCalc.massaLivreGordura(peso, massaGordura));
        definir('grau_obesidade', ComposicaoCalc.grauObesidade(peso, pesoIdeal));
        definir('smi', ComposicaoCalc.smi(mme, altura));

        if (cinturaAvaliacao && quadrilAvaliacao) {
            definir('relacao_cintura_quadril', ComposicaoCalc.relacaoCinturaQuadril(cinturaAvaliacao, quadrilAvaliacao));
        }

        const tmb = ComposicaoCalc.taxaMetabolicaBasal(peso, altura, idade, sexo);
        definir('taxa_metabolica_basal', tmb);
        definir('ingestao_calorica_recomendada', ComposicaoCalc.ingestaoCalorica(tmb, fatorAtividade));

        const pgcAlvo = sexo === 'F' ? 23 : 15;
        const c = ComposicaoCalc.controles(peso, pesoIdeal, massaGordura, pgcAlvo);
        definir('controle_peso', c.controlePeso);
        definir('controle_gordura', c.controleGordura);
        definir('controle_muscular', c.controleMuscular);

        recalcularPgcPadrao();
    }

    ['peso', 'altura', 'massa_gordura', 'massa_muscular_esqueletica', 'idade_referencia', 'sexo_referencia', 'fator_atividade']
        .forEach(id => {
            const el = $(id);
            if (el) el.addEventListener('input', recalcularTudo);
        });

    if ($('data_teste')) {
        $('data_teste').addEventListener('change', function () {
            recalcularIdade();
            recalcularTudo();
        });
    }

    recalcularIdade();
    recalcularTudo();
})();
</script>
<?php endif; ?>
