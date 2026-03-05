<?php
require 'config.php';

$error = '';
$ciudades = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['ciudad'])) {
    $busqueda = trim($_POST['ciudad']);
    $url = "https://api.openweathermap.org/geo/1.0/direct?q=" . urlencode($busqueda) . "&limit=5&appid=" . API_KEY;
    $resp = file_get_contents($url);
    $ciudades = json_decode($resp, true);
    if (empty($ciudades)) {
        $error = "No se encontró la ciudad: " . htmlspecialchars($busqueda);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Weather App</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: 40px auto; padding: 0 20px; background: #f0f4f8; }
        h1 { color: #2c3e50; }
        input[type=text] { width: 300px; padding: 8px; font-size: 16px; }
        button { padding: 8px 20px; background: #3498db; color: white; border: none; cursor: pointer; font-size: 16px; }
        button:hover { background: #2980b9; }
        .error { color: red; margin-top: 10px; }
        .ciudad { background: white; padding: 10px 15px; margin: 8px 0; border-radius: 5px; border: 1px solid #ddd; }
        .ciudad a { margin-right: 10px; color: #3498db; text-decoration: none; }
        .ciudad a:hover { text-decoration: underline; }
        nav a { margin-right: 15px; color: #3498db; }
    </style>
</head>
<body>
    <nav><a href="index.php">Inicio</a> <a href="historial.php">Historial</a></nav>
    <h1>&#127774; Weather App</h1>

    <form method="POST">
        <input type="text" name="ciudad" placeholder="Escribe una ciudad..." value="<?= htmlspecialchars($_POST['ciudad'] ?? '') ?>">
        <button type="submit">Buscar</button>
    </form>

    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <?php if (!empty($ciudades)): ?>
        <h3>Resultados:</h3>
        <?php foreach ($ciudades as $c): ?>
            <div class="ciudad">
                <strong><?= htmlspecialchars($c['name']) ?></strong>
                <?php if (!empty($c['state'])): ?>(<?= htmlspecialchars($c['state']) ?>)<?php endif; ?>
                - <?= htmlspecialchars($c['country']) ?>
                &nbsp;|&nbsp;
                <a href="actual.php?lat=<?= $c['lat'] ?>&lon=<?= $c['lon'] ?>&ciudad=<?= urlencode($c['name']) ?>&pais=<?= urlencode($c['country']) ?>">Tiempo actual</a>
                <a href="horas.php?lat=<?= $c['lat'] ?>&lon=<?= $c['lon'] ?>&ciudad=<?= urlencode($c['name']) ?>&pais=<?= urlencode($c['country']) ?>">Por horas</a>
                <a href="semana.php?lat=<?= $c['lat'] ?>&lon=<?= $c['lon'] ?>&ciudad=<?= urlencode($c['name']) ?>&pais=<?= urlencode($c['country']) ?>">Semanal</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
