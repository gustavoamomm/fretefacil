<?php
class Config {
    private static $instancia = null;

    public static function getConexao() {
        $host = "tini.click";
        $porta = "3306";
        $banco = "fretefacil_db";
        $usuario = "fretefacil_db";
        $senha = "ec34854404589aea43b2fa279a9d827a";
    

        if (self::$instancia === null) {

            if (!in_array('mysql', PDO::getAvailableDrivers(), true)) {
                die('Erro: driver PDO MySQL não encontrado. Habilite a extensão pdo_mysql no php.ini.');
            }

            try {

                self::$instancia = new PDO(
                    "mysql:host=" . $host . ";port=" . $porta . ";dbname=" . $banco . ";charset=utf8",
                    $usuario,
                    $senha
                );
                self::$instancia->setAttribute(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION
                );
            } catch (PDOException $e) {
              die("Erro: " . $e->getMessage());
            }
        }
        return self::$instancia;
    }
}
?>