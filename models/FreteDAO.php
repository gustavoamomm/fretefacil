<?php

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/Frete.php";
class FreteDAO {

    private PDO $conn;

    

    public function __construct() {
        $this->conn = Config::getConexao();
    }

    

    public function cadastrar(Frete $frete) {

        $sql = "INSERT INTO tb_frete
                (
                    origem,
                    uf_origem,
                    destino,
                    uf_destino,
                    peso,
                    valor,
                    data_coleta,
                    hora_coleta,
                    descricao,
                    empresa_id,
                    carro_necessario,
                    status
                )
                VALUES
                (:origem,:uf_origem,:destino,:uf_destino,:peso,:valor,:data_coleta,:hora_coleta,:descricao,:empresa_id,:carro_necessario,'Disponivel')";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':origem', $frete->getOrigem());
        $stmt->bindValue(':uf_origem', $frete->getUfOrigem());
        $stmt->bindValue(':destino', $frete->getDestino());
        $stmt->bindValue(':uf_destino', $frete->getUfDestino());
        $stmt->bindValue(':peso', $frete->getPeso());
        $stmt->bindValue(':valor', $frete->getValor());
        $stmt->bindValue(':data_coleta', $frete->getDataColeta());
        $stmt->bindValue(':hora_coleta', $frete->getHoraColeta());
        $stmt->bindValue(':descricao', $frete->getDescricao());
        $stmt->bindValue(':empresa_id', $frete->getEmpresaId());
        $stmt->bindValue(':carro_necessario', $frete->getCarroNecessario());

        return $stmt->execute();
    }

    public function listarPorEmpresa($empresa_id) {

        $sql = "SELECT *
                FROM tb_frete
                WHERE empresa_id = :empresa_id
                ORDER BY id_frete DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':empresa_id', $empresa_id);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarDisponiveis() {

        $sql = "SELECT *
                FROM tb_frete
                WHERE status = 'Disponivel'
                ORDER BY id_frete DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function atualizar(Frete $frete) {

        // Só permite editar um frete que ainda esteja Disponivel
        // e que pertença à empresa que está fazendo a requisição.
        $sql = "UPDATE tb_frete
                SET
                    origem = :origem,
                    uf_origem = :uf_origem,
                    destino = :destino,
                    uf_destino = :uf_destino,
                    peso = :peso,
                    valor = :valor,
                    data_coleta = :data_coleta,
                    hora_coleta = :hora_coleta,
                    descricao = :descricao,
                    carro_necessario = :carro_necessario
                WHERE id_frete = :id_frete
                    AND empresa_id = :empresa_id
                    AND status = 'Disponivel'";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':origem', $frete->getOrigem());
        $stmt->bindValue(':uf_origem', $frete->getUfOrigem());
        $stmt->bindValue(':destino', $frete->getDestino());
        $stmt->bindValue(':uf_destino', $frete->getUfDestino());
        $stmt->bindValue(':peso', $frete->getPeso());
        $stmt->bindValue(':valor', $frete->getValor());
        $stmt->bindValue(':data_coleta', $frete->getDataColeta());
        $stmt->bindValue(':hora_coleta', $frete->getHoraColeta());
        $stmt->bindValue(':descricao', $frete->getDescricao());
        $stmt->bindValue(':carro_necessario', $frete->getCarroNecessario());
        $stmt->bindValue(':id_frete', $frete->getIdFrete());
        $stmt->bindValue(':empresa_id', $frete->getEmpresaId());

        $stmt->execute();

        // rowCount() == 0 tanto pode significar "não existe" quanto
        // "não é dono" ou "não está mais disponível" — nesses casos
        // consideramos que a edição não foi permitida.
        return $stmt->rowCount() > 0;
    }

    public function excluir($id_frete, $empresa_id) {

        // Mesma trava de segurança: só exclui se for da empresa
        // e ainda estiver Disponivel (frete já aceito não pode sumir).
        $sql = "DELETE FROM tb_frete
                WHERE id_frete = :id_frete
                    AND empresa_id = :empresa_id
                    AND status = 'Disponivel'";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':id_frete', $id_frete);
        $stmt->bindValue(':empresa_id', $empresa_id);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function aceitarFrete($id_frete, $pcu_id) {

        $sql = "UPDATE tb_frete
                SET
                    status = 'Aceito',
                    pcu_id = :pcu_id
                WHERE id_frete = :id_frete
                AND status = 'Disponivel'";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':pcu_id', $pcu_id);
        $stmt->bindParam(':id_frete', $id_frete);

        return $stmt->execute();
    }

    public function listarAceitosMotorista($pcu_id){

        $sql = "SELECT * FROM tb_frete WHERE pcu_id = :pcu_id
        AND status IN ('Aceito','Em andamento', 'Aguardando confirmacao', 'Concluido')
        ORDER BY data_coleta DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":pcu_id", $pcu_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function iniciarViagem($id_frete){
        $sql = "UPDATE tb_frete SET status = 'Em andamento'
        WHERE id_frete = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id_frete]);
    }

    public function solicitarConclusao($id_frete){
        $sql = "UPDATE tb_frete SET status = 'Aguardando confirmacao' WHERE id_frete = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id_frete]);
    }

    public function confirmarEntrega($id_frete){
        $sql = "UPDATE tb_frete SET status = 'Concluido' WHERE id_frete = ?";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([$id_frete]);
    }

    public function buscarPorId($id_frete){
        $sql = "SELECT * FROM tb_frete WHERE id_frete = :id_frete";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id_frete", $id_frete);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function marcarComoPago($id_frete){
        $sql = "UPDATE tb_frete SET status = 'Pago', data_pagamento = NOW() 
        WHERE id_frete = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id",$id_frete);
        return $stmt->execute();
    }
}