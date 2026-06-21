<?php
// Obtener la fecha actual del servidor
date_default_timezone_set('America/Panama');
$fechaActual = date('d/m/Y');
$horaActual  = date('H:i:s');
?>

<footer class="site-footer">
    <p>
        Mini Proyecto #2 &nbsp;|&nbsp; Desarrollo VII &nbsp;|&nbsp; UTP
    </p>
    <p style="margin-top:6px;">
        Fecha de ejecución:
        <span class="fecha-footer"><?= $fechaActual ?></span>
        &nbsp;|&nbsp;
        Hora: <span class="fecha-footer"><?= $horaActual ?></span>
    </p>
    <p style="margin-top:6px;">
        Elaborado por: <span class="fecha-footer">Luis Jiménez &amp; Brian Lee</span>
        &nbsp;|&nbsp; Universidad Tecnológica de Panamá
    </p>
</footer>
