<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
    exit;
}

include_once('../../models/model.ficha.class.php');

$isProfessor = (isset($_SESSION['fk_perfil']) && ($_SESSION['fk_perfil'] == 1 || $_SESSION['fk_perfil'] == 2));

if ($isProfessor) {
    if (!isset($_GET['id'])) {
        die("ID do aluno não informado na URL.");
    }
    $id_aluno = (int) $_GET['id'];
} else {
    // Se for o aluno logado, busca o id_aluno atrelado à pessoa dele
    include_once('../../models/model.aluno.class.php');
    $fk_pessoa = $_SESSION['fk_pessoa'] ?? 0;
    
    // Busca o ID correto do aluno através do vínculo com a pessoa
    $id_aluno = aluno::buscarPorPessoa($fk_pessoa); 
    
    if (!$id_aluno) {
        die("Erro: Seu cadastro de aluno não foi encontrado no sistema.");
    }
}

// Retorna todas as fichas atreladas a este aluno (como um array de arrays)
$fichas = ficha::listarFichasPorAluno($id_aluno);
?>

<?php include_once('../layouts/view.cabecalho.php'); ?>
<?php include_once('../layouts/view.menu.php'); ?>
<?php include_once('../layouts/sidebar.php'); ?>

<div class="container mt-4 mb-5">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Fichas de Treino</h3>
        <div>
            <?php if ($isProfessor): ?>
                <a href="view.ficha_treino.create.php?id_aluno=<?= $id_aluno ?>" class="btn btn-success btn-sm me-2">
                    <i class="bi bi-plus-circle"></i> Novo Treino
                </a>
                <a href="../avaliacao/view.avaliacao.alunos.php" class="btn btn-outline-dark btn-sm">Voltar para Alunos</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (empty($fichas)): // SE NÃO EXISTIR NENHUM TREINO CADASTRADO ?>
        
        <div class="card shadow-sm">
            <div class="card-body alert alert-warning text-center py-5 mb-0">
                <h4>Nenhum treino cadastrado</h4>
                <p class="mb-4">Este aluno ainda não possui uma ficha de exercícios ativa no sistema.</p>
                
                <?php if ($isProfessor): ?>
                    <a href="view.ficha_treino.create.php?id_aluno=<?= $id_aluno ?>" class="btn btn-success btn-lg">
                        <i class="bi bi-plus-circle"></i> Cadastrar Ficha de Treino
                    </a>
                <?php endif; ?>
            </div>
        </div>

    <?php else: // SE EXISTIREM TREINOS, FAZ UM LOOP PARA RENDERIZAR CADA UM ?>

        <?php foreach ($fichas as $fichaAtual): 
            // Busca os exercícios específicos desta ficha no loop
            $exercicios = ficha::buscarExerciciosDaFicha($fichaAtual[0]);
        ?>
            
            <div class="card shadow-sm mb-4 border-top border-primary border-4">
                <div class="card-body">
                    
                    <div class="mb-3 p-3 bg-light rounded border">
                        <h4 class="text-primary mb-3 text-uppercase fw-bold"><?= htmlspecialchars($fichaAtual[3]) ?></h4>
                        <div class="row">
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Criado em:</strong> <?= date('d/m/Y', strtotime($fichaAtual[4])) ?></p>
                            </div>
                            <div class="col-md-4">
                                <?php if(!empty($fichaAtual[5])): ?>
                                    <p class="mb-1 text-danger"><strong>Validade:</strong> <?= date('d/m/Y', strtotime($fichaAtual[5])) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if(!empty($fichaAtual[6])): ?>
                            <hr>
                            <p class="mb-0"><strong>Observações:</strong> <?= nl2br(htmlspecialchars($fichaAtual[6])) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle text-center mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Exercício / Máquina</th>
                                    <th>Séries</th>
                                    <th>Repetições</th>
                                    <th>Carga</th>
                                    <th>Descanso</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($exercicios)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">Nenhum exercício adicionado a esta ficha.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($exercicios as $ex): ?>
                                        <tr>
                                            <td class="text-start fw-bold"><?= htmlspecialchars($ex[2]) ?></td>
                                            <td><?= htmlspecialchars($ex[3]) ?></td>
                                            <td><?= htmlspecialchars($ex[4]) ?></td>
                                            <td>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($ex[5]) ?></span>
                                            </td>
                                            <td><i class="bi bi-stopwatch"></i> <?= htmlspecialchars($ex[6]) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                  <?php if ($isProfessor): ?>
                        <div class="mt-3 text-end">
                            <a href="view.ficha_treino.edit.php?id_ficha=<?= $fichaAtual[0] ?>&id_aluno=<?= $id_aluno ?>" class="btn btn-warning btn-sm me-2">
                                <i class="bi bi-pencil-square"></i> Editar Treino
                            </a>
                            
                            <!-- Botão que aciona o Modal -->
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalExcluir<?= $fichaAtual[0] ?>">
                                <i class="bi bi-trash"></i> Excluir
                            </button>
                        </div>

                        <!-- Modal de Exclusão (Único para cada ficha gerada no loop) -->
                        <div class="modal fade text-start" id="modalExcluir<?= $fichaAtual[0] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $fichaAtual[0] ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title" id="modalLabel<?= $fichaAtual[0] ?>">Confirmar Exclusão</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Tem certeza que deseja excluir permanentemente o <strong><?= htmlspecialchars($fichaAtual[3]) ?></strong>?</p>
                                        <p class="text-muted small mb-0">Esta ação apagará a ficha e todos os exercícios vinculados a ela. Isso não pode ser desfeito.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não</button>
                                        <!-- Botão SIM faz o direcionamento para o seu controller de delete -->
                                        <a href="../../controllers/controller.ficha_treino.delete.php?id_ficha=<?= $fichaAtual[0] ?>&id_aluno=<?= $id_aluno ?>" class="btn btn-danger">
                                            Sim, excluir
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

<?php include_once('../layouts/view.rodape.php'); ?>