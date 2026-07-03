<?php

session_start();

include_once('../models/model.usuario.class.php');

// Só admin logado pode aprovar/rejeitar, e só da própria unidade
if (!isset($_SESSION['id']) || ($_SESSION['fk_perfil'] ?? null) != usuario::PERFIL_ADMIN) {
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

header("Location: ../views/admin/view.usuarios.pendentes.php");
exit;
