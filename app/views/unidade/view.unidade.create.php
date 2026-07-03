<?php include_once('../layouts/view.cabecalho.php'); ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow">
                <div class="card-header">
                    <h3>Cadastrar Unidade</h3>
                    <small class="text-muted">Toda unidade nasce com um administrador responsável.</small>
                </div>
                <div class="card-body">

                    <?php if (isset($_GET['erro'])): ?>
                        <div class="alert alert-danger py-2">
                            <?php if ($_GET['erro'] === 'campos'): ?>
                                Preencha todos os campos obrigatórios.
                            <?php elseif ($_GET['erro'] === 'senha'): ?>
                                A senha do administrador deve ter pelo menos 6 caracteres.
                            <?php else: ?>
                                Não foi possível concluir o cadastro. Verifique os dados e tente novamente.
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="../../controllers/controller.unidade.create.php">

                        <h5 class="mt-2">Dados da unidade</h5>
                        <hr class="mt-1">

                        <div class="mb-3">
                            <label>Nome da unidade</label>
                            <input type="text" name="unidade_nome" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Endereço</label>
                            <input type="text" name="unidade_endereco" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Telefone</label>
                            <input type="text" name="unidade_telefone" class="form-control">
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
                            <input type="password" name="admin_senha" class="form-control" minlength="6" required>
                        </div>

                        <button class="btn btn-success w-100">Cadastrar Unidade e Administrador</button>

                    </form>

                    <div class="mt-3 text-center">
                        <a href="../auth/view.login.php" class="text-decoration-none small">Já tem uma conta? Entrar</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('../layouts/view.rodape.php'); ?>
