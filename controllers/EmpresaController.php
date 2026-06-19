<?php

require_once "../models/EmpresaDAO.php";

try {

    $nome = $_POST['nome'] ?? '';
    $cnpj = $_POST['cnpj'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    if (empty($nome) || empty($cnpj) || empty($senha) || empty($confirmar_senha)) {
        die("Preencha todos os campos obrigatórios.");
    }

    $cnpj = preg_replace('/\D/', '', $cnpj);

    if (strlen($cnpj) !== 14) {
        die("CNPJ inválido.");
    }

    if (strlen($senha) < 8) {
        die("A senha deve conter no mínimo 8 caracteres.");
    }

    if ($senha !== $confirmar_senha) {
        die("As senhas não coincidem.");
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $empresaDAO = new EmpresaDAO();

    $empresaDAO->cadastrar(
        $nome,
        $cnpj,
        $senha_hash
    );

    echo "Empresa cadastrada com sucesso.";

} catch (PDOException $e) {

    echo "ERRO SQL: " . $e->getMessage();

}