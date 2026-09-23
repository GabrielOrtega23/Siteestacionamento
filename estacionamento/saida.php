<?php
require 'config.php';

$erro = '';
$sucesso = '';
$registroSelecionado = null;
$resultado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar'])) {
    $registroId = (int) $_POST['registro_id'];

    $stmt = $pdo->prepare("SELECT * FROM registros WHERE id = ? AND status = 'Ativo'");
    $stmt->execute([$registroId]);
    $registro = $stmt->fetch();

    if (!$registro) {
        $erro = 'Registro não encontrado ou já finalizado.';
    } else {
        $entrada = new DateTime($registro['data_entrada']);
        $saida = new DateTime();
        $calc = calcularValor($pdo, $entrada, $saida);

        $pdo->beginTransaction();
        try {
            $pdo->prepare(
                "UPDATE registros
                 SET data_saida = NOW(), tempo_permanencia = ?, valor_total = ?, status = 'Finalizado'
                 WHERE id = ?"
            )->execute([$calc['tempo_formatado'], $calc['valor_total'], $registroId]);

            $pdo->prepare("UPDATE vagas SET status = 'Livre' WHERE id = ?")->execute([$registro['vaga_id']]);

            $pdo->commit();
            $sucesso = "Saída registrada. Tempo: {$calc['tempo_formatado']} — Valor: R$ " . number_format($calc['valor_total'], 2, ',', '.');
        } catch (Exception $e) {
            $pdo->rollBack();
            $erro = 'Erro ao registrar saída: ' . $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar'])) {
    $placaBusca = strtoupper(trim($_POST['placa_busca'] ?? ''));
    $stmt = $pdo->prepare(
        "SELECT r.*, v.numero_vaga FROM registros r
         JOIN vagas v ON v.id = r.vaga_id
         WHERE r.placa = ? AND r.status = 'Ativo'"
    );
    $stmt->execute([$placaBusca]);
    $registroSelecionado = $stmt->fetch();

    if (!$registroSelecionado) {
        $erro = 'Nenhum veículo ativo encontrado com essa placa.';
    } else {
        $entrada = new DateTime($registroSelecionado['data_entrada']);
        $resultado = calcularValor($pdo, $entrada, new DateTime());
    }
}

$registrosAtivos = $pdo->query(
    "SELECT r.*, v.numero_vaga FROM registros r
     JOIN vagas v ON v.id = r.vaga_id
     WHERE r.status = 'Ativo'
     ORDER BY r.data_entrada"
)->fetchAll();

include 'includes/header.php';
?>

<h2 class="mb-4">Registrar Saída de Veículo</h2>

<?php if ($erro): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>
<?php if ($sucesso): ?>
    <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
<?php endif; ?>

<div class="card shadow-sm mb-4" style="max-width: 500px;">
    <div class="card-body">
        <form method="POST" class="row g-2">
            <div class="col-8">
                <input type="text" name="placa_busca" class="form-control placa-input"
                       placeholder="Digite a placa" maxlength="7" required>
            </div>
            <div class="col-4">
                <button type="submit" name="buscar" value="1" class="btn btn-primary w-100">Buscar</button>
            </div>
        </form>
    </div>
</div>

<?php if ($registroSelecionado && $resultado): ?>
    <div class="card shadow-sm mb-4 border-warning" style="max-width: 500px;">
        <div class="card-body">
            <h5 class="card-title">Placa: <?= htmlspecialchars($registroSelecionado['placa']) ?></h5>
            <p class="mb-1">Vaga: <strong><?= htmlspecialchars($registroSelecionado['numero_vaga']) ?></strong></p>
            <p class="mb-1">Entrada: <strong><?= date('d/m/Y H:i:s', strtotime($registroSelecionado['data_entrada'])) ?></strong></p>
            <p class="mb-1">Tempo decorrido:
                <strong id="relogio" data-entrada="<?= $registroSelecionado['data_entrada'] ?>"><?= $resultado['tempo_formatado'] ?></strong>
            </p>
            <p class="mb-3">Valor estimado: <strong>R$ <?= number_format($resultado['valor_total'], 2, ',', '.') ?></strong></p>

            <form method="POST">
                <input type="hidden" name="registro_id" value="<?= $registroSelecionado['id'] ?>">
                <button type="submit" name="finalizar" value="1" class="btn btn-danger confirmar-saida">
                    Confirmar Saída
                </button>
            </form>
        </div>
    </div>
<?php endif; ?>

<h4 class="mt-4">Veículos Atualmente Estacionados</h4>
<div class="table-responsive">
    <table class="table table-striped table-hover bg-white shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>Placa</th>
                <th>Vaga</th>
                <th>Entrada</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registrosAtivos as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['placa']) ?></td>
                    <td><?= htmlspecialchars($r['numero_vaga']) ?></td>
                    <td><?= date('d/m/Y H:i:s', strtotime($r['data_entrada'])) ?></td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="placa_busca" value="<?= htmlspecialchars($r['placa']) ?>">
                            <button type="submit" name="buscar" value="1" class="btn btn-sm btn-outline-primary">
                                Calcular / Sair
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($registrosAtivos)): ?>
                <tr><td colspan="4" class="text-center text-muted">Nenhum veículo estacionado no momento.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
