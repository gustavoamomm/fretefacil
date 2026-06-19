<?php

require_once "../config/config.php";

class EmpresaDAO {

    private PDO $conn;

    public function __construct() {
        $this->conn = Config::getConexao();
    }

    public function cadastrar($nome, $cnpj, $senha) {

        $sql = "INSERT INTO tb_usuario_empresa
                (nome, cnpj, senha)
                VALUES
                (:nome, :cnpj, :senha)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':cnpj', $cnpj);
        $stmt->bindParam(':senha', $senha);

        return $stmt->execute();
    }
}