<?php
require 'config.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $placa   = strtoupper(trim($_POST['placa'] ?? ''));
    $modelo  = trim($_POST['modelo'] ?? '');
    $cor     = trim($_POST['cor'] ?? '');
    $vagaId  = (int) ($_POST['vaga_id'] ?? 0);

    if ($placa === '' || $vagaId === 0) {
        $erro = 'Preencha a placa e selecione uma vaga disponível.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM registros WHERE placa = ? AND status = 'Ativo'");
        $stmt->execute([$placa]);

        if ($stmt->fetch()) {
            $erro = 'Já existe um registro de entrada ativo para esta placa.';
        } else {
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO registros (placa, modelo, cor, vaga_id, data_entrada, status)
                     VALUES (?, ?, ?, ?, NOW(), 'Ativo')"
                );
                $stmt->execute([$placa, $modelo, $cor, $vagaId]);

                $pdo->prepare("UPDATE vagas SET status = 'Ocupada' WHERE id = ?")->execute([$vagaId]);

                $pdo->commit();
                $sucesso = "Entrada registrada com sucesso para a placa {$placa}.";
            } catch (Exception $e) {
                $pdo->rollBack();
                $erro = 'Erro ao registrar entrada: ' . $e->getMessage();
            }
        }
    }
}

$vagasLivres = $pdo->query("SELECT * FROM vagas WHERE status = 'Livre' ORDER BY tipo, numero_vaga")->fetchAll();

include 'includes/header.php';
?>

<h2 class="mb-4">Registrar Entrada de Veículo</h2>

<?php if ($erro): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>
<?php if ($sucesso): ?>
    <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
<?php endif; ?>

<div class="card shadow-sm" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="placa" class="form-label">Placa do Veículo *</label>
                <input type="text" class="form-control placa-input" id="placa" name="placa"
                       maxlength="7" placeholder="ABC1D23" required>
                <div class="invalid-feedback">Informe a placa do veículo.</div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="modelo" class="form-label">Modelo</label>
                    <input type="text" class="form-control" id="modelo" name="modelo" placeholder="Ex: Honda Civic">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="cor" class="form-label">Cor</label>
                    <input type="text" class="form-control" id="cor" name="cor" placeholder="Ex: Prata">
                </div>
            </div>

            <div class="mb-3">
                <label for="vaga_id" class="form-label">Vaga Disponível *</label>
                <select class="form-select" id="vaga_id" name="vaga_id" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($vagasLivres as $v): ?>
                        <option value="<?= $v['id'] ?>">
                            <?= htmlspecialchars($v['numero_vaga']) ?> (<?= htmlspecialchars($v['tipo']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Selecione uma vaga.</div>
                <?php if (empty($vagasLivres)): ?>
                    <div class="text-danger small mt-1">Não há vagas livres no momento.</div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-success" <?= empty($vagasLivres) ? 'disabled' : '' ?>>
                Registrar Entrada
            </button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
