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
    if (empty($nome) || empty($dt_nasc) || empty($cpf) || empty($cnh) || empty($tipo_veiculo) || empty($placa) || empty($senha) || empty($confirmar_senha)
    ) {

        $_SESSION['feedback'] = [
            'tipo' => 'erro',
            'mensagem' => '⚠️ Preencha todos os campos obrigatórios.'
        ];

        header("Location: ../views/cadastro.php");
        exit;
    }

    // Remove pontos e traços do CPF
    $cpf = preg_replace('/\D/', '', $cpf);

    // Valida CPF
    if (strlen($cpf) !== 11) {

        $_SESSION['feedback'] = [
            'tipo' => 'erro',
            'mensagem' => '⚠️ CPF inválido.'
        ];

        header("Location: ../views/cadastro.php");
        exit;
    }

    $cnh = preg_replace('/\D/', '', $cnh);

    if (strlen($cnh) !== 9) {
        $_SESSION['feedback'] = [
            'tipo' => 'erro',
            'mensagem' => '⚠️ CNH inválida.'
        ];

        header("Location: ../views/cadastro.php");
        exit;
    }

    $tiposPermitidos = ['Furgao', 'Van', 'Caminhao_pequeno'];

    if (!in_array($tipo_veiculo, $tiposPermitidos)) {

        $_SESSION['feedback'] = [
            'tipo' => 'erro',
            'mensagem' => '⚠️ Tipo de veículo invalido.'
        ];

        header("Location: ../views/cadastro.php");
        exit;
    }

    // Remove tudo que não for letra/número e padroniza maiúsculas
    $placa = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $placa));

    // Aceita modelo antigo (AAA9999) e Mercosul (AAA9A99)
    if (!preg_match('/^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$/', $placa)) {

        $_SESSION['feedback'] = [
            'tipo' => 'erro',
            'mensagem' => '⚠️ Placa inválida. Use o formato ABC-1234 ou ABC1D23.'
        ];

        header("Location: ../views/cadastro.php");
        exit;
    }

    // Valida senha
    if (strlen($senha) < 8) {
        $_SESSION['feedback'] = [
            'tipo' => 'erro',
            'mensagem' => '⚠️ A senha deve conter no mínimo 8 caracteres.'
        ];

        header("Location: ../views/cadastro.php");
        exit;
    }
    

    // Confirma senha
    if ($senha !== $confirmar_senha) {
        $_SESSION['feedback'] = [
            'tipo' => 'erro',
            'mensagem' => '⚠️ As senhas não coincidem.'
        ];

        header("Location: ../views/cadastro.php");
        exit;
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