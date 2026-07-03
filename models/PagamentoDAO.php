<?php

require_once __DIR__ . "/../config/config.php";

class PagamentoDAO
{
    private $conn;

    public function __construct()
    {
        $this->conn = Config::getConexao();
    }

    public function realizarPagamento($id_frete)
    {
        try {

            $this->conn->beginTransaction();

            // Busca o frete
            $sql = "SELECT *
                    FROM tb_frete
                    WHERE id_frete = :id_frete";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":id_frete", $id_frete);

            $stmt->execute();

            $frete = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$frete) {
                throw new Exception("Frete não encontrado.");
            }

            // Verifica se o frete já foi pago
            if ($frete["status"] == "Pago") {
                throw new Exception("Este frete já foi pago.");
            }

            // Permite pagamento apenas quando concluído
            if ($frete["status"] != "Aceito") {
                throw new Exception("O frete ainda não pode ser pago.");
            }

            // Registra o pagamento
            $sql = "INSERT INTO tb_pagamento
                    (
                        frete_id,
                        empresa_id,
                        usuario_pcu_id,
                        valor
                    )
                    VALUES
                    (
                        :frete_id,
                        :empresa_id,
                        :usuario_pcu_id,
                        :valor
                    )";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":frete_id", $frete["id_frete"]);
            $stmt->bindValue(":empresa_id", $frete["empresa_id"]);
            $stmt->bindValue(":usuario_pcu_id", $frete["pcu_id"]);
            $stmt->bindValue(":valor", $frete["valor"]);

            $stmt->execute();

            // Atualiza a carteira do motorista
            $sql = "UPDATE tb_carteira
                    SET saldo = saldo + :valor
                    WHERE pcu_id = :pcu_id";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":valor", $frete["valor"]);
            $stmt->bindValue(":pcu_id", $frete["pcu_id"]);

            $stmt->execute();

            // Atualiza o status do frete
            $sql = "UPDATE tb_frete
                    SET
                        status = 'Pago',
                        data_pagamento = NOW()
                    WHERE id_frete = :id_frete";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":id_frete", $frete["id_frete"]);

            $stmt->execute();

            $this->conn->commit();

            return true;

        } catch (Exception $e) {

            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            throw $e;
        }
    }
}