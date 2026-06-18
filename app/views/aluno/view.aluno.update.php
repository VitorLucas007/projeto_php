<?php

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
    exit;
}

include_once('../../models/model.aluno.class.php');
include_once('../../models/model.pessoa.class.php');

$aluno = new aluno('', '');

$dados = $aluno->buscarAluno(
    $_GET['id']
);

$linha = $dados[0];

$pessoa = new pessoa('', '', '', '', '', '');

$pessoas = $pessoa->listarPessoas();

?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4">

    <div class="card">

        <div class="card-header">
            <h2>Editar Aluno</h2>
        </div>

        <div class="card-body">

            <form
                action="../../controllers/controller.aluno.update.php"
                method="POST">

                <input
                    type="hidden"
                    name="id_aluno"
                    value="<?= $linha[0] ?>">

                <div class="mb-3">

                    <label>Pessoa</label>

                    <select
                        name="fk_pessoa"
                        class="form-control">

                        <?php
                        foreach ($pessoas as $p) {
                        ?>

                            <option
                                value="<?= $p[0] ?>"
                                <?= $p[0] == $linha[1] ? 'selected' : '' ?>>

                                <?= $p[1] ?>

                            </option>

                        <?php
                        }
                        ?>

                    </select>

                </div>

                <div class="mb-3">

                    <label>Observações</label>

                    <textarea
                        name="observacoes"
                        class="form-control"
                        rows="4"><?= $linha[2] ?></textarea>

                </div>

                <button
                    type="submit"
                    class="btn btn-primary">
                    Atualizar
                </button>

            </form>

        </div>

    </div>

</div>

<?php include_once('../layouts/view.rodape.php'); ?>