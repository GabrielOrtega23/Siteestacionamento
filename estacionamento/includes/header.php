<?php $paginaAtual = basename($_SERVER['PHP_SELF']); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de Estacionamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">🅿️ Estacionamento</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $paginaAtual === 'index.php' ? 'active fw-bold' : '' ?>" href="index.php">Painel</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $paginaAtual === 'entrada.php' ? 'active fw-bold' : '' ?>" href="entrada.php">Registrar Entrada</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $paginaAtual === 'saida.php' ? 'active fw-bold' : '' ?>" href="saida.php">Registrar Saída</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $paginaAtual === 'registros.php' ? 'active fw-bold' : '' ?>" href="registros.php">Histórico</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $paginaAtual === 'vagas.php' ? 'active fw-bold' : '' ?>" href="vagas.php">Vagas</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container pb-5">
