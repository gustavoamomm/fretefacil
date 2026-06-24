<?php

session_start();

require_once "../models/Frete.php";
require_once "../models/FreteDAO.php";

try {

    $origem = $_POST['origem'];
    $destino = $_POST['destino'];
    $peso = $_POST['peso'];
    $valor = $_POST['valor'];
    $data_coleta = $_POST['data_coleta'];
    $hora_coleta = $_POST['hora_coleta'];
    $descricao = $_POST['descricao'];
    $carro_necessario = $_POST['carro_necessario'];

    $empresa_id = $_SESSION['id'];

    $frete = new Frete(
        $origem,
        $destino,
        $peso,
        $valor,
        $data_coleta,
        $hora_coleta,
        $descricao,
        $empresa_id,
        $carro_necessario
    );

    $dao = new FreteDAO();
    $dao->cadastrar($frete);

    header("Location: ../views/dashboard-emp.php");
    exit;

} catch (Exception $e) {

    echo $e->getMessage();
}