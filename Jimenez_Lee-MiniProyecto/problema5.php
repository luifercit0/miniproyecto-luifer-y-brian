<?php
/**
 * PROBLEMA #5 — Clasificacion de 5 Edades con estadisticas y graficas
 * Fuente original: Problema5Edad.php (adaptado al proyecto)
 *
 * Logica migrada a Utilidades (DRY):
 *   - Definicion de categorias → Utilidades::getCategorias()
 *   - Clasificacion por edad   → Utilidades::clasificarEdad()
 *   - Edades repetidas         → Utilidades::detectarRepetidos()
 *   - Sanitizacion             → Utilidades::sanitizar()
 *   - Validacion               → Utilidades::validarEntero()
 *   - Enlace al menu           → Utilidades::generarEnlaceMenu()
 *
 * PSR-1 · OWASP A03 · DRY · SWITCH (dentro de clasificarEdad)
 * Graficas: Chart.js (barras + dona) — Punto #5 del documento
 */
require_once 'includes/Utilidades.php';

$error           = '';
$personas        = [];
$mostrarResultados = false;

// DRY: categorias obtenidas de Utilidades, unica fuente de verdad
$categorias = Utilidades::getCategorias();

// ── Procesamiento POST ──────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    for ($i = 1; $i <= 5; $i++) {
        // OWASP A03 - XSS: sanitizar antes de usar
        $edadRaw = Utilidades::sanitizar(Utilidades::nvl($_POST["edad{$i}"], ''));

        // OWASP A03 - Input Validation: entero entre 0 y 150
        if (!Utilidades::validarEntero($edadRaw, 0, 150)) {
            $error = "La edad de la persona #{$i} debe ser un entero entre 0 y 150.";
            break;
        }

        $edad     = (int) $edadRaw;
        // SWITCH delegado a Utilidades::clasificarEdad()
        $claveCategoria = Utilidades::clasificarEdad($edad);

        $personas[] = [
            'numero'   => $i,
            'edad'     => $edad,
            'clave'    => $claveCategoria,
            'nombre'   => $categorias[$claveCategoria]['nombre'] ?? 'Desconocido',
        ];
    }

    if (!$error && count($personas) === 5) {
        $mostrarResultados = true;
    }
}

// ── Datos para graficas ──────────────────────────────────────────────────────
$conteoCategorias = ['nino' => 0, 'adolescente' => 0, 'adulto' => 0, 'mayor' => 0];
$edadesRepetidas  = [];

