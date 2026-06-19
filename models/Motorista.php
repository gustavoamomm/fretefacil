<?php

class Motorista {

    private $nome;
    private $dt_nasc;
    private $cpf;
    private $cnh;
    private $tipo_veiculo;
    private $placa;
    private $senha;

    public function __construct($nome, $dt_nasc, $cpf, $cnh, $tipo_veiculo, $placa, $senha) {
        $this->nome = $nome;
        $this->dt_nasc = $dt_nasc;
        $this->cpf = $cpf;
        $this->cpf = $cnh;
        $this->cpf = $tipo_veiculo;
        $this->cpf = $placa;
        $this->senha = $senha;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getDtNasc() {
        return $this->dt_nasc;
    }

    public function getCpf() {
        return $this->cpf;
    }

    public function getCnh() {
        return $this->cnh;
    }

    public function getTipoVeiculo() {
        return $this->tipo_veiculo;
    }

    public function getPlaca() {
        return $this->placa;
    }

    public function getSenha() {
        return $this->senha;
    }
}