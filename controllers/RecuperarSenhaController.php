<?php

session_start();

require_once "../config/config.php";

try {

    $conn = Config::getConexao();

    $tipo = $_POST["tipo"];
    $senha = $_POST["senha"];
    $confirmar = $_POST["confirmar"];

    if ($senha !== $confirmar) {

        $_SESSION["feedback"] = [
            "tipo" => "erro",
            "mensagem" => "❌ As senhas não coincidem."
        ];

        header("Location: ../views/esqueci-senha.php");
        exit;
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    if ($tipo == "empresa") {

        $cnpj = preg_replace('/\D/', '', $_POST["cnpj"]);

        $sql = "SELECT id_empresa
                FROM tb_usuario_empresa
                WHERE cnpj = :cnpj";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":cnpj", $cnpj);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {

            $_SESSION["feedback"] = [
                "tipo" => "erro",
                "mensagem" => "❌ Empresa não encontrada."
            ];

            header("Location: ../views/esqueci-senha.php");
            exit;
        }

        $sql = "UPDATE tb_usuario_empresa
                SET senha = :senha
                WHERE cnpj = :cnpj";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(":senha", $senhaHash);
        $stmt->bindParam(":cnpj", $cnpj);

        $stmt->execute();

    } else {

        $cpf = preg_replace('/\D/', '', $_POST["cpf"]);

        $sql = "SELECT pcu_id
                FROM tb_usuario_pcu
                WHERE cpf = :cpf";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":cpf", $cpf);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {

            $_SESSION["feedback"] = [
                "tipo" => "erro",
                "mensagem" => "❌ Motorista não encontrado."
            ];

            header("Location: ../views/esqueci-senha.php");
            exit;
        }

        $sql = "UPDATE tb_usuario_pcu
                SET senha = :senha
                WHERE cpf = :cpf";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(":senha", $senhaHash);
        $stmt->bindParam(":cpf", $cpf);

        $stmt->execute();

    }

    $_SESSION["feedback"] = [
        "tipo" => "sucesso",
        "mensagem" => "✅ Senha alterada com sucesso! Faça login novamente."
    ];

    header("Location: ../views/login.php");
    exit;

} catch (Exception $e) {

    $_SESSION["feedback"] = [
        "tipo" => "erro",
        "mensagem" => "⚠️ Erro ao alterar a senha."
    ];

    header("Location: ../views/esqueci-senha.php");
    exit;
}