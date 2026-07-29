<?php
include_once('../../models/model.unidade.class.php');
$unidades = unidade::listarUnidadesAtivas();
?>
<?php include_once('../layouts/view.cabecalho.php'); ?>

<div class="container mt-5 mb-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card">

                <div class="card-header">
                    <h3>Cadastro</h3>
                    <small class="text-muted">Seu acesso ficará pendente até a aprovação do administrador da unidade.</small>

                    <ul class="nav nav-pills nav-justified mt-3">
                        <li class="nav-item">
                            <span class="nav-link active">
                                <i class="bi bi-person me-1"></i> Pessoa
                            </span>
                        </li>
                        <li class="nav-item">
                            <a href="../unidade/view.unidade.create.php" class="nav-link">
                                <i class="bi bi-building me-1"></i> Unidade
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">

                    <?php if (empty($unidades)): ?>
                        <div class="alert alert-warning">
                            Ainda não existe nenhuma unidade cadastrada. Peça para o responsável
                            <a href="../unidade/view.unidade.create.php">cadastrar uma unidade</a> primeiro.
                        </div>
                    <?php else: ?>

                    <?php if (isset($_GET['erro'])): ?>
                        <div class="alert alert-danger py-2">
                            <?php if ($_GET['erro'] === 'campos'): ?>
                                Preencha todos os campos obrigatórios.
                            <?php elseif ($_GET['erro'] === 'senha'): ?>
                                A senha deve ter pelo menos 6 caracteres.
                            <?php elseif ($_GET['erro'] === 'tipo'): ?>
                                Selecione se você é Professor ou Aluno.
                            <?php elseif ($_GET['erro'] === 'cpf'): ?>
                                CPF inválido. Verifique os números digitados.
                            <?php else: ?>
                                Não foi possível concluir o cadastro. Verifique os dados e tente novamente.
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="../../controllers/controller.cadastro.php">

                        <div class="mb-3">
                            <label>Unidade</label>
                            <select name="cad_unidade" class="form-select" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($unidades as $u): ?>
                                    <option value="<?= (int) $u[0] ?>"><?= htmlspecialchars($u[1]) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Eu sou</label>
                            <select name="cad_tipo" id="cad_tipo" class="form-select" required onchange="alternarCampos()">
                                <option value="">Selecione...</option>
                                <option value="PROFESSOR">Professor</option>
                                <option value="ALUNO">Aluno</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Nome completo</label>
                            <input type="text" name="cad_nome" class="form-control" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Sexo</label>
                                <select name="cad_sexo" class="form-select" required>
                                    <option value="M">Masculino</option>
                                    <option value="F">Feminino</option>
                                    <option value="OUTRO">Outro</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Data de nascimento</label>
                                <input type="date" name="cad_data_nascimento" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Contato</label>
                            <input type="text" name="cad_contato" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>E-mail (será o login)</label>
                            <input type="email" name="cad_email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Senha</label>
                            <input type="password" name="cad_senha" class="form-control" minlength="6" required>
                        </div>

                        <div id="campos_professor" style="display:none;">
                            <div class="mb-3">
                                <label>CREF</label>
                                <input type="text" name="cad_cref" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label>Especialidade</label>
                                <input type="text" name="cad_especialidade" class="form-control">
                            </div>
                        </div>

                        <div id="campos_aluno" style="display:none;">
                            <div class="mb-3">
                                <label>CPF</label>
                                <input type="text" name="cad_cpf" id="cad_cpf" class="form-control" placeholder="000.000.000-00" maxlength="14">
                                <div id="cpf_feedback" class="form-text"></div>
                            </div>
                            <div class="mb-3">
                                <label>Observações</label>
                                <textarea name="cad_observacoes" class="form-control"></textarea>
                            </div>
                        </div>

                        <button class="btn btn-success w-100" id="btnCadastrar">Cadastrar</button>

                    </form>

                    <?php endif; ?>

                    <div class="mt-3 text-center">
                        <a href="../auth/view.login.php" class="text-decoration-none small">Já tem uma conta? Entrar</a>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
function alternarCampos() {
    const tipo = document.getElementById('cad_tipo').value;
    document.getElementById('campos_professor').style.display = (tipo === 'PROFESSOR') ? 'block' : 'none';
    document.getElementById('campos_aluno').style.display = (tipo === 'ALUNO') ? 'block' : 'none';
    if (typeof atualizarBotaoCadastro === 'function') atualizarBotaoCadastro();
}

(function () {
    const cpfInput = document.getElementById('cad_cpf');
    const cpfFeedback = document.getElementById('cpf_feedback');
    const btnCadastrar = document.getElementById('btnCadastrar');
    const tipoSelect = document.getElementById('cad_tipo');

    if (!cpfInput || !cpfFeedback || !btnCadastrar || !tipoSelect) return;

    let cpfValido = false;

    function atualizarBotaoCadastro() {
        const precisaCpf = tipoSelect.value === 'ALUNO';
        btnCadastrar.disabled = precisaCpf && !cpfValido;
    }
    window.atualizarBotaoCadastro = atualizarBotaoCadastro;

    cpfInput.addEventListener('input', function (e) {
        let v = e.target.value.replace(/\D/g, '').slice(0, 11);
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        e.target.value = v;
    });

    cpfInput.addEventListener('blur', function () {
        const valor = cpfInput.value.trim();

        if (valor === '') {
            cpfFeedback.textContent = '';
            cpfValido = false;
            atualizarBotaoCadastro();
            return;
        }

        const formData = new FormData();
        formData.append('cpf', valor);

        fetch('../../controllers/controller.validar.cpf.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (!data.valido) {
                cpfFeedback.textContent = '❌ CPF inválido.';
                cpfFeedback.className = 'form-text text-danger';
                cpfValido = false;
            } else if (data.existe) {
                cpfFeedback.textContent = '❌ Este CPF já está cadastrado.';
                cpfFeedback.className = 'form-text text-danger';
                cpfValido = false;
            } else {
                cpfFeedback.textContent = '✅ CPF válido.';
                cpfFeedback.className = 'form-text text-success';
                cpfValido = true;
            }
            atualizarBotaoCadastro();
        });
    });
})();
</script>

<?php include_once('../layouts/view.rodape.php'); ?>