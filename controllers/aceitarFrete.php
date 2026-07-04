<?php  

session_start()
require_once "../controllers/FreteDAO.php";

if ($_SERVER["REQUEST_METHOD"] != "POST"){
    header("Location: ../views/dashboard-moto.php");
    exit;
}

$id_frete = $_POST["id_frete"];
$pcu_id = $_SESSION["id"];

$dao = new FreteDAO();

if($dao->aceitarFrete($id_frete,$pcu_id)){
    header("Location: ../views/dashboard-moto.php?sucesso=1");
}else{
    header("Location: ../views/dashboard-moto.php?erro=1");
}

exit;