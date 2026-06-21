<?php
require_once 'includes/Utilidades.php';

$sumaPares    = 0;
$sumaImpares  = 0;
$contPares    = 0;
$contImpares  = 0;
$i            = 1;

while ($i <= 200) {
    $esPar = ($i % 2 === 0);
    $esPar ? ($sumaPares += $i) && $contPares++ : ($sumaImpares += $i) && $contImpares++;
    $i++;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Problema #4 — Pares e Impares</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="site-header">
    <h1>[#] Problema #4</h1>
    <p class="subtitle">Suma de Pares e Impares del 1 al 200</p>
</header>

<main class="container">
    <?= Utilidades::generarEnlaceMenu('index.php') ?>

    <div class="card">
        <h2>>>Pares e Impares (1 – 200)</h2>
        <p class="problema-info">
            Usando un ciclo <strong>while</strong> y el operador ternario,
            se clasifican y suman independientemente todos los números del 1 al 200.
        </p>

        <!-- ── RESULTADOS PRINCIPALES ── -->
        <div class="resultado">
            <h3>-- Resultados</h3>
            <table>
                <thead>
                    <tr><th>Categoría</th><th>Cantidad</th><th>Suma</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="badge">Pares</span></td>
                        <td><?= $contPares ?> números</td>
                        <td><span class="valor"><?= number_format($sumaPares) ?></span></td>
                    </tr>
                    <tr>
                        <td><span class="badge">Impares</span></td>
                        <td><?= $contImpares ?> números</td>
                        <td><span class="valor"><?= number_format($sumaImpares) ?></span></td>
                    </tr>
                    <tr>
                        <td><strong>Total</strong></td>
                        <td>200 números</td>
                        <td><span class="valor"><?= number_format($sumaPares + $sumaImpares) ?></span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <hr class="divider">

        <!-- ── VISTA DE PRIMEROS 20 NÚMEROS ── -->
        <h3>-- Primeros 20 números clasificados</h3>
        <div class="lista-resultados">
            <?php for ($j = 1; $j <= 20; $j++): ?>
                <span class="item" style="<?= $j % 2 === 0 ? 'border-color:#ff4455' : 'border-color:#aa2233' ?>">
                    <?= $j ?> — <?= $j % 2 === 0 ? 'PAR' : 'IMPAR' ?>
                </span>
            <?php endfor; ?>
            <span class="item">... hasta 200</span>
        </div>

        <!-- ── VERIFICACIÓN MATEMÁTICA ── -->
        <div class="resultado" style="margin-top:20px;">
            <h3>-- Verificación Matemática</h3>
            <p>Pares (2+4+...+200) = 100×101 = <span class="valor"><?= number_format(100*101) ?></span></p>
            <p>Impares (1+3+...+199) = 100² = <span class="valor"><?= number_format(100**2) ?></span></p>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
</body>
</html>
