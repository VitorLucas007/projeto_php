<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
    exit;
}

include_once('../../models/model.usuario.class.php');

if (($_SESSION['fk_perfil'] ?? null) != usuario::PERFIL_PROFESSOR) {
    header("Location: ../home/view.home.php");
    exit;
}

include_once('../../models/model.ficha_composicao.class.php');
include_once('../../models/model.avaliacao.class.php');

$idAluno = (int) ($_GET['id'] ?? 0);

if ($idAluno <= 0) {
    header("Location: ../avaliacao/view.avaliacao.alunos.php");
    exit;
}

$dados = ficha_composicao::listarPorAluno($idAluno);
$avaliacoesDisponiveis = avaliacao::listarPorAluno($idAluno);
$nomeAluno = avaliacao::buscarNomeAluno($idAluno);
?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4 mb-5">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="mb-0">Fichas de Composição Corporal <?= $nomeAluno ? '— ' . htmlspecialchars($nomeAluno) : '' ?></h2>
            <a href="../avaliacao/view.avaliacao.alunos.php" class="btn btn-secondary btn-sm">Voltar</a>
        </div>
        <div class="card-body">

            <?php if (empty($avaliacoesDisponiveis)): ?>
                <div class="alert alert-info">
                    Este aluno ainda não tem nenhuma avaliação física cadastrada. Cadastre uma
                    <a href="../avaliacao/view.avaliacao.create.php">avaliação</a> antes de criar a
                    ficha de composição corporal — ela reaproveita a anamnese e as medidas de lá.
                </div>
            <?php else: ?>
                <form method="GET" action="view.composicao.create.php" class="row g-2 align-items-end mb-4">
                    <div class="col-md-8">
                        <label class="form-label">Vincular nova ficha à avaliação</label>
                        <select name="fk_avaliacao" class="form-select">
                            <?php foreach ($avaliacoesDisponiveis as $av): ?>
                                <option value="<?= (int) $av[0] ?>">
                                    Avaliação #<?= (int) $av[0] ?> — <?= date('d/m/Y', strtotime($av[3])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-plus-circle"></i> Nova ficha de composição corporal
                        </button>
                    </div>
                </form>
            <?php endif; ?>

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
