<?php

session_start();

require_once "../config/config.php";

try {

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: ../views/esqueci-senha.php");
        exit;
    }

    $conn = Config::getConexao();

    $tipo = $_POST["tipo"] ?? "";
    $senha = $_POST["senha"] ?? "";
    $confirmar = $_POST["confirmar"] ?? "";

    if (strlen($senha) < 8) {
        $_SESSION["feedback"] = [
            "tipo" => "erro",
            "mensagem" => "❌ A senha deve ter no mínimo 8 caracteres."
        ];

        header("Location: ../views/esqueci-senha.php");
        exit;
    }

    if ($senha !== $confirmar) {

        $_SESSION["feedback"] = [
            "tipo" => "erro",
            "mensagem" => "❌ As senhas não coincidem."
        ];

        header("Location: ../views/esqueci-senha.php");
        exit;
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    if ($tipo === "empresa") {

        $cnpj = preg_replace('/\D/', '', $_POST["cnpj"] ?? "");
        $nomeEmpresa = trim($_POST["nome_empresa"] ?? "");

        $sql = "SELECT id_empresa
                FROM tb_usuario_empresa
                WHERE cnpj = :cnpj
                AND nome = :nome";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":cnpj", $cnpj);
        $stmt->bindValue(":nome", $nomeEmpresa);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {

            $_SESSION["feedback"] = [
                "tipo" => "erro",
                "mensagem" => "❌ Empresa não encontrada. Confira CNPJ e nome da empresa."
            ];

            header("Location: ../views/esqueci-senha.php");
            exit;
        }

        $sql = "UPDATE tb_usuario_empresa
                SET senha = :senha
                WHERE cnpj = :cnpj
                AND nome = :nome";

        $stmt = $conn->prepare($sql);

        $stmt->bindValue(":senha", $senhaHash);
        $stmt->bindValue(":cnpj", $cnpj);
        $stmt->bindValue(":nome", $nomeEmpresa);

        $stmt->execute();

    } elseif ($tipo === "motorista") {

        $cpf = preg_replace('/\D/', '', $_POST["cpf"] ?? "");
        $dtNasc = $_POST["dt_nasc"] ?? "";

        $sql = "SELECT pcu_id
                FROM tb_usuario_pcu
                WHERE cpf = :cpf
                AND dt_nasc = :dt_nasc";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":cpf", $cpf);
        $stmt->bindValue(":dt_nasc", $dtNasc);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {

            $_SESSION["feedback"] = [
                "tipo" => "erro",
                "mensagem" => "❌ Motorista não encontrado. Confira CPF e data de nascimento."
            ];

            header("Location: ../views/esqueci-senha.php");
            exit;
        }

        $sql = "UPDATE tb_usuario_pcu
                SET senha = :senha
                WHERE cpf = :cpf
                AND dt_nasc = :dt_nasc";

        $stmt = $conn->prepare($sql);

        $stmt->bindValue(":senha", $senhaHash);
        $stmt->bindValue(":cpf", $cpf);
        $stmt->bindValue(":dt_nasc", $dtNasc);

        $stmt->execute();

    } else {
        $_SESSION["feedback"] = [
            "tipo" => "erro",
            "mensagem" => "❌ Tipo de usuário inválido."
        ];

        header("Location: ../views/esqueci-senha.php");
        exit;
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
