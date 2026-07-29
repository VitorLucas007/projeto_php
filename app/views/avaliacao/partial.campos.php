<?php
/**
 * Partial com os campos do formulário de avaliação.
 * Espera duas variáveis no escopo de quem inclui:
 *   $v         => array associativo com valores atuais (vazio para create)
 *   $prontuarios => array pra popular o select de prontuário
 *   $nomeProfessorResponsavel => nome exibido no campo travado de professor
 *   $readonly  => opcional; quando true, todos os campos ficam desabilitados
 *   (usado na tela de detalhe somente-leitura de OS-6)
 *
 * Helper local pra não repetir htmlspecialchars($v['campo'] ?? '') o tempo todo.
 */
function campo($v, $chave) {
    return htmlspecialchars($v[$chave] ?? '');
}
function marcado($v, $chave) {
    return (!empty($v[$chave]) && $v[$chave] != 0) ? 'checked' : '';
}

$readonly = $readonly ?? false;
$dis = $readonly ? 'disabled' : '';
?>

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Prontuário (Aluno)</label>
        <select name="fk_prontuario" class="form-select" required <?= $dis ?>>
            <option value="">Selecione...</option>
            <?php foreach ($prontuarios as $p): ?>
                <option value="<?= (int) $p[0] ?>" <?= (($v['fk_prontuario'] ?? '') == $p[0]) ? 'selected' : '' ?>>
                    Prontuário #<?= (int) $p[0] ?> — <?= htmlspecialchars($p[1]) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label>Professor responsável</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($nomeProfessorResponsavel ?? '') ?>" disabled>
        <input type="hidden" name="fk_professor" value="<?= (int) ($v['fk_professor'] ?? 0) ?>">
    </div>
</div>

<div class="mb-3">
    <label>Data da avaliação</label>
    <input type="date" name="data_avaliacao" class="form-control" value="<?= campo($v, 'data_avaliacao') ?>" required <?= $dis ?>>
</div>

<h5 class="mt-4">Sinais vitais</h5>
<hr class="mt-1">
<div class="row">
    <div class="col-md-6 mb-3">
        <label>Frequência cardíaca</label>
        <input type="text" name="frequencia_cardiaca" class="form-control" value="<?= campo($v, 'frequencia_cardiaca') ?>" placeholder="ex: 75 bpm" <?= $dis ?>>
    </div>
    <div class="col-md-6 mb-3">
        <label>Pressão arterial</label>
        <input type="text" name="pressao_arterial" class="form-control" value="<?= campo($v, 'pressao_arterial') ?>" placeholder="ex: 12/8" <?= $dis ?>>
    </div>
</div>

<h5 class="mt-4">Estilo de vida</h5>
<hr class="mt-1">
<div class="row">
    <div class="col-md-4 mb-3 form-check">
        <input type="checkbox" name="sedentario" value="1" class="form-check-input" <?= marcado($v, 'sedentario') ?> <?= $dis ?>>
        <label class="form-check-label">Sedentário</label>
    </div>
    <div class="col-md-4 mb-3 form-check">
        <input type="checkbox" name="tabagismo" value="1" class="form-check-input" <?= marcado($v, 'tabagismo') ?> <?= $dis ?>>
        <label class="form-check-label">Tabagismo</label>
    </div>
    <div class="col-md-4 mb-3 form-check">
        <input type="checkbox" name="alcool" value="1" class="form-check-input" <?= marcado($v, 'alcool') ?> <?= $dis ?>>
        <label class="form-check-label">Consome álcool</label>
    </div>
</div>
<div class="mb-3">
    <label>Atividade física praticada</label>
    <input type="text" name="atividade_fisica" class="form-control" value="<?= campo($v, 'atividade_fisica') ?>" <?= $dis ?>>
</div>

<h5 class="mt-4">Anamnese clínica</h5>
<hr class="mt-1">

<div class="mb-3 form-check">
    <input type="checkbox" name="medicacao_controlada" value="1" class="form-check-input" <?= marcado($v, 'medicacao_controlada') ?> <?= $dis ?>>
    <label class="form-check-label">Faz uso de medicação controlada</label>
