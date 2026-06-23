<?php
class Utilidades
{
    /**
     * OWASP A03 - XSS: convierte caracteres peligrosos en entidades HTML:
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
     * Whitelist: acepta solo valores que estan en $listPermitida.
     */
    public static function validarEnLista(mixed $valor, array $listPermitida): bool
    {
        return in_array($valor, $listPermitida, true);
    }

    /**
     * Retorna el valor si esta definido; si no, retorna $default.
     */
    public static function nvl(mixed &$var, mixed $default = ''): mixed
    {
        return isset($var) ? $var : $default;
    }

    /**
     * Envuelve sqrt() con proteccion contra numeros negativos.
     * Retorna 0.0 si el numero es negativo (evita NaN).
     * Usada internamente por desviacionEstandar().
     */
    public static function raizCuadrada(float $num): float
    {
        return $num >= 0 ? sqrt($num) : 0.0;
    }

    /**
     * Usada en: problema9
     */
    public static function potencia(float $base, int $exp): float
    {
        return pow($base, $exp);
    }

    /**
     * Llama a raizCuadrada() y no usa sqrt() directamente (DRY).
     * Usada en: problema1, problema7
     */
    public static function desviacionEstandar(array $datos): float
    {
        $n = count($datos);
        if ($n < 2) return 0.0;      
        $media = array_sum($datos) / $n;
        $suma  = 0;
        foreach ($datos as $x) {
            $suma += pow($x - $media, 2);
        }
        return self::raizCuadrada($suma / ($n - 1));
    }

    /**
     * Genera el boton HTML de regreso al menu principal.
     */
    public static function generarEnlaceMenu(string $url = 'index.php'): string
    {
        // OWASP: limpiar la URL antes de escribirla en el HTML
        $urlSanitizada = filter_var($url, FILTER_SANITIZE_URL);
        return '<a href="' . $urlSanitizada . '" class="btn-back">Volver al Menu</a>';
    }

    /**
     * Calcula el promedio aritmetico de un arreglo.
     */
    public static function calcularMedia(array $datos): float
    {
        $n = count($datos);
        if ($n === 0) return 0.0;
        return array_sum($datos) / $n;
    }

    /**
     * Retorna el valor minimo de un arreglo.
     */
    public static function calcularMin(array $datos): float
    {
        return empty($datos) ? 0.0 : (float) min($datos);
    }

    /**
     * Retorna el valor maximo de un arreglo.
     */
    public static function calcularMax(array $datos): float
    {
        return empty($datos) ? 0.0 : (float) max($datos);
    }

    /**
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

    /**
     * Retorna el arreglo de categorias de edad (fuente unica — DRY).
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
     * Clasifica una edad en su categoria usando SWITCH.
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
     * Detecta valores duplicados en un arreglo.
     */
    public static function detectarRepetidos(array $datos): array
    {
        $frecuencias = array_count_values($datos);
        return array_filter($frecuencias, fn($cantidad) => $cantidad > 1);
    }

    /**
     * Genera los primeros N multiplos de una base dada con un FOR.
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