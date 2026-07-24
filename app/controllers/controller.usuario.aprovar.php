<?php

session_start();

include_once('../models/model.usuario.class.php');

$perfilLogado = $_SESSION['fk_perfil'] ?? null;

// Só ADMIN (aprova sua própria unidade) ou ROOT (aprova admins de qualquer
// unidade) podem aprovar/rejeitar contas
if (!isset($_SESSION['id']) || !in_array($perfilLogado, [usuario::PERFIL_ADMIN, usuario::PERFIL_ROOT], true)) {
    header("Location: ../views/auth/view.login.php");
    exit;
}

$id = (int) ($_GET['id'] ?? 0);
$acao = $_GET['acao'] ?? '';

if ($id > 0 && $acao === 'aprovar') {
    usuario::aprovar($id);
} elseif ($id > 0 && $acao === 'rejeitar') {
    usuario::rejeitar($id);
}

if ($perfilLogado == usuario::PERFIL_ROOT) {
    header("Location: ../views/root/view.root.admins.pendentes.php");
} else {
    header("Location: ../views/admin/view.usuarios.pendentes.php");
}

exit;
