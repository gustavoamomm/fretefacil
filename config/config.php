<?php

class Config {
    private static $instancia = null;

    public static function getConexao() {

        // Carrega o .env apenas uma vez
        if (file_exists(__DIR__ . '/../.env')) {
            $env = parse_ini_file(__DIR__ . '/../.env');

            foreach ($env as $chave => $valor) {
                $_ENV[$chave] = $valor;
            }
        }

        $host = $_ENV['DB_HOST'];
        $porta = $_ENV['DB_PORT'];
        $banco = $_ENV['DB_NAME'];
        $usuario = $_ENV['DB_USER'];
        $senha = $_ENV['DB_PASS'];


        if (self::$instancia === null) {

            if (!in_array('mysql', PDO::getAvailableDrivers(), true)) {
                die('Erro: driver PDO MySQL não encontrado. Habilite a extensão pdo_mysql no php.ini.');
            }

            try {
                self::$instancia = new PDO(
                    "mysql:host=$host;port=$porta;dbname=$banco;charset=utf8",
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