</div>
<div class="mb-3">
    <label>Quais medicamentos</label>
    <textarea name="medicamentos_descricao" class="form-control" <?= $dis ?>><?= campo($v, 'medicamentos_descricao') ?></textarea>
</div>

<div class="mb-3 form-check">
    <input type="checkbox" name="problema_osteoarticular" value="1" class="form-check-input" <?= marcado($v, 'problema_osteoarticular') ?> <?= $dis ?>>
    <label class="form-check-label">Possui problema osteoarticular</label>
</div>
<div class="mb-3">
    <label>Descrição</label>
    <textarea name="osteoarticular_descricao" class="form-control" <?= $dis ?>><?= campo($v, 'osteoarticular_descricao') ?></textarea>
</div>

<div class="mb-3 form-check">
    <input type="checkbox" name="problema_neuromuscular" value="1" class="form-check-input" <?= marcado($v, 'problema_neuromuscular') ?> <?= $dis ?>>
    <label class="form-check-label">Possui problema neuromuscular</label>
</div>
<div class="mb-3">
    <label>Descrição</label>
    <textarea name="neuromuscular_descricao" class="form-control" <?= $dis ?>><?= campo($v, 'neuromuscular_descricao') ?></textarea>
</div>

<div class="mb-3 form-check">
    <input type="checkbox" name="problema_coronario" value="1" class="form-check-input" <?= marcado($v, 'problema_coronario') ?> <?= $dis ?>>
    <label class="form-check-label">Possui problema coronário</label>
</div>
<div class="mb-3">
    <label>Descrição</label>
    <textarea name="coronario_descricao" class="form-control" <?= $dis ?>><?= campo($v, 'coronario_descricao') ?></textarea>
</div>

<div class="mb-3 form-check">
    <input type="checkbox" name="problema_vascular" value="1" class="form-check-input" <?= marcado($v, 'problema_vascular') ?> <?= $dis ?>>
    <label class="form-check-label">Possui problema vascular</label>
</div>

<div class="mb-3 form-check">
    <input type="checkbox" name="hospitalizacao_5_anos" value="1" class="form-check-input" <?= marcado($v, 'hospitalizacao_5_anos') ?> <?= $dis ?>>
    <label class="form-check-label">Foi hospitalizado nos últimos 5 anos</label>
</div>
<div class="mb-3">
    <label>Descrição</label>
    <textarea name="hospitalizacao_descricao" class="form-control" <?= $dis ?>><?= campo($v, 'hospitalizacao_descricao') ?></textarea>
</div>

<div class="mb-3 form-check">
    <input type="checkbox" name="cirurgia_5_anos" value="1" class="form-check-input" <?= marcado($v, 'cirurgia_5_anos') ?> <?= $dis ?>>
    <label class="form-check-label">Fez cirurgia nos últimos 5 anos</label>
</div>
<div class="mb-3">
    <label>Descrição</label>
    <textarea name="cirurgia_descricao" class="form-control" <?= $dis ?>><?= campo($v, 'cirurgia_descricao') ?></textarea>
</div>

<h5 class="mt-4">Medidas antropométricas (cm)</h5>
<hr class="mt-1">
<div class="row">
    <?php
    $medidas = [
        'torax' => 'Tórax',
        'cintura' => 'Cintura',
        'abdominal' => 'Abdominal',
        'quadril' => 'Quadril',
        'braco_relaxado_direito' => 'Braço relaxado (D)',
        'braco_relaxado_esquerdo' => 'Braço relaxado (E)',
        'braco_contraido_direito' => 'Braço contraído (D)',
        'braco_contraido_esquerdo' => 'Braço contraído (E)',
        'coxa_direita' => 'Coxa (D)',
        'coxa_esquerda' => 'Coxa (E)',
        'panturrilha_direita' => 'Panturrilha (D)',
        'panturrilha_esquerda' => 'Panturrilha (E)',
    ];
    foreach ($medidas as $chave => $rotulo):
    ?>
        <div class="col-md-3 mb-3">
            <label><?= $rotulo ?></label>
            <input type="number" step="0.01" name="<?= $chave ?>" class="form-control" value="<?= campo($v, $chave) ?>" <?= $dis ?>>
        </div>
    <?php endforeach; ?>
</div>
