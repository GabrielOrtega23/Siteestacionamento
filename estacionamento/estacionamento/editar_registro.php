<?php
require 'config.php';

$erro = '';
$sucesso = '';
$registro = null;

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare(
        "SELECT r.*, v.numero_vaga FROM registros r
         JOIN vagas v ON v.id = r.vaga_id
         WHERE r.id = ? AND r.status = 'Ativo'"
    );
    $stmt->execute([$id]);
    $registro = $stmt->fetch();
}

if (!$registro) {
    $erro = 'Registro não encontrado ou já finalizado.';
}

if ($registro && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $placa = strtoupper(trim($_POST['placa'] ?? ''));
    $modelo = trim($_POST['modelo'] ?? '');
    $cor = trim($_POST['cor'] ?? '');

    if ($placa === '') {
        $erro = 'A placa não pode ficar em branco.';
    } else {
        $stmt = $pdo->prepare(
            "SELECT id FROM registros WHERE placa = ? AND status = 'Ativo' AND id != ?"
        );
        $stmt->execute([$placa, $id]);

        if ($stmt->fetch()) {
            $erro = 'Já existe outro veículo ativo com essa placa.';
        } else {
            $pdo->prepare(
                "UPDATE registros SET placa = ?, modelo = ?, cor = ? WHERE id = ?"
            )->execute([$placa, $modelo, $cor, $id]);

            $sucesso = 'Dados do veículo atualizados com sucesso.';

            $stmt = $pdo->prepare(
                "SELECT r.*, v.numero_vaga FROM registros r
                 JOIN vagas v ON v.id = r.vaga_id
                 WHERE r.id = ?"
            );
            $stmt->execute([$id]);
            $registro = $stmt->fetch();
        }
    }
}

include 'includes/header.php';
?>

<h2 class="mb-4">Editar Veículo na Vaga</h2>

<?php if ($erro): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>
<?php if ($sucesso): ?>
    <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
<?php endif; ?>

<?php if ($registro): ?>
    <div class="card shadow-sm" style="max-width: 600px;">
        <div class="card-body">
            <p class="text-muted mb-3">
                Vaga: <strong><?= htmlspecialchars($registro['numero_vaga']) ?></strong> —
                Entrada: <strong><?= date('d/m/Y H:i:s', strtotime($registro['data_entrada'])) ?></strong>
            </p>
            <form method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="id" value="<?= $registro['id'] ?>">

                <div class="mb-3">
                    <label for="placa" class="form-label">Placa do Veículo *</label>
                    <input type="text" class="form-control placa-input" id="placa" name="placa"
                           maxlength="7" value="<?= htmlspecialchars($registro['placa']) ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="modelo" class="form-label">Modelo</label>
                        <input type="text" class="form-control" id="modelo" name="modelo"
                               value="<?= htmlspecialchars($registro['modelo'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="cor" class="form-label">Cor</label>
                        <input type="text" class="form-control" id="cor" name="cor"
                               value="<?= htmlspecialchars($registro['cor'] ?? '') ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                <a href="saida.php" class="btn btn-secondary">Voltar</a>
            </form>
        </div>
    </div>
<?php else: ?>
    <a href="saida.php" class="btn btn-secondary">Voltar</a>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
