<?php

session_start();

require_once "../models/CarteiraDAO.php";

if (
    !isset($_SESSION["tipo"]) ||
    $_SESSION["tipo"] !== "motorista"
) {
    header("Location: ../views/login.php");
    exit;
}

$dao = new CarteiraDAO();

$carteira = $dao->buscarPorMotorista($_SESSION["id"]);