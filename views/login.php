<?php
session_start();
include __DIR__ . '/../components/header.php';
?>
<nav class="topnav">
    <a href="../index.php" class="logo">🚚 Frete<em>Fácil</em></a>
    <div class="nav-r">
        <span class="nav-link">Não tem conta?</span>
        <a href="cadastro.php" class="btn btn-o">Cadastrar</a>
    </div>
</nav>

<main>
    <div class="auth-box">
        <div class="auth-header">
            <div class="auth-icon" id="auth-icon">🔐</div>
            <h1 class="auth-title">ENTRAR</h1>
            <p class="auth-sub" id="auth-sub">Acesse sua conta para gerenciar seus fretes.</p>
        </div>

        <form id="form-login" method="POST" action="../controllers/LoginController.php">
            <input type="hidden"
                    id="tipo-usuario-login"
                    name="tipo"
                    value="empresa">
            <div class="tabs">
                <button type="button" class="tab ativo" id="tab-empresa" onclick="trocarAba('empresa')">🏢 Empresa</button>
                <button type="button" class="tab" id="tab-motorista" onclick="trocarAba('motorista')">🚐 Motorista</button>
            </div>

            <div id="campo-empresa">
                <div class="form-group">
                    <label for="cnpj">CNPJ</label>
                    <input type="text"
                            id="cnpj"
                            name="cnpj"
                            placeholder="00.000.000/0000-00" maxlenght="18">
                </div>
            </div>

        
            <div id="campo-motorista" class="escondido">
                <div class="form-group">
                    <label for="cpf">CPF</label>
                    <input type="text"
                            id="cpf"
                            name="cpf"
                            placeholder="000.000.000-00" maxlenght="14">
                </div>
            </div>

            <div class="form-group">
                <label for="senha">Senha</label>
                <div class="senha-wrap">
                    <input type="password"
                            id="senha"
                            name="senha"
                            placeholder="Sua senha">
                    <span class="toggle-senha" id="toggle-senha">👁️</span>
                </div>
            </div>

            <?php
            if (isset($_SESSION['feedback'])) {

                $feedback = $_SESSION['feedback'];
            ?>
                <div class="form-feedback <?= $feedback['tipo']; ?>">
                    <?= $feedback['mensagem']; ?>
                </div>
                
            <?php
                unset($_SESSION['feedback']);
            } else {
            ?>
                <div class="form-feedback"></div>
            <?php
            }
            ?>

            
        <button type="submit" class="btn btn-o btn-full">
            Entrar →</button>
        
        </form>
        <div class="auth-footer">
        Não tem conta? <a href="cadastro-emp.php">Cadastre-se grátis</a>
        </div>
    </div>
</main>

<script src="../static/script.js"></script>


</body>
</html>
