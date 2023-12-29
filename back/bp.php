<?php

require 'Buscapalabras.php';
if (!empty($_POST['borrar'])) {
    $p = new buscapalabras();
    $p->deleteWord($_POST['borrar']);
} elseif (!empty($_POST['guardar'])) {
    $p = new buscapalabras();
    $p->insertarPalabras(explode(',', $_POST['guardar']));
} elseif (!empty($_POST['exportar'])) {
    $p = new buscapalabras();
    echo ($p->exportWords());
} elseif (!empty($_FILES)) {
    $p = new buscapalabras();
    move_uploaded_file($_FILES['archivo']['tmp_name'], getcwd() . DIRECTORY_SEPARATOR . 'sp.json');
    $p->importarPalabras();
} elseif (!empty($_POST['ronda']) && !empty($_POST['obtener'])) {
    $pe_permitidos = ['DP', 'TP', 'DL', 'TL', ''];
    for ($x = 0; $x < 5; $x++) {
        for ($y = 0; $y < 3 + $x; $y++) {
            $tmp                  = 'pal' . ($x + 3) . 'let' . ($y + 1);
            $puntos_extra[$x][$y] =
                (isset($_POST[$tmp]) && in_array($_POST[$tmp], $pe_permitidos)) ? ($_POST[$tmp]) : ('');
        }
    }
    for ($i = 0; $i < 7; $i++) {
        if (isset($_POST['letrasDisponibles'][$i]) && preg_match('/[A-JL-VX-ZÑa-jl-vx-zñ]/', $_POST['letrasDisponibles'][$i])) {
            $letras_disponibles[$i] = mb_strtoupper($_POST['letrasDisponibles'][$i]);
        } else {
            $letras_disponibles[$i] = '';
            $post_existe            = false;
        }
    }

    $ronda          = intval($_POST['ronda']);
    $obtener        = strip_tags($_POST['obtener']);

    $busca_palabras = new BuscaPalabras();
    $busca_palabras->setPuntosExtra($puntos_extra);
    $busca_palabras->setLetrasDisponibles($letras_disponibles);
    $busca_palabras->setRonda($ronda);
    $busca_palabras->buscar($obtener);

    $palabras_puntos = $busca_palabras->getPalabrasPuntos();
    echo json_encode($palabras_puntos);
}
