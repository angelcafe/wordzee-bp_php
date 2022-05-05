<?php
require('back/buscapalabras.php');
define('VERSION', '0.3.0');
define('PE_PERMITIDOS', ['DP', 'TP', 'DL', 'TL', '']);
$post_existe = true;
for ($x = 0; $x < 5; $x++) {
    for ($y = 0; $y < 3 + $x; $y++) {
        $tmp = 'pal' . ($x + 3) . 'let' . ($y + 1);
        $puntos_extra[$x][$y] = (isset($_POST[$tmp]) && in_array($_POST[$tmp], PE_PERMITIDOS)) ? ($_POST[$tmp]) : ('');
    }
}
for ($i = 0; $i < 7; $i++) {
    if (isset($_POST['letrasDisponibles'][$i]) && preg_match('/[A-JL-VX-ZÑa-jl-vx-zñ]/', $_POST['letrasDisponibles'][$i])) {
        $letras_disponibles[$i] = mb_strtoupper($_POST['letrasDisponibles'][$i]);
    } else {
        $letras_disponibles[$i] = '';
        $post_existe = false;
    }
}
$ronda                    = intval($_POST['ronda'] ?? 1);
$obtener                  = strip_tags($_POST['obtener'] ?? '');
$palabras_encontradas     = array();
$palabras_sugeridas       = array();
$tpl_palabras_encontradas = '';
$tpl_palabras_ganadoras   = '';

if ($post_existe) {
    $busca_palabras = new BuscaPalabras();
    $busca_palabras->setPuntosExtra($puntos_extra);
    $busca_palabras->setLetrasDisponibles($letras_disponibles);
    $busca_palabras->setRonda($ronda);
    $busca_palabras->buscar($obtener);
    if ($obtener == 'encontradas') {
        $palabras_encontradas        = $busca_palabras->getPalabrasResultantes();
        $palabras_encontradas_puntos = $busca_palabras->getPuntosResultantes();
        $tpl_palabras_encontradas    = palabrasEncontradas($palabras_encontradas, $palabras_encontradas_puntos);
    } else if ($obtener == 'ganadoras') {
        $palabras_sugeridas        = $busca_palabras->getPalabrasSugeridas();
        $palabras_sugeridas_puntos = $busca_palabras->getPalabrasSugeridasPuntos();
        $tpl_palabras_ganadoras    = palabrasGanadoras($palabras_sugeridas, $palabras_sugeridas_puntos);
    }
}

$tpl_admin_css = '<link rel="stylesheet" href="indexbeta.css">';

$tpl_herramientas_conmutador = '<div class="form-check form-switch form-check-inline">
            <input class="form-check-input" type="checkbox" role="switch" id="idActivarHerramientas">
            <label class="form-check-label" for="idActivarHerramientas">Activar herramientas</label>
        </div>';

$ronda1 = '';
$ronda2 = '';
$ronda3 = '';
$ronda4 = '';
$ronda5 = '';

eval("\$ronda$ronda = ' checked';");

$tpl_herramientas = file_get_contents('tpl/herramientas.tpl');

$tpl_herramientas_notificaciones = '<div id="modalAlert" class="modal fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Importación finalizada.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
            </div>
            <div id="menu">
            <div>
                <i class="bi bi-eraser-fill"></i> Borrar <span id="idBorrarPalabra"></span> de la Base de Datos
            </div>
            </div>';
$tpl_admin_js = '<script src="indexbeta.js"></script>';

if (!isset($_GET['angel']) || $_GET['angel'] !== 'angel77') {
    $tpl_admin_css = '';
    $tpl_herramientas_conmutador = '';
    $tpl_herramientas_notificaciones = '';
    $tpl_admin_js = '';
}

