<?php

class Empresa {

    private $nome;
    private $cnpj;
    private $senha;

    public function __construct($nome, $cnpj, $senha) {
        $this->nome = $nome;
        $this->cnpj = $cnpj;
        $this->senha = $senha;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getCnpj() {
        return $this->cnpj;
    }

    public function getSenha() {
        return $this->senha;
    }
}