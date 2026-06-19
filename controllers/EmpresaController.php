<?php
require_once "config.php";

try {
    $conn = Config::getConexao();

    $nome = $_POST['nome'] ?? '';
    $cnpj = $_POST['cnpj'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';


    if (empty($nome) || empty($cnpj) || empty($senha) || empty($confirmar_senha)) {
        die("Preencha todos os campos obrigatórios.");
    }

    if (strlen(preg_replace('/\D/', '', $cnpj)) !== 14) {
        die("CNPJ inválido.");
    }

    if (strlen($senha) < 8) {
        die("A senha deve conter no mínimo 8 caracteres.");
    }

    if ($senha !== $confirmar_senha) {
        die("As senhas não coincidem.");
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO tb_usuario_empresa
    (nome, cnpj, senha)
    VALUES
    (:nome, :cnpj, :senha)";

    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':cnpj', $cnpj);
    $stmt->bindParam(':senha', $senha_hash);

    $stmt->execute();

    echo "Empresa cadastrada com sucesso.";

} catch(PDOException $e) {

    echo "ERRO SQL: " . $e->getMessage();

}
?>