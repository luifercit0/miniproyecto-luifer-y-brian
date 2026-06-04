<?php
/**
 * ============================================================
 * CLASE UTILIDADES
 * Punto #7 y #10 del documento
 * PSR-1: StudlyCaps para nombre de clase, camelCase para metodos
 * Principio DRY: toda logica de validacion/sanitizacion centralizada aqui.
 *   Ningun problema repite estas funciones; todas las llaman desde aqui.
 * ============================================================
 *
 * METODOS ESTATICOS DISPONIBLES (todos llamados como Utilidades::metodo()):
 * ─────────────────────────────────────────────────────────────
 * SANITIZACION / XSS:
 *   sanitizar($valor)             → htmlspecialchars + trim. Previene XSS.
 *
 * VALIDACION (Input Validation):
 *   validarEntero($val,$min,$max) → filter_var FILTER_VALIDATE_INT
 *   validarNumero($valor)         → filter_var FILTER_VALIDATE_FLOAT
 *   validarFecha($fecha)          → preg_match formato fecha
 *   validarEnLista($val,$lista)   → in_array whitelist
 *   nvl(&$var, $default)          → null-safe getter
 *
 * MATEMATICAS (no van "contra la piel" del codigo, van aqui):
 *   raizCuadrada($num)            → sqrt() segura (evita negativo)
 *   potencia($base, $exp)         → pow()
 *   desviacionEstandar($datos)    → formula S = sqrt(sum(x-media)^2 / n-1)
 *
 * NAVEGACION (Punto #12):
 *   generarEnlaceMenu($url)       → genera <a href> del boton "Volver al Menu"
 * ─────────────────────────────────────────────────────────────
 *
 * APLICACIONES OWASP IMPLEMENTADAS EN ESTA CLASE:
 *
 * [OWASP A03:2021 - Injection / XSS]
 *   sanitizar(): usa htmlspecialchars(ENT_QUOTES, UTF-8) para codificar
 *   caracteres como <, >, ", ', & antes de imprimirlos en HTML.
 *   Esto evita que un atacante inyecte <script>alert()</script> a traves
 *   de un campo de formulario y que el navegador lo ejecute.
 *   Referencia: https://www.php.net/manual/es/function.htmlspecialchars.php
 *
 * [OWASP A03:2021 - Injection / Input Validation]
 *   validarEntero(): usa filter_var(FILTER_VALIDATE_INT) con rango min/max.
 *   Garantiza que solo se procese un entero del tipo esperado.
 *   Si el usuario envia texto o un numero fuera de rango, se rechaza antes
 *   de que llegue a cualquier calculo o salida.
 *   Referencia: https://www.php.net/manual/es/function.filter-var.php
 *
 *   validarNumero(): usa filter_var(FILTER_VALIDATE_FLOAT) para presupuestos.
 *   Igual principio: dato rechazado si no es el tipo correcto.
 *
 *   validarFecha(): usa preg_match para verificar el patron de fecha.
 *   Referencia: https://www.php.net/manual/es/function.preg-match.php
 *
 * [OWASP - Secure Error Handling]
 *   generarEnlaceMenu(): usa filter_var(FILTER_SANITIZE_URL) para limpiar
 *   la URL del enlace antes de escribirla en el atributo href.
 *   Evita inyeccion de javascript: o data: en href.
 *
 * USO EN CADA PROBLEMA:
 *   Todo campo POST pasa por Utilidades::sanitizar() ANTES de validarse,
 *   y se valida con validarEntero/validarNumero ANTES de procesarse.
 *   Los mensajes de error son genericos (sin rutas, sin trazas PHP).
 */
class Utilidades
{
    // ══════════════════════════════════════════════════════════
    // SANITIZACION
    // OWASP A03:2021 - Prevencion de XSS (Cross-Site Scripting)
    // ══════════════════════════════════════════════════════════

    /**
     * sanitizar()
     * Aplica htmlspecialchars() + trim() sobre cualquier valor de entrada.
     *
     * OWASP A03 - XSS: convierte caracteres peligrosos en entidades HTML:
     *   <  →  &lt;      >  →  &gt;
     *   "  →  &quot;    '  →  &#039;    &  →  &amp;
     * Asi, aunque el usuario ingrese <script>alert(1)</script>, el navegador
     * lo mostrara como texto, no lo ejecutara como codigo.
     *
     * Se llama en TODOS los problemas antes de imprimir $_POST en HTML:
     *   Utilidades::sanitizar($_POST['campo'])
     */
    public static function sanitizar(string $valor): string
    {
        // htmlspecialchars con ENT_QUOTES cubre tanto " como '
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }

    // ══════════════════════════════════════════════════════════
    // VALIDACION DE ENTRADAS
    // OWASP A03:2021 - Input Validation
    // ══════════════════════════════════════════════════════════

