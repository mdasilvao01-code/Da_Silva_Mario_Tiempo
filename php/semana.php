<?php
require_once __DIR__ . '/Controller.php';
$ctrl = new Controller();
$lat = $_GET['lat'] ?? ''; $lon = $_GET['lon'] ?? ''; $ciudad = $_GET['ciudad'] ?? ''; $pais = $_GET['pais'] ?? '';
if (!$lat || !$lon) { header('Location: index.php'); exit; }
extract($ctrl->semana($lat, $lon, $ciudad, $pais));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><title>Prevision semanal - <?= htmlspecialchars($ciudad) ?></title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; padding: 0 20px; background: #f0f4f8; }
        h1 { color: #2c3e50; }
        .tarjeta { background: white; border-radius: 8px; padding: 20px; margin: 15px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #3498db; color: white; padding: 10px; text-align: left; }
        td { padding: 9px 10px; border-bottom: 1px solid #eee; }
        tr:hover td { background: #f9f9f9; }
        nav a { margin-right: 15px; color: #3498db; }
        a.btn { display: inline-block; margin: 5px; padding: 7px 16px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <nav><a href="index.php">&#8592; Volver</a> <a href="historial.php">Historial</a></nav>
    <h1>&#128197; Prevision semanal: <?= htmlspecialchars($ciudad) ?>, <?= htmlspecialchars($pais) ?></h1>
    <div class="tarjeta">
        <table>
            <tr><th>Dia</th><th>Icono</th><th>Descripcion</th><th>Max</th><th>Min</th><th>Lluvia</th></tr>
            <?php $i = 0; foreach ($dias as $fecha => $info): ?>
            <tr>
                <td><?= $labels[$i] ?></td>
                <td><img src="https://openweathermap.org/img/wn/<?= $info['icon'] ?>.png" alt=""></td>
                <td><?= ucfirst($info['desc']) ?></td>
                <td style="color:#e74c3c;font-weight:bold"><?= $maxs[$i] ?>°C</td>
                <td style="color:#3498db"><?= $mins[$i] ?>°C</td>
                <td><?= $pops[$i] ?>%</td>
            </tr>
            <?php $i++; endforeach; ?>
        </table>
    </div>
    <div class="tarjeta"><h3>Temperaturas semanales</h3><canvas id="graficaTemp" height="100"></canvas></div>
    <div class="tarjeta"><h3>Probabilidad de lluvia (%)</h3><canvas id="graficaLluvia" height="80"></canvas></div>
    <p>
        <a class="btn" href="actual.php?lat=<?= $lat ?>&lon=<?= $lon ?>&ciudad=<?= urlencode($ciudad) ?>&pais=<?= urlencode($pais) ?>">Tiempo actual</a>
        <a class="btn" href="horas.php?lat=<?= $lat ?>&lon=<?= $lon ?>&ciudad=<?= urlencode($ciudad) ?>&pais=<?= urlencode($pais) ?>">Por horas</a>
    </p>
    <script>
    const labels = <?= json_encode($labels) ?>;
    new Chart(document.getElementById('graficaTemp'), {
        type: 'line',
        data: { labels, datasets: [
            { label: 'Maxima (°C)', data: <?= json_encode($maxs) ?>, borderColor: '#e74c3c', fill: false, tension: 0.4 },
            { label: 'Minima (°C)', data: <?= json_encode($mins) ?>, borderColor: '#3498db', fill: false, tension: 0.4 }
        ]},
        options: { responsive: true }
    });
    new Chart(document.getElementById('graficaLluvia'), {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Prob. lluvia (%)', data: <?= json_encode($pops) ?>, backgroundColor: '#9b59b6' }] },
        options: { responsive: true, scales: { y: { min: 0, max: 100 } } }
    });
    </script>
</body>
</html>
