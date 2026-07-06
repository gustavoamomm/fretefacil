<?php

        $valorPendente = 0;
        $valorPago = 0;
        $fretesRealizados = count($pagamentos);

        foreach($pagamentos as $pagamento){
            
            if($pagamento["status"] == "Pendente"){
                $valorPendente += $pagamento["valor"];
            }
            if($pagamento["status"] == "Pago"){
                $valorPago += $pagamento["valor"];
            }

                
        }
        
        ?>

        <div class="pagamento-resumo">

            <div class="resumo-card">

                <div class="resumo-icon">⏳</div>

                <div class="resumo-valor" style="color: var(--yellow);">
                    R$ <?= number_format($valorPendente,2,",",".") ?>
                </div>

                <div class="resumo-label">Pendente de pagamento</div>

            </div>

            <div class="resumo-card">

                <div class="resumo-icon">✅</div>

                <div class="resumo-valor" style="color: var(--green);">
                    R$ <?= number_format($totalPagoMes,2,",",".")?>
                </div>

                <div class="resumo-label">
                    Total pago este mês
                </div>

            </div>

            <div class="resumo-card">

                <div class="resumo-icon">
                    🚚
                </div>

                <div class="resumo-valor">
                    <?= $fretesRealizados ?>
                </div>

                <div class="resumo-label">
                    Fretes realizados
                </div>

            </div>

        </div>

        <div class="pagamento-secao">

            <h3 class="secao-titulo">

                Pendentes

                <span class="secao-hint">
                    (pague em até 48h)
                </span>

            </h3>

            <?php foreach($pagamentos as $pagamento): ?>
                <?php if($pagamento["status"] == "Pendente"): ?>

                    <div class="pagamento-item pendente">

                        <div class="pagamento-info">

                            <div class="pagamento-rota">
                                <?= htmlspecialchars($pagamento["origem"])?> →
                                <?= htmlspecialchars($pagamento["destino"])?> 
                        
                            </div>

                            <div class="pagamento-meta">
                                Motorista: <?= htmlspecialchars($pagamento["motorista"])?>· Entregue em <?= date("d/m/Y H:i", strtotime($pagamento["data"]))?>
                            </div>

                        </div>

                         <div class="pagamento-direita">

                            <div class="pagamento-valor">
                                R$ <?= number_format($pagamento["valor"], 2, ",", ".") ?>
                            </div>

                            <div class="pagamento-prazo">
                                ⚠️ Vence em 24h
                            </div>

                        </div>

                       
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    


        <div class="pagamento-secao">

            <h3 class="secao-titulo">✅ Histórico de pagamentos</h3>

            <?php foreach($pagamentos as $pagamento): ?>
                <?php if($pagamento["status"] == "Pago"): ?>

                    <div class="pagamento-item pago">

                        <div class="pagamento-info">

                            <div class="pagamento-rota">
                                <?= htmlspecialchars($pagamento["origem"]) ?> →
                                <?= htmlspecialchars($pagamento["destino"]) ?>
                            </div>

                            <div class="pagamento-meta">
                                Motorista: <?= htmlspecialchars($pagamento["motorista"])?> · 
                                Pago em <?= date("d/m/Y H:i", strtotime($pagamento["data"]))?>
                            </div>

                        </div>

                        <div class="pagamento-direita">

                            <div class="pagamento-valor">
                                R$ <?= number_format($pagamento["valor"], 2, ",", ".") ?>
                            </div>

                            <span class="frete-status pago">
                                Pago
                            </span>

                        </div>

                    </div>
                <?php endif;?>
            <?php endforeach;?>

        </div>