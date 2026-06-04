<?php
/**
 * PROBLEMA #6 — Presupuesto Anual del Hospital
 * Areas: Ginecologia 40%, Traumatologia 35%, Pediatria 25%
 * PSR-1 · OWASP A03 · DRY · Grafica de torta (Chart.js)
 *
 * OWASP aplicado en este archivo:
 * [A03 - Input Validation]  validarNumero() rechaza texto o negativos
 * [A03 - XSS]               htmlspecialchars() al imprimir $nombre en la tabla
 * [Error Management]        $error es generico, no expone rutas ni excepciones
 */
require_once 'includes/Utilidades.php';

$error     = '';
$resultado = null;

/*
 * DRY: porcentajes definidos una sola vez en este arreglo.
 * Si cambia la distribucion, solo se edita aqui.
 * Se elimino la clave 'emoji'; el identificador visual ahora es texto.
 */
$areas = [
    'Ginecologia'   => ['porcentaje' => 40],
    'Traumatologia' => ['porcentaje' => 35],
    'Pediatria'     => ['porcentaje' => 25],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /*
     * OWASP A03 - XSS: sanitizar() aplica htmlspecialchars() al valor POST
     * antes de usarlo, para que <script> u otros tags no lleguen al calculo.
     * OWASP A03 - Input Validation: nvl() evita Undefined index si el campo
     * llega vacio, y validarNumero() rechaza el dato si no es float positivo.
     */
    $presupuestoRaw = Utilidades::sanitizar(Utilidades::nvl($_POST['presupuesto'], ''));

    if (!Utilidades::validarNumero($presupuestoRaw)) {
        /*
         * OWASP - Error Management: el mensaje no menciona funcion interna,
         * tipo de fallo de PHP ni ruta del archivo. Solo indica que hacer.
         */
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
    <title>Problema #6 — Presupuesto Hospitalario</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Chart.js: grafica de torta (Punto del documento: Integrar Graficas) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>

<header class="site-header">
    <h1>Problema #6</h1>
    <p class="subtitle">Distribucion del Presupuesto Hospitalario</p>
</header>

<main class="container">

    <!--
        Punto #12: generarEnlaceMenu() es el unico lugar donde se define
        el boton "Volver al Menu". DRY: no se repite el <a href> en cada pagina.
        OWASP: la URL es sanitizada con filter_var(FILTER_SANITIZE_URL) dentro de Utilidades.
    -->
    <?= Utilidades::generarEnlaceMenu('index.php') ?>

    <div class="card">
        <h2>Presupuesto Hospitalario</h2>
        <p class="problema-info">
            Ingresa el presupuesto anual. El sistema distribuye:
            Ginecologia 40% &middot; Traumatologia 35% &middot; Pediatria 25%
        </p>

        <!-- Tabla de referencia de porcentajes -->
        <table>
            <thead><tr><th>Area</th><th>Porcentaje</th></tr></thead>
            <tbody>
                <!--
                    DRY: la tabla se genera con foreach desde $areas,
                    no con 3 filas <tr> escritas a mano.
                    OWASP A03 - XSS: htmlspecialchars($nombre) evita que
                    un nombre de area con caracteres como < o " rompa el HTML.
                    Aunque aqui son datos fijos del servidor, se aplica por
                    buena practica consistente.
                -->
                <?php foreach ($areas as $nombre => $cfg): ?>
                <tr>
                    <td><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= $cfg['porcentaje'] ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <hr class="divider">

        <!-- FORMULARIO (Punto #9 del documento) -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="presupuesto">Presupuesto Anual ($)</label>
                <!--
                    OWASP A03 - Input Validation: type="number" es validacion
                    en el cliente (navegador). La validacion REAL ocurre en PHP
                    con Utilidades::validarNumero() en el backend.
                    OWASP A03 - XSS: Utilidades::sanitizar() al repintar el value
                    evita que un value malicioso cierre el atributo e inyecte HTML.
                -->
                <input type="number" id="presupuesto" name="presupuesto"
                       min="1" step="0.01" placeholder="Ej: 20000"
                       value="<?= isset($_POST['presupuesto']) ? Utilidades::sanitizar($_POST['presupuesto']) : '' ?>">
            </div>
            <button type="submit" class="btn-submit">Calcular Distribucion</button>
        </form>

        <?php if ($error): ?>
            <!--
                OWASP - Error Management: $error contiene solo texto generico.
                No incluye stack trace, ruta del archivo ni nombre de funcion PHP.
            -->
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
                        <!--
                            OWASP A03 - XSS: htmlspecialchars() sobre $nombre
                            antes de imprimirlo en la tabla de resultados.
                            Aunque $nombre viene del arreglo del servidor,
                            se aplica siempre para consistencia (DRY de seguridad).
                        -->
                        <td><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= $datos['porcentaje'] ?>%</td>
                        <td><span class="valor">$<?= number_format($datos['monto'], 2) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- GRAFICA DE TORTA (Punto del documento: Integrar Graficas) -->
        <h3 style="margin-top:24px;">Grafica de Distribucion</h3>
        <canvas id="graficaPresupuesto" width="350" height="350"></canvas>

        <script>
        /*
         * OWASP A03 - XSS en contexto JavaScript:
         * json_encode() convierte los datos PHP a JSON seguro.
         * json_encode escapa automaticamente caracteres como <, >, &, "
         * usando secuencias Unicode (\u003C, etc.) para que no se
         * interpreten como HTML dentro de un bloque <script>.
         */
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
