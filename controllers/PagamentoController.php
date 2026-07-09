<?php

session_start();

require_once __DIR__ . "/../models/PagamentoDAO.php";
require_once __DIR__ . "/../models/FreteDAO.php";
require_once __DIR__ . "/../models/CarteiraDAO.php";

if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'empresa') {
    header("Location: ../views/login.php");
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

    $empresa_id = $_SESSION["id"];
    $pagamento = $pagamentoDAO->buscarFretePagamento($id_pagamento);

    if (!$pagamento || (int)$pagamento["empresa_id"] !== (int)$empresa_id) {
        throw new Exception("Pagamento não encontrado para esta empresa.");
    }

    if ($pagamento["status"] === "Pago") {
        throw new Exception("Este pagamento já foi realizado.");
    }

    if (!$pagamentoDAO->realizarPagamento($id_pagamento, $metodo, $empresa_id)) {
        throw new Exception("Não foi possível realizar o pagamento.");
    }

    if (!$freteDAO->marcarComoPago($pagamento["frete_id"])) {
        throw new Exception("Este frete já estava marcado como pago.");
    }

    $carteiraDAO->atualizarSaldo(
        $pagamento["usuario_pcu_id"],
        $pagamento["valor"],
        $pagamento["frete_id"],
        "Pagamento do frete #" . $pagamento["frete_id"],
        $id_pagamento
    );

    $_SESSION["sucesso"] = "Pagamento realizado com sucesso!";

}catch (Exception $e){
    $_SESSION["erro"] = $e->getMessage();
}

header("Location: ../views/dashboard-emp.php");
exit;