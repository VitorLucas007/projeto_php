<?php

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
}

?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>

<div class="container mt-4">

    <div class="card">

        <div class="card-body">

            <h2>Bem-vindo <?php echo $_SESSION['nome']; ?></h2>

        </div>

    </div>

</div>

<?php include_once('../layouts/view.rodape.php'); ?>