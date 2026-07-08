<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../models/Frete.php";
require_once __DIR__ . "/../../models/FreteDAO.php";

$dao = new FreteDAO();
$fretes = $dao->listarPorEmpresa($_SESSION["id"]);
?>


<?php if (count($fretes) > 0): ?>
    <?php foreach($fretes as $frete ): ?>
        <div class="frete-card">
            <div class="frete-card-header">
                <div>
                    <div class="frete-rota">
                        <?=  htmlspecialchars($frete['origem'])?>
                        →
                        <?=  htmlspecialchars($frete['destino'])?>
                    </div>

                    <div class="frete-meta">
                        <?= $frete['peso']?> kg ·
                        <?= date('d/m/Y', strtotime($frete['data_coleta'])) ?>
                    </div>
                    <?php if($frete["status"] == "Disponivel"): ?>
                        <div class="frete-aguardando">
                            ⏳ Aguardando um motorista
                        </div>
                    <?php endif; ?>
                </div>
                <span class="frete-status">
                    <?= htmlspecialchars($frete['status'])?>
                </span>
            </div>
            <div class="frete-card-footer">
                <div class="frete-valor">
                    R$ <?= number_format($frete['valor'],2,',','.') ?>
                </div>

                <div class="acoes-frete">
                    <?php if($frete["status"] == "Disponivel"): ?>
                        <button
                            type="button"
                            class="btn-icon btn-icon-editar btn-editar-frete"
                            title="Editar frete"
                            aria-label="Editar frete"
                            data-id-frete="<?= $frete['id_frete'] ?>"
                            data-origem="<?= htmlspecialchars($frete['origem']) ?>"
                            data-uf-origem="<?= htmlspecialchars($frete['uf_origem']) ?>"
                            data-destino="<?= htmlspecialchars($frete['destino']) ?>"
                            data-uf-destino="<?= htmlspecialchars($frete['uf_destino']) ?>"
                            data-peso="<?= htmlspecialchars($frete['peso']) ?>"
                            data-valor="<?= htmlspecialchars($frete['valor']) ?>"
                            data-data-coleta="<?= htmlspecialchars($frete['data_coleta']) ?>"
                            data-hora-coleta="<?= htmlspecialchars(substr($frete['hora_coleta'], 0, 5)) ?>"
                            data-carro="<?= htmlspecialchars($frete['carro_necessario']) ?>"
                            data-descricao="<?= htmlspecialchars($frete['descricao']) ?>">
                            ✏️
                        </button>

                        <form action="../controllers/FreteController.php" method="POST" class="form-excluir-frete">
                            <input type="hidden" name="acao" value="excluir">
                            <input type="hidden" name="id_frete" value="<?= $frete["id_frete"] ?>">

                            <button type="submit" class="btn-icon btn-icon-excluir" title="Excluir frete" aria-label="Excluir frete">
                                🗑️
                            </button>
                        </form>

                    <?php elseif($frete["status"] == "Aceito"): ?>
                        <span class="status-info">
                            🚚 Motorista aceitou o frete
                        </span>
                    <?php elseif($frete["status"] == "Em andamento"): ?>
                        <span class="status-info">
                            🚛 Entrega em andamento
                        </span>
                    <?php elseif($frete["status"] == "Aguardando confirmacao"): ?>
                        <form action="../controllers/FreteController.php" method="POST">
                            <input type="hidden" name="acao" value="confirmar">
                            <input type="hidden" name="id_frete" value="<?= $frete["id_frete"] ?>">

                            <button type="submit" class="btn btn-verde">
                                ✓ Confirmar entrega
                            </button>
                        </form>
                    <?php elseif ($frete["status"] == "Concluido"): ?>
                        <span class="status-info">
                            ✅ Entrega concluída
                        </span>
                    <?php elseif($frete["status"] == "Pago"): ?>
                        <span class="status-info">
                            💰 Frete pago
                        </span>
                    <?php endif;?>

                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Nenhum frete publicado</p>
<?php endif; ?>
