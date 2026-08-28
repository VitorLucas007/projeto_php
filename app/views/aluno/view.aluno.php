<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
    exit;
}

include_once('../../models/model.aluno.class.php');

$aluno = new aluno('', '');

$alunos = $aluno->listarAlunos($_SESSION['fk_unidade']);
?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4">

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h2>Gerenciamento de Alunos</h2>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-striped table-hover align-middle">

                    <thead class="table-dark">

                        <tr>
                            <th>ID Aluno</th>
                            <th>Nome</th>
                            <th>Observações</th>
                            <th>Ações</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php
                        if (!empty($alunos)) {

                            foreach ($alunos as $linha) {
                        ?>

                                <tr>

                                    <td><?= $linha[0] ?></td>

                                    <td><?= $linha[1] ?></td>

                                    <td><?= $linha[2] ?></td>

                                    <td>

                                        <a
                                            href="view.aluno.update.php?id=<?= $linha[0] ?>"
                                            class="btn btn-warning btn-sm">
                                            Editar
                                        </a>

                                        <a
                                            href="../../controllers/controller.aluno.delete.php?id=<?= $linha[0] ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Deseja excluir este aluno?')">
                                            Excluir
                                        </a>
                                    </td>

                                </tr>

                            <?php
                            }
                        } else {
                            ?>

                            <tr>
                                <td colspan="4" class="text-center">
                                    Nenhum aluno cadastrado.
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