<?php

class Buscapalabras
{
    private array $aPuntos = [
        'A' => 1, 'B' => 3, 'C' => 3, 'D' => 2, 'E' => 1, 'F' => 4, 'G' => 2, 'H' => 4,
        'I' => 1, 'J' => 8, 'L' => 1, 'M' => 3, 'N' => 1, 'Ñ' => 8, 'O' => 1, 'P' => 3,
        'Q' => 5, 'R' => 1, 'S' => 1, 'T' => 1, 'U' => 1, 'V' => 4, 'X' => 8, 'Y' => 4, 'Z' => 10
    ];

    private PDO $pdo_sqlite;
    private array $puntos_extra = [];
    private array $letras_disponibles = [];
    private int $ronda;

    private array $palabras_encontradas = [];
    private array $palabras_encontradas_puntos = [];
    private array $palabras_sugeridas = [];
    private array $palabras_sugeridas_puntos = [];
    private array $palabras_puntos = [];

    public function __construct()
    {
        $this->inicializarBD();
    }

    public function getPalabrasPuntos(): array
    {
        return $this->palabras_encontradas_puntos;
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
        return $this->palabras_puntos;
    }

    public function getPalabrasResultantes(): array
    {
        return $this->palabras_encontradas;
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

    public function deleteWord(string $palabra): void
    {
        $palabra = mb_strtoupper(filter_var($palabra, FILTER_SANITIZE_STRING));
        $longitud = mb_strlen($palabra);

        if ($longitud >= 3 && $longitud <= 7) {
            $stmt = $this->pdo_sqlite->prepare("DELETE FROM palabras WHERE palabra = :palabra");
            $stmt->bindParam(':palabra', $palabra, PDO::PARAM_STR);
            $stmt->execute();
        }
    }

    public function exportWords(): string
    {
        $array        = $this->pdo_sqlite->query('SELECT palabra FROM palabras ORDER BY palabra ASC');
        $palabras     = $array->fetchAll(PDO::FETCH_COLUMN, 0);
        $palabrasJson = json_encode($palabras, JSON_UNESCAPED_UNICODE);
        $fp           = fopen('sp.json', 'w');
        fwrite($fp, $palabrasJson);
        fclose($fp);

        return $palabrasJson;
    }

    public function importarPalabras(): void
    {
        $palabras = file_get_contents('sp.json');
        $palabras = json_decode($palabras, false, 1024000000);
        $this->insertarPalabras($palabras);
    }

    public function insertarPalabras(array $palabras_todas): void
    {
        $palabras_todas = array_unique($palabras_todas);
        $this->pdo_sqlite->beginTransaction();
        foreach ($palabras_todas as $value) {
            $letra_longitud = mb_strlen($value);
            if (
                in_array($letra_longitud, range(3, 7)) &&
                preg_match('/^[A-JL-V-XZÑ]{' . $letra_longitud . '}$/i', $value) !== false
            ) {
                $this->pdo_sqlite->exec('INSERT OR IGNORE INTO palabras (palabra) VALUES ("' . mb_strtoupper($value) . '")');
            }
        }
        $this->pdo_sqlite->commit();
    }

    /**
     * 
     * @param string $obtener Pasar 'encontradas' o 'ganadores' para obtener las Palabras encontradas o las Palabras de mayor valor.
     * @return void 
     */
    public function buscar(string $obtener): void
    {
        $this->palabras_encontradas = $this->palabrasEncontradas();
        if ($obtener == 'encontradas') {
            $this->ordenarPalabrasPorPuntos($this->palabras_puntos, $this->palabras_encontradas);
        } elseif ($obtener == 'ganadoras') {
            $this->obtenerPalabrasGanadoras();
        }
        $this->palabrasPuntos();
    }

    private function inicializarBD(): void
    {
        $bd               = __DIR__ . DIRECTORY_SEPARATOR . 'palabras.sqlite';
        $this->pdo_sqlite = new PDO('sqlite:' . $bd);
        $this->pdo_sqlite->sqliteCreateFunction('regexp_like', 'preg_match', 2);
        if (!file_exists($bd)) {
            $this->pdo_sqlite->exec('CREATE TABLE IF NOT EXISTS "palabras" ("palabra" STRING PRIMARY KEY)');
        }
    }

    private function coincidencia($letras, $palabra): bool
    {
        $letras  = str_split($letras);
        $palabra = str_split($palabra);
        foreach ($palabra as $letra) {
            if (in_array($letra, $letras)) {
                $index = array_search($letra, $letras);
                unset($letras[$index]);
            } else {
                return false;
            }
        }

        return true;
    }

    private function palabrasEncontradas(): array
    {
        $palabras_encontradas = array();
        $palabras             = $this->obtenerPalabras();
        $letras_disponibles   = implode($this->letras_disponibles);
        foreach ($palabras as $palabra) {
            $this->palabras_sugeridas_puntos[mb_strlen($palabra) - 3][$palabra] = $this->puntosPalabra($palabra);
            if ($this->coincidencia($letras_disponibles, $palabra)) {
                $palabras_encontradas[mb_strlen($palabra) - 3][] = $palabra;
                $this->palabras_puntos[mb_strlen($palabra) - 3][] = $this->puntosPalabra($palabra);
            }
        }

        return $palabras_encontradas;
    }

    private function palabrasPuntos(): void
    {
        $this->palabras_encontradas_puntos = [];
        foreach ($this->palabras_encontradas as $x => $encontradas) {
            $puntos = $this->palabras_puntos[$x];
            if (is_array($encontradas)) {
                foreach ($encontradas as $y => $palabra) {
                    $this->palabras_encontradas_puntos[$x][$y] = "$palabra - $puntos[$y]";
                }
            } else {
                $this->palabras_encontradas_puntos[$x][0] = "$encontradas - $puntos";
            }
        }
    }

    private function puntosPalabra(string $palabra): int
    {
        $doble_palabra  = $triple_palabra = 1;
        $total          = 0;
        $letras_palabra = mb_str_split($palabra);
        foreach ($letras_palabra as $key => $value) {
            $doble_letra = $triple_letra = 1;
            if (!empty($this->puntos_extra)) {
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

    private function obtenerPalabrasGanadoras(): void
    {
        for ($x = 0; $x < 5; $x++) {
            $maxPuntos = max($this->palabras_sugeridas_puntos[$x] ?? []);
            $this->palabras_encontradas[$x] = array_keys($this->palabras_sugeridas_puntos[$x], $maxPuntos)[0];
            $this->palabras_puntos[$x] = $maxPuntos;
        }
    }

    private function ordenarPalabrasPorPuntos(array &$puntos, array &$palabras): void
    {
        for ($i = 0; $i < 5; $i++) {
            if (isset($puntos[$i])) {
                array_multisort($puntos[$i], SORT_DESC, $palabras[$i]);
            }
        }
        unset($puntos, $palabras);
    }

    private function obtenerPalabras(): array
    {
        $query = $this->pdo_sqlite->query('SELECT palabra FROM palabras ORDER BY palabra ASC');

        return $query->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    private function proteccion(): void
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
