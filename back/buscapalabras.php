<?php

class buscapalabras {
    private array $aPuntos = array( 'A' => 1,
                                    'B' => 3,
                                    'C' => 3,
                                    'D' => 2,
                                    'E' => 1,
                                    'F' => 4,
                                    'G' => 2,
                                    'H' => 4,
                                    'I' => 1,
                                    'J' => 8,
                                    'L' => 1,
                                    'M' => 3,
                                    'N' => 1,
                                    'Ñ' => 8,
                                    'O' => 1,
                                    'P' => 3,
                                    'Q' => 5,
                                    'R' => 1,
                                    'S' => 1,
                                    'T' => 1,
                                    'U' => 1,
                                    'V' => 4,
                                    'X' => 8,
                                    'Y' => 4,
                                    'Z' => 10 );
    private PDO $oBd;

    private array $puntos_extra = array();
    private array $letras_disponibles = array();
    private int $ronda;

    private array $palabras_sugeridas = array();
    private array $palabras_sugeridas_puntos = array();
    private array $palabras_encontradas = array();
    private array $puntos_palabra = array();

    public function __construct() {
        $this->oBd = $this->inicializarBD();
    }

    public function getPalabrasSugeridas() :array {
        return $this->palabras_sugeridas;
    }

    public function getPalabrasSugeridasPuntos() :array {
        return $this->palabras_sugeridas_puntos;
    }

    public function getPuntosResultantes() :array {
        return $this->puntos_palabra;
    }

    public function getPalabrasResultantes() :array {
        return $this->palabras_encontradas;
    }

    public function setPuntosExtra( array $puntosExtra ) :void {
        $this->puntos_extra = $puntosExtra;
    }

    public function setLetrasDisponibles( array $letrasDisponibles ) :void {
        $this->letras_disponibles = $letrasDisponibles;
    }

    public function setRonda( int $ronda ) :void {
        $this->ronda = $ronda;
    }

    private function inicializarBD() :PDO {
        $bd = 'sqlite:' . __DIR__ . DIRECTORY_SEPARATOR . 'palabras.sqlite';
        $pd = new PDO( $bd );
        $pd->exec( 'CREATE TABLE IF NOT EXISTS "palabras" ("palabra" STRING PRIMARY KEY)' );
        $pd->sqliteCreateFunction( 'regexp_like', 'preg_match', 2 );

        return $pd;
    }

    public function buscar( string $obtener ) :void {
        $this->palabras_encontradas = $this->palabrasEncontradas();
        if( $obtener == 'encontradas' ) {
            $this->ordenarPalabrasPorPuntos( $this->puntos_palabra, $this->palabras_encontradas );
        } elseif( $obtener == 'ganadoras' ) {
            $this->obtenerPalabrasGanadoras();
        }
    }

    private function coincidencia( $letras, $palabra ) :bool {
        $letras  = str_split( $letras );
        $palabra = str_split( $palabra );
        foreach( $palabra as $letra ) {
            if( in_array( $letra, $letras ) ) {
                $index = array_search( $letra, $letras );
                unset( $letras[ $index ] );
            } else {
                return false;
            }
        }

        return true;
    }

    private function palabrasEncontradas() :array {
        $palabras_encontradas = array();
        $palabras             = $this->obtenerPalabras();
        $letras_disponibles   = implode( $this->letras_disponibles );
        foreach( $palabras as $palabra ) {
            $this->palabras_sugeridas_puntos[ strlen( $palabra ) - 3 ][ $palabra ] = $this->puntosPalabra( $palabra );
            if( $this->coincidencia( $letras_disponibles, $palabra ) ) {
                $palabras_encontradas[ strlen( $palabra ) - 3 ][] = $palabra;
                $this->puntos_palabra[ strlen( $palabra ) - 3 ][] = $this->puntosPalabra( $palabra );
            }
        }

        return $palabras_encontradas;
    }

    private function puntosPalabra( string $palabra ) :int {
        $doble_palabra  = 1;
        $triple_palabra = 1;
        $total          = 0;
        $letras_palabra = mb_str_split( $palabra );
        foreach( $letras_palabra as $key => $value ) {
            $doble_letra  = 1;
            $triple_letra = 1;
            switch( $this->puntos_extra[ count( $letras_palabra ) - 3 ][ $key ] ) {
            case 'DL':
                $doble_letra = 2;
                break;
            case 'TL':
                $triple_letra = 3;
                break;
            default:
                break;
            }
            $total += intval( $this->aPuntos[ $value ] * $this->ronda * $doble_letra * $triple_letra );
        }
        if( !empty( $this->puntos_extra ) && in_array( 'DP', $this->puntos_extra[ count( $letras_palabra ) - 3 ] ) ) {
            $doble_palabra = 2;
        } elseif( !empty( $this->puntos_extra ) && in_array( 'TP', $this->puntos_extra[ count( $letras_palabra ) - 3 ] ) ) {
            $triple_palabra = 3;
        }

        return $total * $doble_palabra * $triple_palabra;
    }

