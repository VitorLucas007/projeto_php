<?php
session_start();

// Verifica se o usuário está logado e se é Admin (1) ou Professor (2)
if (!isset($_SESSION['id']) || !isset($_SESSION['fk_perfil']) || !in_array($_SESSION['fk_perfil'], [1, 2])) {
    header("Location: ../auth/view.login.php");
    exit;
}

// Captura o ID do aluno passado pela URL
$id_aluno = (int) ($_GET['id_aluno'] ?? 0);

if ($id_aluno === 0) {
    die("Erro: Aluno não identificado. Volte e tente novamente.");
}
?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4 mb-5">
    
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Cadastrar Nova Ficha de Treino</h3>
            <a href="view.ficha_treino.php?id=<?= $id_aluno ?>" class="btn btn-outline-light btn-sm">Cancelar</a>
        </div>

        <div class="card-body">
            
            <?php if (isset($_GET['erro']) && $_GET['erro'] === 'campos'): ?>
                <div class="alert alert-danger">
                    Preencha os dados obrigatórios da ficha (Nome do Treino).
                </div>
            <?php endif; ?>

            <form action="../../controllers/controller.ficha_treino.create.php" method="POST">
                
                <!-- ID do aluno oculto para enviar ao controller -->
                <input type="hidden" name="id_aluno" value="<?= $id_aluno ?>">

                <h5 class="text-secondary border-bottom pb-2 mb-3">Dados da Ficha</h5>
                
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Nome do Treino *</label>
                        <input type="text" name="nome_treino" class="form-control" placeholder="Ex: Treino A - Peito e Tríceps, Adaptação..." required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Data de Validade</label>
                        <input type="date" name="data_validade" class="form-control">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Observações Gerais</label>
                    <textarea name="observacoes" class="form-control" rows="2" placeholder="Restrições médicas, foco do treino, aquecimento..."></textarea>
                </div>

                <h5 class="text-secondary border-bottom pb-2 mb-3 d-flex justify-content-between align-items-center">
                    Exercícios
                    <button type="button" id="btn-add-exercicio" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus"></i> Adicionar Linha
                    </button>
                </h5>

                <!-- Container onde as linhas de exercícios serão adicionadas dinamicamente -->
                <div id="exercicios-container">
                    
                    <!-- Linha Padrão (Item 1) -->
                    <div class="row mb-3 exercicio-item align-items-end p-3 border rounded bg-light mx-0">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="form-label small fw-bold">Exercício / Máquina *</label>
                            <input type="text" name="exercicio[]" class="form-control" placeholder="Ex: Supino Reto" required>
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <label class="form-label small fw-bold">Séries</label>
                            <input type="number" name="series[]" class="form-control" placeholder="Ex: 3">
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <label class="form-label small fw-bold">Repetições</label>
                            <input type="text" name="repeticoes[]" class="form-control" placeholder="Ex: 10 a 12">
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <label class="form-label small fw-bold">Carga</label>
                            <input type="text" name="carga[]" class="form-control" placeholder="Ex: 20kg">
                        </div>
                        <div class="col-md-1 mb-2 mb-md-0">
                            <label class="form-label small fw-bold">Descanso</label>
                            <input type="text" name="descanso[]" class="form-control" placeholder="60s">
                        </div>
                        <div class="col-md-1 text-center">
                            <button type="button" class="btn btn-outline-danger btn-remover-exercicio w-100" title="Remover">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>

                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-success btn-lg px-5">
                        <i class="bi bi-save"></i> Salvar Ficha de Treino
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

    // Função para adicionar uma nova linha de exercício
    btnAdd.addEventListener('click', function() {
        // Pega a primeira linha de exercício existente para clonar
        const primeiraLinha = container.querySelector('.exercicio-item');
        const novaLinha = primeiraLinha.cloneNode(true);
        
        // Limpa os valores dos inputs clonados
        const inputs = novaLinha.querySelectorAll('input');
        inputs.forEach(input => {
            input.value = '';
        });

        // Adiciona a nova linha no container
        container.appendChild(novaLinha);
    });

    // Função para remover uma linha de exercício
    container.addEventListener('click', function(e) {
        // Verifica se o clique foi no botão de remover ou no ícone da lixeira dentro dele
        if (e.target.classList.contains('btn-remover-exercicio') || e.target.closest('.btn-remover-exercicio')) {
            const linhas = container.querySelectorAll('.exercicio-item');
            
            // Impede que o usuário apague a última linha que sobrou
            if (linhas.length > 1) {
                const linhaAtual = e.target.closest('.exercicio-item');
                linhaAtual.remove();
            } else {
                alert('A ficha de treino precisa ter pelo menos uma linha de exercício.');
            }
        }
    });
});
</script>

<?php include_once('../layouts/view.rodape.php'); ?>