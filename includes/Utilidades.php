<?php
class Utilidades
{
    /**
     * OWASP A03 - XSS: convierte caracteres peligrosos en entidades HTML:
     *   <  →  &lt;      >  →  &gt;
     *   "  →  &quot;    '  →  &#039;    &  →  &amp;
     */
    public static function sanitizar(string $valor): string
    {
        // htmlspecialchars con ENT_QUOTES cubre tanto " como '
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }

    /*
     * Usado en: problema1, problema3, problema5, problema7, problema8, problema9
     */
    public static function validarEntero(mixed $valor, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): bool
    {
        return filter_var($valor, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => $min, 'max_range' => $max]
        ]) !== false;
    }

    /*
     * OWASP A03 - Input Validation: el presupuesto hospitalario (problema6)
     */
    public static function validarNumero(mixed $valor): bool
    {
        return filter_var($valor, FILTER_VALIDATE_FLOAT) !== false && $valor > 0;
    }

    /*
     * Usa preg_match para verificar patron de fecha DD-MM o MM/DD/YYYY.
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
