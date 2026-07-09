<?php include_once('../layouts/view.cabecalho.php'); ?>

<div class="container-fluid vh-100 login-page">
    <div class="row g-0 h-100">
        <div class="col-lg-4 d-flex align-items-center justify-content-center">
            <div class="login-card card shadow border-0 p-4 p-sm-5 w-100 mx-3">
                <div class="mb-4 text-center">
                    <div class="logo badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-4 py-2 mb-3">
                        <i class="bi bi-heart-pulse-fill me-2"></i> SGA
                    </div>
                    <h1 class="h3 fw-semibold mb-2">Sistema de Gestão Acadêmica e Avaliações</h1>
                    <p class="text-muted mb-0">Gerencie alunos, professores, prontuários e avaliações físicas em uma única plataforma.</p>
                </div>

                <?php if (isset($_GET['erro']) && $_GET['erro'] == 1): ?>
                    <div class="alert alert-danger py-2" role="alert">
                        Usuário ou senha incorretos.
                    </div>
                <?php elseif (isset($_GET['erro']) && $_GET['erro'] === 'pendente'): ?>
                    <div class="alert alert-warning py-2" role="alert">
                        Seu cadastro ainda está aguardando aprovação do administrador da unidade.
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['erro_unidade'])): ?>
                    <div class="alert alert-danger py-2" role="alert">
                        CNPJ ou senha incorretos.
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['cadastro']) && $_GET['cadastro'] === 'pendente'): ?>
                    <div class="alert alert-success py-2" role="alert">
                        Cadastro realizado! Assim que o administrador da unidade aprovar, você poderá entrar.
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['unidade_criada'])): ?>
                    <div class="alert alert-success py-2" role="alert">
                        Unidade cadastrada com sucesso! Entre com o CNPJ e senha do administrador.
                    </div>
                <?php endif; ?>

                <ul class="nav nav-pills nav-justified mb-4" id="loginTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-pessoa-btn" data-bs-toggle="pill" data-bs-target="#tab-pessoa" type="button" role="tab" aria-controls="tab-pessoa" aria-selected="true">
                            <i class="bi bi-person me-1"></i> Pessoa
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-unidade-btn" data-bs-toggle="pill" data-bs-target="#tab-unidade" type="button" role="tab" aria-controls="tab-unidade" aria-selected="false">
                            <i class="bi bi-building me-1"></i> Unidade
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="loginTabContent">
                    <div class="tab-pane fade show active" id="tab-pessoa" role="tabpanel" aria-labelledby="tab-pessoa-btn">
                        <form method="POST" action="../../controllers/controller.login.php">
                            <div class="mb-3">
                                <label for="login_email" class="form-label small text-uppercase text-muted">Email</label>
                                <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden">
                                    <span class="input-group-text bg-white border-0 text-primary"><i class="bi bi-person"></i></span>
                                    <input id="login_email" type="email" name="login_email" class="form-control border-0" placeholder="Digite seu e-mail" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="login_senha" class="form-label small text-uppercase text-muted">Senha</label>
                                <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden">
                                    <span class="input-group-text bg-white border-0 text-primary"><i class="bi bi-lock"></i></span>
                                    <input id="login_senha" type="password" name="login_senha" class="form-control border-0" placeholder="Digite sua senha" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100">Entrar</button>
                        </form>

                        <div class="mt-4 text-center">
                            <a href="view.cadastro.php" class="text-decoration-none text-secondary small">Ainda não tem conta? Cadastre-se</a>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-unidade" role="tabpanel" aria-labelledby="tab-unidade-btn">
                        <form method="POST" action="../../controllers/controller.login.unidade.php">
                            <div class="mb-3">
                                <label for="login_cnpj" class="form-label small text-uppercase text-muted">CNPJ</label>
                                <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden">
                                    <span class="input-group-text bg-white border-0 text-primary"><i class="bi bi-building"></i></span>
                                    <input id="login_cnpj" type="text" name="login_cnpj" class="form-control border-0" placeholder="00.000.000/0000-00" maxlength="18" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="login_senha_unidade" class="form-label small text-uppercase text-muted">Senha</label>
                                <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden">
                                    <span class="input-group-text bg-white border-0 text-primary"><i class="bi bi-lock"></i></span>
                                    <input id="login_senha_unidade" type="password" name="login_senha" class="form-control border-0" placeholder="Digite sua senha" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100">Entrar</button>
                        </form>

                        <div class="mt-4 text-center">
                            <a href="../unidade/view.unidade.create.php" class="text-decoration-none text-secondary small">Cadastrar nova unidade</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 d-none d-lg-block overflow-hidden">
            <div class="h-100 bg-image">
                <img src="../../../public/assets/img/doodle-login.png" alt="Ilustração de gestão acadêmica" class="img-fluid w-100 h-100 object-fit-cover">
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('login_cnpj').addEventListener('input', function (e) {
    let v = e.target.value.replace(/\D/g, '').slice(0, 14);
    v = v.replace(/(\d{2})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d)/, '$1/$2');
    v = v.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
    e.target.value = v;
});
</script>

<?php include_once('../layouts/view.rodape.php'); ?>
