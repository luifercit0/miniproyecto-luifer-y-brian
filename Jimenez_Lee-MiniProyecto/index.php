<?php
/**
 * INDEX PRINCIPAL - Mini Proyecto #2
 * Desarrollo Web VII | Universidad Tecnológica de Panamá
 * PSR-1: punto de entrada del sistema MVC (View)
 * Punto #11: Menú que permite seleccionar el problema
 */

// ── Definición de problemas (DRY: arreglo único como fuente de verdad) ──
$problemas = [
    ['num' => 1, 'titulo' => 'Estadísticas 5 Números',    'archivo' => 'problema1.php'],
    ['num' => 2, 'titulo' => 'Suma 1 al 1,000',           'archivo' => 'problema2.php'],
    ['num' => 3, 'titulo' => 'Multiples de 4',            'archivo' => 'problema3.php'],
    ['num' => 4, 'titulo' => 'Pares\Impares 1-200',     'archivo' => 'problema4.php'],
    ['num' => 5, 'titulo' => 'Clasificar\Edades',         'archivo' => 'problema5.php'],
    ['num' => 6, 'titulo' => 'Presupuesto\Hospital',      'archivo' => 'problema6.php'],
    ['num' => 7, 'titulo' => 'Calculadora\Estadistica',   'archivo' => 'problema7.php'],
    ['num' => 8, 'titulo' => 'Estacion\del Año',         'archivo' => 'problema8.php'],
    ['num' => 9, 'titulo' => 'Potencias\del Número',      'archivo' => 'problema9.php'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Proyecto #2 — Luis Jimenez &amp; Brian Lee</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- HEADER PRINCIPAL -->
<header class="site-header">
    <h1>Mini Proyecto #2</h1>
    <p class="subtitle">Desarrollo de Software VII &nbsp;|&nbsp; Sentencias de Control &amp; Clases &nbsp;|&nbsp; PHP</p>
</header>

<!-- CONTENIDO -->
<main class="container">

    <div class="card">
        <h2>Selecciona un Problema</h2>
        <p class="problema-info">
            Universidad Tecnologica de Panama &nbsp;|&nbsp; Facultad de Ingenieria en Sistemas Computacionales<br>
            Instructor: Ing. Irina Fong &nbsp;|&nbsp; Estudiantes: <strong> Luis Jimenez &amp; Brian Lee</strong><br>
            Aplicando: PSR-1 &middot; PSR-4 &middot; DRY &middot; OWASP &middot; MVC &middot; POO
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

    <!-- INFO DEL PROYECTO -->
    <div class="card">
        <h3>Tecnologias &amp; Estandares Aplicados</h3>
        <table>
            <thead>
                <tr>
                    <th>Estandar / Tecnologia</th>
                    <th>Descripcion</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>PSR-1</td><td>StudlyCaps para clases, camelCase para metodos y variables</td></tr>
                <tr><td>Principio DRY</td><td>Codigo centralizado; sin duplicacion de logica ni HTML</td></tr>
                <tr><td>OWASP A03 - XSS</td><td>htmlspecialchars() en toda salida de datos al navegador</td></tr>
                <tr><td>OWASP A03 - Input Validation</td><td>filter_var() y preg_match() validan tipos antes de procesar</td></tr>
                <tr><td>OWASP - Error Management</td><td>Mensajes genericos sin exponer rutas ni stack traces de PHP</td></tr>
                <tr><td>MVC</td><td>Logica de negocio separada de la presentacion (includes/)</td></tr>
                <tr><td>PHP 8+</td><td>Tipos mixtos, match(), operador ternario, foreach</td></tr>
                <tr><td>Footer externo</td><td>includes/footer.php con la fecha de ejecucion dinamica</td></tr>
            </tbody>
        </table>
    </div>

</main>

<!-- FOOTER EXTERNO (Punto #6 del documento) -->
<?php require_once 'includes/footer.php'; ?>

</body>
</html>
