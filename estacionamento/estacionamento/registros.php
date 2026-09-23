<?php
require 'config.php';

$filtroPlaca = trim($_GET['placa'] ?? '');

$sql = "SELECT r.*, v.numero_vaga FROM registros r JOIN vagas v ON v.id = r.vaga_id WHERE r.status = 'Finalizado'";
$params = [];

if ($filtroPlaca !== '') {
    $sql .= " AND r.placa LIKE ?";
    $params[] = '%' . strtoupper($filtroPlaca) . '%';
}

$sql .= " ORDER BY r.data_saida DESC LIMIT 200";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$registros = $stmt->fetchAll();

$totalArrecadado = $pdo->query("SELECT COALESCE(SUM(valor_total),0) FROM registros WHERE status = 'Finalizado'")->fetchColumn();

include 'includes/header.php';
?>

<h2 class="mb-4">Histórico de Movimentações</h2>

<div class="card shadow-sm mb-3">
    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="placa" class="form-control" placeholder="Filtrar por placa"
                   value="<?= htmlspecialchars($filtroPlaca) ?>">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </form>
        <div class="fs-5">
            Total arrecadado: <strong class="text-success">R$ <?= number_format($totalArrecadado, 2, ',', '.') ?></strong>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover bg-white shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>Placa</th>
                <th>Modelo</th>
                <th>Vaga</th>
                <th>Entrada</th>
                <th>Saída</th>
                <th>Permanência</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registros as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['placa']) ?></td>
                    <td><?= htmlspecialchars($r['modelo'] ?: '-') ?></td>
                    <td><?= htmlspecialchars($r['numero_vaga']) ?></td>
                    <td><?= date('d/m/Y H:i:s', strtotime($r['data_entrada'])) ?></td>
                    <td><?= date('d/m/Y H:i:s', strtotime($r['data_saida'])) ?></td>
                    <td><?= htmlspecialchars($r['tempo_permanencia']) ?></td>
                    <td>R$ <?= number_format($r['valor_total'], 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($registros)): ?>
                <tr><td colspan="7" class="text-center text-muted">Nenhum registro encontrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
