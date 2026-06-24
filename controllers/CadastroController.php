<?php

session_start();

$tipo = $_POST['tipo'] ?? '';

if ($tipo === 'empresa') {

    require_once "EmpresaController.php";
    exit;

}

if ($tipo === 'motorista') {

    require_once "MotoristaController.php";
    exit;

}

$_SESSION['feedback'] = [
    'tipo' => 'erro',
    'mensagem' => 'Tipo de usuário inválido.'
];

header("Location: ../views/cadastro.php");
exit;