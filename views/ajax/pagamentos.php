<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../../models/FreteDAO.php";
require_once __DIR__ . "/../../models/PagamentoDAO.php";

$pagamentoDAO = new PagamentoDAO();
$pagamentos = $pagamentoDAO->listarPorEmpresa($_SESSION["id"]);
$totalPagoMes = $pagamentoDAO->totalPagoMes($_SESSION["id"]);



$valorPendente = 0;
$valorPago = 0;
$fretesRealizados = 0;

foreach($pagamentos as $pagamento){
            
    if($pagamento["status"] == "Pendente"){
        $valorPendente += $pagamento["valor"];
    }
    if($pagamento["status"] == "Pago"){
        $valorPago += $pagamento["valor"];
        $fretesRealizados++;
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

                            <button class="btn btn-o btn-pagar" data-id="<?= $pagamento['id'] ?>" data-valor="<?= $pagamento['valor'] ?>" data-rota="<?= htmlspecialchars($pagamento['origem'] . ' → ' . $pagamento['destino']) ?>">Pagar agora</button>

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

<div id="modalPagamento" class="modal-overlay escondido">
    <div class="modal">

        <div class="modal-header">
            <div>
                <div class="modal-rota" id="rotaPagamento"></div>
                <div class="modal-meta">Pagamento do frete</div>
            </div>
            <button type="button" class="modal-fechar" data-fechar-modal>✕</button>
        </div>

        <div class="modal-valor-wrap">
            <span class="modal-label">Valor a pagar</span>
            <span class="modal-valor" id="valorPagamento"></span>
        </div>

        <input type="hidden" id="id_pagamento">

        <div class="metodo-tabs">
            <label class="metodo-tab">
                <input type="radio" name="metodo" value="PIX" checked>
                PIX
            </label>
            <label class="metodo-tab">
                <input type="radio" name="metodo" value="Cartao">
                Cartão
            </label>
            <label class="metodo-tab">
                <input type="radio" name="metodo" value="Boleto">
                Boleto
            </label>
        </div>

        <!-- PIX -->
        <div class="metodo-painel" id="painel-pix">

            <div class="pix-qr-wrap">
                <img id="pixQrCode" src="" alt="QR Code PIX" class="pix-qr">
            </div>

            <div class="pix-chave-wrap">
                <span class="modal-label">Chave PIX (copia e cola)</span>
                <div class="pix-chave">
                    <code id="pixChave">fretefacil@pix.com</code>
                    <button type="button" class="btn" id="copiarChavePix">Copiar</button>
                </div>
            </div>

            <div class="pix-timer">
                Tempo restante: <strong id="pixTimer">10:00</strong>
            </div>

            <div class="pagamento-status" id="pixStatus"></div>

            <button type="button" class="btn btn-o btn-block" id="btnJaPaguei">Já realizei o pagamento</button>

        </div>

        <!-- CARTÃO -->
        <div class="metodo-painel escondido" id="painel-cartao">

            <div class="form-group">
                <label for="cartaoNumero">Número do cartão</label>
                <input type="text" id="cartaoNumero" placeholder="0000 0000 0000 0000" maxlength="19" inputmode="numeric">
                <span class="campo-erro" id="erroCartaoNumero"></span>
            </div>

            <div class="form-group">
                <label for="cartaoNome">Nome impresso</label>
                <input type="text" id="cartaoNome" placeholder="Nome como está no cartão">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="cartaoValidade">Validade</label>
                    <input type="text" id="cartaoValidade" placeholder="MM/AA" maxlength="5" inputmode="numeric">
                    <span class="campo-erro" id="erroCartaoValidade"></span>
                </div>
                <div class="form-group">
                    <label for="cartaoCvv">CVV</label>
                    <input type="text" id="cartaoCvv" placeholder="000" maxlength="3" inputmode="numeric">
                    <span class="campo-erro" id="erroCartaoCvv"></span>
                </div>
            </div>

            <div class="form-group">
                <label>Parcelas</label>
                <div class="parcelas-lista" id="parcelasLista"></div>
            </div>

            <div class="pagamento-status" id="cartaoStatus"></div>

            <button type="button" class="btn btn-o btn-block" id="btnPagarCartao">Pagar</button>

        </div>

        <!-- BOLETO -->
        <div class="metodo-painel escondido" id="painel-boleto">

            <div class="boleto-info">
                <div>
                    <span class="modal-label">Vencimento</span>
                    <div class="boleto-vencimento" id="boletoVencimento"></div>
                </div>
            </div>

            <div class="boleto-codigo-wrap">
                <span class="modal-label">Código de barras</span>
                <code class="boleto-codigo" id="boletoCodigo"></code>
            </div>

            <div class="pagamento-status" id="boletoStatus"></div>

            <div class="modal-btns" id="boletoAcoesGerar">
                <button type="button" class="btn btn-o btn-block" id="btnGerarBoleto">Gerar boleto</button>
            </div>

            <div class="modal-btns escondido" id="boletoAcoesPosGeracao">
                <button type="button" class="btn" id="btnBaixarBoleto">Baixar PDF</button>
                <button type="button" class="btn btn-o" id="btnSimularPagamentoBoleto">Simular pagamento</button>
            </div>

        </div>

        <div class="modal-btns">
            <button type="button" class="btn" data-fechar-modal>Cancelar</button>
        </div>

    </div>
</div>