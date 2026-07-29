<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once('../../models/model.usuario.class.php');

$qtdPendentesUnidade = 0;
$qtdPendentesAdmin = 0;

if (($_SESSION['fk_perfil'] ?? null) == usuario::PERFIL_ADMIN) {
    $qtdPendentesUnidade = usuario::contarPendentes($_SESSION['fk_unidade']);
} elseif (($_SESSION['fk_perfil'] ?? null) == usuario::PERFIL_ROOT) {
    $qtdPendentesAdmin = usuario::contarPendentesAdmin();
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
                        <a class="nav-link" href="../admin/view.usuarios.pendentes.php">
                            Aprovações Pendentes
                            <?php if ($qtdPendentesUnidade > 0): ?>
                                <span class="badge bg-danger"><?= $qtdPendentesUnidade ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (($_SESSION['fk_perfil'] ?? null) == 4): // ROOT ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../root/view.root.admins.pendentes.php">
                            Unidades Pendentes
                            <?php if ($qtdPendentesAdmin > 0): ?>
                                <span class="badge bg-danger"><?= $qtdPendentesAdmin ?></span>
                            <?php endif; ?>
                        </a>
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