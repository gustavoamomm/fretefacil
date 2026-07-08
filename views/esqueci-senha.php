<?php
session_start();
include __DIR__ . '/../components/header.php';
?>

<nav class="topnav">
    <a href="../index.php" class="logo">🚚 Frete<em>Fácil</em></a>

    <div class="nav-r">
        <a href="login.php" class="btn btn-o">
            Voltar ao login
        </a>
    </div>
</nav>

<main>

    <div class="auth-box">

        <div class="auth-header">

            <div class="auth-icon">
                🔑
            </div>

            <h1 class="auth-title">
                RECUPERAR SENHA
            </h1>

            <p class="auth-sub">
                Informe seus dados para cadastrar uma nova senha.
            </p>

        </div>

        <form
            method="POST"
            action="../controllers/RecuperarSenhaController.php">

            <div class="tabs">

                <button
                    type="button"
                    class="tab ativo"
                    id="tab-empresa">

                    🏢 Empresa

                </button>

                <button
                    type="button"
                    class="tab"
                    id="tab-motorista">

                    🚐 Motorista

                </button>

            </div>

            <input
                type="hidden"
                id="tipo"
                name="tipo"
                value="empresa">

            <div id="empresa">

                <div class="form-group">

                    <label>CNPJ</label>

                    <input
                        type="text"
                        name="cnpj"
                        placeholder="00.000.000/0000-00">

                </div>

                <div class="form-group">

                    <label>Nome da empresa</label>

                    <input
                        type="text"
                        name="nome_empresa"
                        placeholder="Nome da empresa">

                </div>

            </div>

            <div id="motorista" class="escondido">

                <div class="form-group">

                    <label>CPF</label>

                    <input
                        type="text"
                        name="cpf"
                        placeholder="000.000.000-00">

                </div>

                <div class="form-group">

                    <label>Data de nascimento</label>

                    <input
                        type="date"
                        name="dt_nasc">

                </div>

            </div>

            <div class="form-group">

                <label>Nova senha</label>

                <input
                    type="password"
                    name="senha"
                    minlength="8"
                    required>

            </div>

            <div class="form-group">

                <label>Confirmar senha</label>

                <input
                    type="password"
                    name="confirmar"
                    minlength="8"
                    required>

            </div>

            <?php
            if (isset($_SESSION["feedback"])) {
            ?>

                <div class="form-feedback <?= $_SESSION["feedback"]["tipo"] ?>">

                    <?= $_SESSION["feedback"]["mensagem"] ?>

                </div>

            <?php

                unset($_SESSION["feedback"]);

            } else {

            ?>

                <div class="form-feedback"></div>

            <?php } ?>

            <button
                type="submit"
                class="btn btn-o btn-full">

                Alterar senha →

            </button>

        </form>

    </div>

</main>

<script>

const empresa = document.getElementById("empresa");
const motorista = document.getElementById("motorista");

const tabEmpresa = document.getElementById("tab-empresa");
const tabMotorista = document.getElementById("tab-motorista");

const tipo = document.getElementById("tipo");

tabEmpresa.onclick = function(){

    tipo.value = "empresa";

    empresa.classList.remove("escondido");
    motorista.classList.add("escondido");

    tabEmpresa.classList.add("ativo");
    tabMotorista.classList.remove("ativo");

}

tabMotorista.onclick = function(){

    tipo.value = "motorista";

    motorista.classList.remove("escondido");
    empresa.classList.add("escondido");

    tabMotorista.classList.add("ativo");
    tabEmpresa.classList.remove("ativo");

}

</script>

</body>
</html>