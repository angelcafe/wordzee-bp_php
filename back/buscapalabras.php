<?php
// declare(strict_types = 1);
class BuscaPalabras
{
    private $aPuntos = array('A' => 1, 'B' => 3, 'C' => 3, 'D' => 2, 'E' => 1, 'F' => 4, 'G' => 2, 'H' => 4, 'I' => 1, 'J' => 8, 'L' => 1, 'M' => 3, 'N' => 1, 'Ñ' => 8, 'O' => 1, 'P' => 3, 'Q' => 5, 'R' => 1, 'S' => 1, 'T' => 1, 'U' => 1, 'V' => 4, 'X' => 8, 'Y' => 4, 'Z' => 10);
    private $oBd;

    private $puntos_extra = array();
    private $letras_disponibles = array();
    private $ronda;

    private $palabras_sugeridas = array();
    private $palabras_sugeridas_puntos = array();
    private $palabras_encontradas = array();
    private $puntos_palabra = array();

    public function __construct()
    {
        $this->oBd = $this->inicializarBD();
    }

    public function getPalabrasSugeridas(): array
    {
        return $this->palabras_sugeridas;
    }

    public function getPalabrasSugeridasPuntos(): array
    {
        return $this->palabras_sugeridas_puntos;
    }

    public function getPuntosResultantes(): array
    {
        return $this->puntos_palabra;
    }

    public function getPalabrasResultantes(): array
    {
        return $this->palabras_encontradas;
    }

    private function getWords(array $palabras_BD): string
    {
        return json_encode($palabras_BD, JSON_UNESCAPED_UNICODE);;
    }

    public function setPuntosExtra(array $puntosExtra): void
    {
        $this->puntos_extra = $puntosExtra;
    }

    public function setLetrasDisponibles(array $letrasDisponibles): void
    {
        $this->letras_disponibles = $letrasDisponibles;
    }

    public function setRonda(int $ronda): void
    {
        $this->ronda = $ronda;
    }

    private function inicializarBD(): PDO
    {
        $bd = (file_exists('back/palabras.db') === false) ? ('sqlite:palabras.db') : ('sqlite:back/palabras.db');
        $pd = new PDO($bd);
        $pd->exec('CREATE TABLE IF NOT EXISTS "letras_3" ("palabra" STRING PRIMARY KEY)');
        $pd->exec('CREATE TABLE IF NOT EXISTS "letras_4" ("palabra" STRING PRIMARY KEY)');
        $pd->exec('CREATE TABLE IF NOT EXISTS "letras_5" ("palabra" STRING PRIMARY KEY)');
        $pd->exec('CREATE TABLE IF NOT EXISTS "letras_6" ("palabra" STRING PRIMARY KEY)');
        $pd->exec('CREATE TABLE IF NOT EXISTS "letras_7" ("palabra" STRING PRIMARY KEY)');
        //$pd->sqliteCreateFunction('regexp_like', 'preg_match', 2);
        return $pd;
    }

    public function buscar(string $obtener): void
    {
        $this->palabras_encontradas = $this->palabrasEncontradas();
        if ($obtener == 'encontradas') {
            $this->ordenarPalabrasPorPuntos($this->puntos_palabra, $this->palabras_encontradas);
        } elseif ($obtener == 'ganadoras') {
            $this->obtenerPalabrasGanadoras();
        }
    }

    private function palabrasEncontradas(): array
    {
        $palabras_BD        = $this->obtenerPalabras();
        $letras_disponibles = implode($this->letras_disponibles);
        for ($x = 0; $x < 5; $x++) {
            foreach ($palabras_BD[$x] as $palabra) {
                $this->palabras_sugeridas_puntos[$x][$palabra] = $this->puntosPalabra($palabra);
                foreach (count_chars($palabra, 1) as $letra => $cantidad) {
                    if (substr_count($letras_disponibles, chr($letra)) < $cantidad) {
                        continue 2;
                    }
                }
                $palabras_encontradas[$x][] = $palabra;
                $this->puntos_palabra[$x][] = $this->puntosPalabra($palabra);
            }
        }
        return $palabras_encontradas;
    }

    private function puntosPalabra(string $palabra): int
    {
        $doble_palabra = 1;
        $triple_palabra = 1;
        $total = 0;
        $letras_palabra = mb_str_split($palabra);
        foreach ($letras_palabra as $key => $value) {
            $doble_letra = 1;
            $triple_letra = 1;
            switch ($this->puntos_extra[count($letras_palabra) - 3][$key]) {
                case 'DL':
                    $doble_letra = 2;
                    break;
                case 'TL':
                    $triple_letra = 3;
                    break;
                default:
                    break;
            }
            $total += intval($this->aPuntos[$value] * $this->ronda * $doble_letra * $triple_letra);
        }
        if (!empty($this->puntos_extra) && in_array('DP', $this->puntos_extra[count($letras_palabra) - 3])) {
            $doble_palabra = 2;
        } elseif (!empty($this->puntos_extra) && in_array('TP', $this->puntos_extra[count($letras_palabra) - 3])) {
            $triple_palabra = 3;
        }
        return $total * $doble_palabra * $triple_palabra;
    }

