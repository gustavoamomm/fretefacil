<?php

require_once "../models/EmpresaDAO.php";

try {

    $nome = $_POST['nome'] ?? '';
    $cnpj = $_POST['cnpj'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    if (empty($nome) || empty($cnpj) || empty($senha) || empty($confirmar_senha)) {
        
        $_SESSION['feedback'] = [
            'tipo' => 'erro',
            'mensagem' => '⚠️ Preencha todos os campos obrigatórios.'
        ];

        header("Location: ../views/cadastro.php");
        exit;
        
    }

    $cnpj = preg_replace('/\D/', '', $cnpj);

    if (strlen($cnpj) !== 14) {

        $_SESSION['feedback'] = [
            'tipo' => 'erro',
            'mensagem' => '⚠️ CNPJ inválido.'
        ];

        header("Location: ../views/cadastro.php");
        exit;

    }

    if (strlen($senha) < 8) {
        $_SESSION['feedback'] = [
            'tipo' => 'erro',
            'mensagem' => '⚠️ A senha deve conter no mínimo 8 caracteres.'
        ];

        header("Location: ../views/cadastro.php");
        exit;
    }

    if ($senha !== $confirmar_senha) {
        $_SESSION['feedback'] = [
            'tipo' => 'erro',
            'mensagem' => '⚠️ As senhas não coincidem.'
        ];

        header("Location: ../views/cadastro.php");
        exit;
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $empresaDAO = new EmpresaDAO();

    $empresaDAO->cadastrar(
        $nome,
        $cnpj,
        $senha_hash
    );

    $_SESSION['feedback'] = [
        'tipo' => 'sucesso',
        'mensagem' => '✅ Empresa cadastrada com sucesso!'
    ];

    header("Location: ../views/login.php");
    exit;

} catch (PDOException $e) {

    $_SESSION['feedback'] = [
        'tipo' => 'erro',
        'mensagem' => '⚠️ Erro ao cadastrar empresa. Verifique se o CNPJ já está cadastrado e tente novamente.'
    ];

    header("Location: ../views/cadastro.php");
    exit;

}