<?php
session_start();

if (
    !isset($_SESSION['tipo']) ||
    $_SESSION['tipo'] !== 'empresa'
) {
    header("Location: login.php");
    exit;
}
?>
<?php 
$bodyClass = 'dashboard';
include __DIR__  . "/../components/header.php";
?>


<aside class="sidebar">
    <a href="index.php" class="sidebar-logo">
        🚚 Frete<em>Fácil</em>
    </a>


    <div class="sidebar-user">
        <div class="user-avatar">🏢</div>
        <div>
            <div class="user-name">Distribuidora Silva</div>
            <div class="user-role">Empresa</div>
        </div>
    </div>

    <div class="sidebar-divider"></div>

    <nav class="sidebar-nav">
        <button class="nav-item ativo" onclick="trocarAba('publicar')">
            <span class="nav-icon">📦</span>
            Publicar Frete
        </button>
        <button class="nav-item" onclick="trocarAba('fretes')">
            <span class="nav-icon">📋</span>
            Fretes Publicados
        </button>
        <button class="nav-item" onclick="trocarAba('pagamentos')">
            <span class="nav-icon">💳</span>
            Pagamentos
            <span class="nav-badge" id="badge-pendentes">2</span>
        </button>
    </nav>

    <div class="sidebar-bottom">
        <div class="sidebar-divider"></div>
        <a href="../logout.php" class="nav-item sair">
            <span class="nav-icon">🚪</span>
            Sair
        </a>
    </div>
</aside>

<main class="main-content">
    <div class="page-header">
        <div>
            <h1 class="page-title" id="page-title">PUBLICAR FRETE</h1>
            <p class="page-sub" id="page-sub">
                Preencha os dados e motoristas da sua região verão em instantes.
            </p>
        </div>
    </div>
    
    <div class="aba" id="aba-publicar">
        <form class="dash-form" id="form-frete">
            <div class="form-row">
                <div class="form-group">
                    <label for="origem">Cidade de origem</label>
                    <input type="text" id="origem" placeholder="Ex: Joinville - SC">
                </div>
                <div class="form-group">
                    <label for="destino">Cidade de destino</label>
                    <input type="text" id="destino" placeholder="Ex: Blumenau - SC">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="peso">Peso da carga (kg)</label>
                    <input type="number" id="peso" placeholder="Ex: 350">
                </div>
                <div class="form-group">
                    <label for="valor">Valor do frete (R$)</label>
                    <input type="number" id="valor" placeholder="Ex: 280">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="data">Data da coleta</label>
                    <input type="date" id="data">
                </div>
                <div class="form-group">
                    <label for="hora">Hora da coleta</label>
                    <input type="time" id="hora">
                </div>
            </div>

            <div class="form-group">
                <label for="descricao">Descrição da carga</label>
                <textarea id="descricao" rows="3" placeholder="Ex: Caixas frágeis, necessário cuidado no manuseio."></textarea>
            </div>

            <div class="form-feedback" id="feedback-frete"></div>

            <button type="submit" class="btn btn-o">
                Publicar frete →
            </button>
        </form>
    </div>

    <div class="aba escondido" id="aba-fretes">
        <div class="fretes-grid" id="fretes-grid">
            <div class="frete-card">
                <div class="frete-card-header"> 
                    <div>
                        <div class="frete-rota">Joinville → Blumenau</div>
                        <div class="frete-meta">400 kg · Amanhã, 08h</div>
                    </div>
                    <span class="frete-status aberto">Aberto</span>
                </div>
                <div class="frete-card-footer">
                    <div class="frete-valor">R$ 280,00</div>
                    <button class="btn btn-cancelar">Cancelar</button>
                </div>
            </div>

            <div class="frete-card">
                <div class="frete-card-header">
                    <div>
                        <div class="frete-rota">Joinville → Itajaí</div>
                        <div class="frete-meta">200 kg · Hoje, 14h</div>
                    </div>
                    <span class="frete-status aceito">Aceito ✓</span>
                </div>
                <div class="frete-card-footer">
                    <div class="frete-valor">R$ 190,00</div>
                    <div class="motorista-info">🚐 Roberto S.</div>
                </div>
            </div>
        </div>
        
    </div>

    <div class="aba escondido" id="aba-pagamentos">
        <div class="pagamento-resumo">
            <div class="resumo-card">
                <div class="resumo-icon">⏳</div>
                <div class="resumo-valor" style="color: var(--yellow);">R$ 470,00</div>
                <div class="resumo-label">Pendente de pagamento</div>
            </div>
            <div class="resumo-card">
                <div class="resumo-icon">✅</div>
                <div class="resumo-valor" style="color: var(--green);">R$ 1.240,00</div>
                <div class="resumo-label">Total pago este mês</div>
            </div>
            <div class="resumo-card">
                <div class="resumo-icon">🚚</div>
                <div class="resumo-valor">8</div>
                <div class="resumo-label">Fretes realizados</div>
            </div>
        </div>

        <div class="pagamento-secao">
            <h3 class="secao-titulo">Pendentes <span class="secao-hint">(pague em até 48h)</span></h3>

            <div class="pagamento-item pendente">
                <div class="pagamento-info">
                    <div class="pagamento-rota">Joinville → Itajaí</div>
                    <div class="pagamento-meta">Motorista: Roberto S. · Entregue ontem às 15h</div>
                </div>
                <div class="pagamento-direita">
                    <div class="pagamento-valor">R$ 190,00</div>
                    <div class="pagamento-prazo">⚠️ Vence em 24h</div>
                </div>
                <button class="btn btn-o btn-pagar">Pagar agora</button>
            </div>


            <div class="pagamento-item pendente">
                <div class="pagamento-info">
                    <div class="pagamento-rota">Joinville → Florianópolis</div>
                    <div class="pagamento-meta">Motorista: Carlos M. · Entregue hoje às 10h</div>
                </div>
                <div class="pagamento-direita">
                    <div class="pagamento-valor">R$ 280,00</div>
                    <div class="pagamento-prazo">✅ Vence em 48h</div>
                </div>
                <button class="btn btn-o btn-pagar">Pagar agora</button>
            </div>
        </div>
        
        <div class="pagamento-secao">
            <h3 class="secao-titulo">✅ Histórico de pagamentos</h3>

            <div class="pagamento-item pago">
                <div class="pagamento-info">
                   <div class="pagamento-rota">Joinville → Blumenau</div>
                   <div class="pagamento-meta">Motorista: João P. · Pago em 20/05/2025</div>
                </div>
                <div class="pagamento-direita">
                   <div class="pagamento-valor">R$ 220,00</div>
                  <span class="frete-status pago">Pago</span>
                </div>


            </div>

        </div>
    </div>
