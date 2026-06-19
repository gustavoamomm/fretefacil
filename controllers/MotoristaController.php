<?php

require_once "../models/MotoristaDAO.php";

try {

    $nome = $_POST['nome'] ?? '';
    $dt_nasc = $_POST['dt_nasc'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $cnh = $_POST['cnh'] ?? '';
    $tipo_veiculo = $_POST['tipo_veiculo'] ?? '';
    $placa = $_POST['placa'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    

    // Verifica campos obrigatórios
    if (
        empty($nome) ||
        empty($dt_nasc) ||
        empty($cpf) ||
        empty($cnh) ||
        empty($tipo_veiculo) ||
        empty($placa) ||
        empty($senha) ||
        empty($confirmar_senha)
    ) {
        die("Preencha todos os campos obrigatórios.");
    }

    // Remove pontos e traços do CPF
    $cpf = preg_replace('/\D/', '', $cpf);

    // Valida CPF
    if (strlen($cpf) !== 11) {
        die("CPF inválido.");
    }

    $cnh = preg_replace('/\D/', '', $cnh);

    if (strlen($cnh) !== 11) {
        die("CNH Inválida.");
    }

    $tiposPermitidos = ['Fiorino', 'Sprinter', 'HR'];

    if (!in_array($tipo_veiculo, $tiposPermitidos)) {
        die("Tipo de veículo inválido.");
    }

    // Valida senha
    if (strlen($senha) < 8) {
        die("A senha deve conter no mínimo 8 caracteres.");
    }

    // Confirma senha
    if ($senha !== $confirmar_senha) {
        die("As senhas não coincidem.");
    }

    // Criptografa a senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $motoristaDAO = new MotoristaDAO();

    $motoristaDAO->cadastrar(
        $nome,
        $dt_nasc,
        $cpf,
        $cnh,
        $tipo_veiculo,
        $placa,
        $senha_hash
    );

    echo "Motorista cadastrado com sucesso!";

} catch (PDOException $e) {

    echo "ERRO SQL: " . $e->getMessage();

}