if ($mostrarResultados) {
    // Conteo por categoria (FOREACH)
    foreach ($personas as $p) {
        if (isset($conteoCategorias[$p['clave']])) {
            $conteoCategorias[$p['clave']]++;
        }
    }
    // Detectar repetidas con metodo de Utilidades (DRY)
    $soloEdades      = array_column($personas, 'edad');
    $edadesRepetidas = Utilidades::detectarRepetidos($soloEdades);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Problema #5 - Clasificacion de Edades</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Chart.js para graficas (Punto #5: integrar graficas) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>

<header class="site-header">
    <h1>Problema #5</h1>
    <p class="subtitle">Clasificacion de Edades · Estadisticas · Graficas</p>
</header>

<main class="container">
    <!-- Punto #12: Utilidades::generarEnlaceMenu() con parametro URL -->
    <?= Utilidades::generarEnlaceMenu('index.php') ?>

    <!-- FORMULARIO (Punto #9) -->
    <div class="card">
        <h2>>> Clasificar 5 Edades</h2>
        <p class="problema-info">
            Ingresa 5 edades. El sistema las clasifica con
            <code>Utilidades::clasificarEdad()</code> (SWITCH interno).
            Las categorias se definen en <code>Utilidades::getCategorias()</code> (DRY).
        </p>

        <!-- Tabla de rangos de referencia -->
        <table>
            <thead>
                <tr><th>Categoria</th><th>Rango de Edad</th></tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $clave => $cat): ?>
                <tr>
                    <td><span class="badge"><?= htmlspecialchars($cat['nombre']) ?></span></td>
                    <td><?= $cat['min'] ?> - <?= $cat['max'] ?> anos</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <hr class="divider">

        <form method="POST" action="">
            <!-- FOR: genera los 5 campos sin repetir HTML (DRY) -->
            <?php for ($i = 1; $i <= 5; $i++): ?>
            <div class="form-group">
                <label for="edad<?= $i ?>">Persona <?= $i ?> (edad en anos)</label>
                <!-- OWASP A03 XSS: value repintado sanitizado -->
                <input type="number"
                       id="edad<?= $i ?>"
                       name="edad<?= $i ?>"
                       min="0" max="150"
                       placeholder="0 - 150"
                       value="<?= isset($_POST["edad{$i}"]) ? Utilidades::sanitizar($_POST["edad{$i}"]) : '' ?>">
            </div>
            <?php endfor; ?>

            <!-- OWASP Secure Error Handling: error generico -->
            <?php if ($error): ?>
                <div class="alerta alerta-error"><?= $error ?></div>
            <?php endif; ?>

            <button type="submit" class="btn-submit">Clasificar Edades</button>
        </form>
    </div>

    <!-- RESULTADOS -->
    <?php if ($mostrarResultados): ?>

    <!-- Tabla de detalle -->
    <div class="card">
        <h2>-- Detalle por Persona</h2>
        <table>
            <thead>
                <tr><th>#</th><th>Edad</th><th>Categoria</th><th>Rango</th></tr>
            </thead>
            <tbody>
                <?php foreach ($personas as $p): ?>
                <tr>
                    <td>Persona <?= $p['numero'] ?></td>
                    <td><?= $p['edad'] ?> anos</td>
                    <!-- OWASP A03 XSS: htmlspecialchars en todo dato impreso -->
                    <td><span class="badge"><?= htmlspecialchars($p['nombre']) ?></span></td>
                    <td>
                        <?= $categorias[$p['clave']]['min'] ?>
                        -
                        <?= $categorias[$p['clave']]['max'] ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Edades repetidas — Utilidades::detectarRepetidos() -->
        <?php if (!empty($edadesRepetidas)): ?>
        <div class="resultado" style="margin-top:20px; border-color:#cc8800;">
            <h3>-- Edades Repetidas Detectadas</h3>
            <p style="color:var(--blanco-dim); font-size:0.85rem; margin-bottom:12px;">
                Las siguientes edades aparecen mas de una vez:
            </p>
            <div class="lista-resultados">
                <?php foreach ($edadesRepetidas as $edad => $cantidad): ?>
                    <span class="item">
                        <?= $edad ?> anos &nbsp; <span class="valor">x<?= $cantidad ?></span>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="alerta alerta-ok" style="margin-top:16px;">
            No hay edades repetidas entre las 5 personas.
        </div>
        <?php endif; ?>
    </div>

    <!-- Graficas (Punto #5: integrar graficas) -->
    <div class="card">
        <h2>-- Visualizacion Grafica</h2>
        <p style="color:var(--blanco-dim); font-size:0.83rem; margin-bottom:20px;">
            Grafica de barras y dona generadas con Chart.js a partir de los datos clasificados.
        </p>

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px,1fr)); gap:24px;">
            <div>
                <h3>Barras — Personas por Categoria</h3>
                <canvas id="chartBarras" height="260"></canvas>
            </div>
            <div>
                <h3>Dona — Porcentaje por Categoria</h3>
                <canvas id="chartDona" height="260"></canvas>
            </div>
        </div>
    </div>

    <script>
    // Datos PHP → JS (OWASP: json_encode escapa caracteres especiales)
    const conteos   = <?= json_encode(array_values($conteoCategorias)) ?>;
    const etiquetas = ['Nino (0-12)', 'Adolescente (13-17)', 'Adulto (18-64)', 'Adulto Mayor (65+)'];
    const colores   = ['rgba(255,34,51,0.8)', 'rgba(180,0,0,0.8)',
                       'rgba(255,100,100,0.8)', 'rgba(100,0,0,0.8)'];
    const optsBase  = {
        responsive: true,
        plugins: { legend: { labels: { color: '#ccc', font: { family: 'monospace' } } } },
        scales: {
            x: { ticks: { color: '#ccc' }, grid: { color: 'rgba(255,34,51,0.1)' } },
            y: { ticks: { color: '#ccc', stepSize: 1 }, grid: { color: 'rgba(255,34,51,0.1)' }, min: 0 }
        }
    };

    // Grafica de barras
    new Chart(document.getElementById('chartBarras'), {
        type: 'bar',
        data: { labels: etiquetas, datasets: [{ label: 'Personas', data: conteos,
                backgroundColor: colores, borderColor: '#ff2233', borderWidth: 1 }] },
        options: optsBase
    });

    // Grafica de dona (sin ejes)
    new Chart(document.getElementById('chartDona'), {
        type: 'doughnut',
        data: { labels: etiquetas, datasets: [{ data: conteos,
                backgroundColor: colores, borderColor: '#111', borderWidth: 2 }] },
        options: { responsive: true,
            plugins: { legend: { position: 'bottom', labels: { color: '#ccc', padding: 16 } } } }
    });
    </script>

    <?php endif; // fin mostrarResultados ?>
</main>

<!-- FOOTER EXTERNO (Punto #6) -->
<?php require_once 'includes/footer.php'; ?>
</body>
</html>
