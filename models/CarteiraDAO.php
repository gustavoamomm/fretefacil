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

    public function atualizarSaldo($pcu_id, $valor, $frete_id = null, $descricao = null)
    {
        $this->conn->beginTransaction();

        try {
            $sql = "UPDATE tb_carteira
                    SET saldo = saldo + :valor
                    WHERE pcu_id = :pcu_id";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":valor", $valor);
            $stmt->bindValue(":pcu_id", $pcu_id);

            $stmt->execute();

            $carteira = $this->buscarPorMotorista($pcu_id);

            if (!$carteira) {
                throw new Exception("Carteira não encontrada para o motorista informado.");
            }

            $sqlMov = "INSERT INTO tb_movimentacao
                        (carteira_id, frete_id, valor, tipo, descricao, data)
                        VALUES
                        (:carteira_id, :frete_id, :valor, 'Entrada', :descricao, NOW())";

            $stmtMov = $this->conn->prepare($sqlMov);
            $stmtMov->bindValue(":carteira_id", $carteira["id"]);
            $stmtMov->bindValue(":frete_id", $frete_id);
            $stmtMov->bindValue(":valor", $valor);
            $stmtMov->bindValue(":descricao", $descricao ?? "Pagamento de frete recebido");
            $stmtMov->execute();

            $this->conn->commit();

            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function buscarTotalRecebido($carteira_id)
    {
        $sql = "SELECT COALESCE(SUM(valor),0) total
                FROM tb_movimentacao
                WHERE carteira_id = :carteira_id
                AND tipo='Entrada'";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":carteira_id",$carteira_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)["total"];
    }    
    public function buscarAguardandoPagamento($pcu_id)
    {
        $sql = "SELECT COALESCE(SUM(valor),0) total
                FROM tb_frete
                WHERE pcu_id=:pcu_id
                AND status='Concluido'
                AND data_pagamento IS NULL";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":pcu_id",$pcu_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)["total"];
    }
    public function buscarFretesConcluidos($pcu_id)
    {
        $sql="SELECT COUNT(*) total
            FROM tb_frete
            WHERE pcu_id=:pcu_id
            AND status='Concluido'";

        $stmt=$this->conn->prepare($sql);
        $stmt->bindValue(":pcu_id",$pcu_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)["total"];
    }    
    public function buscarExtrato($carteira_id)
    {
        $sql="SELECT *
            FROM tb_movimentacao
            WHERE carteira_id=:carteira_id
            ORDER BY data DESC";

        $stmt=$this->conn->prepare($sql);
        $stmt->bindValue(":carteira_id",$carteira_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}