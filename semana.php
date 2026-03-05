<?php
require 'config.php';

$lat    = $_GET['lat'] ?? '';
$lon    = $_GET['lon'] ?? '';
$ciudad = $_GET['ciudad'] ?? '';
$pais   = $_GET['pais'] ?? '';

if (!$lat || !$lon) { header('Location: index.php'); exit; }

$url  = "https://api.openweathermap.org/data/2.5/forecast?lat=$lat&lon=$lon&units=metric&lang=es&appid=" . API_KEY;
$resp = file_get_contents($url);
$d    = json_decode($resp, true);

// Guardar consulta
$pdo = getDB();
$pdo->prepare("INSERT INTO consultas (ciudad, pais, lat, lon, tipo) VALUES (?,?,?,?,?)")
    ->execute([$ciudad, $pais, $lat, $lon, 'semana']);

// Agrupar por día
$dias = [];
foreach ($d['list'] as $e) {
    $dia = date('Y-m-d', $e['dt']);
    if (!isset($dias[$dia])) {
        $dias[$dia] = ['temps' => [], 'desc' => $e['weather'][0]['description'], 'icon' => $e['weather'][0]['icon'], 'pop' => []];
    }
    $dias[$dia]['temps'][] = $e['main']['temp'];
    $dias[$dia]['pop'][]   = $e['pop'] ?? 0;
}

$dias_nombres = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
$labels = $maxs = $mins = $pops = [];

foreach ($dias as $fecha => $info) {
    $labels[] = $dias_nombres[date('w', strtotime($fecha))] . ' ' . date('d/m', strtotime($fecha));
    $maxs[]   = round(max($info['temps']), 1);
    $mins[]   = round(min($info['temps']), 1);
    $pops[]   = round(max($info['pop']) * 100);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Previsión semanal - <?= htmlspecialchars($ciudad) ?></title>
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
    <h1>&#128197; Previsión semanal: <?= htmlspecialchars($ciudad) ?>, <?= htmlspecialchars($pais) ?></h1>

    <div class="tarjeta">
        <table>
            <tr>
                <th>Día</th>
                <th>Icono</th>
                <th>Descripción</th>
                <th>Máx</th>
                <th>Mín</th>
                <th>Lluvia</th>
            </tr>
            <?php
            $i = 0;
            foreach ($dias as $fecha => $info):
            ?>
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

    <div class="tarjeta">
        <h3>Temperaturas semanales</h3>
        <canvas id="graficaTemp" height="100"></canvas>
    </div>

    <div class="tarjeta">
        <h3>Probabilidad de lluvia (%)</h3>
        <canvas id="graficaLluvia" height="80"></canvas>
    </div>

    <p>
        <a class="btn" href="actual.php?lat=<?= $lat ?>&lon=<?= $lon ?>&ciudad=<?= urlencode($ciudad) ?>&pais=<?= urlencode($pais) ?>">Tiempo actual</a>
        <a class="btn" href="horas.php?lat=<?= $lat ?>&lon=<?= $lon ?>&ciudad=<?= urlencode($ciudad) ?>&pais=<?= urlencode($pais) ?>">Por horas</a>
    </p>

    <script>
    const labels = <?= json_encode($labels) ?>;
    const maxs   = <?= json_encode($maxs) ?>;
    const mins   = <?= json_encode($mins) ?>;
    const pops   = <?= json_encode($pops) ?>;

    new Chart(document.getElementById('graficaTemp'), {
        type: 'line',
        data: {
            labels,
            datasets: [
                { label: 'Máxima (°C)', data: maxs, borderColor: '#e74c3c', backgroundColor: 'rgba(231,76,60,0.1)', fill: false, tension: 0.4 },
                { label: 'Mínima (°C)', data: mins, borderColor: '#3498db', backgroundColor: 'rgba(52,152,219,0.1)', fill: false, tension: 0.4 }
            ]
        },
        options: { responsive: true }
    });

    new Chart(document.getElementById('graficaLluvia'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{ label: 'Prob. lluvia (%)', data: pops, backgroundColor: '#9b59b6' }]
        },
        options: { responsive: true, scales: { y: { min: 0, max: 100 } } }
    });
    </script>
</body>
</html>
