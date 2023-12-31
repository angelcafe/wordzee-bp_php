<?php

class Buscar
{
    private PDO $pdo_sqlite;
    private array $letras = [];
    private array $palabras = [];
    private array $resultado = [];

    public function __construct(array $letras)
    {
        $this->inicializarBD();
        $this->letras = $letras;
        $this->obtenerPalabras();
    }

    public function getPalabras(): array{
        return $this->palabras;
    }

    public function getResultado(): array
    {
        return $this->resultado;
    }

    public function borrarPalabra(string $palabra): void
    {
        $palabra  = mb_strtoupper(filter_var($palabra, FILTER_SANITIZE_STRING));
        $longitud = mb_strlen($palabra);

        if ($longitud >= 3 && $longitud <= 7) {
            $stmt = $this->pdo_sqlite->prepare("DELETE FROM palabras WHERE palabra = :palabra");
            $stmt->bindParam(':palabra', $palabra, PDO::PARAM_STR);
            $stmt->execute();
        }
    }

    public function exportarPalabras(): string
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

    public function insertarPalabras(array $palabras): void
    {
        $palabras_unicas = array_unique($palabras);
        try {
            $this->pdo_sqlite->beginTransaction();
            foreach ($palabras_unicas as $palabra) {
                $palabra = htmlspecialchars($palabra);
                $letra_longitud = mb_strlen($palabra);
                if (
                    in_array($letra_longitud, range(3, 7)) &&
                    preg_match('/^[A-JL-V-XZÑ]{' . $letra_longitud . '}$/i', $palabra) !== false
                ) {
                    $stmt = $this->pdo_sqlite->prepare('INSERT OR IGNORE INTO palabras (palabra) VALUES (?)');
                    $stmt->execute([mb_strtoupper($palabra)]);
                }
            }
            $this->pdo_sqlite->commit();
        } catch (PDOException $e) {
            $this->pdo_sqlite->rollBack();
        }
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

    private function obtenerPalabras(): void
    {
        $query = $this->pdo_sqlite->query('SELECT palabra FROM palabras ORDER BY palabra ASC');
        while ($palabra = $query->fetch(PDO::FETCH_COLUMN, 0)) {
            $this->palabras[] = $palabra;
            if ($this->coincidencia($palabra)) {
                $this->resultado[] = $palabra;
            }
        }
    }

    private function coincidencia(string $palabra): bool
    {
        $letras  = $this->letras;
        $palabra = mb_str_split($palabra);
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
}
