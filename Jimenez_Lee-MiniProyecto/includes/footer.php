<?php
/**
 * FOOTER EXTERNO - Requerido por punto #6 del documento
 * PSR-1: archivo de inclusión con responsabilidad única
 * Muestra la fecha del día en que se ejecuta el sistema
 */

// Obtener la fecha actual del servidor (DRY: lógica centralizada aquí)
$fechaActual = date('d/m/Y'); // Día/Mes/Año
$horaActual  = date('H:i:s'); // Hora en formato 24h
?>
<!-- FOOTER EXTERNO - includes/footer.php -->
<footer class="site-footer">
    <p>
        Mini Proyecto #2 &nbsp;|&nbsp; Desarrollo de Software VII &nbsp;|&nbsp; UTP
    </p>
    <p style="margin-top:6px;">
        Fecha de ejecución:
        <span class="fecha-footer"><?= $fechaActual ?></span>
        &nbsp;|&nbsp;
        Hora: <span class="fecha-footer"><?= $horaActual ?></span>
    </p>
    <p style="margin-top:6px;">
        Elaborado por: <span class="fecha-footer"> Luis Jiménez &amp; Brian Lee</span>
        &nbsp;|&nbsp; Universidad Tecnológica de Panamá
    </p>
</footer>
