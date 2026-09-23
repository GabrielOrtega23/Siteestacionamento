<?php
require 'config.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar'])) {
    $numero = strtoupper(trim($_POST['numero_vaga'] ?? ''));
    $tipo = $_POST['tipo'] ?? 'Carro';

    if ($numero === '') {
        $erro = 'Informe o número da vaga.';
    } else {
        try {
            $pdo->prepare("INSERT INTO vagas (numero_vaga, tipo) VALUES (?, ?)")->execute([$numero, $tipo]);
            $sucesso = "Vaga {$numero} adicionada.";
        } catch (Exception $e) {
            $erro = 'Já existe uma vaga com esse número.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir'])) {
    $id = (int) $_POST['vaga_id'];
    $stmt = $pdo->prepare("SELECT status FROM vagas WHERE id = ?");
    $stmt->execute([$id]);
    $vaga = $stmt->fetch();

    if ($vaga && $vaga['status'] === 'Livre') {
        try {
            $pdo->prepare("DELETE FROM vagas WHERE id = ?")->execute([$id]);
            $sucesso = 'Vaga excluída.';
        } catch (Exception $e) {
            $erro = 'Não é possível excluir: existem registros vinculados a esta vaga.';
        }
    } else {
        $erro = 'Só é possível excluir vagas livres.';
    }
}

$vagas = $pdo->query("SELECT * FROM vagas ORDER BY tipo, numero_vaga")->fetchAll();

include 'includes/header.php';
?>

<h2 class="mb-4">Gerenciar Vagas</h2>

<?php if ($erro): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>
<?php if ($sucesso): ?>
    <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
<?php endif; ?>

<div class="card shadow-sm mb-4" style="max-width: 500px;">
    <div class="card-body">
        <h5>Nova Vaga</h5>
        <form method="POST" class="row g-2">
            <div class="col-5">
                <input type="text" name="numero_vaga" class="form-control" placeholder="Número (ex: A06)" required>
            </div>
            <div class="col-4">
                <select name="tipo" class="form-select">
                    <option value="Carro">Carro</option>
                    <option value="Moto">Moto</option>
                    <option value="Caminhão">Caminhão</option>
                </select>
            </div>
            <div class="col-3">
                <button type="submit" name="adicionar" value="1" class="btn btn-success w-100">Adicionar</button>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover bg-white shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>Número</th>
                <th>Tipo</th>
                <th>Status</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vagas as $v): ?>
                <tr>
                    <td><?= htmlspecialchars($v['numero_vaga']) ?></td>
                    <td><?= htmlspecialchars($v['tipo']) ?></td>
                    <td>
                        <span class="badge <?= $v['status'] === 'Livre' ? 'bg-success' : 'bg-danger' ?>">
                            <?= $v['status'] ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Excluir esta vaga?');" class="d-inline">
                            <input type="hidden" name="vaga_id" value="<?= $v['id'] ?>">
                            <button type="submit" name="excluir" value="1" class="btn btn-sm btn-outline-danger"
                                <?= $v['status'] !== 'Livre' ? 'disabled' : '' ?>>
                                Excluir
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
