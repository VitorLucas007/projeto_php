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
            <h3>Perfil</h3>
        </div>

        <div class="card-body">

            <p><b>ID:</b> <?php echo $_SESSION['id']; ?></p>

            <p><b>Nome:</b> <?php echo $_SESSION['nome']; ?></p>

            <p><b>Email:</b> <?php echo $_SESSION['email']; ?></p>

        </div>

    </div>

</div>

<?php include_once('../layouts/view.rodape.php'); ?>