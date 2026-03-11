<?php
require_once __DIR__ . '/Controller.php';
$ctrl = new Controller();
$lat = $_GET['lat'] ?? ''; $lon = $_GET['lon'] ?? ''; $ciudad = $_GET['ciudad'] ?? ''; $pais = $_GET['pais'] ?? '';
if (!$lat || !$lon) { header('Location: index.php'); exit; }
extract($ctrl->actual($lat, $lon, $ciudad, $pais));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><title>Tiempo actual - <?= htmlspecialchars($ciudad) ?></title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: 40px auto; padding: 0 20px; background: #f0f4f8; }
        h1 { color: #2c3e50; }
        .tarjeta { background: white; border-radius: 8px; padding: 20px; margin: 15px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .temp { font-size: 64px; font-weight: bold; color: #e74c3c; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td { padding: 8px 12px; border-bottom: 1px solid #eee; }
        td:first-child { font-weight: bold; color: #555; width: 40%; }
        nav a { margin-right: 15px; color: #3498db; }
        a.btn { display: inline-block; margin: 5px; padding: 7px 16px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <nav><a href="index.php">&#8592; Volver</a> <a href="historial.php">Historial</a></nav>
    <h1>&#127777; Tiempo actual en <?= htmlspecialchars($ciudad) ?>, <?= htmlspecialchars($pais) ?></h1>
    <div class="tarjeta">
        <img src="https://openweathermap.org/img/wn/<?= $icono ?>@2x.png" alt="<?= $desc ?>">
        <div class="temp"><?= $temp ?>°C</div>
        <p><?= $desc ?></p>
        <table>
            <tr><td>Sensacion termica</td><td><?= $sens ?>°C</td></tr>
            <tr><td>Humedad</td><td><?= $hum ?>%</td></tr>
            <tr><td>Viento</td><td><?= $viento ?> km/h</td></tr>
            <tr><td>Presion</td><td><?= $presion ?> hPa</td></tr>
            <tr><td>Visibilidad</td><td><?= $visib ?> km</td></tr>
        </table>
    </div>
    <div class="tarjeta">
        <h3>Indicadores</h3>
        <canvas id="grafica" height="120"></canvas>
    </div>
    <p>
        <a class="btn" href="horas.php?lat=<?= $lat ?>&lon=<?= $lon ?>&ciudad=<?= urlencode($ciudad) ?>&pais=<?= urlencode($pais) ?>">Ver por horas</a>
        <a class="btn" href="semana.php?lat=<?= $lat ?>&lon=<?= $lon ?>&ciudad=<?= urlencode($ciudad) ?>&pais=<?= urlencode($pais) ?>">Ver semanal</a>
    </p>
    <script>
    new Chart(document.getElementById('grafica'), {
        type: 'bar',
        data: {
            labels: ['Temperatura (°C)','Sensacion (°C)','Humedad (%)','Viento (km/h)'],
            datasets: [{ label: '<?= htmlspecialchars(addslashes($ciudad)) ?>', data: [<?= $temp ?>,<?= $sens ?>,<?= $hum ?>,<?= $viento ?>], backgroundColor: ['#e74c3c','#e67e22','#3498db','#2ecc71'] }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });
    </script>
</body>
</html>
