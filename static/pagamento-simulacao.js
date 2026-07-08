// =========================================================
// SIMULAÇÃO DE PAGAMENTO (PIX / Cartão / Boleto)
// =========================================================
// Tudo aqui usa DELEGAÇÃO DE EVENTOS (listener no "document")
// em vez de listener direto nos botões. Isso é necessário
// porque a aba de pagamentos é recarregada via AJAX depois
// de cada pagamento confirmado — se o listener estivesse
// preso a um botão específico, ele deixaria de existir assim
// que o HTML fosse substituído.

let pagamentoAtual = { id: null, valor: 0, rota: "" };
let pixTimerInterval = null;

// ---------------------------------------------------------
// ABRIR / FECHAR MODAL
// ---------------------------------------------------------

function abrirModalPagamento(botao) {
    pagamentoAtual = {
        id: botao.dataset.id,
        valor: Number(botao.dataset.valor),
        rota: botao.dataset.rota
    };

    document.getElementById("rotaPagamento").textContent = pagamentoAtual.rota;
    document.getElementById("id_pagamento").value = pagamentoAtual.id;
    document.getElementById("valorPagamento").textContent =
        "R$ " + pagamentoAtual.valor.toLocaleString("pt-BR", { minimumFractionDigits: 2 });

    resetarPainelPix();
    resetarPainelCartao();
    resetarPainelBoleto();

    // sempre volta pro método PIX ao abrir
    document.querySelector('input[name="metodo"][value="PIX"]').checked = true;
    trocarPainelMetodo("PIX");

    document.getElementById("modalPagamento").classList.remove("escondido");
}

function fecharModalPagamento() {
    document.getElementById("modalPagamento").classList.add("escondido");
    if (pixTimerInterval) {
        clearInterval(pixTimerInterval);
        pixTimerInterval = null;
    }
}

function trocarPainelMetodo(metodo) {
    document.querySelectorAll(".metodo-painel").forEach(p => p.classList.add("escondido"));

    if (metodo === "PIX") {
        document.getElementById("painel-pix").classList.remove("escondido");
    } else if (metodo === "Cartao") {
        document.getElementById("painel-cartao").classList.remove("escondido");
    } else if (metodo === "Boleto") {
        document.getElementById("painel-boleto").classList.remove("escondido");
    }
}

// ---------------------------------------------------------
// CONFIRMAÇÃO REAL NO BACK-END
// ---------------------------------------------------------
// Depois que a "encenação" visual termina, avisamos de verdade
// o servidor pra marcar o pagamento como Pago no banco.

async function confirmarPagamentoBackend(idPagamento, metodo) {
    try {
        await fetch("../controllers/PagamentoController.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ id_pagamento: idPagamento, metodo: metodo })
        });
    } catch (erro) {
        console.error("Erro ao confirmar pagamento no servidor:", erro);
    }

    await atualizarAbaPagamentos();
}

async function atualizarAbaPagamentos() {
    try {
        const res = await fetch("ajax/pagamentos.php");
        const html = await res.text();
        document.getElementById("aba-pagamentos").innerHTML = html;
        atualizarBadgePendentes();
    } catch (erro) {
        console.error("Erro ao atualizar lista de pagamentos:", erro);
    }
}

function atualizarBadgePendentes() {
    const badge = document.getElementById("badge-pendentes");
    if (!badge) return;
    const pendentes = document.querySelectorAll("#aba-pagamentos .pagamento-item.pendente").length;
    badge.textContent = pendentes;
    badge.style.display = pendentes > 0 ? "" : "none";
}

// ---------------------------------------------------------
// PIX
// ---------------------------------------------------------

function resetarPainelPix() {
    const texto = "fretefacil-pagamento-" + pagamentoAtual.id;
    document.getElementById("pixQrCode").src =
        "https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=" + encodeURIComponent(texto);

    document.getElementById("pixStatus").innerHTML = "";
    document.getElementById("pixStatus").className = "pagamento-status";

    const botao = document.getElementById("btnJaPaguei");
    botao.disabled = false;
    botao.textContent = "Já realizei o pagamento";

    iniciarTimerPix();
}

function iniciarTimerPix() {
    if (pixTimerInterval) clearInterval(pixTimerInterval);

    let segundos = 10 * 60;
    const elTimer = document.getElementById("pixTimer");

    function render() {
        const m = String(Math.floor(segundos / 60)).padStart(2, "0");
        const s = String(segundos % 60).padStart(2, "0");
        elTimer.textContent = `${m}:${s}`;
    }

    render();

    pixTimerInterval = setInterval(() => {
        segundos--;
        render();
        if (segundos <= 0) {
            clearInterval(pixTimerInterval);
            elTimer.textContent = "Expirado";
        }
    }, 1000);
}

