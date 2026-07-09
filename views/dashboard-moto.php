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
require_once __DIR__ . "/../models/CarteiraDAO.php";

$daoCarteira = new CarteiraDAO();
$carteira = $daoCarteira->buscarPorMotorista($_SESSION["id"]);

$totalRecebido = $daoCarteira->buscarTotalRecebido($carteira["id"]);
$aguardando = $daoCarteira->buscarAguardandoPagamento($_SESSION["id"]);
$concluidos = $daoCarteira->buscarFretesConcluidos($_SESSION["id"]);
$extrato = $daoCarteira->buscarExtrato($carteira["id"]);
$saques = $daoCarteira->buscarSaques($carteira["id"]);

$dao = new FreteDAO();
$fretesDisponiveis = $dao->listarDisponiveis();
$fretesAceitos = $dao->listarAceitosMotorista($_SESSION["id"]);

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
            <span class="nav-badge" style="background:var(--blue);"><?= count($fretesDisponiveis) ?></span>
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

    <button class="nav-item" id="btn-tema">
        <span class="nav-icon">🌙</span>
        <span id="texto-tema">Alterar Tema</span>
    </button>

    <div class="sidebar-bottom">
        <div class="sidebar-divider"></div>
        <a href="../logout.php" class="nav-item sair">
            <span class="nav-icon">🚪</span>
            Sair
        </a>
    </div>
</aside>

<main class="main-content">
    <?php if (!empty($_SESSION['erro'])): ?>
        <div class="alert alert-erro" style="background:#fdecea;color:#611a15;border:1px solid #f5c6cb;padding:12px 16px;border-radius:8px;margin-bottom:16px;">
            <?= htmlspecialchars($_SESSION['erro']) ?>
        </div>
        <?php unset($_SESSION['erro']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['sucesso'])): ?>
        <div class="alert alert-sucesso" style="background:#e6f4ea;color:#1e4620;border:1px solid #b7dfc0;padding:12px 16px;border-radius:8px;margin-bottom:16px;">
            <?= htmlspecialchars($_SESSION['sucesso']) ?>
        </div>
        <?php unset($_SESSION['sucesso']); ?>
    <?php endif; ?>
    <div class="page-header">
        <div>
            <h1 class="page-title" id="page-title">FRETES DISPONÍVEIS</h1>
            <p class="page-sub" id="page-sub">
                Fretes próximos de você. Clique em um para ver os detalhes e aceitar.
            </p>
        </div>
    </div>

    <?php if (isset($_SESSION['feedback'])): $feedback = $_SESSION['feedback']; ?>
        <div class="form-feedback <?= $feedback['tipo']; ?>" style="margin-bottom:20px;">
            <?= $feedback['mensagem'] ?>
        </div>
        <?php unset($_SESSION['feedback']); ?>
    <?php endif; ?>

    <div class="aba" id="aba-disponiveis">
        <div class="filtro-bar">
            <input type="text" id="filtro-cidade" placeholder="🔍 Filtrar por cidade de origem..." oninput="filtrarFretes()"> 
        </div>

        <div class="fretes-grid" id="lista-disponiveis">
            <?php include "ajax/fretes-disponiveis.php"; ?>
        </div>
    </div>

    <script>
        document.querySelectorAll('.btn-ver').forEach(function(botao){
            botao.addEventListener('click', function(){
                abrirModal(
                    botao.dataset.idFrete,
                    botao.dataset.origem,
                    botao.dataset.ufOrigem,
                    botao.dataset.destino,
                    botao.dataset.ufDestino,
                    botao.dataset.valor,
                    botao.dataset.meta,
                    botao.dataset.desc
                );
            });
        });
    </script>

    <div class="aba escondido" id="aba-aceitos">
        <div class="fretes-grid">
            <?php if (empty($fretesAceitos)): ?>
                <p>Você ainda não aceitou nenhum frete.</p>
            <?php else: ?>
                <?php foreach ($fretesAceitos as $frete): ?>
                    <div class="frete-card">
                        <div class="frete-card-header">
                            <div>
                                <div class="frete-rota">
                                    <?= htmlspecialchars($frete["origem"]) ?> → <?= htmlspecialchars($frete["destino"]) ?>
                                </div>
                                <div class="frete-meta">
                                    <?= $frete["peso"] ?> kg ·
                                    <?= date("d/m", strtotime($frete["data_coleta"])) ?> ·
                                    <?= substr($frete["hora_coleta"], 0, 5) ?>h
                                </div>
                            </div>
                            <span class="frete-status">
                                <?= $frete["status"] ?>
                            </span>
                        </div>

                        <div class="frete-card-footer">
                            <div class="frete-valor">
                                R$ <?= number_format($frete["valor"], 2, ",", ".") ?>
                            </div>

                            <div class="acoes-frete">
                                <?php if ($frete["status"] == "Aceito"): ?>
                                    <form method="POST" action="../controllers/FreteController.php">
                                        <input type="hidden" name="acao" value="iniciar">
                                        <input type="hidden" name="id_frete" value="<?= $frete["id_frete"] ?>">
                                        <button type="submit" class="btn btn-verde">▶ Iniciar viagem</button>
                                    </form>
                                <?php elseif ($frete["status"] == "Em andamento"): ?>
                                    <form method="POST" action="../controllers/FreteController.php">
                                        <input type="hidden" name="acao" value="concluir">
                                        <input type="hidden" name="id_frete" value="<?= $frete["id_frete"] ?>">
                                        <button type="submit" class="btn btn-laranja">✔ Solicitar conclusão</button>
                                    </form>
                                <?php elseif ($frete["status"] == "Aguardando confirmacao"): ?>
                                    <span class="status-info">⏳ Aguardando confirmação da empresa</span>
                                <?php elseif ($frete["status"] == "Concluido"): ?>
                                    <span class="status-info">Entrega concluída</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="aba escondido" id="aba-carteira">
        <?php include "ajax/carteira-motorista.php"; ?>
    </div>
