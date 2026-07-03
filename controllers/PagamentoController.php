<?php

session_start();

require_once __DIR__ . "/../models/PagamentoDAO.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../views/dashboard-emp.php");
    exit;
}

$id_frete = $_POST["id_frete"] ?? null;

if (empty($id_frete)) {
    $_SESSION["erro"] = "Frete inválido.";

    header("Location: ../views/dashboard-emp.php");
    exit;
}

try {

    $dao = new PagamentoDAO();

    $dao->realizarPagamento($id_frete);

    $_SESSION["sucesso"] = "Pagamento realizado com sucesso!";

} catch (Exception $e) {

    $_SESSION["erro"] = $e->getMessage();

}

header("Location: ../views/dashboard-emp.php");
exit;