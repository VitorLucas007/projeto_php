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

$dados = $pessoa->buscarPessoa(
    $_GET['id']
);

$linha = $dados[0];

?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4">

    <div class="card">

        <div class="card-header">
            <h2>Editar Pessoa</h2>
        </div>

        <div class="card-body">

            <form
                action="../../controllers/controller.pessoa.update.php"
                method="POST"
            >

                <input
                    type="hidden"
                    name="id_pessoa"
                    value="<?= $linha[0] ?>"
                >

                <div class="mb-3">
                    <label>Nome</label>

                    <input
                        type="text"
                        name="nome"
                        class="form-control"
                        value="<?= $linha[1] ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label>Sexo</label>

                    <select
                        name="sexo"
                        class="form-control"
                    >
                        <option value="M" <?= $linha[2]=='M'?'selected':'' ?>>
                            Masculino
                        </option>

                        <option value="F" <?= $linha[2]=='F'?'selected':'' ?>>
                            Feminino
                        </option>

                        <option value="OUTRO" <?= $linha[2]=='OUTRO'?'selected':'' ?>>
                            Outro
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Data Nascimento</label>

                    <input
                        type="date"
                        name="data_nascimento"
                        class="form-control"
                        value="<?= $linha[3] ?>"
                    >
                </div>

                <div class="mb-3">
                    <label>Profissão</label>

                    <input
                        type="text"
                        name="profissao"
                        class="form-control"
                        value="<?= $linha[4] ?>"
                    >
                </div>

                <div class="mb-3">
                    <label>Contato</label>

                    <input
                        type="text"
                        name="contato"
                        class="form-control"
                        value="<?= $linha[5] ?>"
                    >
                </div>

                <div class="mb-3">
                    <label>Email</label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="<?= $linha[6] ?>"
                    >
                </div>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Atualizar
                </button>

            </form>

        </div>

    </div>

</div>

<?php include_once('../layouts/view.rodape.php'); ?>