<?php

$DB_HOST = 'localhost';
$DB_NAME = 'estacionamento';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Erro de conexão com o banco de dados: ' . $e->getMessage());
}

function calcularValor(PDO $pdo, DateTime $entrada, DateTime $saida): array
{
    $stmt = $pdo->query("SELECT * FROM tarifas WHERE ativo = 1 LIMIT 1");
    $tarifa = $stmt->fetch();

    if (!$tarifa) {
        $tarifa = ['valor_primeira_hora' => 5.00, 'valor_hora_adicional' => 3.00];
    }

    $segundos = $saida->getTimestamp() - $entrada->getTimestamp();
    if ($segundos < 0) {
        $segundos = 0;
    }

    $horas = (int) ceil($segundos / 3600);
    if ($horas < 1) {
        $horas = 1;
    }

    $valor = (float) $tarifa['valor_primeira_hora'];
    if ($horas > 1) {
        $valor += ($horas - 1) * (float) $tarifa['valor_hora_adicional'];
    }

    $h = floor($segundos / 3600);
    $m = floor(($segundos % 3600) / 60);
    $s = $segundos % 60;
    $tempoFormatado = sprintf('%02d:%02d:%02d', $h, $m, $s);

    return [
        'horas_cobradas' => $horas,
        'tempo_formatado' => $tempoFormatado,
        'valor_total' => round($valor, 2),
    ];
}
