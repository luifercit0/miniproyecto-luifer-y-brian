<?php
    require_once 'includes/Utilidades.php';

    $error      = '';
    $resultados = null;

    // Procesamiento POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $numeros = [];

        // FOR: recoger y validar los 5 campos (DRY: no 5 bloques separados)
        for ($i = 1; $i <= 5; $i++) {
            // OWASP A03 - XSS: sanitizar antes de usar el valor
            $val = Utilidades::sanitizar(Utilidades::nvl($_POST["numero{$i}"], ''));

            // OWASP A03 - Input Validation: float positivo obligatorio
            if (!filter_var($val, FILTER_VALIDATE_FLOAT) || (float)$val <= 0) {
                $error = "El numero #{$i} debe ser un valor positivo mayor que 0.";
                break;
            }
            $numeros[] = (float) $val;
        }

        if (!$error && count($numeros) === 5) {
            // Calculos delegados a Utilidades
            $media     = Utilidades::calcularMedia($numeros);
            $desviacion = Utilidades::desviacionEstandarPoblacional($numeros);
            $minimo    = Utilidades::calcularMin($numeros);
            $maximo    = Utilidades::calcularMax($numeros);

            $resultados = compact('numeros', 'media', 'desviacion', 'minimo', 'maximo');
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Problema #1 - Estadisticas</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="site-header">
    <h1>[#] Problema #1</h1>
    <p class="subtitle">Media · Desviacion Estandar · Minimo · Maximo</p>
</header>

<main class="container">

    <?= Utilidades::generarEnlaceMenu('index.php') ?>

    <div class="card">
        <h2>>> Estadisticas de 5 Numeros Positivos</h2>
        <p class="problema-info">
            Introduce 5 numeros positivos. El sistema calcula:
            media, desviacion estandar, minimo y maximo.
        </p>

        <!-- FORMULARIO -->
        <form method="POST" action="">

            <?php for ($i = 1; $i <= 5; $i++): ?>
            <div class="form-group">
                <label for="numero<?= $i ?>">Numero <?= $i ?></label>

                <input type="number"
                       id="numero<?= $i ?>"
                       name="numero<?= $i ?>"
                       step="any"
                       min="0.01"
                       placeholder="Ej: 23.5"
                       value="<?= isset($_POST["numero{$i}"]) ? Utilidades::sanitizar($_POST["numero{$i}"]) : '' ?>">
            </div>
            <?php endfor; ?>

            <!-- OWASP Secure Error Handling: mensaje generico sin datos del servidor -->
            <?php if ($error): ?>
                <div class="alerta alerta-error"><?= $error ?></div>
            <?php endif; ?>

            <button type="submit" class="btn-submit">Calcular Estadisticas</button>
        </form>

        <!-- RESULTADOS -->
        <?php if ($resultados): ?>
        <div class="resultado">
            <h3>-- Resultados</h3>

            <p>Numeros: <span class="valor"><?= implode(', ', $resultados['numeros']) ?></span></p>

            <table style="margin-top:16px;">
                <thead>
                    <tr><th>Metrica</th><th>Valor</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Media (Promedio)</td>
                        <td><span class="valor"><?= number_format($resultados['media'], 2) ?></span></td>
                    </tr>
                    <tr>
                        <td>Desviacion Estandar (poblacional)</td>
                        <td><span class="valor"><?= number_format($resultados['desviacion'], 4) ?></span></td>
                    </tr>
                    <tr>
                        <td>Valor Minimo</td>
                        <td><span class="valor"><?= $resultados['minimo'] ?></span></td>
                    </tr>
                    <tr>
                        <td>Valor Maximo</td>
                        <td><span class="valor"><?= $resultados['maximo'] ?></span></td>
                    </tr>
                </tbody>
            </table>

            <p style="margin-top:16px; font-size:0.78rem; color:var(--blanco-dim);">
                Formula desv. estandar poblacional: S = sqrt( Sum(x - media)^2 / n )
            </p>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
</body>
</html>