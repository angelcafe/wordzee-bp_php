<?php

class Puntos
{
    private array $aPuntos = [
        'A' => 1, 'B' => 3, 'C' => 3, 'D' => 2, 'E' => 1, 'F' => 4, 'G' => 2, 'H' => 4,
        'I' => 1, 'J' => 8, 'L' => 1, 'M' => 3, 'N' => 1, 'Ñ' => 8, 'O' => 1, 'P' => 3,
        'Q' => 5, 'R' => 1, 'S' => 1, 'T' => 1, 'U' => 1, 'V' => 4, 'X' => 8, 'Y' => 4, 'Z' => 10
    ];
    private array $pExtra = ['' => 1, 'DL' => 2, 'TL' => 3, 'DP' => 1, 'TP' => 1];

    private array $puntos_extra = [];
    private array $letras_disponibles = [];
    private int $ronda;

    private array $palabras = [];
    private array $palabras_puntos = [];
    private array $encontradas_palabras = [];
    private array $encontradas_puntos = [];
    private array $encontradas_palabras_puntos = [];
    private array $sugeridas_palabras = [];
    private array $sugeridas_puntos = [];
    private array $sugeridas_palabras_puntos = [];

    public static function getPalabrasPuntos(array $palabras, array $puntos_extra, int $ronda)
    {
        $aPuntos = [
            'A' => 1, 'B' => 3, 'C' => 3, 'D' => 2, 'E' => 1, 'F' => 4, 'G' => 2, 'H' => 4,
            'I' => 1, 'J' => 8, 'L' => 1, 'M' => 3, 'N' => 1, 'Ñ' => 8, 'O' => 1, 'P' => 3,
            'Q' => 5, 'R' => 1, 'S' => 1, 'T' => 1, 'U' => 1, 'V' => 4, 'X' => 8, 'Y' => 4, 'Z' => 10
        ];
        $pExtra = ['' => 1, 'DL' => 2, 'TL' => 3, 'DP' => 1, 'TP' => 1];
        foreach ($palabras as $palabra) {
            $letras = mb_str_split($palabra);
            $total = 0;
            $letras_count = count($letras);
            $puntos_extra_ronda = $puntos_extra[$letras_count - 3];
            foreach ($letras as $key => $value) {
                $total += $aPuntos[$value] * $ronda * $pExtra[$puntos_extra_ronda[$key]];
            }
            if ($letras_count === 6 && in_array('DP', $puntos_extra_ronda)) {
                $total *= 2;
            } elseif ($letras_count === 7 && in_array('TP', $puntos_extra_ronda)) {
                $total *= 3;
            }
        }
    }

    public function setPuntosExtra(array $puntosExtra): void
    {
        $this->puntos_extra = $puntosExtra;
    }

    public function setRonda(int $ronda): void
    {
        $this->ronda = $ronda;
    }

    public function buscar(string $buscar): void
    {
        if (empty($this->puntos_extra) || empty($this->letras_disponibles) || empty($this->ronda)) {
            throw new Exception("Antes de utilizar la función buscar hay que cargar información con 'setPuntosExtra', 'setLetrasDisponibles' y 'setRonda'");
        }
        $this->obtenerPalabras();

        foreach ($this->encontradas_palabras as $palabra) {
            $this->palabras_puntos += [$palabra => $this->puntos($palabra)];
        }
        arsort($this->palabras_puntos);

        // $this->encontradas_palabras = $this->palabrasEncontradas();
        // $this->palabrasEncontradas();
        if ($buscar == 'encontradas') {
            $this->ordenarPalabrasPorPuntos($this->encontradas_palabras_puntos, $this->encontradas_palabras);
            echo json_encode($this->encontradas_palabras_puntos);
        } elseif ($buscar == 'ganadoras') {
            $this->obtenerPalabrasSugeridas();
            echo json_encode($this->sugeridas_palabras_puntos);
        } else {
            throw new Exception("Solamente se acepta buscar 'encontradas' y 'ganadoras'.");
        }
        // $this->palabrasPuntos();
    }

    private function palabrasEncontradas()
    {
        $encontradas_palabras = array();
        $palabras             = $this->obtenerPalabras();
        $letras_disponibles   = implode($this->letras_disponibles);
        $palabra_puntos = [];
        foreach ($palabras as $palabra) {
            $palabra_puntos += [$palabra => $this->puntosPalabra($palabra)];
            $this->sugeridas_puntos[mb_strlen($palabra) - 3][$palabra] = $this->puntosPalabra($palabra);
            if ($this->coincidencia($letras_disponibles, $palabra)) {
                $encontradas_palabras[mb_strlen($palabra) - 3][] = $palabra;
                $this->encontradas_palabras_puntos[mb_strlen($palabra) - 3][] = $this->puntosPalabra($palabra);
            }
        }
        arsort($palabra_puntos);

        return $encontradas_palabras;
    }

    private function puntos(string $palabra): int
    {
        $letras = mb_str_split($palabra);
        $total = 0;
        $letras_count = count($letras);
        $puntos_extra_ronda = $this->puntos_extra[$letras_count - 3];
        foreach ($letras as $key => $value) {
            $total += $this->aPuntos[$value] * $this->ronda * $this->pExtra[$puntos_extra_ronda[$key]];
        }
        if ($letras_count === 6 && in_array('DP', $puntos_extra_ronda)) {
            $total *= 2;
        } elseif ($letras_count === 7 && in_array('TP', $puntos_extra_ronda)) {
            $total *= 3;
        }

        return $total;
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

    private function obtenerPalabrasSugeridas(): void
    {
        for ($x = 0; $x < 5; $x++) {
            $maxPuntos = max($this->sugeridas_puntos[$x] ?? []);
            $this->sugeridas_palabras[$x] = array_keys($this->sugeridas_puntos[$x], $maxPuntos)[0];
            $this->sugeridas_palabras_puntos[$x] = $maxPuntos;
        }
    }

    private function palabrasPuntos(): void
    {
        $this->encontradas_puntos = [];
        foreach ($this->encontradas_palabras as $x => $encontradas) {
            $puntos = $this->encontradas_palabras_puntos[$x];
            if (is_array($encontradas)) {
                foreach ($encontradas as $y => $palabra) {
                    $this->encontradas_puntos[$x][$y] = "$palabra - $puntos[$y]";
                }
            } else {
                $this->encontradas_puntos[$x][0] = "$encontradas - $puntos";
            }
        }
    }
}
