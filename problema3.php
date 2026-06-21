<?php
require_once 'includes/Utilidades.php';

$error     = '';
$multiplos = [];
$n         = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // OWASP A03 - XSS: sanitizar el valor recibido
    $nRaw = Utilidades::sanitizar(Utilidades::nvl($_POST['n'], ''));

    // OWASP A03 - Input Validation: entero positivo entre 1 y 10,000
    if (!Utilidades::validarEntero($nRaw, 1, 10000)) {
        // OWASP Secure Error Handling: mensaje generico
        $error = 'Ingresa un numero entero entre 1 y 10,000.';
    } else {
        $n = (int) $nRaw;
        // DRY: generacion de multiplos delegada a Utilidades::generarMultiplos()
        $multiplos = Utilidades::generarMultiplos(4, $n);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Problema #3 - Multiples de 4</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="site-header">
    <h1>[#] Problema #3</h1>
    <p class="subtitle">N Primeros Multiples de 4</p>
</header>

<main class="container">
    <!-- Punto #12: enlace generado por Utilidades::generarEnlaceMenu() -->
    <?= Utilidades::generarEnlaceMenu('index.php') ?>

    <div class="card">
        <h2>>> Multiples de 4</h2>
        <p class="problema-info">
            Introduce N y el sistema genera los N primeros multiples de 4
            (4x1, 4x2, ...). La generacion usa
            <code>Utilidades::generarMultiplos(4, N)</code> que encapsula el FOR (DRY).
        </p>

        <!-- FORMULARIO (Punto #9) -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="n">Cantidad de multiples (N) — max 10,000</label>
                <input type="number"
                       id="n" name="n"
                       min="1" max="10000"
                       placeholder="Ej: 10"
                       value="<?= $n ? Utilidades::sanitizar((string)$n) : '' ?>">
            </div>

            <!-- OWASP Secure Error Handling: error generico -->
            <?php if ($error): ?>
                <div class="alerta alerta-error"><?= $error ?></div>
            <?php endif; ?>

            <button type="submit" class="btn-submit">Generar Multiples</button>
        </form>

        <!-- RESULTADOS -->
        <?php if (!empty($multiplos)): ?>
        <div class="resultado">
            <h3>-- Los <?= $n ?> primeros multiples de 4</h3>

            <?php if ($n <= 50): ?>
                <!-- Vista chips para N pequeno -->
                <div class="lista-resultados">
                    <?php foreach ($multiplos as $m): ?>
                        <span class="item">
                            4 x <?= $m['i'] ?> = <strong><?= number_format($m['valor'], 0, ',', '.') ?></strong>
                        </span>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
                <!-- Vista tabla para N grande -->
                <p style="margin-bottom:10px; font-size:0.83rem; color:var(--blanco-dim);">
                    Mostrando primeros 50 y el ultimo resultado:
                </p>
                <table>
                    <thead>
                        <tr><th>#</th><th>Operacion</th><th>Resultado</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach (array_slice($multiplos, 0, 50) as $m): ?>
                        <tr>
                            <td><?= $m['i'] ?></td>
                            <td>4 x <?= $m['i'] ?></td>
                            <td><?= number_format($m['valor'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($n > 50):
                        $ultimo = end($multiplos); ?>
                        <tr>
                            <td colspan="3" style="text-align:center; color:var(--rojo-neon);">
                                ... (<?= $n - 50 ?> mas) ...
                            </td>
                        </tr>
                        <tr>
                            <td><?= $ultimo['i'] ?></td>
                            <td>4 x <?= $ultimo['i'] ?></td>
                            <td><span class="valor"><?= number_format($ultimo['valor'], 0, ',', '.') ?></span></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- INFO DE DESBORDAMIENTO (del archivo original) -->
        <div class="resultado" style="margin-top:16px;">
            <h3>-- Desbordamiento (Overflow)?</h3>
            <p style="font-size:0.82rem; color:var(--blanco-dim); line-height:1.8;">
                En PHP 64-bit, el entero maximo es
                <span class="valor"><?= number_format(PHP_INT_MAX) ?></span>.<br>
                El overflow ocurre cuando
                <strong>N &gt; <?= number_format(intdiv(PHP_INT_MAX, 4)) ?></strong>
                (4 x N supera PHP_INT_MAX). PHP convierte automaticamente a
                <code>float</code>, perdiendo precision de decimales.<br>
                El limite practico es la memoria del servidor y
                <code>max_execution_time</code>.
            </p>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- FOOTER EXTERNO (Punto #6) -->
<?php require_once 'includes/footer.php'; ?>
</body>
</html>