    public function deleteWord(string $palabra)
    {
        $palabra = mb_strtoupper(filter_var($palabra, 513));
        $longitud = mb_strlen($palabra);
        if ($longitud >= 3 && $longitud <=7 ) {
            $this->oBd->exec("DELETE FROM letras_$longitud WHERE palabra = '$palabra'");
        }
    }

    public function exportWords(): string
    {
        $array = $this->oBd->query('SELECT palabra FROM palabras ORDER BY palabra ASC');
        $palabras = $array->fetchAll(PDO::FETCH_COLUMN, 0);
        $palabrasJson = json_encode($palabras, JSON_UNESCAPED_UNICODE);
        $fp = fopen('sp.json', 'w');
        fwrite($fp, $palabrasJson);
        fclose($fp);
        return $palabrasJson;
    }

    public function importarPalabras()
    {
        $palabras = file_get_contents('sp.json');
        $palabras = json_decode($palabras, false, 1024000000);
        $this->insertarPalabras($palabras);
    }

    public function insertarPalabras(array $palabrasTodas)
    {
        $palabrasTodas = array_unique($palabrasTodas);
        $this->oBd->beginTransaction();
        foreach ($palabrasTodas as $value) {
            $letra_longitud = mb_strlen($value);
            if (in_array($letra_longitud, range(3, 7)) && preg_match('/^[A-JL-V-XZÑ]{' . $letra_longitud . '}$/i', $value) !== false) {
                $this->oBd->exec('INSERT OR IGNORE INTO "letras_' . $letra_longitud . '" (palabra) VALUES ("' . mb_strtoupper($value) . '")');
            }
        }
        $this->oBd->commit();
    }

    private function obtenerPalabrasGanadoras(): void
    {
        for ($x = 0; $x < 5; $x++) {
            $this->palabras_sugeridas[$x] = array_keys($this->palabras_sugeridas_puntos[$x], max($this->palabras_sugeridas_puntos[$x]));
            $this->palabras_sugeridas_puntos[$x] = max($this->palabras_sugeridas_puntos[$x]);
        }
    }

    private function ordenarPalabrasPorPuntos(array &$puntos, array &$palabras): void
    {
        for ($i = 0; $i < 5; $i++) {
            if (!isset($puntos[$i])) continue;
            uasort($puntos[$i], function ($a, $b) {
                if ($a == $b) {
                    return 0;
                }
                return ($a < $b) ? -1 : 1;
            });
            arsort($puntos[$i]);
            $keys = array_keys($puntos[$i]);
            $values = array_values($palabras[$i]);
            foreach ($keys as $key => $value) {
                $palabras[$i][$key] = $values[$value];
            }
            rsort($puntos[$i]);
        }
        unset($puntos, $palabras);
    }

    private function obtenerPalabras(): array
    {
        for ($x = 0; $x < 5; $x++) {
            $array = $this->oBd->query('SELECT palabra FROM letras_' . ($x + 3) . ' ORDER BY palabra ASC');
            $palabras[$x] = $array->fetchAll(PDO::FETCH_COLUMN, 0);
        }
        return $palabras;
    }

    /**
     * 
     */

    private function proteccion()
    {
        session_start();
        if (isset($_SESSION['cache'])) {
            if (time() - $_SESSION['cache'] < 2) {
                echo ('No se permite hacer flood.');
                exit();
            } else {
                $_SESSION['cache'] = time();
            }
        } else {
            $_SESSION['cache'] = time();
        }
    }
}
/**
 * Código extra para admin
 */
if (isset($_POST['borrar'])) {
    $p = new BuscaPalabras();
    $p->deleteWord($_POST['borrar']);
} elseif (isset($_POST['guardar'])) {
    $p = new BuscaPalabras();
    $p->insertarPalabras(explode(',', $_POST['guardar']));
} elseif (isset($_POST['exportar'])) {
    $p = new BuscaPalabras();
    echo ($p->exportWords());
}
if (!empty($_FILES)) {
    $p = new BuscaPalabras();
    move_uploaded_file($_FILES['archivo']['tmp_name'], getcwd() . DIRECTORY_SEPARATOR . 'sp.json');
    $p->importarPalabras();
}
