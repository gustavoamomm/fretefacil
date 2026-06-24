<?php

class Frete {
    private $id_frete;
    private $origem;
    private $destino;
    private $valor;
    private $empresa_id;
    private $carro_necessario;
    private $status;
    private $pcu_id;
    private $data_pagamento;


    public function __construct($origem, $destino, $valor, $empresa_id, $carro_necessario, $status = "Pendente", $pcu_id = null, $data_pagamento = null){

        $this->origem = $origem;
        $this->destino = $destino;
        $this->valor = $valor;
        $this->empresa_id = $empresa_id;
        $this->carro_necessario = $carro_necessario;
        $this->status = $status;
        $this->pcu_id = $pcu_id;
        $this->data_pagamento = $data_pagamento;
    }

    public function getIdFrete(){return $this->id_frete;}
    public function setIdFrete($id_frete){$this->id_frete = $id_frete;}


    public function getOrigem(){return $this->origem;}
    public function setOrigem($origem){$this->origem = $origem;}

    public function getDestino(){return $this->destino;}
    public function setODestino($destino){$this->destino = $destino;}
    

    public function getValor(){return $this->valor;}
    public function setValor($valor){$this->valor = $origem;}

    public function getEmpresaId(){return $this->empresa_id;}
    public function setEmpresaId($empresa_id){$this->empresa_id = $empresa_id;}

    public function getCarroNecessario(){return $this->carro_necessario;}
    public function setCarroNecessario($carro_necessario){$this->carro_necessario = $carro_necessario;}

    public function getStatus(){return $this->status;}
    public function setStatus($carro_necessario){$this->carro_necessario = $carro_necessario;}

    public function getPcuId(){return $this->pcu_id;}
    public function setPcuID($pcu_id){$this->pcu_id = $pcu_id;}


    public function getDataPagamento(){return $this->data_pagamento;}
    public function setDataPagamento($data_pagamento){$this->data_pagamento = $data_pagamento;}


}