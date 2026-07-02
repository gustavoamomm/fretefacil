<?php

require_once "../config/config.php";
require_once "Carteira.php";

class CarteiraDAO
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Config::getConexao();
    }

    public function buscarPorMotorista($pcu_id)
    {
        $sql = "SELECT *
                FROM tb_carteira
                WHERE pcu_id = :pcu_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":pcu_id", $pcu_id);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizarSaldo($pcu_id, $valor)
    {
        $sql = "UPDATE tb_carteira
                SET saldo = saldo + :valor
                WHERE pcu_id = :pcu_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":valor", $valor);
        $stmt->bindValue(":pcu_id", $pcu_id);

        return $stmt->execute();
    }
}