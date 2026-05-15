<?php

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
}

?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>

<div class="container mt-5">

    <div class="card">

        <div class="card-header">
            <h3>Create Usuário</h3>
        </div>

        <div class="card-body">

            <form method="POST" action="../../controllers/controller.cadastro.php">

                <div class="mb-3">
                    <label>Nome</label>
                    <input type="text" name="cad_nome" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="cad_email" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Senha</label>
                    <input type="password" name="cad_senha" class="form-control">
                </div>

                <button class="btn btn-success">
                    Cadastrar
                </button>

            </form>

        </div>

    </div>

</div>

<?php include_once('../layouts/view.rodape.php'); ?>