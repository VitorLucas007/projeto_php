<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$perfil = $_SESSION['fk_perfil'] ?? null;

$ehAdmin = ($perfil == 1);
$ehProfessor = ($perfil == 2);
$ehAluno = ($perfil == 3);
$ehRoot = ($perfil == 4);

?>

<!-- Offcanvas trigger -->
<div class="d-flex justify-content-start">
    <button class="btn btn-primary m-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu">
        ☰ Menu
    </button>
</div>

<!-- Offcanvas sidebar -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasMenuLabel">Menu</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="list-group">

        <?php if ($ehAdmin): ?>
            <a href="../professor/view.professor.php" class="list-group-item list-group-item-action">Professor</a>
            <a href="../aluno/view.aluno.php" class="list-group-item list-group-item-action">Aluno</a>
        <?php endif; ?>

        <?php if ($ehProfessor): ?>
            <a href="../avaliacao/view.avaliacao.php" class="list-group-item list-group-item-action">Avaliação</a>
            <a href="../avaliacao/view.avaliacao.alunos.php" class="list-group-item list-group-item-action">Alunos</a>
        <?php endif; ?>

        <?php if ($ehAluno): ?>
            <a href="../avaliacao/view.minhas.avaliacoes.php" class="list-group-item list-group-item-action">Minhas Avaliações</a>
            <a href="../treino/view.ficha.treino.php" class="list-group-item list-group-item-action">Meus Treinos</a>
        <?php endif; ?>

        <?php if ($ehRoot): ?>
            <a href="../root/view.root.admins.pendentes.php" class="list-group-item list-group-item-action">Unidades Pendentes</a>
            <a href="../pessoa/view.pessoa.php" class="list-group-item list-group-item-action">Pessoas</a>
        <?php endif; ?>

    </div>
  </div>
</div>