<?php
session_start();

if (
    !isset($_SESSION['tipo']) ||
    $_SESSION['tipo'] !== 'motorista'
) {
    header("Location: login.php");
    exit;
}
?>
<?php 
$bodyClass = 'dashboard';
include __DIR__ . "/../components/header.php";
?>

<head>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>

<aside class="sidebar motorista">
    <a href="index.php" class="sidebar-logo">
        🚚 Frete<em>Fácil</em>
    </a>
    <div class="sidebar-user">
        <div class="user-avatar">🚐</div>
        <div>
            <div class="user-name">João da Silva</div>
            <div class="user-role">Motorista · Sprinter</div>
        </div>
    </div>
    <div class="sidebar-divider"></div>
    
    <nav class="sidebar-nav">
        <button class="nav-item ativo" onclick="trocarAba('disponiveis', this)">
            <span class="nav-icon">🔍</span>
            Fretes Disponíveis
            <span class="nav-badge" style="background:var(--blue);">8</span>
        </button>
        <button class="nav-item" onclick="trocarAba('aceitos', this)">
            <span class="nav-icon">📦</span>
            Meus Fretes Aceitos
        </button>
        <button class="nav-item" onclick="trocarAba('carteira', this)">
            <span class="nav-icon">👛</span>
            Carteira
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
    <div  class="page-header">
        <div>
            <h1 class="page-title" id="page-title">FRETES DISPONÍVEIS</h1>
            <p class="page-sub" id="page-sub">
                Fretes próximos de você. Clique em um para ver os detalhes e aceitar.
            </p>
        </div>
    </div>

    <div class="aba" id="aba-disponiveis">
        <div class="filtro-bar">
            <input type="text" id="filtro-cidade" placeholder="🔍 Filtrar por cidade de origem..." oninput="filtrarFretes()"> 
        </div>

        <div class="fretes-grid" id="lista-disponiveis">
            
            
            <div class="frete-card disponivel" data-origem="joinville">
                <div class="frete-card-header">
                    <div>
                        <div class="frete-rota">Joinville → Blumenau</div>
                        <div class="frete-meta">400 kg · Amanhã, 08h · Caixas frágeis</div>
                    </div>
                    <span class="frete-status aberto">Disponível</span>
                </div>
                <div class="frete-dist">📍 3 km de você</div>
                <div class="frete-card-footer">
                    <div class="frete-valor">R$ 280,00</div>
                    <button class="btn btn-b btn-ver" onclick="abrirModal('Joinville', 'Blumenau', 'R$ 280,00', '400 kg · Amanhã 08h', 'Caixas frágeis, necessário cuidado no manuseio.')">Ver detalhes →

                    </button>
                </div>
            </div>

            <div class="frete-card disponivel" data-origem="joinville">

                <div class="frete-card-header">
                    <div>
                        <div class="frete-rota">Joinville → Itajaí</div>
                        <div class="frete-meta">200 kg · Hoje, 14h · Eletrônicos</div>
                    </div>
                    <span class="frete-status aberto">Disponível</span>
                </div>
                <div class="frete-dist">📍 7 km de você</div>
                <div class="frete-card-footer">
                    <div class="frete-valor">R$ 220,00</div>
                    <button class="btn btn-b btn-ver" onclick="abrirModal('Joinville', 'Itajaí', 'R$ 220,00', '200 kg · Hoje 14h', 'Eletrônicos embalados. Entrega no centro.')">Ver detalhes →</button>
                </div>
            </div>

            <div class="frete-card disponivel" data-origem="jaraguá">
                <div class="frete-card-header">
                    <div>
                        <div class="frete-rota">Jaraguá do Sul → Florianópolis</div>
                        <div class="frete-meta">300 kg · Amanhã, 10h · Móveis</div>
                    </div>
                    <span class="frete-status aberto">Disponível</span>
                </div>
                <div class="frete-dist">📍 22 km de você</div>
                <div class="frete-card-footer">
                    <div class="frete-valor">R$ 380,00</div>
                    <button class="btn btn-b btn-ver" onclick="abrirModal('Jaraguá do Sul', 'Florianópolis', 'R$ 380,00', '300 kg · Amanhã 10h', 'Móveis desmontados. Ajuda na carga incluída.')">Ver detalhes →</button>
                </div>
        </div>
    </div>

    
    <div class="aba escondido" id="aba-aceitos">
        
        <div class="fretes-grid">
            <div class="frete-card">
                <div class="frete-card-header">
                    <div>
                        <div class="frete-rota">Joinville → Blumenau</div>
                        <div class="frete-meta">400 kg · Hoje, 08h</div>
                    </div>
                    <span class="frete-status aceito">Em andamento</span>
                </div>
                <div class="frete-dist">💰 Pagamento pendente (até 48h)</div>
                <div class="frete-card-footer">
                    <div class="frete-valor">R$ 280,00</div>
                    <button class="btn btn-b btn-ver" onclick="abrirModal('Joinville', 'Blumenau', 'R$ 280,00', '400 kg · Hoje 08h', 'Caixas frágeis.')">Ver rota →</button>
                </div>
            </div>
            
            <div class="frete-card">
                <div class="frete-card-header">
                    <div>
                        <div class="frete-rota">Joinville → Itajaí</div>
                        <div class="frete-meta">200 kg · Ontem, 14h</div>
                    </div>
                    <span class="frete-status pago">Pago ✓</span>
                </div>
                <div class="frete-dist" style="color:var(--green)">✅ Pagamento recebido</div>
                <div class="frete-card-footer">
                    <div class="frete-valor">R$ 220,00</div>
                </div>
            </div>
        </div>
    </div>

    <div class="aba escondido" id="aba-carteira">
        <div class="carteira-saldo">
            <div class="saldo-label">💰 Saldo disponível</div>
            <div class="saldo-valor">R$ 1.840,00</div>
            <button class="btn btn-b" style="margin-top:20px;">Solicitar saque →
                
            </button>
        </div>

        <div class="pagamento-resumo" style="margin-top:20px;">
            <div class="resumo-card">
                <div class="resumo-icon">✅</div>
                <div class="resumo-valor" style="color: var(--green);">R$ 1.840,00</div>
                <div class="resumo-label">Total recebido</div>
            </div>
            <div class="resumo-card">
                <div class="resumo-icon">⏳</div>
                <div class="resumo-valor" style="color: var(--yellow);">R$ 280,00</div>
                <div class="resumo-label">Aguardando pagamento</div>
            </div>
            <div class="resumo-card">
                <div class="resumo-icon">🚚</div>
                <div class="resumo-valor">12</div>
                <div class="resumo-label">Fretes realizados</div>
            </div>
        </div>

        <div class="pagamento-secao" style="margin-top:36px;">
            <h3 class="secao-titulo">📋 Histórico de pagamento</h3>

            <div class="pagamento-item pago">
                <div class="pagamento-info">
                    <div class="pagamento-rota">Joiville → Itajaí</div>
                    <div class="pagamento-meta">Empresa: Distribuidora Silva · Pago em 20/05/2025</div>
                </div>
                <div class="pagamento-direita">
                    <div class="pagamento-valor">R$ 280,00</div>
                    <span class="frete-status pago">Recebido</span>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal-overlay escondido" id="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <div>
                <div class="modal-rota" id="modal-rota">Joinville → Blumenau</div>
                <div class="modal-meta" id="modal-meta">400 kg · Amanhã 08h</div>
            </div>
            <button class="modal-fechar" onclick="fecharModal()">✕</button>
        </div>
        <div class="modal-desc" id="modal-desc">
            Descrição da carga aqui
        </div>

        <div class="modal-valor-wrap">
            <span class="modal-label">Valor do frete</span>
            <span class="modal-valor" id="modal-valor">R$ 280,00</span>
        </div>

        <div class="modal-mapa">
            <div id="mapa-div" style="width:100%; height:300px; border-radius:12px; overflow:hidden;"></div>
            <div id="modal-rota-info" style="display:none; font-size:13px; color:var(--muted); margin-top:10px; text-align:center;"></div>
        </div>

        <div class="modal-btns">
            <button class="btn btn-gh" onclick="fecharModal()">Cancelar</button>
            <button class="btn btn-b" onclick="aceitarFrete()">✓ Aceitar frete</button>
        </div>
    </div>
</div>

<script src="../static/script.js"></script>

</body>
</html>