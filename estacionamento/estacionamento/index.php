<?php
require 'config.php';

$totalVagas = $pdo->query("SELECT COUNT(*) FROM vagas")->fetchColumn();
$vagasOcupadas = $pdo->query("SELECT COUNT(*) FROM vagas WHERE status = 'Ocupada'")->fetchColumn();
$vagasLivres = $totalVagas - $vagasOcupadas;
$veiculosHoje = $pdo->query("SELECT COUNT(*) FROM registros WHERE DATE(data_entrada) = CURDATE()")->fetchColumn();

$vagas = $pdo->query("SELECT * FROM vagas ORDER BY tipo, numero_vaga")->fetchAll();

include 'includes/header.php';
?>

<h2 class="mb-4">Painel do Estacionamento</h2>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary shadow-sm">
            <div class="card-body">
                <h6 class="card-title">Total de Vagas</h6>
                <h3><?= $totalVagas ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success shadow-sm">
            <div class="card-body">
                <h6 class="card-title">Vagas Livres</h6>
                <h3><?= $vagasLivres ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger shadow-sm">
            <div class="card-body">
                <h6 class="card-title">Vagas Ocupadas</h6>
                <h3><?= $vagasOcupadas ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info shadow-sm">
            <div class="card-body">
                <h6 class="card-title">Entradas Hoje</h6>
                <h3><?= $veiculosHoje ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Mapa de Vagas</h4>
    <div>
        <a href="entrada.php" class="btn btn-success btn-sm">+ Registrar Entrada</a>
        <a href="saida.php" class="btn btn-warning btn-sm">Registrar Saída</a>
    </div>
</div>

<div class="row g-3">
    <?php foreach ($vagas as $v): ?>
        <div class="col-6 col-md-3 col-lg-2">
            <div class="card card-vaga shadow-sm <?= $v['status'] === 'Livre' ? 'vaga-livre' : 'vaga-ocupada' ?>">
                <div class="card-body text-center py-3">
                    <div class="fs-4 fw-bold"><?= htmlspecialchars($v['numero_vaga']) ?></div>
                    <div class="text-muted small"><?= htmlspecialchars($v['tipo']) ?></div>
                    <span class="badge <?= $v['status'] === 'Livre' ? 'bg-success' : 'bg-danger' ?> mt-1">
                        <?= $v['status'] ?>
                    </span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>