    public function deleteWord( string $palabra ) :void {
        $palabra  = mb_strtoupper( filter_var( $palabra, 513 ) );
        $longitud = mb_strlen( $palabra );
        if( $longitud >= 3 && $longitud <= 7 ) {
            $this->oBd->exec( "DELETE FROM palabras WHERE palabra = '$palabra'" );
        }
    }

    public function exportWords() :string {
        $array        = $this->oBd->query( 'SELECT palabra FROM palabras ORDER BY palabra ASC' );
        $palabras     = $array->fetchAll( PDO::FETCH_COLUMN, 0 );
        $palabrasJson = json_encode( $palabras, JSON_UNESCAPED_UNICODE );
        $fp           = fopen( 'sp.json', 'w' );
        fwrite( $fp, $palabrasJson );
        fclose( $fp );

        return $palabrasJson;
    }

    public function importarPalabras() :void {
        $palabras = file_get_contents( 'sp.json' );
        $palabras = json_decode( $palabras, false, 1024000000 );
        $this->insertarPalabras( $palabras );
    }

    public function insertarPalabras( array $palabras_todas ) :void {
        $palabras_todas = array_unique( $palabras_todas );
        $this->oBd->beginTransaction();
        foreach( $palabras_todas as $value ) {
            $letra_longitud = mb_strlen( $value );
            if( in_array( $letra_longitud, range( 3, 7 ) ) &&
                preg_match( '/^[A-JL-V-XZÑ]{' . $letra_longitud . '}$/i', $value ) !== false ) {
                $this->oBd->exec( 'INSERT OR IGNORE INTO palabras (palabra) VALUES ("' . mb_strtoupper( $value ) . '")' );
            }
        }
        $this->oBd->commit();
    }

    private function obtenerPalabrasGanadoras() :void {
        for( $x = 0; $x < 5; $x++ ) {
            $this->palabras_sugeridas[ $x ]        =
                array_keys( $this->palabras_sugeridas_puntos[ $x ], max( $this->palabras_sugeridas_puntos[ $x ] ) );
            $this->palabras_sugeridas_puntos[ $x ] = max( $this->palabras_sugeridas_puntos[ $x ] );
        }
    }

    private function ordenarPalabrasPorPuntos( array &$puntos, array &$palabras ) :void {
        for( $i = 0; $i < 5; $i++ ) {
            if( !isset( $puntos[ $i ] ) ) {
                continue;
            }
            uasort( $puntos[ $i ], function( $a, $b ) {
                if( $a == $b ) {
                    return 0;
                }

                return ( $a < $b ) ? -1 : 1;
            } );
            arsort( $puntos[ $i ] );
            $keys   = array_keys( $puntos[ $i ] );
            $values = array_values( $palabras[ $i ] );
            foreach( $keys as $key => $value ) {
                $palabras[ $i ][ $key ] = $values[ $value ];
            }
            rsort( $puntos[ $i ] );
        }
        unset( $puntos, $palabras );
    }

    private function obtenerPalabras() :array {
        $query = $this->oBd->query( 'SELECT palabra FROM palabras ORDER BY palabra ASC' );

        return $query->fetchAll( PDO::FETCH_COLUMN, 0 );
    }

    private function proteccion() :void {
        session_start();
        if( isset( $_SESSION['cache'] ) ) {
            if( time() - $_SESSION['cache'] < 2 ) {
                echo( 'No se permite hacer flood.' );
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
if( isset( $_POST['borrar'] ) ) {
    $p = new buscapalabras();
    $p->deleteWord( $_POST['borrar'] );
} elseif( isset( $_POST['guardar'] ) ) {
    $p = new buscapalabras();
    $p->insertarPalabras( explode( ',', $_POST['guardar'] ) );
} elseif( isset( $_POST['exportar'] ) ) {
    $p = new buscapalabras();
    echo( $p->exportWords() );
}
if( !empty( $_FILES ) ) {
    $p = new buscapalabras();
    move_uploaded_file( $_FILES['archivo']['tmp_name'], getcwd() . DIRECTORY_SEPARATOR . 'sp.json' );
    $p->importarPalabras();
}
