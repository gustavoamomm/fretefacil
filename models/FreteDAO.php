<?php

require_once "../config/config.php";
require_once "../models/Frete.php";
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
                (:origem,:uf_origem,:destino,:uf_destino,:peso,:valor,:data_coleta,:hora_coleta,:descricao,:empresa_id,:carro_necessario,'Pendente')";

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
                WHERE status = 'Pendente'
                ORDER BY id_frete DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function aceitarFrete($id_frete, $pcu_id) {

        $sql = "UPDATE tb_frete
                SET
                    status = 'Aceito',
                    pcu_id = :pcu_id
                WHERE id_frete = :id_frete
                AND status = 'Pendente'";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':pcu_id', $pcu_id);
        $stmt->bindParam(':id_frete', $id_frete);

        return $stmt->execute();
    }

    public function listarAceitosMotorista($pcu_id){

        $sql = "SELECT * FROM tb_frete WHERE pcu_id = :pcu_id
        AND status IN ('Aceito','Pago')
        ORDER BY data_coleta DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":pcu_id", $pcu_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}