$tpl = array(
    'herramientas_css' => $tpl_admin_css,
    'version' => VERSION,
    'herramientas_conmutador' => $tpl_herramientas_conmutador,
    'pal3let1' => $puntos_extra[0][0],
    'pal3let2' => $puntos_extra[0][1],
    'pal3let3' => $puntos_extra[0][2],
    'pal4let1' => $puntos_extra[1][0],
    'pal4let2' => $puntos_extra[1][1],
    'pal4let3' => $puntos_extra[1][2],
    'pal4let4' => $puntos_extra[1][3],
    'pal5let1' => $puntos_extra[2][0],
    'pal5let2' => $puntos_extra[2][1],
    'pal5let3' => $puntos_extra[2][2],
    'pal5let4' => $puntos_extra[2][3],
    'pal5let5' => $puntos_extra[2][4],
    'pal6let1' => $puntos_extra[3][0],
    'pal6let2' => $puntos_extra[3][1],
    'pal6let3' => $puntos_extra[3][2],
    'pal6let4' => $puntos_extra[3][3],
    'pal6let5' => $puntos_extra[3][4],
    'pal6let6' => $puntos_extra[3][5],
    'pal7let1' => $puntos_extra[4][0],
    'pal7let2' => $puntos_extra[4][1],
    'pal7let3' => $puntos_extra[4][2],
    'pal7let4' => $puntos_extra[4][3],
    'pal7let5' => $puntos_extra[4][4],
    'pal7let6' => $puntos_extra[4][5],
    'pal7let7' => $puntos_extra[4][6],
    'letdis1' => $letras_disponibles[0],
    'letdis2' => $letras_disponibles[1],
    'letdis3' => $letras_disponibles[2],
    'letdis4' => $letras_disponibles[3],
    'letdis5' => $letras_disponibles[4],
    'letdis6' => $letras_disponibles[5],
    'letdis7' => $letras_disponibles[6],
    'ronda1' => $ronda1,
    'ronda2' => $ronda2,
    'ronda3' => $ronda3,
    'ronda4' => $ronda4,
    'ronda5' => $ronda5,
    'herramientas_panel' => $tpl_herramientas,
    'palabras_sugeridas' => $tpl_palabras_ganadoras,
    'palabras_encontradas' => $tpl_palabras_encontradas,
    'herramientas_notificaciones' => $tpl_herramientas_notificaciones,
    'herramientas_js' => $tpl_admin_js
);

echo mostrarTpl('inicio', $tpl);

function mostrarTpl(string $fichero, array $vars)
{
    define('TPL_PATRON', '/\{([a-z0-9_]*?)\}/');
    if (empty($vars) === false) {
        $tpl = file_get_contents('tpl/' . $fichero . '.tpl');
        return preg_replace_callback(
            TPL_PATRON,
            function ($coincidencias) use ($vars) {
                return $vars[$coincidencias[1]];
            },
            $tpl
        );
    }
}

function palabrasEncontradas(array $palabras, array $puntos): string
{
    $devolver = '';
    $max = (isset($palabras) && count($palabras) > 0) ? (count(max($palabras))) : (0);
    for ($i = 0; $i < $max; $i++) {
        $devolver .= '<tr>';
        for ($x = 0; $x < 5; $x++) {
            $valor = $palabras[$x][$i] ?? '';
            $devolver .= '<td class="menu palenc noselect">';
            $devolver .= (!empty($valor)) ? ($valor . ' - ' . ($puntos[$x][$i] ?? '')) : ('');
            $devolver .= '</td>';
        }
        $devolver .= '</tr>';
    }
    if (strpos($_SERVER["CONTENT_TYPE"], 'form') !== false) {
        echo $devolver;
        exit();
    }
    return $devolver;
}

function palabrasGanadoras(array $palabras, array $puntos): string
{
    $devolver = '';
    if (empty($palabras)) {
        $max = 0;
    } elseif (count(max($palabras)) > 5) {
        $max = 5;
    } else {
        $max = count(max($palabras));
    }
    for ($i = 0; $i < $max; $i++) {
        $devolver .= '<tr>';
        for ($x = 0; $x < 5; $x++) {
            $valor = $palabras[$x][$i] ?? '';
            $devolver .= '<td class="noselect">';
            $devolver .= (!empty($valor)) ? ($valor . ' - ' . ($puntos[$x] ?? '')) : ('');
            $devolver .= '</td>';
        }
        $devolver .= '</tr>';
    }
    if (strpos($_SERVER["CONTENT_TYPE"], 'form') !== false) {
        echo $devolver;
        exit();
    }
    return $devolver;
}
