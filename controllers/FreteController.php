<?php

session_start();

require_once "../models/Frete.php";
require_once "../models/FreteDAO.php";
require_once "../models/Pagamento.php";
require_once "../models/PagamentoDAO.php";

$dao = new FreteDAO();
$pagamentoDAO = new PagamentoDAO();

function validarDataColeta($data)
{
    $dataObj = DateTime::createFromFormat('Y-m-d', $data);

    if (!$dataObj || $dataObj->format('Y-m-d') !== $data) {
        return false;
    }

    $hoje = new DateTime();
    $hoje->setTime(0, 0, 0);

    $limite = new DateTime();
    $limite->modify('+2 years');

    return $dataObj >= $hoje && $dataObj <= $limite;
}



if (isset($_POST["acao"])) {

    switch ($_POST["acao"]) {

        case "editar":

            if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'empresa') {
                header("Location: ../views/login.php");
                exit;
            }

            $id_frete = $_POST["id_frete"];
            $empresa_id = $_SESSION["id"];

            if (!validarDataColeta($_POST['data_coleta'])) {
                $_SESSION["feedback"] = [
                    "tipo" => "erro",
                    "mensagem" => "⚠️ A data de coleta deve estar entre hoje e os próximos 2 anos."
                ];

             header("Location: ../views/dashboard-emp.php");
            exit;
            }

            $frete = new Frete(
                $_POST['origem'],
                $_POST['uf_origem'],
                $_POST['destino'],
                $_POST['uf_destino'],
                $_POST['peso'],
                $_POST['valor'],
                $_POST['data_coleta'],
                $_POST['hora_coleta'],
                $_POST['descricao'],
                $empresa_id,
                $_POST['carro_necessario']
            );
            $frete->setIdFrete($id_frete);

            if ($dao->atualizar($frete)) {

                $_SESSION["feedback"] = [
                    "tipo" => "sucesso",
                    "mensagem" => "✅ Frete atualizado com sucesso!"
                ];

            } else {

                $_SESSION["feedback"] = [
                    "tipo" => "erro",
                    "mensagem" => "⚠️ Não foi possível editar o frete. Ele só pode ser editado enquanto estiver disponível."
                ];

            }

            header("Location: ../views/dashboard-emp.php");
            exit;

        case "excluir":

            if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'empresa') {
                header("Location: ../views/login.php");
                exit;
            }

            $id_frete = $_POST["id_frete"];
            $empresa_id = $_SESSION["id"];

            if ($dao->excluir($id_frete, $empresa_id)) {

                $_SESSION["feedback"] = [
                    "tipo" => "sucesso",
                    "mensagem" => "🗑️ Frete excluído com sucesso!"
                ];

            } else {

                $_SESSION["feedback"] = [
                    "tipo" => "erro",
                    "mensagem" => "⚠️ Não foi possível excluir o frete. Ele só pode ser excluído enquanto estiver disponível."
                ];

            }

            header("Location: ../views/dashboard-emp.php");
            exit;

        case "aceitar":

            if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'motorista') {
                header("Location: ../views/login.php");
                exit;
            }

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

            if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'motorista') {
                header("Location: ../views/login.php");
                exit;
            }

            $id_frete = $_POST["id_frete"];
            $pcu_id = $_SESSION["id"];

            if ($dao->iniciarViagem($id_frete, $pcu_id)) {

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

            if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'motorista') {
                header("Location: ../views/login.php");
                exit;
            }

            $id_frete = $_POST["id_frete"];
            $pcu_id = $_SESSION["id"];

            if ($dao->solicitarConclusao($id_frete, $pcu_id)) {

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

            if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'empresa') {
                header("Location: ../views/login.php");
                exit;
            }

            $id_frete = $_POST["id_frete"];
            $empresa_id = $_SESSION["id"];

            if ($dao->confirmarEntrega($id_frete, $empresa_id)) {
                $frete = $dao->buscarPorId($id_frete);

                if (!$pagamentoDAO->buscarPorFrete($id_frete)) {
                    $pagamento = new Pagamento(
                        $frete["id_frete"],
                        $frete["empresa_id"],
                        $frete["pcu_id"],
                        $frete["valor"]
                    );

                    $pagamento->setStatus("Pendente");

                    $pagamentoDAO->criarPagamento($pagamento);
                }
                
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

    if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'empresa') {
        header("Location: ../views/login.php");
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
        
        if (!validarDataColeta($data_coleta)) {

            $_SESSION["feedback"] = [
                "tipo" => "erro",
                "mensagem" => "⚠️ A data de coleta deve estar entre hoje e os próximos 2 anos."
            ];

        header("Location: ../views/dashboard-emp.php");
        exit;
        }
        
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

        $_SESSION["feedback"] = [
            "tipo" => "sucesso",
            "mensagem" => "✅ Frete publicado com sucesso!"
        ];

        header("Location: ../views/dashboard-emp.php");
        exit;
        

    } catch (Exception $e) {

        $_SESSION["feedback"] = [
            "tipo" => "erro",
            "mensagem" => "⚠️ Não foi possível publicar o frete. Tente novamente."
        ];

        header("Location: ../views/dashboard-emp.php");
        exit;

    }

}
