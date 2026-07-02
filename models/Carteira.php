<?php

class Carteira
{
    private $id;
    private $saldo;
    private $pcu_id;

    public function __construct(
        $saldo,
        $pcu_id
    ) {
        $this->saldo = $saldo;
        $this->pcu_id = $pcu_id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSaldo()
    {
        return $this->saldo;
    }

    public function getPcuId()
    {
        return $this->pcu_id;
    }

    public function setSaldo($saldo)
    {
        $this->saldo = $saldo;
    }
}