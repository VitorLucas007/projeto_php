<?php include_once('../layouts/view.cabecalho.php'); ?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-4">

            <div class="card">

                <div class="card-header">
                    <h3>Login</h3>
                </div>

                <div class="card-body">

                    <form method="POST" action="../../controllers/controller.login.php">

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="login_email" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Senha</label>
                            <input type="password" name="login_senha" class="form-control">
                        </div>

                        <button class="btn btn-primary w-100">
                            Entrar
                        </button>

                    </form>

                    <hr>

                    <a href="view.cadastro.php" class="btn btn-secondary w-100">
                        Cadastrar
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include_once('../layouts/view.rodape.php'); ?>