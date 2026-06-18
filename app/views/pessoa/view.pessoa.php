<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
    exit;
}

include_once('../../models/model.pessoa.class.php');

$pessoa = new pessoa(
    '',
    '',
    '',
    '',
    '',
    ''
);

$dados = $pessoa->listarPessoas();

?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4">

    <div class="card shadow">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h2>Gerenciamento de Pessoas</h2>

            <a
                href="view.pessoa.create.php"
                class="btn btn-success btn-sm"
            >
                Nova Pessoa
            </a>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-striped table-hover align-middle">

                    <thead class="table-dark">

                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Sexo</th>
                            <th>Data Nasc.</th>
                            <th>Contato</th>
                            <th>E-mail</th>
                            <th>Profissão</th>
                            <th width="180">Ações</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php

                    if (!empty($dados)) {

                        foreach ($dados as $linha) {

                    ?>

                        <tr>

                            <td><?= $linha[0] ?></td>

                            <td><?= $linha[1] ?></td>

                            <td><?= $linha[2] ?></td>

                            <td>
                                <?= date('d/m/Y', strtotime($linha[3])) ?>
                            </td>

                            <td><?= $linha[5] ?></td>

                            <td><?= $linha[6] ?></td>

                            <td><?= $linha[4] ?></td>

                            <td>

                                <a
                                    href="view.pessoa.update.php?id=<?= $linha[0] ?>"
                                    class="btn btn-warning btn-sm"
                                >
                                    Editar
                                </a>

                                <a
                                    href="../../controllers/controller.pessoa.delete.php?id=<?= $linha[0] ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Deseja realmente excluir esta pessoa?')"
                                >
                                    Excluir
                                </a>

                            </td>

                        </tr>

                    <?php

                        }

                    } else {

                    ?>

                        <tr>

                            <td colspan="8" class="text-center">

                                Nenhuma pessoa cadastrada.

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