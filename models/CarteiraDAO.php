<?php

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/Carteira.php";

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
        $stmt->bindValue(":pcu_id", $pcu_id, PDO::PARAM_INT);
        $stmt->execute();

        $carteira = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$carteira) {
            $this->criarCarteira($pcu_id);

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":pcu_id", $pcu_id, PDO::PARAM_INT);
            $stmt->execute();
            $carteira = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return $carteira;
    }

    private function criarCarteira($pcu_id)
    {
        $sql = "INSERT INTO tb_carteira (saldo, pcu_id) VALUES (0.00, :pcu_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":pcu_id", $pcu_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function pagamentoJaCreditado($frete_id, $pagamento_id = null)
    {
        if ($pagamento_id !== null) {
            $sql = "SELECT COUNT(*) AS total
                    FROM tb_movimentacao
                    WHERE pagamento_id = :pagamento_id
                    AND tipo = 'Entrada'";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":pagamento_id", $pagamento_id, PDO::PARAM_INT);
            $stmt->execute();

            if ((int)$stmt->fetch(PDO::FETCH_ASSOC)["total"] > 0) {
                return true;
            }
        }

        $sql = "SELECT COUNT(*) AS total
                FROM tb_movimentacao
                WHERE frete_id = :frete_id
                AND tipo = 'Entrada'";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":frete_id", $frete_id, PDO::PARAM_INT);
        $stmt->execute();

        return (int)$stmt->fetch(PDO::FETCH_ASSOC)["total"] > 0;
    }

    public function creditarPagamentoFrete($pcu_id, $valor, $frete_id, $pagamento_id = null, $descricao = null)
    {
        $this->conn->beginTransaction();

        try {
            $carteira = $this->buscarPorMotorista($pcu_id);

            if (!$carteira) {
                throw new Exception("Carteira não encontrada para o motorista informado.");
            }

            if ($this->pagamentoJaCreditado($frete_id, $pagamento_id)) {
                $this->conn->commit();
                return false;
            }

            $sql = "UPDATE tb_carteira
                    SET saldo = saldo + :valor
                    WHERE id = :carteira_id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":valor", $valor);
            $stmt->bindValue(":carteira_id", $carteira["id"], PDO::PARAM_INT);
            $stmt->execute();

            $sqlMov = "INSERT INTO tb_movimentacao
                        (carteira_id, frete_id, valor, tipo, descricao, data, pagamento_id)
                        VALUES
                        (:carteira_id, :frete_id, :valor, 'Entrada', :descricao, NOW(), :pagamento_id)";

            $stmtMov = $this->conn->prepare($sqlMov);
            $stmtMov->bindValue(":carteira_id", $carteira["id"], PDO::PARAM_INT);
            $stmtMov->bindValue(":frete_id", $frete_id, PDO::PARAM_INT);
            $stmtMov->bindValue(":valor", $valor);
            $stmtMov->bindValue(":descricao", $descricao ?? "Pagamento de frete recebido");
            if ($pagamento_id !== null) {
                $stmtMov->bindValue(":pagamento_id", $pagamento_id, PDO::PARAM_INT);
            } else {
                $stmtMov->bindValue(":pagamento_id", null, PDO::PARAM_NULL);
            }
            $stmtMov->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function atualizarSaldo($pcu_id, $valor, $frete_id = null, $descricao = null, $pagamento_id = null)
    {
        if ($frete_id !== null) {
            return $this->creditarPagamentoFrete($pcu_id, $valor, $frete_id, $pagamento_id, $descricao);
        }

        $this->conn->beginTransaction();

        try {
            $carteira = $this->buscarPorMotorista($pcu_id);

            if (!$carteira) {
                throw new Exception("Carteira não encontrada para o motorista informado.");
            }

            $sql = "UPDATE tb_carteira
                    SET saldo = saldo + :valor
                    WHERE id = :carteira_id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":valor", $valor);
            $stmt->bindValue(":carteira_id", $carteira["id"], PDO::PARAM_INT);
            $stmt->execute();

            $sqlMov = "INSERT INTO tb_movimentacao
                        (carteira_id, frete_id, valor, tipo, descricao, data)
                        VALUES
                        (:carteira_id, 0, :valor, 'Entrada', :descricao, NOW())";

            $stmtMov = $this->conn->prepare($sqlMov);
            $stmtMov->bindValue(":carteira_id", $carteira["id"], PDO::PARAM_INT);
            $stmtMov->bindValue(":valor", $valor);
            $stmtMov->bindValue(":descricao", $descricao ?? "Crédito na carteira");
            $stmtMov->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    private function buscarUltimoPagamentoCreditado($carteira_id)
    {
        $sql = "SELECT frete_id, pagamento_id
                FROM tb_movimentacao
                WHERE carteira_id = :carteira_id
                AND tipo = 'Entrada'
                ORDER BY data DESC
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":carteira_id", $carteira_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function solicitarSaque($pcu_id, $valor, $chave_pix)
    {
        $valor = (float)$valor;
        $chave_pix = trim($chave_pix);

        if ($valor <= 0) {
            throw new Exception("Informe um valor de saque válido.");
        }

        if ($chave_pix === "") {
            throw new Exception("Informe a chave PIX para saque.");
        }

        $this->conn->beginTransaction();

        try {
            $carteira = $this->buscarPorMotorista($pcu_id);

            if (!$carteira) {
                throw new Exception("Carteira não encontrada.");
            }

            if ((float)$carteira["saldo"] < $valor) {
                throw new Exception("Saldo insuficiente para realizar o saque.");
            }

            $sql = "UPDATE tb_carteira
                    SET saldo = saldo - :valor
                    WHERE id = :carteira_id
                    AND saldo >= :valor";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":valor", $valor);
            $stmt->bindValue(":carteira_id", $carteira["id"], PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                throw new Exception("Não foi possível realizar o saque. Verifique o saldo disponível.");
            }

            $descricao = "Solicitação de saque via PIX - chave: " . $chave_pix;

            // A tabela tb_movimentacao possui chave estrangeira obrigatória para tb_frete.
            // Como saque não pertence a um frete específico, usamos o último frete pago que
            // gerou crédito nessa carteira apenas para respeitar a FK do banco.
            $pagamentoReferencia = $this->buscarUltimoPagamentoCreditado($carteira["id"]);

            if (!$pagamentoReferencia || empty($pagamentoReferencia["frete_id"])) {
                throw new Exception("Não foi encontrado um frete pago para vincular ao saque.");
            }

            $sqlMov = "INSERT INTO tb_movimentacao
                        (carteira_id, frete_id, valor, tipo, descricao, data, pagamento_id)
                        VALUES
                        (:carteira_id, :frete_id, :valor, 'Saida', :descricao, NOW(), :pagamento_id)";

            $stmtMov = $this->conn->prepare($sqlMov);
            $stmtMov->bindValue(":carteira_id", $carteira["id"], PDO::PARAM_INT);
            $stmtMov->bindValue(":frete_id", (int)$pagamentoReferencia["frete_id"], PDO::PARAM_INT);
            $stmtMov->bindValue(":valor", $valor);
            $stmtMov->bindValue(":descricao", $descricao);
            if (!empty($pagamentoReferencia["pagamento_id"])) {
                $stmtMov->bindValue(":pagamento_id", (int)$pagamentoReferencia["pagamento_id"], PDO::PARAM_INT);
            } else {
                $stmtMov->bindValue(":pagamento_id", null, PDO::PARAM_NULL);
            }
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
        $stmt->bindValue(":carteira_id", $carteira_id, PDO::PARAM_INT);
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
        $stmt->bindValue(":pcu_id", $pcu_id, PDO::PARAM_INT);
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
        $stmt->bindValue(":pcu_id", $pcu_id, PDO::PARAM_INT);
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
        $stmt->bindValue(":carteira_id", $carteira_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarSaques($carteira_id)
    {
        $sql="SELECT *
            FROM tb_movimentacao
            WHERE carteira_id=:carteira_id
            AND tipo='Saida'
            ORDER BY data DESC";

        $stmt=$this->conn->prepare($sql);
        $stmt->bindValue(":carteira_id", $carteira_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
