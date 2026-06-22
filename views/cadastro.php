<?php include __DIR__ . '/../components/header.php';?>
<?php include __DIR__ . '/../components/header.php';?>

<header>
    <nav class="topnav">
        <a href="../index.php" class="logo">🚚 Frete<em>Fácil</em></a>
        <div class="nav-r">
            <span class="nav-link">Já tem conta?</span>
            <a href="login.php" class="btn btn-gh">Entrar</a>
        </div>
    </nav>
</header>

<main>
  <div class="auth-box">
    <div class="auth-header">
      <div id="auth-icon">🏢</div>
      <h1 class="auth-title">CRIAR CONTA</h1>
      <p id="auth-sub">
      Cadastre sua empresa e publique fretes em minutos.
      </p>
    </div>

    <form id="form-cadastro" method="POST" novalidate>
      <input type="hidden" id="tipo-usuario" name="tipo" value="empresa">

      <!-- Abas -->
      <div class="tabs">
        <button type="button" class="tab ativo" id="tab-empresa"
          onclick="trocarAba('empresa')">🏢 Empresa</button>
        <button type="button" class="tab" id="tab-motorista"
          onclick="trocarAba('motorista')">🚐 Motorista</button>
      </div>

      <!-- Campos da empresa -->
      <div class="campos" id="campo-empresa">
        <div class="form-group">
          <label for="nome-empresa">Nome da empresa</label>
          <input type="text" id="nome-empresa" name="nome"
            placeholder="Ex: Distribuidora Silva Ltda" required>
        </div>
        <div class="form-group">
          <label for="cnpj">CNPJ</label>
          <input type="text" id="cnpj" name="cnpj"
            placeholder="00.000.000/0000-00" maxlength="18" required>
          <span class="field-hint">Será usado para fazer login</span>
        </div>
      </div>

      <!-- Campos do motorista -->
      <div class="campos escondido" id="campo-motorista">
        <div class="form-group">
          <label for="nome-motorista">Nome completo</label>
          <input type="text" id="nome-motorista" name="nome"
            placeholder="Ex: João da Silva">
        </div>
        <div class="form-group">
          <label for="cpf">CPF</label>
          <input type="text" id="cpf" name="cpf"
            placeholder="000.000.000-00" maxlength="14">
          <span class="field-hint">Será usado para fazer login</span>
        </div>
        <div class="form-group">
          <label for="cnh">CNH</label>
          <input type="text" id="cnh" name="cnh"
            placeholder="" maxlength="11">
        </div>
        <div class="form-group">
          <label for="dt_nasc">Data de nascimento</label>
          <input type="date" id="dt_nasc" name="dt_nasc">
        </div>
        <div class="form-group">
          <label for="veiculo">Tipo de veículo</label>
          <select id="veiculo" name="tipo_veiculo">
            <option value="">Selecione seu veículo</option>
            <option value="Fiorino">Fiorino</option>
            <option value="Sprinter">Sprinter</option>
            <option value="HR">HR</option>
          </select>
        </div>
        <div class="form-group">
          <label for="placa">Placa do veículo</label>
          <input type="text" id="placa" name="placa"
            placeholder="Ex: ABC-1234" maxlength="8">
        </div>
      </div>

      <!-- Senha — comum aos dois -->
      <div class="form-group">
        <label for="senha">Senha</label>
        <div class="senha-wrap">
          <input type="password" id="senha" name="senha" placeholder="Mínimo 8 caracteres" required>
          <span class="toggle-senha" id="toggle-senha">👁️</span>
        </div>
      </div>
      <div class="form-group">
        <label for="confirmar-senha">Confirmar senha</label>
        <div class="senha-wrap">
          <input type="password" id="confirmar-senha" name="confirmar_senha" placeholder="Repita a senha" required>
          <span class="toggle-senha" id="toggle-confirmar">👁️</span>
        </div>
      </div>

      <div class="form-feedback" id="feedback"></div>

      <button type="submit" class="btn btn-o btn-full">
        Criar conta →
      </button>

    </form>

    <div class="auth-footer">
      Já tem uma conta? <a href="login.html">Fazer login</a>
    </div>

  </div>
</main>

<script src="../static/script.js"></script>
    
</body>
</html>