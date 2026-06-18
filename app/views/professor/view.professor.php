<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
    exit;
}

include_once('../../models/model.professor.class.php');

$professor = new professor('', '', '');

$professores = $professor->listarProfessores();
?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4">

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h2>Gerenciamento de Professores</h2>

            <a
                href="view.professor.create.php"
                class="btn btn-success btn-sm">
                Novo Professor
            </a>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-striped table-hover align-middle">

                    <thead class="table-dark">

                        <tr>
                            <th>ID Professor</th>
                            <th>Nome do Professor</th>
                            <th>CREF</th>
                            <th>Especialidade</th>
                            <th>Ações</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php
                        if (!empty($professores)) {

                            foreach ($professores as $linha) {
                        ?>

                                <tr>

                                    <td><?= $linha[0] ?></td>

                                    <td><?= $linha[1] ?></td>

                                    <td><?= $linha[2] ?></td>

                                    <td><?= $linha[3] ?></td>

                                    <td>

                                        <a
                                            href="view.professor.update.php?id=<?= $linha[0] ?>"
                                            class="btn btn-warning btn-sm">
                                            Editar
                                        </a>

                                        <a
                                            href="../../controllers/controller.professor.delete.php?id=<?= $linha[0] ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Deseja excluir este professor?')">
                                            Excluir
                                        </a>

                                    </td>

                                </tr>

                        <?php
                            }
                        } else {
                        ?>

                            <tr>

                                <td colspan="5" class="text-center">
                                    Nenhum professor cadastrado.
                                </td>

                            </tr>

                        <?php
                        }
                        ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php include_once('../layouts/view.rodape.php'); ?>