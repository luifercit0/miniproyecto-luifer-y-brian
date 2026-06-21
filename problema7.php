<?php
require_once 'includes/Utilidades.php';

$error    = '';
$notas    = [];
$cantidad = 0;
$etapa    = 1;

// Deteccion de etapa
if (isset($_POST['cantidad']) && !isset($_POST['notas'])) {

    // OWASP A03 - XSS + Input Validation en cantidad
    $cantRaw  = Utilidades::sanitizar(Utilidades::nvl($_POST['cantidad'], ''));
    $cantidad = (int) $cantRaw;

    if (!Utilidades::validarEntero($cantRaw, 1, 50)) {
        $error = 'La cantidad debe estar entre 1 y 50 notas.';
        $etapa = 1;
    } else {
        $etapa = 2;
    }

} elseif (isset($_POST['notas']) && is_array($_POST['notas'])) {

    $cantidad = count($_POST['notas']);

    foreach ($_POST['notas'] as $idx => $valor) {
        // OWASP A03 - XSS: sanitizar valor antes de procesar
        $notaRaw = Utilidades::sanitizar((string) $valor);

        if (!filter_var($notaRaw, FILTER_VALIDATE_FLOAT)) {
            $error = 'La nota #' . ($idx + 1) . ' no es un numero valido.';
            $etapa = 2;
            break;
        }
        $notaFloat = (float) $notaRaw;
        if ($notaFloat < 0 || $notaFloat > 10) {
            // OWASP Secure Error Handling: mensaje generico con solo el indice
            $error = 'La nota #' . ($idx + 1) . ' debe estar entre 0 y 10.';
            $etapa = 2;
            break;
        }
        $notas[] = $notaFloat;
    }

    if (!$error) {
        $etapa = 3;
    }
}

//cálculos
$promedio   = 0.0;
$desviacion = 0.0;
$minima     = 0.0;
$maxima     = 0.0;
$aprobadas  = 0;
$reprobadas = 0;

