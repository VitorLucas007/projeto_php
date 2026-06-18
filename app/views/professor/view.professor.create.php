<?php

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
    exit;
}

include_once('../../models/model.professor.class.php');

$professor = new professor('', '', '');

$pessoas = $professor->listarPessoasDisponiveis();

?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4">

    <div class="card">

        <div class="card-header">
            <h2>Novo Professor</h2>
        </div>

        <div class="card-body">

            <form
                action="../../controllers/controller.professor.create.php"
                method="POST"
            >

                <div class="mb-3">

                    <label>Pessoa</label>

                    <select
                        name="fk_pessoa"
                        class="form-control"
                        required
                    >

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

                    <label>CREF</label>

                    <input
                        type="text"
                        name="cref"
                        class="form-control"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label>Especialidade</label>

                    <input
                        type="text"
                        name="especialidade"
                        class="form-control"
                    >

                </div>

                <button
                    type="submit"
                    class="btn btn-success"
                >
                    Salvar
                </button>

            </form>

        </div>

    </div>

</div>

<?php include_once('../layouts/view.rodape.php'); ?>