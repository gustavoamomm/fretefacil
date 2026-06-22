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
    public function getOrigem(){
        return $this->origem;
    }

    public function getDestino(){
        return $this->destino;
    }

    public function getValor(){
        return $this->valor;
    }

    // public function getOrigem(){
    //     return $this->origem;
    // }
}