<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../models/Frete.php";
require_once __DIR__ . "/../../models/FreteDAO.php";

$dao = new FreteDAO();
$fretes = $dao->listarPorEmpresa($_SESSION["id"]);
?>

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
                                data-id-frete="<?= $frete['id_frete'] ?>"
                                data-origem="<?= htmlspecialchars($frete['origem']) ?>"
                                data-uf-origem="<?= htmlspecialchars($frete['uf_origem']) ?>"
                                data-destino="<?= htmlspecialchars($frete['destino']) ?>"
                                data-uf-destino="<?= htmlspecialchars($frete['uf_destino']) ?>"
                                data-valor="R$ <?= number_format($frete['valor'], 2, ',', '.') ?>"
                                data-meta="<?= htmlspecialchars($frete['peso'] . ' kg · ' . date('d/m', strtotime($frete['data_coleta'])) . ' ' . substr($frete['hora_coleta'], 0, 5) . 'h') ?>"
                                data-desc="<?= htmlspecialchars($frete['descricao']) ?>">
                                Ver detalhes →
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>