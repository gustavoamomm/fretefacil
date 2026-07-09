<?php include 'components/header.php'; ?>



<nav class="topnav">

  <a href="#" class="logo">🚚 Frete<em>Fácil</em></a> 
  <div class="nav-r">
    <a href="#como" class="nav-link">Como funciona</a>
    <a href="views/login.php" class="btn btn-gh">Login</a>
    <a href="views/cadastro.php" class="btn btn-o">Cadastre-se</a>
  </div>
</nav>


<section class="hero reveal">
    <div class="container">
      <div class="hero-wrap">

      <div>
        <div class="hero-eyebrow">🚀 Plataforma de fretes on-demand</div>
        <h1 class="hero-title">
          CONECTAMOS <br><em>EMPRESAS</em></br>
          E MOTORISTAS
        </h1>

        <p class="hero-sub">
          Empresas postam seus fretes.<br><strong>Proprietários de utilitários</strong> visualizam e aceitam na hora.
        </p>
        <div class="hero-ctas">
          <a href="#" class="btn btn-o">Postar frete agora →</a>
          <a href="#como" class="btn btn-gh">Como funciona</a>
        </div>
      </div>


      <div class="hero-card">
        <div class="card-label">🚐 Fretes disponíveis pra você</div>
        <div class="frete-item">
          <div>
            <div class="frete-rota">Joinville → Blumenau</div>
            <div class="frete-info">400 kg · Amanhã 08h · 3 km de você</div>
          </div>
          <div class="frete-valor">R$ 280</div>
        </div>
        <div class="frete-item">
          <div>
            <div class="frete-rota">Joinville → Itajaí</div>
            <div class="frete-info">200 kg · Hoje 14h · 7 km de você</div>
          </div>
          <div class="frete-valor">R$ 220</div>
        </div>
      </div>



      </div>
    </div>
</section>


<section id="como">
  <div class="container">

  <div class="s-head reveal">

    <div class="s-tag">Como funciona</div>
    <h2 class="s-title">DO CLIQUE À ENTREGA</h2>
    <p class="s-sub">Um fluxo simples que conecta quem precisa enviar com quem pode entregar</p>
  </div>

  <div class="how-grid">

    <div class="how-side empresa">
      <h3>🏢 Para empresas</h3>

      <div class="step">
        <div class="step-num">1</div>
        <div class="step-body">
          <h4>Publique o frete</h4>
          <p>Informe origem, destino, peso e horário. Leva menos de 2 minutos.</p>
        </div>
      </div>

      <div class="step">
        <div class="step-num">2</div>
        <div class="step-body">
          <h4>Receba confirmação</h4>
          <p>Motoristas da sua região visualizam e aceitam na hora.</p>
        </div>
      </div>

      <div class="step">
        <div class="step-num">3</div>
        <div class="step-body">
          <h4>Rastreie em tempo real</h4>
          <p>Acompanhe a entrega direto no painel. Notificação quando chegar.</p>
        </div>
      </div>
    </div>


    <div class="how-side motorista">
      <h3>🚐 Para motoristas</h3>

      <div class="step">
        <div class="step-num">1</div>
        <div class="step-body">
          <h4>Crie seu perfil</h4>
          <p>Cadastre seu veículo, região e disponibilidade. Gratuito.</p>
        </div>
      </div>

      <div class="step">
        <div class="step-num">2</div>
        <div class="step-body">
          <h4>Veja fretes disponíveis</h4>
          <p>Fretes próximos de você em tempo real, com valor e rota.</p>
        </div>
      </div>

      <div class="step">
        <div class="step-num">3</div>
        <div class="step-body">
          <h4>Aceite e ganhe</h4>
          <p>Escolha os fretes que quiser e receba após a entrega.</p>
        </div>
      </div>
    </div>
  </div>
  </div>
</section>




<section id="cta">
  <div class="container">
    <div class="cta-grid">

    <div class="cta-card empresa reveal">
      <h3>PARA EMPRESAS</h3>
      <P>Poste seu primeiro frete gratuitamente e veja o quanto você economiza.</P>
      <a href="#" class="btn btn-o">Postar frete grátis →</a>
    </div>

    <div class="cta-card motorista reveal">
      <h3>PARA MOTORISTAS</h3>
      <p>Cadastre seu veículo e receba fretes na sua região ainda hoje.</p>
      <a href="#" class="btn btn-b">Ver fretes disponíveis →</a>
    </div>

    </div>
  </div>
</section>

    <?php include 'components/footer.php'; ?>

    <script src="/static/script.js"></script>


</body>
</html>