</main>

<div class="modal-overlay escondido" id="modal-saque">
    <div class="modal">
        <div class="modal-header">
            <div>
                <div class="modal-rota">Solicitar saque</div>
                <div class="modal-meta">Saldo disponível: R$ <?= number_format($carteira["saldo"], 2, ",", ".") ?></div>
            </div>
            <button class="modal-fechar" type="button" onclick="fecharModalSaque()">✕</button>
        </div>

        <form method="POST" action="../controllers/SaqueController.php">
            <div class="form-group">
                <label for="valor-saque">Valor do saque</label>
                <input type="number" id="valor-saque" name="valor" min="0.01" step="0.01" max="<?= htmlspecialchars($carteira["saldo"]) ?>" required>
            </div>

            <div class="form-group">
                <label for="chave-pix-saque">Chave PIX</label>
                <input type="text" id="chave-pix-saque" name="chave_pix" placeholder="CPF, e-mail, telefone ou chave aleatória" required>
            </div>

            <div class="modal-btns">
                <button type="button" class="btn btn-gh" onclick="fecharModalSaque()">Cancelar</button>
                <button type="submit" class="btn btn-b">Confirmar saque</button>
            </div>
        </form>
    </div>
</div>

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

<script>
    const btnTema = document.getElementById("btn-tema");

    if (localStorage.getItem("tema_motorista") === "light") {
        document.body.classList.add("light");
    }

    btnTema.addEventListener("click", () => {
        document.body.classList.toggle("light");

        if (document.body.classList.contains("light")) {
            localStorage.setItem("tema_motorista", "light");
        } else {
            localStorage.setItem("tema_motorista", "dark");
        }
    });
</script>

<script>
    const btnAbrirSaque = document.getElementById("btnAbrirSaque");
    const modalSaque = document.getElementById("modal-saque");
    const tabExtrato = document.getElementById("tabExtrato");
    const tabSaques = document.getElementById("tabSaques");
    const conteudoExtrato = document.getElementById("conteudoExtrato");
    const conteudoSaques = document.getElementById("conteudoSaques");

    function abrirModalSaque() {
        modalSaque.classList.remove("escondido");
    }

    function fecharModalSaque() {
        modalSaque.classList.add("escondido");
    }

    if (btnAbrirSaque) {
        btnAbrirSaque.addEventListener("click", abrirModalSaque);
    }

    if (tabExtrato && tabSaques) {
        tabExtrato.addEventListener("click", () => {
            tabExtrato.classList.add("ativo");
            tabSaques.classList.remove("ativo");
            conteudoExtrato.classList.remove("escondido");
            conteudoSaques.classList.add("escondido");
        });

        tabSaques.addEventListener("click", () => {
            tabSaques.classList.add("ativo");
            tabExtrato.classList.remove("ativo");
            conteudoSaques.classList.remove("escondido");
            conteudoExtrato.classList.add("escondido");
        });
    }
</script>

<script src="../static/script.js"></script>
</body>
</html>