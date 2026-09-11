<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth/view.login.php");
    exit;
}

include_once('../../models/model.ficha.class.php');

$isProfessor = (isset($_SESSION['fk_perfil']) && ($_SESSION['fk_perfil'] == 1 || $_SESSION['fk_perfil'] == 2));

if ($isProfessor) {
    if (!isset($_GET['id_aluno'])) {
        die("ID do aluno não informado.");
    }
    $id_aluno = (int) $_GET['id_aluno'];
} else {
    include_once('../../models/model.aluno.class.php');
    $fk_pessoa = $_SESSION['fk_pessoa'] ?? 0;
    
    $id_aluno = aluno::buscarPorPessoa($fk_pessoa);
    if (!$id_aluno) {
        die("Erro: Seu cadastro de aluno não encontrado.");
    }
}

if (!isset($_GET['id_ficha'])) {
    die("Ficha específica não informada para impressão.");
}

$id_ficha_desejada = (int) $_GET['id_ficha'];
$fichas = ficha::listarFichasPorAluno($id_aluno); 

// Filtra para pegar apenas a ficha selecionada
$fichaImprimir = null;
foreach ($fichas as $f) {
    if ($f[0] == $id_ficha_desejada) {
        $fichaImprimir = $f;
        break;
    }
}

if (!$fichaImprimir) {
    die("Ficha não encontrada.");
}

$exercicios = ficha::buscarExerciciosDaFicha($fichaImprimir[0]); 
$data_impressao = date('d/m/Y');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Ficha de Treino - <?= htmlspecialchars($fichaImprimir[3]) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            color: #212529;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        .print-container {
            max-width: 850px;
            margin: 40px auto;
            background: #ffffff;
            padding: 50px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
        .ficha-header {
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .table th {
            background-color: #f1f3f5 !important;
            color: #495057;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        .table td {
            vertical-align: middle;
            font-size: 0.95rem;
        }
        @media print {
            body {
                background-color: #ffffff;
            }
            .print-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
            @page {
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body>

    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-primary btn-lg shadow-sm me-3">
            <i class="bi bi-printer"></i> Imprimir / Salvar PDF
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary btn-lg shadow-sm">
            Fechar Aba
        </button>
    </div>

    <div class="print-container">
        
        <!-- Cabeçalho -->
        <div class="ficha-header d-flex justify-content-between align-items-end">
            <div>
                <h1 class="text-uppercase fw-bold text-primary mb-1"><?= htmlspecialchars($fichaImprimir[3]) ?></h1>
                <p class="text-muted mb-0 fs-5">Plano de Treinamento</p>
            </div>
            <div class="text-end">
                <p class="mb-1"><strong>Data de Emissão:</strong> <?= $data_impressao ?></p>
                <?php if (!empty($fichaImprimir[5])): ?>
                    <p class="mb-0 text-danger"><strong>Validade:</strong> <?= date('d/m/Y', strtotime($fichaImprimir[5])) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Observações Gerais -->
        <?php if (!empty($fichaImprimir[6])): ?>
            <div class="alert alert-light border border-secondary text-dark mb-4" role="alert">
                <h6 class="alert-heading fw-bold"><i class="bi bi-info-circle"></i> Observações / Objetivos:</h6>
                <p class="mb-0"><?= nl2br(htmlspecialchars($fichaImprimir[6])) ?></p>
            </div>
        <?php endif; ?>

        <!-- Tabela de Exercícios -->
        <h5 class="fw-bold mb-3"><i class="bi bi-card-checklist"></i> Exercícios</h5>
        
        <?php if (empty($exercicios)): ?>
            <p class="text-center text-muted py-4 border rounded">Nenhum exercício cadastrado nesta ficha.</p>
        <?php else: ?>
            <table class="table table-bordered table-striped text-center">
                <thead>
                    <tr>
                        <th class="text-start" style="width: 40%;">Exercício / Máquina</th>
                        <th style="width: 15%;">Séries</th>
                        <th style="width: 15%;">Repetições</th>
                        <th style="width: 15%;">Carga</th>
                        <th style="width: 15%;">Descanso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exercicios as $ex): ?>
                        <tr>
                            <td class="text-start fw-bold"><?= htmlspecialchars($ex[2]) ?></td>
                            <td><?= htmlspecialchars($ex[3]) ?></td>
                            <td><?= htmlspecialchars($ex[4]) ?></td>
                            <td>
                                <?= !empty($ex[5]) ? htmlspecialchars($ex[5]) : '<span class="text-muted">-</span>' ?>
                            </td>
                            <td>
                                <?= !empty($ex[6]) ? '<i class="bi bi-stopwatch"></i> ' . htmlspecialchars($ex[6]) : '<span class="text-muted">-</span>' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="mt-5 text-center text-muted border-top pt-3">
            <p class="mb-0 fw-bold">Tenha um excelente treino!</p>
            <small>Respeite seus limites e procure orientação do instrutor em caso de dúvidas.</small>
        </div>

    </div>

</body>
</html>