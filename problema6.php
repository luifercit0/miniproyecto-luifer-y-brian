<?php
require_once 'includes/Utilidades.php';

$error     = '';
$resultado = null;

$areas = [
    'Ginecologia'   => ['porcentaje' => 40],
    'Traumatologia' => ['porcentaje' => 35],
    'Pediatria'     => ['porcentaje' => 25],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $presupuestoRaw = Utilidades::sanitizar(Utilidades::nvl($_POST['presupuesto'], ''));

    if (!Utilidades::validarNumero($presupuestoRaw)) {

        $error = 'Ingresa un presupuesto valido (numero positivo).';
    } else {
        $presupuesto  = (float) $presupuestoRaw;
        $distribucion = [];

        // DRY: calcular cada area con foreach en lugar de 3 bloques repetidos
        foreach ($areas as $nombreArea => $config) {
            $distribucion[$nombreArea] = [
                'porcentaje' => $config['porcentaje'],
                'monto'      => $presupuesto * $config['porcentaje'] / 100,
            ];
        }
        $resultado = ['presupuesto' => $presupuesto, 'distribucion' => $distribucion];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Problema #6 — Presupuesto Hospital</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>

<header class="site-header">
    <h1>Problema #6</h1>
    <p class="subtitle">Distribucion del Presupuesto Hospitalario</p>
</header>

<main class="container">

    <?= Utilidades::generarEnlaceMenu('index.php') ?>

    <div class="card">
        <h2>Presupuesto del Hospital</h2>
        <p class="problema-info">
            Ingresa el presupuesto anual. El sistema distribuye:
            Ginecologia 40% &middot; Traumatologia 35% &middot; Pediatria 25%
        </p>

        <!-- Tabla de referencia de porcentajes -->
        <table>
            <thead><tr><th>Area</th><th>Porcentaje</th></tr></thead>
            <tbody>

                <?php foreach ($areas as $nombre => $cfg): ?>
                <tr>
                    <td><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= $cfg['porcentaje'] ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <hr class="divider">

        <form method="POST" action="">
            <div class="form-group">
                <label for="presupuesto">Presupuesto Anual ($)</label>

                <input type="number" id="presupuesto" name="presupuesto"
                       min="1" step="0.01" placeholder="Ej: 20000"
                       value="<?= isset($_POST['presupuesto']) ? Utilidades::sanitizar($_POST['presupuesto']) : '' ?>">
            </div>
            <button type="submit" class="btn-submit">Calcular Distribucion</button>
        </form>

        <?php if ($error): ?>

            <div class="alerta alerta-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <!-- RESULTADOS -->
        <?php if ($resultado): ?>
        <div class="resultado">
            <h3>Resultados del Presupuesto</h3>
            <p>Presupuesto total: <span class="valor">$<?= number_format($resultado['presupuesto'], 2) ?></span></p>

            <table style="margin-top:14px;">
                <thead><tr><th>Area</th><th>Porcentaje</th><th>Monto</th></tr></thead>
                <tbody>
                    <?php foreach ($resultado['distribucion'] as $nombre => $datos): ?>
                    <tr>
                        <td><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= $datos['porcentaje'] ?>%</td>
                        <td><span class="valor">$<?= number_format($datos['monto'], 2) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h3 style="margin-top:24px;">Grafica de Distribucion</h3>
        <canvas id="graficaPresupuesto" width="350" height="350"></canvas>

        <script>

        const montos    = <?= json_encode(array_column($resultado['distribucion'], 'monto')) ?>;
        const etiquetas = <?= json_encode(array_keys($resultado['distribucion'])) ?>;
        const pct       = <?= json_encode(array_column($resultado['distribucion'], 'porcentaje')) ?>;

        new Chart(document.getElementById('graficaPresupuesto'), {
            type: 'pie',
            data: {
                labels: etiquetas.map((l, i) => `${l} (${pct[i]}%)`),
                datasets: [{
                    data: montos,
                    backgroundColor: [
                        'rgba(255,34,51,0.8)',
                        'rgba(180,0,0,0.8)',
                        'rgba(255,120,120,0.8)'
                    ],
                    borderColor: '#ff2233',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { labels: { color: '#ccc', font: { family: 'monospace', size: 13 } } },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` $${ctx.parsed.toLocaleString('es-PA', {minimumFractionDigits:2})}`
                        }
                    }
                }
            }
        });
        </script>
        <?php endif; ?>
    </div>
</main>

<!-- FOOTER EXTERNO (Punto #6 del documento) -->
<?php require_once 'includes/footer.php'; ?>
</body>
</html>
