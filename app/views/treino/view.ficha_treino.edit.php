<?php
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['fk_perfil']) || !in_array($_SESSION['fk_perfil'], [1, 2])) {
    header("Location: ../auth/view.login.php");
    exit;
}

include_once('../../models/model.ficha.class.php');

$id_ficha = (int) ($_GET['id_ficha'] ?? 0);
$id_aluno = (int) ($_GET['id_aluno'] ?? 0);

if ($id_ficha === 0) {
    die("Ficha não identificada.");
}

// Para usar o método da sua classe, talvez precise de ajustes dependendo de como você busca por ID da ficha
// Aqui presumimos que você tem os dados, mas como a busca original era por aluno, vamos usar o array direto se possível
$fichaAtual = ficha::buscarFichaPorAluno($id_aluno); 
$exercicios = ficha::buscarExerciciosDaFicha($id_ficha);
?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4 mb-5">
    
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Editar Ficha de Treino</h3>
            <a href="view.ficha_treino.php?id=<?= $id_aluno ?>" class="btn btn-outline-dark btn-sm">Cancelar</a>
        </div>

        <div class="card-body">

            <form action="../../controllers/controller.ficha_treino.edit.php" method="POST">
                
                <input type="hidden" name="id_ficha" value="<?= $id_ficha ?>">
                <input type="hidden" name="id_aluno" value="<?= $id_aluno ?>">

                <h5 class="text-secondary border-bottom pb-2 mb-3">Dados da Ficha</h5>
                
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Nome do Treino *</label>
                        <input type="text" name="nome_treino" class="form-control" value="<?= htmlspecialchars($fichaAtual[3] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Data de Validade</label>
                        <input type="date" name="data_validade" class="form-control" value="<?= htmlspecialchars($fichaAtual[5] ?? '') ?>">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Observações Gerais</label>
                    <textarea name="observacoes" class="form-control" rows="2"><?= htmlspecialchars($fichaAtual[6] ?? '') ?></textarea>
                </div>

                <h5 class="text-secondary border-bottom pb-2 mb-3 d-flex justify-content-between align-items-center">
                    Exercícios
                    <button type="button" id="btn-add-exercicio" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus"></i> Adicionar Linha
                    </button>
                </h5>

                <div id="exercicios-container">
                    <?php if (!empty($exercicios)): ?>
                        <?php foreach ($exercicios as $ex): ?>
                            <div class="row mb-3 exercicio-item align-items-end p-3 border rounded bg-light mx-0">
                                <div class="col-md-4 mb-2 mb-md-0">
                                    <label class="form-label small fw-bold">Exercício / Máquina *</label>
                                    <input type="text" name="exercicio[]" class="form-control" value="<?= htmlspecialchars($ex[2]) ?>" required>
                                </div>
                                <div class="col-md-2 mb-2 mb-md-0">
                                    <label class="form-label small fw-bold">Séries</label>
                                    <input type="number" name="series[]" class="form-control" value="<?= htmlspecialchars($ex[3]) ?>">
                                </div>
                                <div class="col-md-2 mb-2 mb-md-0">
                                    <label class="form-label small fw-bold">Repetições</label>
                                    <input type="text" name="repeticoes[]" class="form-control" value="<?= htmlspecialchars($ex[4]) ?>">
                                </div>
                                <div class="col-md-2 mb-2 mb-md-0">
                                    <label class="form-label small fw-bold">Carga</label>
                                    <input type="text" name="carga[]" class="form-control" value="<?= htmlspecialchars($ex[5]) ?>">
                                </div>
                                <div class="col-md-1 mb-2 mb-md-0">
                                    <label class="form-label small fw-bold">Descanso</label>
                                    <input type="text" name="descanso[]" class="form-control" value="<?= htmlspecialchars($ex[6]) ?>">
                                </div>
                                <div class="col-md-1 text-center">
                                    <button type="button" class="btn btn-outline-danger btn-remover-exercicio w-100" title="Remover">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-warning btn-lg px-5">
                        <i class="bi bi-save"></i> Atualizar Ficha
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const container = document.getElementById('exercicios-container');
    const btnAdd = document.getElementById('btn-add-exercicio');

    btnAdd.addEventListener('click', function() {
        const primeiraLinha = container.querySelector('.exercicio-item');
        const novaLinha = primeiraLinha.cloneNode(true);
        
        const inputs = novaLinha.querySelectorAll('input');
        inputs.forEach(input => input.value = '');

        container.appendChild(novaLinha);
    });

    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-remover-exercicio') || e.target.closest('.btn-remover-exercicio')) {
            const linhas = container.querySelectorAll('.exercicio-item');
            if (linhas.length > 1) {
                e.target.closest('.exercicio-item').remove();
            } else {
                alert('A ficha de treino precisa ter pelo menos uma linha de exercício.');
            }
        }
    });
});
</script>

<?php include_once('../layouts/view.rodape.php'); ?>