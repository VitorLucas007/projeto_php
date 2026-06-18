<?php

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
    exit;
}

include_once('../../models/model.aluno.class.php');

$aluno = new aluno('', '');

$pessoas = $aluno->listarPessoasDisponiveis();

?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4">

    <div class="card">

        <div class="card-header">
            <h2>Novo Aluno</h2>
        </div>

        <div class="card-body">

            <form
                action="../../controllers/controller.aluno.create.php"
                method="POST">

                <div class="mb-3">

                    <label>Pessoa</label>

                    <select
                        name="fk_pessoa"
                        class="form-control"
                        required>

                        <option value="">
                            Selecione
                        </option>

                        <?php
                        foreach ($pessoas as $p) {
                        ?>

                            <option value="<?= $p[0] ?>">
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
                        rows="4"></textarea>

                </div>

                <button
                    type="submit"
                    class="btn btn-success">
                    Salvar
                </button>

            </form>

        </div>

    </div>

</div>

<?php include_once('../layouts/view.rodape.php'); ?>