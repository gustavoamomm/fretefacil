<?php

session_start();

require_once "../models/Frete.php";
require_once "../models/FreteDAO.php";
require_once "../models/Pagamento.php";
require_once "../models/PagamentoDAO.php";

$dao = new FreteDAO();
$pagamentoDAO = new PagamentoDAO();



if (isset($_POST["acao"])) {

    switch ($_POST["acao"]) {

        case "aceitar":

            $id_frete = $_POST["id_frete"];
            $pcu_id = $_SESSION["id"];

            if ($dao->aceitarFrete($id_frete, $pcu_id)) {
                $_SESSION["feedback"] = [
                    "tipo" => "sucesso",
                    "mensagem" => "Frete aceito com sucesso!"
                ];

            } else {

                $_SESSION["feedback"] = [
                    "tipo" => "erro",
                    "mensagem" => "Não foi possível aceitar o frete."
                ];

            }


            header("Location: ../views/dashboard-moto.php");
            exit;

        case "iniciar":

            $id_frete = $_POST["id_frete"];

            if ($dao->iniciarViagem($id_frete)) {

                $_SESSION["feedback"] = [
                    "tipo" => "sucesso",
                    "mensagem" => "Viagem iniciada!"
                ];

            } else {

                $_SESSION["feedback"] = [
                    "tipo" => "erro",
                    "mensagem" => "Não foi possível iniciar a viagem."
                ];

            }

            header("Location: ../views/dashboard-moto.php");
            exit;

        case "concluir":

            $id_frete = $_POST["id_frete"];

            if ($dao->solicitarConclusao($id_frete)) {

                $_SESSION["feedback"] = [
                    "tipo" => "sucesso",
                    "mensagem" => "Entrega enviada para confirmação!"
                ];

            } else {

                $_SESSION["feedback"] = [
                    "tipo" => "erro",
                    "mensagem" => "Não foi possível solicitar a conclusão."
                ];

            }

            header("Location: ../views/dashboard-moto.php");
            exit;

        case "confirmar":

            $id_frete = $_POST["id_frete"];

            if ($dao->confirmarEntrega($id_frete)) {
                $frete = $dao->buscarPorId($id_frete);
                
                $pagamento = new Pagamento(
                    $frete["id_frete"],
                    $frete["empresa_id"],
                    $frete["pcu_id"],
                    $frete["valor"]
                );

                $pagamento->setStatus("Pendente");

                $pagamentoDAO->criarPagamento($pagamento);
                
                $_SESSION["feedback"] = [
                    "tipo" => "sucesso",
                    "mensagem" => "Entrega confirmada! O pagamento já pode ser realizado."
                ];

            } else {

                $_SESSION["feedback"] = [
                    "tipo" => "erro",
                    "mensagem" => "Não foi possível confirmar a entrega."
                ];

            }

            header("Location: ../views/dashboard-emp.php");
            exit;
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST["acao"])){
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

        $dao->cadastrar($frete);

        header("Location: ../views/dashboard-emp.php");
        exit;
        

    } catch (Exception $e) {

        echo $e->getMessage();

    }

}

