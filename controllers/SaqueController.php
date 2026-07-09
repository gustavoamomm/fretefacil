<?php

session_start();

require_once __DIR__ . "/../models/CarteiraDAO.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION["tipo"]) || $_SESSION["tipo"] !== "motorista") {
    header("Location: ../views/login.php");
    exit;
}

$valor = $_POST["valor"] ?? null;
$chavePix = $_POST["chave_pix"] ?? "";

try {
    $dao = new CarteiraDAO();
    $dao->solicitarSaque($_SESSION["id"], $valor, $chavePix);

    $_SESSION["sucesso"] = "Saque solicitado com sucesso! O valor foi debitado da sua carteira.";
} catch (Exception $e) {
    $_SESSION["erro"] = $e->getMessage();
}

header("Location: ../views/dashboard-moto.php");
exit;