async function processarPagamentoPix() {
    const status = document.getElementById("pixStatus");
    const botao = document.getElementById("btnJaPaguei");

    botao.disabled = true;
    status.className = "pagamento-status";
    status.innerHTML = '<span class="spinner"></span> Verificando pagamento...';

    await esperar(2000);

    status.className = "pagamento-status sucesso";
    status.innerHTML = "✔ PIX recebido com sucesso!";

    await confirmarPagamentoBackend(pagamentoAtual.id, "PIX");

    await esperar(1200);
    fecharModalPagamento();
}

// ---------------------------------------------------------
// CARTÃO
// ---------------------------------------------------------

function resetarPainelCartao() {
    document.getElementById("cartaoNumero").value = "";
    document.getElementById("cartaoNome").value = "";
    document.getElementById("cartaoValidade").value = "";
    document.getElementById("cartaoCvv").value = "";
    document.getElementById("erroCartaoNumero").textContent = "";
    document.getElementById("erroCartaoValidade").textContent = "";
    document.getElementById("erroCartaoCvv").textContent = "";
    document.getElementById("cartaoStatus").innerHTML = "";
    document.getElementById("cartaoStatus").className = "pagamento-status";

    const botao = document.getElementById("btnPagarCartao");
    botao.disabled = false;
    botao.textContent = "Pagar";

    renderParcelas();
}

function renderParcelas() {
    const lista = document.getElementById("parcelasLista");
    const valor = pagamentoAtual.valor;
    lista.innerHTML = "";

    for (let n = 1; n <= 3; n++) {
        const parcela = valor / n;
        const label = document.createElement("label");
        label.className = "parcela-opcao";
        label.innerHTML = `
            <input type="radio" name="parcelas" value="${n}" ${n === 1 ? "checked" : ""}>
            ${n}x de R$ ${parcela.toLocaleString("pt-BR", { minimumFractionDigits: 2 })}
        `;
        lista.appendChild(label);
    }
}

function formatarCartaoNumero(input) {
    let digitos = input.value.replace(/\D/g, "").slice(0, 16);
    input.value = digitos.replace(/(.{4})/g, "$1 ").trim();
}

function formatarValidade(input) {
    let digitos = input.value.replace(/\D/g, "").slice(0, 4);
    if (digitos.length > 2) {
        input.value = digitos.slice(0, 2) + "/" + digitos.slice(2);
    } else {
        input.value = digitos;
    }
}

function validarCartao() {
    let valido = true;

    const numero = document.getElementById("cartaoNumero").value.replace(/\D/g, "");
    const validade = document.getElementById("cartaoValidade").value;
    const cvv = document.getElementById("cartaoCvv").value;

    const erroNumero = document.getElementById("erroCartaoNumero");
    const erroValidade = document.getElementById("erroCartaoValidade");
    const erroCvv = document.getElementById("erroCartaoCvv");

    erroNumero.textContent = "";
    erroValidade.textContent = "";
    erroCvv.textContent = "";

    if (numero.length !== 16) {
        erroNumero.textContent = "O número do cartão deve ter 16 dígitos.";
        valido = false;
    }

    if (!/^\d{2}\/\d{2}$/.test(validade)) {
        erroValidade.textContent = "Use o formato MM/AA.";
        valido = false;
    }

    if (!/^\d{3}$/.test(cvv)) {
        erroCvv.textContent = "O CVV deve ter 3 dígitos.";
        valido = false;
    }

    return valido;
}

async function processarPagamentoCartao() {
    if (!validarCartao()) return;

    const status = document.getElementById("cartaoStatus");
    const botao = document.getElementById("btnPagarCartao");

    botao.disabled = true;

    status.className = "pagamento-status";
    status.innerHTML = '<span class="spinner"></span> Conectando à operadora...';
    await esperar(1200);

    status.innerHTML = '<span class="spinner"></span> Validando cartão...';
    await esperar(1200);

    status.className = "pagamento-status sucesso";
    status.innerHTML = "✔ Pagamento aprovado!";

    await confirmarPagamentoBackend(pagamentoAtual.id, "Cartao");

    await esperar(1200);
    fecharModalPagamento();
}

// ---------------------------------------------------------
// BOLETO
// ---------------------------------------------------------