</main>

<script>
  // ── Troca de aba ──
  function trocarAba(aba) {
    // Esconde todas as abas
    document.querySelectorAll('.aba').forEach(function(el) {
      el.classList.add('escondido');
    });
    // Remove ativo de todos os itens do menu
    document.querySelectorAll('.nav-item').forEach(function(el) {
      el.classList.remove('ativo');
    });

    // Mostra a aba escolhida
    document.getElementById('aba-' + aba).classList.remove('escondido');

    // Ativa o item do menu correspondente
    event.currentTarget.classList.add('ativo');

    // Atualiza título e subtítulo
    const titulos = {
      publicar:   ['PUBLICAR FRETE',      'Preencha os dados e motoristas da sua região verão em instantes.'],
      fretes:     ['FRETES PUBLICADOS',    'Acompanhe todos os fretes que sua empresa publicou.'],
      pagamentos: ['PAGAMENTOS',           'Gerencie pagamentos pendentes e veja seu histórico.']
    };
    document.getElementById('page-title').textContent = titulos[aba][0];
    document.getElementById('page-sub').textContent   = titulos[aba][1];
  }

  // ── Publicar frete ──
  document.getElementById('form-frete').addEventListener('submit', function(e) {
    e.preventDefault();

    const feedback = document.getElementById('feedback-frete');
    const origem   = document.getElementById('origem').value.trim();
    const destino  = document.getElementById('destino').value.trim();
    const peso     = document.getElementById('peso').value;
    const valor    = document.getElementById('valor').value;
    const data     = document.getElementById('data').value;

    if (!origem || !destino || !peso || !valor || !data) {
      feedback.className = 'form-feedback erro';
      feedback.textContent = '⚠️ Preencha todos os campos obrigatórios.';
      return;
    }

    feedback.className = 'form-feedback sucesso';
    feedback.textContent = '✅ Frete publicado! Motoristas já podem visualizá-lo.';
    this.reset();
  });
</script>