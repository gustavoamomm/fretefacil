<?php

class Pagamento
{
    private $frete_id;
    private $empresa_id;
    private $usuario_pcu_id;
    private $valor;
    private $metodo;
    private $status;
    private $data;

    public function __construct(
        $frete_id,
        $empresa_id,
        $usuario_pcu_id,
        $valor,
        $metodo = null,
        $status = "Pendente",
        $data = null
    ) {
        $this->frete_id = $frete_id;
        $this->empresa_id = $empresa_id;
        $this->usuario_pcu_id = $usuario_pcu_id;
        $this->valor = $valor;
        $this->metodo = $metodo;
        $this->status = $status;
        $this->data = $data;
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

    public function getMetodo()
    {
        return $this->metodo;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setMetodo($metodo)
    {
        $this->metodo = $metodo;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setData($data)
    {
        $this->data = $data;
    }
}