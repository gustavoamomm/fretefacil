<?php

require_once "models/PagamentoDAO.php";

$dao = new PagamentoDAO();

try {

    $dao->realizarPagamento(10); // coloque aqui o id_frete que você preparou

    echo "Pagamento realizado com sucesso!";

} catch (Exception $e) {

    echo $e->getMessage();

}