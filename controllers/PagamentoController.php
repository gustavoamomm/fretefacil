<?php

session_start();

require_once __DIR__ . "/../models/PagamentoDAO.php";
require_once __DIR__ . "/../models/FreteDAO.php";
require_once __DIR__ . "/../models/CarteiraDAO.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../views/dashboard-emp.php");
    exit;
}

$id_pagamento = $_POST["id_pagamento"] ?? null;
$metodo = $_POST["metodo"] ?? null;

if (empty($id_pagamento) || empty($metodo)){
    $_SESSION["erro"] = "Dados do pagamento inválidos.";

    header("Location: ../views/dashboard-emp.php");
    exit;
}

try{
    $pagamentoDAO = new PagamentoDAO();
    $freteDAO = new FreteDAO;
    $carteiraDAO = new CarteiraDAO();

    $pagamentoDAO->realizarPagamento($id_pagamento, $metodo);

    $pagamento = $pagamentoDAO->buscarFretePagamento($id_pagamento);

    $freteDAO->marcarComoPago($pagamento["frete_id"]);

    // Credita o valor do frete na carteira do motorista que fez a entrega
    $carteiraDAO->atualizarSaldo($pagamento["usuario_pcu_id"], $pagamento["valor"]);

    $_SESSION["sucesso"] = "Pagamento realizado com sucesso!";

}catch (Exception $e){
    $_SESSION["erro"] = $e->getMessage();
}

header("Location: ../views/dashboard-emp.php");
exit;