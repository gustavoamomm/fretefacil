<?php

session_start();

require_once "../models/Frete.php";
require_once "../models/FreteDAO.php";

$dao = new FreteDAO;

if (isset($_POST["acao"]) && $_POST["acao"] == "aceitar") {
    $id_frete = $_POST["id_frete"];
    $pcu_id = $_SESSION["id"];

   
    if ($dao->aceitarFrete($id_frete, $pcu_id)){

        $_SESSION["feedback"] = [
            "tipo" => "sucesso",
            "mensagem" => "Frete aceito com sucesso!"
        ];
    }else{

        $_SESSION["feedback"] = [
            "tipo" => "erro",
            "mensagem" => "Não foi possível aceitar o frete."
        ];
    }

    header("Location: ../views/dashboard-moto.php");
    exit;
}

try {

    $origem = $_POST['origem'];
    $uf_origem = $_POST['uf_origem'];
    $destino = $_POST['destino'];
    $uf_destino = $_POST['uf_destino'];
    $peso = $_POST['peso'];
    $valor = $_POST['valor'];
    $data_coleta = $_POST['data_coleta'];
    $hora_coleta = $_POST['hora_coleta'];
    $descricao = $_POST['descricao'];
    $carro_necessario = $_POST['carro_necessario'];

    $empresa_id = $_SESSION['id'];

    $frete = new Frete(
        $origem,
        $uf_origem,
        $destino,
        $uf_destino,
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