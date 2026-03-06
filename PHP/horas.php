<?php
require 'config.php';

$lat    = $_GET['lat'] ?? '';
$lon    = $_GET['lon'] ?? '';
$ciudad = $_GET['ciudad'] ?? '';
$pais   = $_GET['pais'] ?? '';

if (!$lat || !$lon) { header('Location: index.php'); exit; }

$url  = "https://api.openweathermap.org/data/2.5/forecast?lat=$lat&lon=$lon&cnt=8&units=metric&lang=es&appid=" . API_KEY;
$resp = file_get_contents($url);
$d    = json_decode($resp, true);

// Guardar consulta
$pdo = getDB();
$pdo->prepare("INSERT INTO consultas (ciudad, pais, lat, lon, tipo) VALUES (?,?,?,?,?)")
    ->execute([$ciudad, $pais, $lat, $lon, 'horas']);

$horas  = [];
$temps  = [];
$lluvias = [];
$descs  = [];
$iconos = [];

foreach ($d['list'] as $e) {
    $horas[]   = date('H:i', $e['dt']);
    $temps[]   = round($e['main']['temp'], 1);
    $lluvias[] = round(($e['pop'] ?? 0) * 100);
    $descs[]   = ucfirst($e['weather'][0]['description']);
    $iconos[]  = $e['weather'][0]['icon'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Previsión por horas - <?= htmlspecialchars($ciudad) ?></title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; padding: 0 20px; background: #f0f4f8; }
        h1 { color: #2c3e50; }
        .tarjeta { background: white; border-radius: 8px; padding: 20px; margin: 15px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .horas-grid { display: flex; gap: 10px; flex-wrap: wrap; }
        .hora { background: #ecf0f1; border-radius: 6px; padding: 10px; text-align: center; min-width: 80px; }
        .hora strong { display: block; font-size: 18px; }
        .hora small { color: #777; font-size: 11px; }
        nav a { margin-right: 15px; color: #3498db; }
        a.btn { display: inline-block; margin: 5px; padding: 7px 16px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <nav><a href="index.php">&#8592; Volver</a> <a href="historial.php">Historial</a></nav>
    <h1>&#128336; Previsión por horas: <?= htmlspecialchars($ciudad) ?>, <?= htmlspecialchars($pais) ?></h1>

    <div class="tarjeta">
        <div class="horas-grid">
            <?php foreach ($horas as $i => $h): ?>
            <div class="hora">
                <span><?= $h ?></span>
                <img src="https://openweathermap.org/img/wn/<?= $iconos[$i] ?>.png" alt="">
                <strong><?= $temps[$i] ?>°C</strong>
                <small><?= $descs[$i] ?></small>
                <small>🌧 <?= $lluvias[$i] ?>%</small>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="tarjeta">
        <h3>Temperatura por horas</h3>
        <canvas id="grafica" height="100"></canvas>
    </div>

    <div class="tarjeta">
        <h3>Probabilidad de lluvia (%)</h3>
        <canvas id="graficaLluvia" height="80"></canvas>
    </div>

    <p>
        <a class="btn" href="actual.php?lat=<?= $lat ?>&lon=<?= $lon ?>&ciudad=<?= urlencode($ciudad) ?>&pais=<?= urlencode($pais) ?>">Tiempo actual</a>
        <a class="btn" href="semana.php?lat=<?= $lat ?>&lon=<?= $lon ?>&ciudad=<?= urlencode($ciudad) ?>&pais=<?= urlencode($pais) ?>">Ver semanal</a>
    </p>

    <script>
    const labels = <?= json_encode($horas) ?>;
    const temps  = <?= json_encode($temps) ?>;
    const lluvia = <?= json_encode($lluvias) ?>;

    new Chart(document.getElementById('grafica'), {
        type: 'line',
        data: {
            labels,
            datasets: [{ label: 'Temperatura (°C)', data: temps, borderColor: '#e74c3c', backgroundColor: 'rgba(231,76,60,0.1)', fill: true, tension: 0.4 }]
        },
        options: { responsive: true }
    });

    new Chart(document.getElementById('graficaLluvia'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{ label: 'Prob. lluvia (%)', data: lluvia, backgroundColor: '#3498db' }]
        },
        options: { responsive: true, scales: { y: { min: 0, max: 100 } } }
    });
    </script>
</body>
</html>