if ($etapa === 3 && count($notas) > 0) {
    $promedio   = Utilidades::calcularMedia($notas);
    $desviacion = Utilidades::desviacionEstandarPoblacional($notas);
    $minima     = Utilidades::calcularMin($notas);
    $maxima     = Utilidades::calcularMax($notas);

    // FOREACH: contar aprobadas/reprobadas con operador ternario
    foreach ($notas as $nota) {
        $nota >= 6 ? $aprobadas++ : $reprobadas++;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Problema #7 - Calculadora Estadistica</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>

<header class="site-header">
    <h1>[#] Problema #7</h1>
    <p class="subtitle">Calculadora de Datos Estadisticos</p>
</header>

<main class="container">
    <!-- Punto #12: Utilidades::generarEnlaceMenu() con parametro URL -->
    <?= Utilidades::generarEnlaceMenu('index.php') ?>

    <div class="card">
        <h2>>> Calculadora Estadistica de Notas</h2>
        <p class="problema-info">
            Paso 1: indica cuantas notas (1-50).<br>
            Paso 2: ingresa las notas (0-10). El sistema calcula promedio,
            desviacion estandar, minimo y maximo usando FOREACH y
            metodos de <code>Utilidades</code> (DRY).
        </p>

        <!-- OWASP Secure Error Handling: mensaje generico -->
        <?php if ($error): ?>
            <div class="alerta alerta-error"><?= $error ?></div>
        <?php endif; ?>

        <!-- ── PASO 1: Cantidad de notas ── -->
        <?php if ($etapa === 1): ?>
        <p style="color:var(--rojo-neon); font-family:var(--fuente-display);
                  font-size:0.72rem; letter-spacing:2px; margin-bottom:16px;">
            PASO 1 DE 2
        </p>
        <form method="POST" action="">
            <div class="form-group">
                <label for="cantidad">Cuantas notas deseas ingresar? (1 - 50)</label>
                <input type="number" id="cantidad" name="cantidad"
                       min="1" max="50" placeholder="Ej: 5" autofocus>
            </div>
            <button type="submit" class="btn-submit">Continuar</button>
        </form>
        <?php endif; ?>

        <!-- ── PASO 2: Ingresar notas ── -->
        <?php if ($etapa === 2): ?>
        <p style="color:var(--rojo-neon); font-family:var(--fuente-display);
                  font-size:0.72rem; letter-spacing:2px; margin-bottom:16px;">
            PASO 2 DE 2 — Ingresa <?= $cantidad ?> nota(s) (0 - 10)
        </p>
        <form method="POST" action="">
            <!-- DRY: FOR genera los inputs dinamicamente -->
            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(140px,1fr)); gap:12px;">
                <?php for ($i = 0; $i < $cantidad; $i++): ?>
                <div class="form-group">
                    <label for="nota<?= $i ?>">Nota <?= $i + 1 ?></label>
                    <input type="number"
                           id="nota<?= $i ?>"
                           name="notas[]"
                           step="0.01"
                           min="0" max="10"
                           placeholder="0 - 10">
                </div>
                <?php endfor; ?>
            </div>
            <button type="submit" class="btn-submit" style="margin-top:16px;">
                Calcular Estadisticas
            </button>
        </form>
        <?php endif; ?>

        <!-- ── PASO 3: Resultados ── -->
        <?php if ($etapa === 3): ?>
        <div class="resultado">
            <h3>-- Resultados Estadisticos</h3>

            <p style="margin-bottom:14px;">
                Notas ingresadas:
                <span class="valor"><?= implode(', ', $notas) ?></span>
            </p>

            <table>
                <thead>
                    <tr><th>Metrica</th><th>Valor</th></tr>
                </thead>
                <tbody>
                    <tr><td>Promedio</td>
                        <td><span class="valor"><?= number_format($promedio, 2) ?></span></td></tr>
                    <tr><td>Desviacion Estandar (poblacional)</td>
                        <td><span class="valor"><?= number_format($desviacion, 4) ?></span></td></tr>
                    <tr><td>Nota Minima</td>
                        <td><span class="valor"><?= number_format($minima, 2) ?></span></td></tr>
                    <tr><td>Nota Maxima</td>
                        <td><span class="valor"><?= number_format($maxima, 2) ?></span></td></tr>
                    <tr><td>Aprobadas (&gt;= 6)</td>
                        <td><?= $aprobadas ?></td></tr>
                    <tr><td>Reprobadas (&lt; 6)</td>
                        <td><?= $reprobadas ?></td></tr>
                    <tr><td>Total notas analizadas</td>
                        <td><?= count($notas) ?></td></tr>
                </tbody>
            </table>

            <p style="margin-top:14px; font-size:0.78rem; color:var(--blanco-dim);">
                Formulas: Promedio = Sum(x)/n &nbsp;|&nbsp;
                Desv. Est. = sqrt( Sum(x-media)^2 / n ) &nbsp;|&nbsp;
                Min/Max = valores extremos
            </p>
        </div>

        <!-- GRAFICA de notas -->
        <h3 style="margin-top:24px;">-- Grafica de Notas</h3>
        <canvas id="graficaNotas" height="220"></canvas>

        <script>
        const notas = <?= json_encode($notas) ?>;
        new Chart(document.getElementById('graficaNotas'), {
            type: 'bar',
            data: {
                labels: notas.map((_, i) => 'Nota ' + (i + 1)),
                datasets: [{
                    label: 'Nota',
                    data: notas,
                    // Verde si aprobada, rojo si reprobada
                    backgroundColor: notas.map(n => n >= 6
                        ? 'rgba(0,180,60,0.65)'
                        : 'rgba(255,34,51,0.75)'),
                    borderColor: '#ff2233',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { min: 0, max: 10,
                         ticks: { color: '#ccc' },
                         grid:  { color: 'rgba(255,34,51,0.1)' } },
                    x: { ticks: { color: '#ccc' },
                         grid:  { color: 'rgba(255,34,51,0.1)' } }
                },
                plugins: { legend: { labels: { color: '#ccc' } } }
            }
        });
        </script>
        <p style="font-size:0.78rem; color:var(--blanco-dim); margin-top:8px;">
            Verde = aprobada (&gt;= 6) · Rojo = reprobada (&lt; 6)
        </p>

        <br>
        <!-- DRY: boton de reinicio usando Utilidades::generarEnlaceMenu() -->
        <a href="problema7.php" class="btn-submit" style="display:inline-block; text-decoration:none;">
            Nueva operacion
        </a>

        <?php endif; ?>
    </div>
</main>

<!-- FOOTER EXTERNO (Punto #6) -->
<?php require_once 'includes/footer.php'; ?>
</body>
</html>
