<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
    exit;
}
?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>Histórico de Avaliações Físicas</h2>
            <a href="#" class="btn btn-success btn-sm">Nova Avaliação</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Cód. Prontuário</th>
                            <th>Professor Avaliador</th>
                            <th>Data Avaliação</th>
                            <th>F. Cardíaca</th>
                            <th>P. Arterial</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Prontuário #12</td>
                            <td>Prof. Carlos Souza</td>
                            <td>10/06/2026</td>
                            <td>75 bpm</td>
                            <td>12/8</td>
                            <td>
                                <a href="#" class="btn btn-info btn-sm">Ver Tudo</a>
                                <a href="#" class="btn btn-warning btn-sm">Editar</a>
                                <a href="#" class="btn btn-danger btn-sm">Excluir</a>
                            </td>
                        </tr>
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once('../layouts/view.rodape.php'); ?>