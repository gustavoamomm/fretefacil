<?php
session_start();

if (
    !isset($_SESSION['tipo']) ||
    $_SESSION['tipo'] !== 'motorista'
) {
    header("Location: login.php");
    exit;
}
require_once __DIR__ . "/../models/Frete.php";
require_once __DIR__ . "/../models/FreteDAO.php";

$dao = new FreteDAO();
$fretesDisponiveis = $dao->listarDisponiveis();
$fretesAceitos = $dao->listarAceitosMotorista($_SESSION["id"]);
?>
<?php 
$bodyClass = 'dashboard';
include __DIR__ . "/../components/header.php";

?>

<head>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>

<aside class="sidebar">
    <a href="index.php" class="sidebar-logo">
        🚚 Frete<em>Fácil</em>
    </a>
    <div class="sidebar-user">
        <div class="user-avatar">🚐</div>
        <div>
            <div class="user-name"><?= $_SESSION['nome'] ?></div>
            <div class="user-role">Motorista · Sprinter</div>
        </div>
    </div>
    <div class="sidebar-divider"></div>
    
    <nav class="sidebar-nav">
        <button class="nav-item ativo" onclick="trocarAbaMotorista('disponiveis', this)">
            <span class="nav-icon">🔍</span>
            Fretes Disponíveis
            <span class="nav-badge" style="background:var(--blue);"><?=count($fretesDisponiveis)?></span>
        </button>
        <button class="nav-item" onclick="trocarAbaMotorista('aceitos', this)">
            <span class="nav-icon">📦</span>
            Meus Fretes Aceitos
        </button>
        <button class="nav-item" onclick="trocarAbaMotorista('carteira', this)">
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

    <?php if(isset($_SESSION['feedback'])): $feedback = $_SESSION['feedback']; ?>
        <div class="form-feedback <?= $feedback['tipo']; ?>" style="margin-bottom:20px;">
            <?= $feedback['mensagem']?>
        </div>
        <?php unset($_SESSION['feedback']); ?>
    <?php endif; ?>


    <div class="aba" id="aba-disponiveis">
        <div class="filtro-bar">
            <input type="text" id="filtro-cidade" placeholder="🔍 Filtrar por cidade de origem..." oninput="filtrarFretes()"> 
        </div>

        <div class="fretes-grid" id="lista-disponiveis">

            <?php if(empty($fretesDisponiveis)): ?>
                <p>Nenhum frete disponível no momento.</p>
            <?php else: ?>
                <?php foreach($fretesDisponiveis as $frete): ?>
                    <div class="frete-card disponivel" data-origem="<?= strtolower(htmlspecialchars($frete['origem'])) ?>">
                        <div class="frete-card-header">
                            <div>
                                <div class="frete-rota">
                                    <?= htmlspecialchars($frete['origem'])?> → <?= htmlspecialchars($frete['destino'])?>
                                </div>
                                <div class="frete-meta">
                                    <?= htmlspecialchars($frete['peso']) ?> kg ·
                                    <?= date('d/m', strtotime($frete['data_coleta'])) ?>,
                                    <?= substr($frete['hora_coleta'], 0, 5) ?>h ·
                                    <?= htmlspecialchars($frete['carro_necessario']) ?>
                                </div>
                            </div>
                            <span class="frete-status aberto">Disponível</span>
                        </div>
                        <div class="frete-card-footer">
                            <div class="frete-valor">R$ <?= number_format($frete['valor'], 2, ',', '.') ?></div>
                            <button class="btn btn-b btn-ver" 
                            onclick="abrirModal(
                                <?= $frete['id_frete']?>, 
                                '<?= addslashes($frete['origem'])?>',
                                '<?= addslashes($frete['uf_origem'])?>',
                                '<?= addslashes($frete['destino'])?>',
                                '<?= addslashes($frete['uf_destino'])?>',
                                'R$ <?= number_format($frete['valor'], 2, ',', '.') ?>',
                                '<?= addslashes($frete['peso'] . ' kg · ' . date('d/m', strtotime($frete['data_coleta'])) . ' ' . substr($frete['hora_coleta'], 0, 5) . 'h') ?>',
                                '<?= addslashes($frete['descricao']) ?>'
                                )">
                                Ver detalhes →
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="aba escondido" id="aba-aceitos">
        <div class="fretes-grid">

            <?php if(empty($fretesAceitos)): ?>
                <p>Você ainda não aceitou nenhum frete.</p>
            <?php else: ?>

                <?php foreach($fretesAceitos as $frete): ?>

                    <div class="frete-card">

                        <div class="frete-card-header">
                            <div>
                                <div class="frete-rota">
                                    <?= htmlspecialchars($frete["origem"])?> 
                                    →
                                    <?= htmlspecialchars($frete["destino"])?>
                                </div>

                                <div class="frete-meta">
                                    <?= $frete["peso"]?> kg ·
                                    <?= date("d/m", strtotime($frete["data_coleta"]))?> ·
                                    <?= substr($frete["hora_coleta"],0,5) ?>h
                                </div>
                            </div>
                            <span class="frete-status">
                                <?= $frete["status"]?>
                            </span>
                        </div>
                    <div class="frete-dist">
                        <?php if($frete["status"] == "Pago"): ?>
                            ✅ Pagamento recebido
                        <?php else: ?>
                            💰 Pagamento pendente
                        <?php endif; ?>
                    </div>

                    <div class="frete-card-footer">
                        <div class="frete-valor">
                            R$ <?= number_format($frete["valor"],2,",",".") ?>
                        </div>

                        <button class="btn btn-b btn-ver" 
                        onclick="abrirModal(
                             <?= $frete['id_frete']?>,
                            '<?= addslashes($frete['origem'])?>',
                            '<?= addslashes($frete['uf_origem'])?>',
                            '<?= addslashes($frete['destino'])?>',
                            '<?= addslashes($frete['uf_destino'])?>',
                            'R$ <?= number_format($frete['valor'],2,',','.') ?>',
                            '<?= addslashes($frete['peso'].' kg · '.date('d/m',strtotime($frete['data_coleta'])).' '.substr($frete['hora_coleta'],0,5).'h')?>',
                            '<?= addslashes($frete['descricao'])?>
                        )">

                        Ver detalhes →
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
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

        <form method="POST" action="../controllers/FreteController.php">
            <input type="hidden" name="acao" value="aceitar">
            <input type="hidden" name="id_frete" id="modal-id-frete">


            <div class="modal-btns">
                <button type="button" class="btn btn-gh" onclick="fecharModal()">Cancelar</button>
                <button type="submit" class="btn btn-b">✓ Aceitar frete</button>
            </div>
        </form>
    
    </div>
</div>

<script src="../static/script.js"></script>


</body>
</html>