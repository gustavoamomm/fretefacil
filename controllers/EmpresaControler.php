<?php
require_once "config.php";

try {

    $conn = Config::getConexao();

    $nome = $_POST['nome'] ?? '';
    $dt_nasc = $_POST['dt_nasc'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    // Verifica campos obrigatórios
    if (
        empty($nome) ||
        empty($dt_nasc) ||
        empty($cpf) ||
        empty($senha) ||
        empty($confirmar_senha)
    ) {
        die("Preencha todos os campos obrigatórios.");
    }

    // Remove pontos e traços do CPF
    $cpf = preg_replace('/\D/', '', $cpf);

    // Valida CPF (apenas tamanho)
    if (strlen($cpf) !== 11) {
        die("CPF inválido.");
    }

    // Valida senconha
    if (strlen($senha) < 8) {
        die("A senha deve conter no mínimo 8 caracteres.");
    }

    // Confirma senha
    if ($senha !== $confirmar_senha) {
        die("As senhas não coincidem.");
    }

    // Criptografa a senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO tb_usuario_pcu
            (nome, dt_nasc, cpf, senha)
            VALUES
            (:nome, :dt_nasc, :cpf, :senha)";

    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':dt_nasc', $dt_nasc);
    $stmt->bindParam(':cpf', $cpf);
    $stmt->bindParam(':senha', $senha_hash);

    $stmt->execute();

    echo "Motorista cadastrado com sucesso!";

} catch (PDOException $e) {

    echo "ERRO SQL: " . $e->getMessage();

}
?>