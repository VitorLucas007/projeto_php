<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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
      <a href="../pessoa/view.pessoa.php" class="list-group-item list-group-item-action">Pessoa</a>
      <a href="../professor/view.professor.php" class="list-group-item list-group-item-action">Professor</a>
      <a href="../aluno/view.aluno.php" class="list-group-item list-group-item-action">Aluno</a>
      <a href="../avaliacao/view.avaliacao.php" class="list-group-item list-group-item-action">Avaliação</a>
    </div>
  </div>
</div>
