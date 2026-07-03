<?php

class Pagamento
{
    private $frete_id;
    private $empresa_id;
    private $usuario_pcu_id;
    private $valor;

    public function __construct(
        $frete_id,
        $empresa_id,
        $usuario_pcu_id,
        $valor
    ) {
        $this->frete_id = $frete_id;
        $this->empresa_id = $empresa_id;
        $this->usuario_pcu_id = $usuario_pcu_id;
        $this->valor = $valor;
    }

    public function getFreteId()
    {
        return $this->frete_id;
    }

    public function getEmpresaId()
    {
        return $this->empresa_id;
    }

    public function getUsuarioPcuId()
    {
        return $this->usuario_pcu_id;
    }

    public function getValor()
    {
        return $this->valor;
    }
}