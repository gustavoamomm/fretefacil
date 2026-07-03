<?php
session_start();

require_once "../config/config.php";

try {

    $conn = Config::getConexao();

    $tipo = $_POST['tipo'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if ($tipo === 'empresa') {

        $cnpj = preg_replace('/\D/', '', $_POST['cnpj']);

        $sql = "SELECT * FROM tb_usuario_empresa
                WHERE cnpj = :cnpj";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cnpj', $cnpj);
        $stmt->execute();
        

        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($empresa && password_verify($senha, $empresa['senha'])) {

            $_SESSION['tipo'] = 'empresa';
            $_SESSION['id'] = $empresa['id_empresa'];
            $_SESSION['nome'] = $empresa['nome'];

            header("Location: ../views/dashboard-emp.php");
            exit;
        }
    }

    if ($tipo === 'motorista') {

        $cpf = preg_replace('/\D/', '', $_POST['cpf']);

        $sql = "SELECT * FROM tb_usuario_pcu
                WHERE cpf = :cpf";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->execute();

        $motorista = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($motorista && password_verify($senha, $motorista['senha'])) {

            $_SESSION['tipo'] = 'motorista';
            $_SESSION['id'] = $motorista['pcu_id'];
            $_SESSION['nome'] = $motorista['nome'];

            header("Location: ../views/dashboard-moto.php");
            exit;
        }
    }

    $_SESSION['feedback'] = [
        'tipo' => 'sucesso',
        'mensagem' => '✅ Motorista cadastrado com sucesso!'
    ];

    header("Location: ../views/login.php");
    exit;

} catch (PDOException $e) {

    $_SESSION['feedback'] = [
        'tipo' => 'erro',
        'mensagem' => '⚠️ Erro ao cadastrar motorista. Tente novamente.'
    ];

    header("Location: ../views/cadastro.php");
    exit;
}