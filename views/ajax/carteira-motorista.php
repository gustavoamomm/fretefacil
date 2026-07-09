<div class="carteira-saldo">
            <div class="saldo-label">Saldo disponível</div>
            <div class="saldo-valor">
                R$ <?= number_format($carteira["saldo"], 2, ",", ".") ?>
            </div>
            <br>
            <button class="btn btn-b" id="btnAbrirSaque">
                Solicitar saque
            </button>
        </div>

        <br><br>

        <div class="pagamento-resumo">
            <div class="resumo-card">
                <div class="resumo-icon">💰</div>
                <div class="resumo-valor">
                    R$ <?= number_format($totalRecebido, 2, ",", ".") ?>
                </div>
                <div class="resumo-label">Total recebido</div>
            </div>

            <div class="resumo-card">
                <div class="resumo-icon">⏳</div>
                <div class="resumo-valor">
                    R$ <?= number_format($aguardando, 2, ",", ".") ?>
                </div>
                <div class="resumo-label">Aguardando pagamento</div>
            </div>

            <div class="resumo-card">
                <div class="resumo-icon">🚚</div>
                <div class="resumo-valor">
                    <?= $concluidos ?>
                </div>
                <div class="resumo-label">Fretes concluídos</div>
            </div>
        </div>

        <br>

        <div class="tabs">
            <button class="tab ativo" id="tabExtrato">Extrato</button>
            <button class="tab" id="tabSaques">Histórico de Saques</button>
        </div>

        <div id="conteudoExtrato">
            <?php if (empty($extrato)): ?>
                <p class="page-sub">Nenhuma movimentação encontrada.</p>
            <?php else: ?>
                <?php foreach ($extrato as $mov): ?>
                    <div class="pagamento-item">
                        <div class="pagamento-info">
                            <div class="pagamento-rota">
                                <?= $mov["descricao"] ?>
                            </div>
                            <div class="pagamento-meta">
                                <?= date("d/m/Y H:i", strtotime($mov["data"])) ?>
                            </div>
                        </div>
                        <div class="pagamento-direita">
                            <div class="pagamento-valor">
                                <?php if ($mov["tipo"] == "Entrada"): ?>
                                    + R$ <?= number_format($mov["valor"], 2, ",", ".") ?>
                                <?php else: ?>
                                    - R$ <?= number_format($mov["valor"], 2, ",", ".") ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div id="conteudoSaques" class="escondido">
            <?php if (empty($saques)): ?>
                <p class="page-sub">Nenhum saque solicitado.</p>
            <?php else: ?>
                <?php foreach ($saques as $saque): ?>
                    <div class="pagamento-item">
                        <div class="pagamento-info">
                            <div class="pagamento-rota">
                                <?= htmlspecialchars($saque["descricao"]) ?>
                            </div>
                            <div class="pagamento-meta">
                                <?= date("d/m/Y H:i", strtotime($saque["data"])) ?>
                            </div>
                        </div>
                        <div class="pagamento-direita">
                            <div class="pagamento-valor">
                                - R$ <?= number_format($saque["valor"], 2, ",", ".") ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>