    /**
     * validarEntero()
     * Usa filter_var(FILTER_VALIDATE_INT) con rango min/max.
     *
     * OWASP A03 - Input Validation: garantiza que el dato sea del tipo
     * esperado (entero dentro de rango) ANTES de usarlo en calculos.
     * Si falla, el problema muestra un mensaje de error generico y NO procesa.
     *
     * Usado en: problema1, problema3, problema5, problema7, problema8, problema9
     */
    public static function validarEntero(mixed $valor, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): bool
    {
        return filter_var($valor, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => $min, 'max_range' => $max]
        ]) !== false;
    }

    /**
     * validarNumero()
     * Usa filter_var(FILTER_VALIDATE_FLOAT) para numeros decimales positivos.
     *
     * OWASP A03 - Input Validation: el presupuesto hospitalario (problema6)
     * no se procesa si el usuario envia texto o un valor negativo.
     */
    public static function validarNumero(mixed $valor): bool
    {
        return filter_var($valor, FILTER_VALIDATE_FLOAT) !== false && $valor > 0;
    }

    /**
     * validarFecha()
     * Usa preg_match para verificar patron de fecha DD-MM o MM/DD/YYYY.
     *
     * OWASP A03 - Input Validation: evita procesar cadenas arbitrarias como fechas.
     * Referencia: https://www.php.net/manual/es/function.preg-match.php
     *
     * Usado en: problema8
     */
    public static function validarFecha(string $fecha): bool
    {
        return (bool) preg_match('/^\d{2}[-\/]\d{2}([-\/]\d{4})?$/', $fecha);
    }

    /**
     * validarEnLista()
     * Whitelist: acepta solo valores que estan en $listPermitida.
     * DRY: evita repetir in_array() en multiples archivos.
     */
    public static function validarEnLista(mixed $valor, array $listPermitida): bool
    {
        return in_array($valor, $listPermitida, true);
    }

    /**
     * nvl()
     * Retorna el valor si esta definido; si no, retorna $default.
     * Equivalente al NVL() de bases de datos. Evita Undefined index warnings.
     */
    public static function nvl(mixed &$var, mixed $default = ''): mixed
    {
        return isset($var) ? $var : $default;
    }

    // ══════════════════════════════════════════════════════════
    // FUNCIONES MATEMATICAS
    // Punto #7: "no deben estar contra la piel del codigo, sino en Utilidades"
    // ══════════════════════════════════════════════════════════

    /**
     * raizCuadrada()
     * Envuelve sqrt() con proteccion contra numeros negativos.
     * Retorna 0.0 si el numero es negativo (evita NaN).
     * Usada internamente por desviacionEstandar().
     */
    public static function raizCuadrada(float $num): float
    {
        return $num >= 0 ? sqrt($num) : 0.0;
    }

    /**
     * potencia()
     * Envuelve pow(base, exp).
     * Centralizada aqui para cumplir DRY: el problema9 la llama sin usar pow() directo.
     * Usada en: problema9
     */
    public static function potencia(float $base, int $exp): float
    {
        return pow($base, $exp);
    }

    /**
     * desviacionEstandar()
     * Formula del documento: S = sqrt( Sum(x - x_media)^2 / (n - 1) )
     * Llama a raizCuadrada() y no usa sqrt() directamente (DRY).
     * Usada en: problema1, problema7
     */
    public static function desviacionEstandar(array $datos): float
    {
        $n = count($datos);
        if ($n < 2) return 0.0;            // No hay desviacion con menos de 2 datos
        $media = array_sum($datos) / $n;
        $suma  = 0;
        foreach ($datos as $x) {
            $suma += pow($x - $media, 2);  // Acumula (x - x_media)^2
        }
        return self::raizCuadrada($suma / ($n - 1));
    }

    // ══════════════════════════════════════════════════════════
    // NAVEGACION
    // Punto #12: enlace "Volver al Menu" centralizado en funcion con parametro URL
    // ══════════════════════════════════════════════════════════

    /**
     * generarEnlaceMenu()
     * Genera el boton HTML de regreso al menu principal.
     *
     * Punto #12 del documento: "Utilizar un enlace para volver al menu principal.
     * Que ese enlace este en una funcion, pasar el parametro URL."
     *
     * PSR-1 camelCase. DRY: el boton "Volver al Menu" se define UNA sola vez aqui.
     * Todos los problemas lo llaman: Utilidades::generarEnlaceMenu('index.php')
     *
     * OWASP - URL Sanitization: filter_var(FILTER_SANITIZE_URL) elimina caracteres
     * peligrosos de la URL antes de escribirla en href, previniendo
     * inyecciones del tipo javascript: o data: en el atributo href.
     *
     * @param string $url  URL de destino (por defecto 'index.php')
     * @return string      Etiqueta <a> con clase btn-back lista para imprimir
     */
    public static function generarEnlaceMenu(string $url = 'index.php'): string
    {
        // OWASP: limpiar la URL antes de escribirla en el HTML
        $urlSanitizada = filter_var($url, FILTER_SANITIZE_URL);
        return '<a href="' . $urlSanitizada . '" class="btn-back">Volver al Menu</a>';
    }

    // ══════════════════════════════════════════════════════════
    // CALCULOS ESTADISTICOS GENERALES
    // Extraidos de PRoblema1Media.php y Problema7CalvularodraDatos.php
    // DRY: un solo lugar para promedio, min, max
    // ══════════════════════════════════════════════════════════

    /**
     * calcularMedia()
     * Calcula el promedio aritmetico de un arreglo.
     * Extraido de PRoblema1Media.php (calculo estaba inline en el bloque POST).
     * DRY: problema1 y problema7 lo comparten desde aqui.
     */
    public static function calcularMedia(array $datos): float
    {
        $n = count($datos);
        if ($n === 0) return 0.0;
        return array_sum($datos) / $n;
    }

    /**
     * calcularMin()
     * Retorna el valor minimo de un arreglo.
     * Extraido de PRoblema1Media.php (min($numeros)).
     */
    public static function calcularMin(array $datos): float
    {
        return empty($datos) ? 0.0 : (float) min($datos);
    }

    /**
     * calcularMax()
     * Retorna el valor maximo de un arreglo.
     * Extraido de PRoblema1Media.php (max($numeros)).
     */
    public static function calcularMax(array $datos): float
    {
        return empty($datos) ? 0.0 : (float) max($datos);
    }

    /**
     * desviacionEstandarPoblacional()
     * Formula poblacional: S = sqrt( Sum(x - x_media)^2 / n )
     * Extraido de PRoblema1Media.php y Problema7CalvularodraDatos.php,
     * que usaban division entre n (no n-1).
     * NOTA: desviacionEstandar() existente usa (n-1) — formula muestral del documento.
     * Esta version (poblacional) respeta la logica de los archivos subidos.
     * Usada en: problema1, problema7
     */
    public static function desviacionEstandarPoblacional(array $datos): float
    {
        $n = count($datos);
        if ($n === 0) return 0.0;
        $media = self::calcularMedia($datos);
        $suma  = 0;
        foreach ($datos as $x) {
            $suma += pow($x - $media, 2);
        }
        return self::raizCuadrada($suma / $n);
    }

    // ══════════════════════════════════════════════════════════
    // CLASIFICACION DE EDADES
    // Extraido de Problema5Edad.php
    // DRY: la logica de categorias existe una sola vez aqui
    // ══════════════════════════════════════════════════════════

    /**
     * getCategorias()
     * Retorna el arreglo de categorias de edad (fuente unica — DRY).
     * Extraido de Problema5Edad.php donde estaba definido inline.
     * problema5 lo llama para clasificar y para renderizar tabla y graficas.
     */
    public static function getCategorias(): array
    {
        return [
            'nino'        => ['min' =>  0, 'max' =>  12, 'nombre' => 'Nino'],
            'adolescente' => ['min' => 13, 'max' =>  17, 'nombre' => 'Adolescente'],
            'adulto'      => ['min' => 18, 'max' =>  64, 'nombre' => 'Adulto'],
            'mayor'       => ['min' => 65, 'max' => 150, 'nombre' => 'Adulto Mayor'],
        ];
    }

    /**
     * clasificarEdad()
     * Clasifica una edad en su categoria usando SWITCH.
     * Extraido de Problema5Edad.php (clasificacion con foreach inline).
     * PSR-1: camelCase. DRY: unico punto de clasificacion de edades.
     * OWASP Secure Error Handling: default sin exponer datos internos.
     */
    public static function clasificarEdad(int $edad): string
    {
        switch (true) {
            case ($edad >=  0 && $edad <= 12): return 'nino';
            case ($edad >= 13 && $edad <= 17): return 'adolescente';
            case ($edad >= 18 && $edad <= 64): return 'adulto';
            case ($edad >= 65):                return 'mayor';
            default: return 'desconocido'; // OWASP: no expone logica interna
        }
    }

    /**
     * detectarRepetidos()
     * Detecta valores duplicados en un arreglo.
     * Extraido de Problema5Edad.php (array_filter sobre frecuencias inline).
     * DRY: reutilizable para edades u otros datos.
     *
     * @return array  [valor => cantidad] solo de los que se repiten
     */
    public static function detectarRepetidos(array $datos): array
    {
        $frecuencias = array_count_values($datos);
        return array_filter($frecuencias, fn($cantidad) => $cantidad > 1);
    }

    // ══════════════════════════════════════════════════════════
    // GENERACION DE MULTIPLOS
    // Extraido de Problema3multiplos.php
    // DRY: logica de negocio separada del HTML
    // ══════════════════════════════════════════════════════════

    /**
     * generarMultiplos()
     * Genera los primeros N multiplos de una base dada con un FOR.
     * Extraido de Problema3multiplos.php (bucle for estaba dentro del POST).
     * DRY: si otro problema necesita multiplos, llama esta funcion.
     *
     * @param  int   $base  Numero base (ej: 4)
     * @param  int   $n     Cantidad de multiplos
     * @return array        Arreglo de ['i'=>indice, 'valor'=>resultado]
     */
    public static function generarMultiplos(int $base, int $n): array
    {
        $resultado = [];
        for ($i = 1; $i <= $n; $i++) {
            $resultado[] = ['i' => $i, 'valor' => $base * $i];
        }
        return $resultado;
    }
}
