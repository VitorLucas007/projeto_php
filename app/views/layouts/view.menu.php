<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

    <div class="container">

        <a class="navbar-brand" href="#">Sistema MVC</a>

        <div class="collapse navbar-collapse">

            <ul class="navbar-nav me-auto">

                <li class="nav-item">
                    <a class="nav-link" href="../home/view.home.php">Home</a>
                </li>

                <?php if (($_SESSION['fk_perfil'] ?? null) == 1): // ADMIN ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../admin/view.usuarios.pendentes.php">Aprovações Pendentes</a>
                    </li>
                <?php endif; ?>

            </ul>

            <ul class="navbar-nav">

                <li class="nav-item">
                    <a class="nav-link" href="../profile/view.perfil.php">
                        Perfil
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-warning" href="../../controllers/controller.logout.php">
                        Sair
                    </a>
                </li>

            </ul>

        </div>

    </div>

</nav>