function resetarPainelBoleto() {
    document.getElementById("boletoStatus").innerHTML = "";
    document.getElementById("boletoStatus").className = "pagamento-status";
    document.getElementById("boletoAcoesGerar").classList.remove("escondido");
    document.getElementById("boletoAcoesPosGeracao").classList.add("escondido");
    document.getElementById("boletoCodigo").textContent = "";
    document.getElementById("boletoVencimento").textContent = "";

    const botaoSimular = document.getElementById("btnSimularPagamentoBoleto");
    botaoSimular.disabled = false;
    botaoSimular.textContent = "Simular pagamento";
}

function gerarCodigoBarras() {
    function bloco(tamanho) {
        let s = "";
        for (let i = 0; i < tamanho; i++) s += Math.floor(Math.random() * 10);
        return s;
    }
    return `${bloco(5)}.${bloco(5)} ${bloco(5)}.${bloco(6)} ${bloco(5)}.${bloco(6)} ${bloco(1)} ${bloco(14)}`;
}

function calcularVencimento() {
    const data = new Date();
    data.setDate(data.getDate() + 5);
    return data.toLocaleDateString("pt-BR");
}

function gerarBoleto() {
    document.getElementById("boletoVencimento").textContent = calcularVencimento();
    document.getElementById("boletoCodigo").textContent = gerarCodigoBarras();

    document.getElementById("boletoStatus").className = "pagamento-status sucesso";
    document.getElementById("boletoStatus").innerHTML =
        "✔ Boleto gerado com sucesso! Seu boleto está disponível.";

    document.getElementById("boletoAcoesGerar").classList.add("escondido");
    document.getElementById("boletoAcoesPosGeracao").classList.remove("escondido");
}

function baixarBoletoPdf() {
    const conteudo =
        `FreteFácil - Boleto de Pagamento\n\n` +
        `Rota: ${pagamentoAtual.rota}\n` +
        `Valor: R$ ${pagamentoAtual.valor.toLocaleString("pt-BR", { minimumFractionDigits: 2 })}\n` +
        `Vencimento: ${document.getElementById("boletoVencimento").textContent}\n` +
        `Código de barras: ${document.getElementById("boletoCodigo").textContent}\n\n` +
        `(Documento simulado apenas para fins de teste do sistema.)`;

    const blob = new Blob([conteudo], { type: "text/plain" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = `boleto-frete-${pagamentoAtual.id}.txt`;
    link.click();
    URL.revokeObjectURL(link.href);
}

async function processarPagamentoBoleto() {
    const status = document.getElementById("boletoStatus");
    const botao = document.getElementById("btnSimularPagamentoBoleto");

    botao.disabled = true;
    status.className = "pagamento-status";
    status.innerHTML = '<span class="spinner"></span> Aguardando compensação bancária...';

    await esperar(2500);

    status.className = "pagamento-status sucesso";
    status.innerHTML = "Pagamento compensado. ✔ Frete pago.";

    await confirmarPagamentoBackend(pagamentoAtual.id, "Boleto");

    await esperar(1200);
    fecharModalPagamento();
}

// ---------------------------------------------------------
// UTILITÁRIO
// ---------------------------------------------------------

function esperar(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// ---------------------------------------------------------
// EVENTOS (DELEGADOS NO DOCUMENT)
// ---------------------------------------------------------

document.addEventListener("click", function (e) {

    const botaoPagar = e.target.closest(".btn-pagar");
    if (botaoPagar) {
        abrirModalPagamento(botaoPagar);
        return;
    }

    if (e.target.closest("[data-fechar-modal]")) {
        fecharModalPagamento();
        return;
    }

    if (e.target.id === "copiarChavePix") {
        navigator.clipboard.writeText(document.getElementById("pixChave").textContent.trim());
        e.target.textContent = "Copiado!";
        setTimeout(() => { e.target.textContent = "Copiar"; }, 1500);
        return;
    }

    if (e.target.id === "btnJaPaguei") {
        processarPagamentoPix();
        return;
    }

    if (e.target.id === "btnPagarCartao") {
        processarPagamentoCartao();
        return;
    }

    if (e.target.id === "btnGerarBoleto") {
        gerarBoleto();
        return;
    }

    if (e.target.id === "btnBaixarBoleto") {
        baixarBoletoPdf();
        return;
    }

    if (e.target.id === "btnSimularPagamentoBoleto") {
        processarPagamentoBoleto();
        return;
    }

});

document.addEventListener("change", function (e) {
    if (e.target.name === "metodo") {
        trocarPainelMetodo(e.target.value);
    }
});

document.addEventListener("input", function (e) {
    if (e.target.id === "cartaoNumero") {
        formatarCartaoNumero(e.target);
    }
    if (e.target.id === "cartaoValidade") {
        formatarValidade(e.target);
    }
    if (e.target.id === "cartaoCvv") {
        e.target.value = e.target.value.replace(/\D/g, "").slice(0, 3);
    }
});