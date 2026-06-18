<?php

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
    exit; // É uma boa prática adicionar exit após um redirecionamento de header
}

?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4">

    <div class="card">

        <div class="card-body text-center">

            <h2 class="mb-4">Bem-vindo, <?php echo $_SESSION['nome']; ?></h2>

            <p class="text-muted">Abra o menu para acessar as seções.</p>

        </div>

    </div>

</div>

<?php include_once('../layouts/view.rodape.php'); ?>