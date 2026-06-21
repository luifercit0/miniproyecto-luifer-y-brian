<?php
require_once 'includes/Utilidades.php';

$error    = '';
$base     = null;
$potencias = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $baseRaw = Utilidades::sanitizar(Utilidades::nvl($_POST['base'], ''));

    // OWASP A03: validar que sea entero entre 1 y 9
    if (!Utilidades::validarEntero($baseRaw, 1, 9)) {
        $error = 'Ingresa un número entero entre 1 y 9.';
    } else {
        $base = (int) $baseRaw;

        // Calcular las 15 primeras potencias (DRY: método Utilidades::potencia)
        for ($exp = 1; $exp <= 15; $exp++) {
            $valor = Utilidades::potencia($base, $exp); // Método estático de Utilidades
            $potencias[] = [
                'exponente' => $exp,
                'formula'   => "{$base}^{$exp}",
                'valor'     => $valor,
                'digitos'   => strlen((string)(int)$valor), // Cantidad de dígitos
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Problema #9 — Potencias</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>

<header class="site-header">
    <h1>[#] Problema #9</h1>
    <p class="subtitle">15 Primeras Potencias de un Número</p>
</header>

<main class="container">
    <?= Utilidades::generarEnlaceMenu('index.php') ?>

    <div class="card">
        <h2>>>Potencias del Número</h2>
        <p class="problema-info">
            Ingresa un número del 1 al 9. El sistema calculará sus 15 primeras potencias
            usando <strong>Utilidades::potencia(base, exp)</strong> y un ciclo <strong>for</strong>.
            <br>Fórmula: a<sup>n</sup> = a × a × ... × a (n veces)
        </p>

        <!-- ── FORMULARIO ── -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="base">Número base (1 al 9)</label>
                <input type="number" id="base" name="base"
                       min="1" max="9" placeholder="Ej: 4"
                       value="<?= $base ? $base : '' ?>">
            </div>
            <button type="submit" class="btn-submit">Generar Potencias</button>
        </form>

        <?php if ($error): ?>
            <div class="alerta alerta-error"><?= $error ?></div>
        <?php endif; ?>

        <!-- ── RESULTADOS ── -->
        <?php if (!empty($potencias)): ?>
        <div class="resultado">
            <h3>-- 15 Potencias de <?= $base ?></h3>

            <!-- Vista chips: cada potencia -->
            <div class="lista-resultados">
                <?php foreach ($potencias as $p): ?>
                <span class="item">
                    <strong><?= $base ?><sup><?= $p['exponente'] ?></sup></strong>
                    = <?= number_format($p['valor']) ?>
                </span>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Tabla detallada -->
        <table style="margin-top:20px;">
            <thead>
                <tr><th>Exponente</th><th>Fórmula</th><th>Resultado</th><th>Dígitos</th></tr>
            </thead>
            <tbody>
                <?php foreach ($potencias as $p): ?>
                <tr>
                    <td><?= $p['exponente'] ?></td>
                    <td><?= $base ?><sup><?= $p['exponente'] ?></sup></td>
                    <td><span class="valor"><?= number_format($p['valor']) ?></span></td>
                    <td><?= $p['digitos'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- GRÁFICA de crecimiento -->
        <h3 style="margin-top:24px;">-- Crecimiento de <?= $base ?><sup>n</sup></h3>
        <canvas id="graficaPotencias" width="500" height="250"></canvas>
        <script>
        const valores   = <?= json_encode(array_column($potencias, 'valor')) ?>;
        const etiquetas = <?= json_encode(array_map(fn($p) => "{$base}^{$p['exponente']}", $potencias)) ?>;

        new Chart(document.getElementById('graficaPotencias'), {
            type: 'line',
            data: {
                labels: etiquetas,
                datasets: [{
                    label: 'Valor de la potencia',
                    data: valores,
                    borderColor: '#ff2233',
                    backgroundColor: 'rgba(255,34,51,0.15)',
                    pointBackgroundColor: '#ff2233',
                    pointRadius: 5,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { ticks: { color: '#ccc' }, grid: { color: 'rgba(255,34,51,0.1)' } },
                    x: { ticks: { color: '#ccc', maxRotation: 45 }, grid: { color: 'rgba(255,34,51,0.1)' } }
                },
                plugins: { legend: { labels: { color: '#ccc' } } }
            }
        });
        </script>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
</body>
</html>
