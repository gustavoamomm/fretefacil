<?php

require_once __DIR__ . "/../config/config.php";

class PagamentoDAO{
    private $conn;

    public function __construct()
    {
        $this->conn = Config::getConexao();
    }

    public function criarPagamento(Pagamento $pagamento){

        $sql = "INSERT INTO tb_pagamento
                (
                    frete_id,
                    empresa_id,
                    usuario_pcu_id,
                    valor,
                    metodo,
                    status
                )
                VALUES
                (
                    :frete_id,
                    :empresa_id,
                    :usuario_pcu_id,
                    :valor,
                    :metodo,
                    :status
                )";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":frete_id", $pagamento->getFreteId());
        $stmt->bindValue(":empresa_id", $pagamento->getEmpresaId());
        $stmt->bindValue(":usuario_pcu_id", $pagamento->getUsuarioPcuId());
        $stmt->bindValue(":valor", $pagamento->getValor());
        $stmt->bindValue(":metodo", $pagamento->getMetodo());
        $stmt->bindValue(":status", $pagamento->getStatus());

        return $stmt->execute();
    }

    public function buscarPorFrete($frete_id){
        $sql = "SELECT *
                FROM tb_pagamento
                WHERE frete_id = :frete_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":frete_id", $frete_id);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function liberarPagamento($frete_id){
        $sql = "UPDATE tb_pagamento
                SET status = 'Pendente'
                WHERE frete_id = :frete_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":frete_id", $frete_id);

        return $stmt->execute();
    }

    public function definirMetodo($frete_id, $metodo){
        $sql = "UPDATE tb_pagamento
                SET metodo = :metodo
                WHERE frete_id = :frete_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":metodo", $metodo);
        $stmt->bindValue(":frete_id", $frete_id);

        return $stmt->execute();
    }

    public function processarPagamento($frete_id){
        $sql = "UPDATE tb_pagamento
                SET status = 'Processando'
                WHERE frete_id = :frete_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":frete_id", $frete_id);

        return $stmt->execute();
    }

    public function finalizarPagamento($frete_id){
        $sql = "UPDATE tb_pagamento
                SET
                   status = 'Pago',
                   data = NOW()
                WHERE frete_id = :frete_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":frete_id", $frete_id);

        return $stmt->execute();
    }

    public function listarPorEmpresa($empresa_id){
        $sql = "SELECT p.*, f.origem, f.destino, m.nome AS motorista FROM tb_pagamento p INNER JOIN tb_frete f ON p.frete_id = f.id_frete INNER JOIN tb_usuario_pcu m ON p.usuario_pcu_id = m.pcu_id WHERE p.empresa_id = :empresa_id ORDER BY p.data DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":empresa_id", $empresa_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);


    }



}