<?php include_once('../layouts/view.cabecalho.php'); ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow">
                <div class="card-header">
                    <h3>Cadastrar Unidade</h3>
                    <small class="text-muted">Toda unidade nasce com um administrador responsável.</small>

                    <ul class="nav nav-pills nav-justified mt-3">
                        <li class="nav-item">
                            <a href="../auth/view.cadastro.php" class="nav-link">
                                <i class="bi bi-person me-1"></i> Pessoa
                            </a>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link active">
                                <i class="bi bi-building me-1"></i> Unidade
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="card-body">

                    <?php if (isset($_GET['erro'])): ?>
                        <div class="alert alert-danger py-2">
                            <?php if ($_GET['erro'] === 'campos'): ?>
                                Preencha todos os campos obrigatórios.
                            <?php elseif ($_GET['erro'] === 'senha'): ?>
                                As senhas devem coincidir e ter pelo menos 6 caracteres.
                            <?php elseif ($_GET['erro'] === 'cnpj'): ?>
                                CNPJ inválido. Verifique os números digitados.
                            <?php elseif ($_GET['erro'] === 'cnpj_existe'): ?>
                                Já existe uma unidade cadastrada com esse CNPJ.
                            <?php else: ?>
                                Não foi possível concluir o cadastro. Verifique os dados e tente novamente.
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="../../controllers/controller.unidade.create.php" id="formUnidade">

                        <h5 class="mt-2">Dados da unidade</h5>
                        <hr class="mt-1">

                        <div class="mb-3">
                            <label>Nome da unidade</label>
                            <input type="text" name="unidade_nome" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>CNPJ</label>
                            <input type="text" name="unidade_cnpj" id="unidade_cnpj" class="form-control" placeholder="00.000.000/0000-00" maxlength="18" required>
                            <div id="cnpj_feedback" class="form-text"></div>
                        </div>

                        <div class="mb-3">
                            <label>Endereço</label>
                            <input type="text" name="unidade_endereco" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Telefone</label>
                            <input type="text" name="unidade_telefone" id="unidade_telefone" class="form-control" placeholder="(00) 00000-0000" maxlength="15">
                        </div>

                        <h5 class="mt-4">Dados do administrador</h5>
                        <hr class="mt-1">

                        <div class="mb-3">
                            <label>Nome completo</label>
                            <input type="text" name="admin_nome" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Data de nascimento</label>
                            <input type="date" name="admin_data_nascimento" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>E-mail (será o login)</label>
                            <input type="email" name="admin_email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Senha</label>
                            <input type="password" name="admin_senha" id="admin_senha" class="form-control" minlength="6" required>
                        </div>

                        <div class="mb-3">
                            <label>Confirmar senha</label>
                            <input type="password" name="admin_senha_confirma" id="admin_senha_confirma" class="form-control" minlength="6" required>
                            <div id="senha_feedback" class="form-text"></div>
                        </div>

                        <button type="submit" class="btn btn-success w-100" id="btnCadastrar">Cadastrar Unidade e Administrador</button>

                    </form>

                    <div class="mt-3 text-center">
                        <a href="../auth/view.login.php" class="text-decoration-none small">Já tem uma conta? Entrar</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const cnpjInput = document.getElementById('unidade_cnpj');
    const cnpjFeedback = document.getElementById('cnpj_feedback');
    const telefoneInput = document.getElementById('unidade_telefone');
    const senha = document.getElementById('admin_senha');
    const senhaConfirma = document.getElementById('admin_senha_confirma');
    const senhaFeedback = document.getElementById('senha_feedback');
    const btnCadastrar = document.getElementById('btnCadastrar');

    let cnpjValido = false;
    let senhasCoincidem = false;

    function atualizarBotao() {
        btnCadastrar.disabled = !(cnpjValido && senhasCoincidem);
    }

    cnpjInput.addEventListener('input', function (e) {
        let v = e.target.value.replace(/\D/g, '').slice(0, 14);
        v = v.replace(/(\d{2})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d)/, '$1/$2');
        v = v.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
        e.target.value = v;
    });

    cnpjInput.addEventListener('blur', function () {
        const valor = cnpjInput.value.trim();

        if (valor === '') {
            cnpjFeedback.textContent = '';
            cnpjValido = false;
            atualizarBotao();
            return;
        }

        const formData = new FormData();
        formData.append('cnpj', valor);

        fetch('../../controllers/controller.validar.cnpj.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (!data.valido) {
                cnpjFeedback.textContent = '❌ CNPJ inválido.';
                cnpjFeedback.className = 'form-text text-danger';
                cnpjValido = false;
            } else if (data.existe) {
                cnpjFeedback.textContent = '❌ Este CNPJ já está cadastrado.';
                cnpjFeedback.className = 'form-text text-danger';
                cnpjValido = false;
            } else {
                cnpjFeedback.textContent = '✅ CNPJ válido.';
                cnpjFeedback.className = 'form-text text-success';
                cnpjValido = true;
            }
            atualizarBotao();
        });
    });

    telefoneInput.addEventListener('input', function (e) {
        let v = e.target.value.replace(/\D/g, '').slice(0, 11);
        v = v.replace(/(\d{2})(\d)/, '($1) $2');
        v = v.replace(/(\d{5})(\d{1,4})$/, '$1-$2');
        e.target.value = v;
    });

    function verificarSenhas() {
        if (senhaConfirma.value === '') {
            senhaFeedback.textContent = '';
            senhasCoincidem = false;
        } else if (senha.value === senhaConfirma.value && senha.value.length >= 6) {
            senhaFeedback.textContent = 'Senhas coincidem.';
            senhaFeedback.className = 'form-text text-success';
            senhasCoincidem = true;
        } else {
            senhaFeedback.textContent = 'Senhas não coincidem.';
            senhaFeedback.className = 'form-text text-danger';
            senhasCoincidem = false;
        }
        atualizarBotao();
    }

    senha.addEventListener('input', verificarSenhas);
    senhaConfirma.addEventListener('input', verificarSenhas);
})();
</script>

<?php include_once('../layouts/view.rodape.php'); ?>