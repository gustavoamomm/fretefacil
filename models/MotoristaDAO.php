<?php

require_once "../config/config.php";

class MotoristaDAO {

    private PDO $conn;

    public function __construct() {
        $this->conn = Config::getConexao();
    }

    public function cadastrar($nome, $dt_nasc, $cpf, $cnh, $tipo_veiculo, $placa, $senha) {

        $this->conn->beginTransaction();

        try {

            $sql = "INSERT INTO tb_usuario_pcu
                    (nome, dt_nasc, cpf, cnh, tipo_veiculo, placa, senha)
                    VALUES
                    (:nome, :dt_nasc, :cpf, :cnh, :tipo_veiculo, :placa, :senha)";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':dt_nasc', $dt_nasc);
            $stmt->bindParam(':cpf', $cpf);
            $stmt->bindParam(':cnh', $cnh);
            $stmt->bindParam(':tipo_veiculo', $tipo_veiculo);
            $stmt->bindParam(':placa', $placa);
            $stmt->bindParam(':senha', $senha);

            $stmt->execute();

            $idMotorista = $this->conn->lastInsertId();

            $sqlCarteira = "INSERT INTO tb_carteira
                            (saldo, pcu_id)
                            VALUES
                            (0.00, :pcu_id)";

            $stmtCarteira = $this->conn->prepare($sqlCarteira);

            $stmtCarteira->bindParam(
                ':pcu_id',
                $idMotorista,
                PDO::PARAM_INT
            );

            $stmtCarteira->execute();

            $this->conn->commit();

            return true;

        } catch (Exception $e) {

            $this->conn->rollBack();

            throw $e;
        }
    }
}