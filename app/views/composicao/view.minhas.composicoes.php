<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
    exit;
}

include_once('../../models/model.usuario.class.php');

if (($_SESSION['fk_perfil'] ?? null) != usuario::PERFIL_PESSOA) {
    header("Location: ../home/view.home.php");
    exit;
}

include_once('../../models/model.ficha_composicao.class.php');
include_once('../../models/model.aluno.class.php');

$idAluno = aluno::buscarPorPessoa($_SESSION['fk_pessoa']);
$dados = $idAluno ? ficha_composicao::listarPorAluno($idAluno) : [];
?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h2>Minhas Fichas de Composição Corporal</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Data do teste</th>
                            <th>Peso</th>
                            <th>% Gordura</th>
                            <th>IMC</th>
                            <th>Professor</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($dados)): ?>
                        <?php foreach ($dados as $linha): ?>
                            <tr class="cursor-pointer" onclick="window.location='view.composicao.detalhe.php?id=<?= (int) $linha[0] ?>'" style="cursor:pointer">
                                <td><?= (int) $linha[0] ?></td>
                                <td><?= date('d/m/Y', strtotime($linha[1])) ?></td>
                                <td><?= $linha[2] !== null ? number_format($linha[2], 2, ',', '.') . ' kg' : '—' ?></td>
                                <td><?= $linha[3] !== null ? number_format($linha[3], 2, ',', '.') . ' %' : '—' ?></td>
                                <td><?= $linha[4] !== null ? number_format($linha[4], 2, ',', '.') : '—' ?></td>
                                <td><?= htmlspecialchars($linha[5]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">Nenhuma ficha de composição corporal cadastrada ainda.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once('../layouts/view.rodape.php'); ?>
