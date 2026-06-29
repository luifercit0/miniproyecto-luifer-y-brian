# Mini Proyecto #2 — Sentencias de Control y Clases

Resolviendo problemas con estructuras de decisión y repetición en PHP

## Introducción

Este proyecto fue desarrollado para el curso **Desarrollo Web VII** de la Universidad Tecnológica de Panamá, como parte del Mini Proyecto #2. Consiste en una aplicación web en PHP que resuelve 9 problemas algorítmicos distintos (estadística, ciclos, clasificación, validación de fechas, potencias, entre otros), aplicando estructuras de control (`if`, `switch`, operadores ternarios), estructuras repetitivas (`for`, `while`, `foreach`), arreglos, funciones y Programación Orientada a Objetos.

Todo el sistema sigue el principio **DRY** (Don't Repeat Yourself), centralizando la lógica repetida (validaciones, cálculos matemáticos, navegación y pie de página) en una única clase de utilidades, además de aplicar buenas prácticas de codificación (**PSR-1**) y recomendaciones de seguridad (**OWASP Top 10**).

## Tecnologías Utilizadas

- **PHP** (programación orientada a objetos, métodos estáticos)
- **HTML5 / CSS3** — hoja de estilos única con tema visual "Tron Rojo" (oscuro, acentos neón)
- **JavaScript**
- **Chart.js (v4.4.0, vía CDN)** — generación de gráficas interactivas
- **Git / GitHub** — control de versiones

## Estructura del Proyecto

```
Jimenez_Lee-MiniProyecto/
├── index.php                 # Menú principal (selección de problemas)
├── problema1.php              # Estadísticas de 5 números
├── problema2.php              # Suma del 1 al 1,000
├── problema3.php              # N primeros múltiplos de 4
├── problema4.php              # Pares e impares del 1 al 200
├── problema5.php              # Clasificación de edades
├── problema6.php              # Presupuesto del hospital
├── problema7.php              # Calculadora de datos estadísticos
├── problema8.php              # Estación del año
├── problema9.php              # Potencias de un número
├── includes/
│   ├── Utilidades.php          # Clase de utilidades (métodos estáticos)
│   └── footer.php              # Footer dinámico reutilizable
├── css/
│   └── style.css               # Estilo global (tema Tron Rojo)
└── img/                         # Íconos SVG de las estaciones del año
```

El proyecto sigue una separación de responsabilidades tipo MVC simplificado: la lógica de negocio (cálculos y validaciones) vive en `Utilidades.php`, mientras que cada `problemaN.php` actúa como vista/controlador del problema correspondiente.

## Clase de Utilidades y Funciones Principales

Toda la lógica reutilizable está centralizada en la clase estática `Utilidades` (`includes/Utilidades.php`), cumpliendo el principio DRY y el punto del taller que exige que las funciones matemáticas no estén "contra la piel del código":

| Método | Descripción |
|---|---|
| `sanitizar()` | Limpia entradas de usuario con `htmlspecialchars()` (prevención de XSS) |
| `validarEntero()` / `validarNumero()` | Validan enteros y números con `filter_var()` |
| `validarFecha()` | Valida formato de fecha con `preg_match()` |
| `validarEnLista()` | Valida valores contra una lista blanca (`in_array()`) |
| `nvl()` | Evita warnings de índices indefinidos (similar al `NVL` de SQL) |
| `raizCuadrada()` / `potencia()` | Envuelven `sqrt()` y `pow()` de forma segura |
| `calcularMedia()`, `calcularMin()`, `calcularMax()` | Estadística básica sobre arreglos |
| `desviacionEstandar()` / `desviacionEstandarPoblacional()` | Cálculo de desviación estándar (muestral y poblacional) |
| `clasificarEdad()` | Clasifica una edad por categoría usando `switch` |
| `detectarRepetidos()` | Detecta valores duplicados en un arreglo |
| `generarMultiplos()` | Genera los N primeros múltiplos de un número |
| `generarEnlaceMenu()` | Genera el botón "Volver al Menú", sanitizando la URL |

### Footer dinámico

El pie de página (`includes/footer.php`) se incluye como archivo externo en todas las vistas y genera la fecha y hora de ejecución de forma dinámica con `date('d/m/Y')` y `date('H:i:s')`, usando la zona horaria de Panamá (`date_default_timezone_set('America/Panama')`), tal como lo exige el punto 6 del taller.

### Gráficas con Chart.js

Las gráficas se generan en el navegador usando la librería **Chart.js (v4.4.0)**, cargada desde CDN. El backend en PHP calcula los datos y los inyecta al script con `json_encode()`:

- **Problema #5 (Clasificación de edades):** gráfica de barras y de dona mostrando la distribución de personas por categoría.
- **Problema #6 (Presupuesto del hospital):** gráfica de pastel (`type: 'pie'`) que distribuye visualmente el presupuesto entre Ginecología, Traumatología y Pediatría según sus porcentajes.

## Aplicación de OWASP

- **Prevención de XSS:** toda entrada del usuario pasa por `Utilidades::sanitizar()` (basada en `htmlspecialchars`) antes de imprimirse en pantalla.
- **Validación de entradas:** los datos numéricos (presupuesto, edades, notas) se validan con `filter_var()` antes de procesarse; si el dato no es válido, se detiene la ejecución con un mensaje de error controlado.
- **Gestión de errores segura:** los `switch` e `if-else` siempre incluyen un caso `default` o `else` genérico, evitando exponer rutas internas o errores de PHP al usuario final.

## Descripción de los Problemas

### Problema #1 — Estadísticas de 5 números
Calcula media, desviación estándar, mínimo y máximo de 5 números positivos ingresados por formulario.

<img width="870" height="936" alt="Captura de pantalla 2026-06-29 163743" src="https://github.com/user-attachments/assets/1c668bb0-fb3a-47bd-8372-f428dd808acd" />


### Problema #2 — Suma del 1 al 1,000
Suma todos los enteros del 1 al 1,000 usando un ciclo `for` (resultado esperado: 500,500).

<img width="911" height="781" alt="Captura de pantalla 2026-06-29 163917" src="https://github.com/user-attachments/assets/9ca68550-147c-4693-9ad2-d8248b06b12c" />


### Problema #3 — Múltiplos de 4
Genera los N primeros múltiplos de 4, donde N es ingresado por el usuario.

<img width="846" height="774" alt="Captura de pantalla 2026-06-29 163958" src="https://github.com/user-attachments/assets/d227adfb-3a75-4491-ab62-8d538798a53f" />


### Problema #4 — Pares e impares (1–200)
Calcula, de forma independiente, la suma de los números pares e impares entre 1 y 200 usando un ciclo `while` y operador ternario.

<img width="888" height="838" alt="Captura de pantalla 2026-06-29 164437" src="https://github.com/user-attachments/assets/a4187437-ac11-4e04-986f-9b08d5799778" />


### Problema #5 — Clasificación de edades
Clasifica la edad de 5 personas en niño, adolescente, adulto o adulto mayor, detecta edades repetidas y muestra gráficas de barras y dona.

<img width="859" height="891" alt="Captura de pantalla 2026-06-29 164127" src="https://github.com/user-attachments/assets/2f42230c-fcf5-47a4-816b-3173bfb8988f" />
<img width="809" height="833" alt="Captura de pantalla 2026-06-29 164208" src="https://github.com/user-attachments/assets/693e2af9-624a-4eae-9f98-e7491b237336" />

### Problema #6 — Presupuesto del hospital
Distribuye un presupuesto hospitalario entre Ginecología (40%), Traumatología (35%) y Pediatría (25%), con gráfica de pastel.

<img width="757" height="941" alt="Captura de pantalla 2026-06-29 164540" src="https://github.com/user-attachments/assets/beebeabf-1920-4604-a5c1-91dca12d84d5" />

### Problema #7 — Calculadora de datos estadísticos
Solicita N notas mediante un formulario en dos etapas y calcula promedio, desviación estándar, nota mínima y máxima usando `foreach`.

<img width="783" height="678" alt="Captura de pantalla 2026-06-29 164709" src="https://github.com/user-attachments/assets/377eb246-ffa7-4931-b9a1-7bf348626388" />


### Problema #8 — Estación del año
Determina la estación del año a partir de una fecha ingresada, validando el formato con `preg_match()`.

<img width="743" height="806" alt="Captura de pantalla 2026-06-29 165027" src="https://github.com/user-attachments/assets/05db3a88-8df8-491a-bd5b-01a69ae8f4c0" />


### Problema #9 — Potencias de un número
Calcula las 15 primeras potencias de un número entre 1 y 9 ingresado por el usuario.

<img width="740" height="416" alt="image" src="https://github.com/user-attachments/assets/45f14f88-47b5-4ae0-8e20-7aef7cd7ba72" />
<img width="714" height="768" alt="image" src="https://github.com/user-attachments/assets/b93e0aaf-b1b4-44fc-8ddb-21d92fe806cc" />


## Dificultades y Solución

La mayor dificultad fue mantener consistencia visual y arquitectónica en los 9 problemas, asegurando que cada uno compartiera el mismo estilo y estructura. Para solucionarlo, aplicamos el principio DRY centralizando en Utilidades toda la lógica repetitiva (validaciones, cálculos, navegación y footer). Además, unificamos el diseño con un solo archivo CSS y utilizamos Chart.js para todas las gráficas, logrando un proyecto limpio, ordenado y visualmente coherente.

## Información de los Estudiantes

| Campo | Información |
|---|---|
| Nombre | Luis Jiménez (8-1018-1285) |
| Nombre | Brian Lee (8-1031-2047) |
| Curso | Desarrollo de Software 7 |
| Fecha de Ejecución del Laboratorio | el de hoy |
| Instructor del Laboratorio | Irina Fong |
