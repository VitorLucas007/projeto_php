<?php

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
}

include_once('../../models/model.usuario.class.php');

$usuario = new usuario('', '', '');

$dados = $usuario->buscarUsuario($_GET['id']);

?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>

<div class="container mt-5">

    <div class="card">

        <div class="card-header">
            <h3>Update Usuário</h3>
        </div>

        <div class="card-body">

            <form method="POST" action="../../controllers/controller.update.php">

                <input type="hidden" name="id" value="<?php echo $dados[0][0]; ?>">

                <div class="mb-3">
                    <label>Nome</label>
                    <input type="text" name="nome" class="form-control" value="<?php echo $dados[0][1]; ?>">
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo $dados[0][2]; ?>">
                </div>

                <button class="btn btn-warning">
                    Atualizar
                </button>

            </form>

        </div>

    </div>

</div>

<?php include_once('../layouts/view.rodape.php'); ?>