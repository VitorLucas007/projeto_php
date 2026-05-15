<?php

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
}

include_once('../../models/model.usuario.class.php');

$usuario = new usuario('', '', '');

$dados = $usuario->listarUsuarios();

?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>

<div class="container mt-5">

    <div class="card">

        <div class="card-header">
            <h3>Read Usuários</h3>
        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Ações</th>
                </tr>

                <?php

                if (isset($dados)) {

                    foreach ($dados as $valor) {

                        echo "<tr>";

                        echo "<td>" . $valor[0] . "</td>";
                        echo "<td>" . $valor[1] . "</td>";
                        echo "<td>" . $valor[2] . "</td>";

                        echo "<td>
<a href='view.update.php?id=" . $valor[0] . "' class='btn btn-warning btn-sm'>
Editar
</a>

<a href='../../controllers/controller.delete.php?id=" . $valor[0] . "' class='btn btn-danger btn-sm'>
Excluir
</a>
</td>";

                        echo "</tr>";
                    }
                }

                ?>

            </table>

        </div>

    </div>

</div>

<?php include_once('../layouts/view.rodape.php'); ?>