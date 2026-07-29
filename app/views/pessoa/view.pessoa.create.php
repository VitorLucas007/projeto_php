<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
    exit;
}

include_once('../../models/model.usuario.class.php');

if (($_SESSION['fk_perfil'] ?? null) != usuario::PERFIL_ROOT) {
    header("Location: ../home/view.home.php");
    exit;
}
?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4">

    <div class="card shadow">

        <div class="card-header">
            <h2>Cadastro de Pessoa</h2>
        </div>

        <div class="card-body">

            <form
                action="../../controllers/controller.pessoa.create.php"
                method="POST"
            >

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Nome
                        </label>

                        <input
                            type="text"
                            name="nome"
                            class="form-control"
                            required
                        >

                    </div>

                    <div class="col-md-3 mb-3">

                        <label class="form-label">
                            Sexo
                        </label>

                        <select
                            name="sexo"
                            class="form-select"
                            required
                        >
                            <option value="">
                                Selecione
                            </option>

                            <option value="M">
                                Masculino
                            </option>

                            <option value="F">
                                Feminino
                            </option>

                            <option value="OUTRO">
                                Outro
                            </option>

                        </select>

                    </div>

                    <div class="col-md-3 mb-3">

                        <label class="form-label">
                            Data de Nascimento
                        </label>

                        <input
                            type="date"
                            name="data_nascimento"
                            class="form-control"
                            required
                        >

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Profissão
                        </label>

                        <input
                            type="text"
                            name="profissao"
                            class="form-control"
                        >

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Contato
                        </label>

                        <input
                            type="text"
                            name="contato"
                            class="form-control"
                        >

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            E-mail
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                        >

                    </div>

                </div>

                <hr>

                <div class="d-flex justify-content-end">

                    <a
                        href="view.pessoa.php"
                        class="btn btn-secondary me-2"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn btn-success"
                    >
                        Salvar Pessoa
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?php include_once('../layouts/view.rodape.php'); ?>