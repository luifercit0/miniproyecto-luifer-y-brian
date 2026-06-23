<?php
require_once 'includes/Utilidades.php';

$suma       = 0;
$detallePares = 0;
$detalleImpares = 0;

for ($i = 1; $i <= 1000; $i++) {
    $suma += $i;
    // Operador ternario
    $i % 2 === 0 ? $detallePares += $i : $detalleImpares += $i;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Problema #2 — Suma 1 al 1000</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="site-header">
    <h1>[#] Problema #2</h1>
    <p class="subtitle">Suma de los Números del 1 al 1,000</p>
</header>

<main class="container">
    <?= Utilidades::generarEnlaceMenu('index.php') ?>

    <div class="card">
        <h2>>>Suma 1 al 1,000</h2>
        <p class="problema-info">
            Utiliza un ciclo <strong>for</strong> para sumar todos los enteros del 1 al 1,000.
            Resultado esperado: <strong>500,500</strong>
        </p>

        <!-- ── RESULTADO PRINCIPAL ── -->
        <div class="resultado">
            <h3>-- Resultado del FOR</h3>
            <p>Suma total (1 + 2 + ... + 1000):</p>
            <p style="font-size:2rem; margin-top:10px;">
                <span class="valor"><?= number_format($suma) ?></span>
            </p>
        </div>

        <hr class="divider">

        <table>
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Suma total (1–1000)</td>
                    <td><span class="valor"><?= number_format($suma) ?></span></td>
                </tr>
                <tr>
                    <td>Suma de números <strong>pares</strong> (2, 4, ... 1000)</td>
                    <td><?= number_format($detallePares) ?></td>
                </tr>
                <tr>
                    <td>Suma de números <strong>impares</strong> (1, 3, ... 999)</td>
                    <td><?= number_format($detalleImpares) ?></td>
                </tr>
                <tr>
                    <td>Verificación (Gauss): n×(n+1)/2</td>
                    <td><?= number_format(1000 * 1001 / 2) ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Muestra los primeros 10 acumulados como referencia -->
        <h3 style="margin-top:24px;">-- Primeros 10 acumulados</h3>
        <div class="lista-resultados">
            <?php
            $acc = 0;
            for ($j = 1; $j <= 10; $j++) {
                $acc += $j;
                echo "<span class='item'>{$j} → {$acc}</span>";
            }
            ?>
            <span class="item">... hasta 1000 → <?= number_format($suma) ?></span>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
</body>
</html>