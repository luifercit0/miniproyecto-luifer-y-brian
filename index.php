<?php
/**
 * INDEX PRINCIPAL - Mini Proyecto #2
 * Desarrollo Web VII | Universidad Tecnológica de Panamá
 * PSR-1: punto de entrada del sistema MVC (View)
 * Punto #11: Menú que permite seleccionar el problema
 */

// ── Definición de problemas (DRY: arreglo único como fuente de verdad) ──
$problemas = [
    ['num' => 1, 'titulo' => 'Estadisticas 5 Números',    'archivo' => 'problema1.php'],
    ['num' => 2, 'titulo' => 'Sumas del 1 al 1,000',           'archivo' => 'problema2.php'],
    ['num' => 3, 'titulo' => 'Multiples de 4',            'archivo' => 'problema3.php'],
    ['num' => 4, 'titulo' => 'Pares e Impares 1-200',     'archivo' => 'problema4.php'],
    ['num' => 5, 'titulo' => 'Clasificar Edades',         'archivo' => 'problema5.php'],
    ['num' => 6, 'titulo' => 'Presupuesto del Hospital',      'archivo' => 'problema6.php'],
    ['num' => 7, 'titulo' => 'Calculadora de Estadistica',   'archivo' => 'problema7.php'],
    ['num' => 8, 'titulo' => 'Estacion del Año',         'archivo' => 'problema8.php'],
    ['num' => 9, 'titulo' => 'Potencias del Número',      'archivo' => 'problema9.php'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Proyecto #2 — Jimenez &amp; Lee</title>
    <!-- CSS global Tron-Rojo (Punto: un solo CSS para todos) -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- HEADER PRINCIPAL -->
<header class="site-header">
    <h1>Mini Proyecto #2</h1>
    <p class="subtitle">Desarrollo Web VII &nbsp;|&nbsp; Sentencias de Control &amp; Clases &nbsp;|&nbsp; PHP</p>
</header>

<!-- CONTENIDO -->
<main class="container">

    <div class="card">
        <h2>Selecciona un Problema</h2>
        <p class="problema-info">
            Universidad Tecnologica de Panama &nbsp;|&nbsp; Facultad de Ingenieria en Sistemas Computacionales<br>
            Estudiantes: <strong>Luis Jimenez &amp; Brian Lee</strong><br>
        </p>

        <!-- MENU GRID (Punto #11) -->
        <nav class="menu-grid" aria-label="Menu de problemas">
            <?php
            /*
             * DRY: renderizado de botones con un solo foreach.
             * Si se agrega un problema, solo se edita el arreglo $problemas arriba.
             * OWASP A03 - htmlspecialchars() en $titulo y $archivo evita que
             * cualquier caracter especial inyectado sea interpretado como HTML/script
             * en el atributo href o en el texto del boton.
             */
            foreach ($problemas as $p):
                $titulo  = htmlspecialchars($p['titulo'],  ENT_QUOTES, 'UTF-8');
                $archivo = htmlspecialchars($p['archivo'], ENT_QUOTES, 'UTF-8');
            ?>
            <a href="<?= $archivo ?>" class="btn-problema" aria-label="Problema <?= $p['num'] ?>">
                <span class="num"><?= sprintf('%02d', $p['num']) ?></span>
                <?= nl2br($titulo) ?>
            </a>
            <?php endforeach; ?>
        </nav>
    </div>

</main>

<!-- FOOTER EXTERNO (Punto #6 del documento) -->
<?php require_once 'includes/footer.php'; ?>

</body>
</html>
