<?php

    require_once 'includes/Utilidades.php';

    $error    = '';
    $estacion = null;
    $fechaStr = '';

    $infoEstaciones = [
        'Verano' => [
            'imagen' => 'https://img.magnific.com/foto-gratis/al-atardecer-playa-tropical-mar-palmeras-coco_74190-1075.jpg?semt=ais_hybrid&w=740&q=80',
            'alt'    => '',
            'desc'   => '',
        ],
        'Otono' => [
            'imagen' => 'https://www.nippon.com/es/ncommon/contents/japan-glances/161494/161494.jpg',
            'alt'    => '',
            'desc'   => '',
        ],
        'Invierno' => [
            'imagen' => 'https://images.squarespace-cdn.com/content/v1/59cee1aaa8b2b058e9643877/1575654685994-YU1JEQMD8KLCZPXVA63Z/denny-ryanto.jpg',
            'alt'    => '',
            'desc'   => '',
        ],
        'Primavera' => [
            'imagen' => 'https://queverentusviajes.com/wp-content/uploads/Fotos-de-Japon-Monte-Fuji-pagoda-y-sakura.jpg',
            'alt'    => '',
            'desc'   => '',
        ],
    ];

    function determinarEstacion(int $mes, int $dia): string
    {
    
        $clave = $mes * 100 + $dia;

        switch (true) {
        
            case ($clave >= 1221 || $clave <= 320):  return 'Verano';
            case ($clave >= 321  && $clave <= 621):  return 'Otono';
            case ($clave >= 622  && $clave <= 922):  return 'Invierno';
            case ($clave >= 923  && $clave <= 1220): return 'Primavera';
            // OWASP Secure Error Handling: caso invalido sin exponer datos internos
            default: return 'Desconocido';
        }
    }

    // Procesamiento POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // OWASP A03 - XSS: sanitizar entrada antes de cualquier uso
        $fechaRaw = Utilidades::sanitizar(Utilidades::nvl($_POST['fecha'], ''));

        // OWASP A03 - Input Validation: validar formato YYYY-MM-DD con preg_match
        if (empty($fechaRaw) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaRaw)) {
            $error = 'Ingresa una fecha valida en formato correcto.';
        } else {
            $partes = explode('-', $fechaRaw);
            $mes    = (int) $partes[1];
            $dia    = (int) $partes[2];

            // Validar rango logico de mes y dia con Utilidades::validarEntero()
            if (!Utilidades::validarEntero($mes, 1, 12) || !Utilidades::validarEntero($dia, 1, 31)) {
                // OWASP Secure Error Handling: mensaje generico sin datos del servidor
                $error = 'Fecha invalida. Verifica mes y dia.';
            } else {
                $estacion = determinarEstacion($mes, $dia);
                $fechaStr = sprintf('%02d-%02d', $dia, $mes); // Formato DD-MM para mostrar
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Problema #8 - Estacion del Ano</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .img-estacion {
            width: 100%;
            max-width: 420px;
            height: 240px;
            object-fit: cover;
            border: 2px solid var(--rojo-neon);
            border-radius: 4px;
            box-shadow: var(--sombra-neon);
            display: block;
            margin: 18px auto 0;
        }
    </style>
</head>
<body>

<header class="site-header">
    <h1>[#] Problema #8</h1>
    <p class="subtitle">Que Estacion del Ano es?</p>
</header>

<main class="container">

    <?= Utilidades::generarEnlaceMenu('index.php') ?>

    <div class="card">
        <h2>>> Estacion del Ano</h2>
        <p class="problema-info">
            Ingresa una fecha y el sistema determinara la estacion usando <strong>switch</strong>.
        </p>

        <table>
            <thead>
                <tr><th>Estacion</th><th>Periodo</th></tr>
            </thead>
            <tbody>
                <?php
            
                foreach ($infoEstaciones as $nombre => $info):
                    $periodo = match ($nombre) {
                        'Verano'    => '21 Dic al 20 Mar',
                        'Otono'     => '21 Mar al 21 Jun',
                        'Invierno'  => '22 Jun al 22 Sep',
                        'Primavera' => '23 Sep al 20 Dic',
                        default     => 'N/A'
                    };
                ?>
                <tr>
                    <!-- OWASP A03 XSS: htmlspecialchars en todo valor impreso -->
                    <td><?= htmlspecialchars($nombre) ?></td>
                    <td style="color:var(--blanco-dim); font-size:0.85rem;"><?= $periodo ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <hr class="divider">

        <form method="POST" action="">
            <div class="form-group">
                <label for="fecha">Fecha</label>
                <!-- OWASP: valor repintado pasa por sanitizar() antes de imprimirse -->
                <input type="date" id="fecha" name="fecha"
                       value="<?= isset($_POST['fecha']) ? Utilidades::sanitizar($_POST['fecha']) : '' ?>">
            </div>
            <button type="submit" class="btn-submit">Determinar Estacion</button>
        </form>

        <!-- OWASP Secure Error Handling: mensaje generico, sin rutas ni trazas PHP -->
        <?php if ($error): ?>
            <div class="alerta alerta-error"><?= $error ?></div>
        <?php endif; ?>

        <!-- Resultados con imagen -->
        <?php if ($estacion && isset($infoEstaciones[$estacion])): ?>
        <?php $info = $infoEstaciones[$estacion]; ?>
        <div class="resultado" style="text-align:center; padding:28px;">

            <p style="font-size:0.85rem; color:var(--blanco-dim);">
                Fecha ingresada: <strong><?= $fechaStr ?></strong>
            </p>

            <p style="font-size:0.95rem; color:var(--blanco-dim); margin-top:14px;">
                La estacion es:
            </p>

            <p style="font-family:var(--fuente-display); font-size:2rem;
                      color:var(--rojo-neon); text-shadow:var(--sombra-neon);
                      font-weight:900; letter-spacing:4px; margin:8px 0;">
                <?= htmlspecialchars($estacion) ?>
            </p>

            <p style="color:var(--blanco-dim); font-size:0.83rem;">
                <?= htmlspecialchars($info['desc']) ?>
            </p>

            <img
                src="<?= htmlspecialchars($info['imagen']) ?>"
                alt="<?= htmlspecialchars($info['alt']) ?>"
                class="img-estacion"
                loading="lazy"
            >
        </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
</body>
</html>