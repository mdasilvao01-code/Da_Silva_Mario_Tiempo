<?php
require 'config.php';

$pdo = getDB();
$consultas = $pdo->query("SELECT * FROM consultas ORDER BY fecha DESC LIMIT 100")->fetchAll();
$total     = $pdo->query("SELECT COUNT(*) FROM consultas")->fetchColumn();
$tipos     = $pdo->query("SELECT tipo, COUNT(*) as n FROM consultas GROUP BY tipo")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de consultas</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; padding: 0 20px; background: #f0f4f8; }
        h1 { color: #2c3e50; }
        .tarjeta { background: white; border-radius: 8px; padding: 20px; margin: 15px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #3498db; color: white; padding: 10px; text-align: left; }
        td { padding: 8px 10px; border-bottom: 1px solid #eee; }
        tr:hover td { background: #f9f9f9; }
        .resumen { display: flex; gap: 20px; margin-bottom: 15px; }
        .num { font-size: 28px; font-weight: bold; color: #3498db; }
        nav a { margin-right: 15px; color: #3498db; }
        a.btn { display: inline-block; margin: 5px; padding: 7px 16px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <nav><a href="index.php">&#8592; Inicio</a></nav>
    <h1>&#128203; Historial de consultas</h1>

    <div class="tarjeta">
        <div class="resumen">
            <div><div class="num"><?= $total ?></div><small>Total consultas</small></div>
            <?php foreach ($tipos as $t): ?>
            <div><div class="num"><?= $t['n'] ?></div><small><?= ucfirst($t['tipo']) ?></small></div>
            <?php endforeach; ?>
        </div>
        <canvas id="graficaTipos" height="80"></canvas>
    </div>

    <div class="tarjeta">
        <h3>Últimas consultas</h3>
        <?php if (empty($consultas)): ?>
            <p>No hay consultas todavía.</p>
        <?php else: ?>
        <table>
            <tr>
                <th>#</th>
                <th>Ciudad</th>
                <th>País</th>
                <th>Tipo</th>
                <th>Fecha</th>
                <th></th>
            </tr>
            <?php foreach ($consultas as $c): ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td><?= htmlspecialchars($c['ciudad']) ?></td>
                <td><?= htmlspecialchars($c['pais']) ?></td>
                <td><?= $c['tipo'] ?></td>
                <td><?= $c['fecha'] ?></td>
                <td>
                    <a href="<?= $c['tipo'] ?>.php?lat=<?= $c['lat'] ?>&lon=<?= $c['lon'] ?>&ciudad=<?= urlencode($c['ciudad']) ?>&pais=<?= urlencode($c['pais']) ?>">Ver</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>

    <script>
    const tipoLabels = <?= json_encode(array_column($tipos, 'tipo')) ?>;
    const tipoData   = <?= json_encode(array_map('intval', array_column($tipos, 'n'))) ?>;
    new Chart(document.getElementById('graficaTipos'), {
        type: 'pie',
        data: {
            labels: tipoLabels,
            datasets: [{ data: tipoData, backgroundColor: ['#e74c3c','#3498db','#2ecc71'] }]
        },
        options: { responsive: true }
    });
    </script>
</body>
</html>
