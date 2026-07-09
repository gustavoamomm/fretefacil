// =========================
// ANIMAÇÃO
// =========================

const elementos = document.querySelectorAll('.reveal');

const observador = new IntersectionObserver(function(entradas) {

    entradas.forEach(function(entrada) {

        if (entrada.isIntersecting) {
            entrada.target.classList.add('visible');
            observador.unobserve(entrada.target);
        }

    });

}, { threshold: 0.12 });

elementos.forEach(function(el) {
    observador.observe(el);
});


// =========================
// ABA ATIVA
// =========================

let abaAtiva = 'empresa';


// =========================
// TROCA DE ABAS
// =========================

function setTabFields(tabId, enabled) {
    const container = document.getElementById(tabId);
    if (!container) return;
    const fields = container.querySelectorAll('input, select, textarea');
    fields.forEach(function(field) {
        field.disabled = !enabled;
    });
}

function trocarAba(aba) {

    abaAtiva = aba;

    const tipoLogin =
    document.getElementById('tipo-usuario-login');

    if(tipoLogin){
        tipoLogin.value = aba;
    }

    const campoEmpresa   = document.getElementById('campo-empresa');
    const campoMotorista = document.getElementById('campo-motorista');

    const tabEmpresa    = document.getElementById('tab-empresa');
    const tabMotorista  = document.getElementById('tab-motorista');

    const icone   = document.getElementById('auth-icon');
    const sub     = document.getElementById('auth-sub');

    // EMPRESA
    if (aba === 'empresa') {

        campoEmpresa.classList.remove('escondido');
        campoMotorista.classList.add('escondido');

        tabEmpresa.classList.add('ativo');
        tabEmpresa.classList.remove('azul');

        tabMotorista.classList.remove('ativo', 'azul');

        if (icone) {
            icone.textContent = '🏢';
        }

        if (sub) {
            sub.textContent =
            'Cadastre sua empresa e publique fretes em minutos.';
        }

        const tipoInput = document.getElementById('tipo-usuario');
        if (tipoInput) {
            tipoInput.value = 'empresa';
        }

        setTabFields('campo-empresa', true);
        setTabFields('campo-motorista', false);

    }

    // MOTORISTA
    else {

        campoMotorista.classList.remove('escondido');
        campoEmpresa.classList.add('escondido');

        tabMotorista.classList.add('ativo', 'azul');

        tabEmpresa.classList.remove('ativo');

        if (icone) {
            icone.textContent = '🚐';
        }

        if (sub) {
            sub.textContent =
            'Cadastre seu veículo e comece a receber fretes hoje.';
        }

        const tipoInput = document.getElementById('tipo-usuario');
        if (tipoInput) {
            tipoInput.value = 'motorista';
        }

        setTabFields('campo-motorista', true);
        setTabFields('campo-empresa', false);

    }

}

// Inicializa campos ao carregar a página
setTabFields('campo-empresa', true);
setTabFields('campo-motorista', false);



// =========================
// MÁSCARA CPF
// =========================

const cpf = document.getElementById('cpf');

if (cpf) {

    cpf.addEventListener('input', function() {

        let v = this.value.replace(/\D/g, '');

        if (v.length > 3)
            v = v.slice(0,3) + '.' + v.slice(3);

        if (v.length > 7)
            v = v.slice(0,7) + '.' + v.slice(7);

        if (v.length > 11)
            v = v.slice(0,11) + '-' + v.slice(11);

        this.value = v;

    });

}


// =========================
// MÁSCARA CNPJ
// =========================

const cnpj = document.getElementById('cnpj');

if (cnpj) {

    cnpj.addEventListener('input', function() {

        let v = this.value.replace(/\D/g, '');

        if (v.length > 2)
            v = v.slice(0,2) + '.' + v.slice(2);

        if (v.length > 6)
            v = v.slice(0,6) + '.' + v.slice(6);

        if (v.length > 10)
            v = v.slice(0,10) + '/' + v.slice(10);

        if (v.length > 15)
            v = v.slice(0,15) + '-' + v.slice(15);

        this.value = v;

    });

}

const cnh = document.getElementById('cnh');

if (cnh) {

    cnh.addEventListener('input', function() {

        let v = this.value.replace(/\D/g, '');

        if (v.length > 9)
            v = v.slice(0, 9);

        this.value = v;

    });

}


// =========================
// MÁSCARA PLACA (antiga + Mercosul)
// =========================
// Formato antigo:  AAA-9999  (3 letras, 4 números)
// Formato Mercosul: AAA9A99  (3 letras, 1 número, 1 letra, 2 números)
// O 5º caractere decide o formato: letra = Mercosul (sem traço),
// número = modelo antigo (com traço após as 3 letras).

const placa = document.getElementById('placa');

if (placa) {

    placa.addEventListener('input', function() {

        let v = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        v = v.slice(0, 7);

        let letras = v.slice(0, 3).replace(/[^A-Z]/g, '');
        let resto = v.slice(3);

        if (resto.length > 0) {
            resto = resto.slice(0, 1).replace(/[^0-9]/g, '') + resto.slice(1);
        }

        if (resto.length > 1) {
            resto = resto.slice(0, 1) + resto.slice(1, 2).replace(/[^A-Z0-9]/g, '') + resto.slice(2);
        }

        if (resto.length > 2) {
            resto = resto.slice(0, 2) + resto.slice(2).replace(/[^0-9]/g, '');
        }

        v = letras + resto;

        let formatado = v;

        if (v.length > 4) {
            const quintoChar = v.charAt(4);
            const ehMercosul = /[A-Z]/.test(quintoChar);

            if (!ehMercosul) {
                formatado = v.slice(0, 3) + '-' + v.slice(3);
            }
        }

        this.value = formatado;

    });

}


// =========================
// MOSTRAR SENHA
// =========================

function toggleSenha(inputId, btnId) {

    const input = document.getElementById(inputId);
    const btn = document.getElementById(btnId);

    if (!input || !btn) return;

    btn.addEventListener('click', function() {

        if (input.type === 'password') {

            input.type = 'text';
            btn.textContent = '🙈';

        } else {

            input.type = 'password';
            btn.textContent = '👁️';

        }

    });

}

toggleSenha('senha', 'toggle-senha');
toggleSenha('confirmar-senha', 'toggle-confirmar');


//feedback

document.addEventListener("DOMContentLoaded", function () {

    const feedback = document.querySelector(".form-feedback");

    if (feedback && feedback.textContent.trim() !== "") {

        setTimeout(function () {

            feedback.style.transition = "opacity 0.5s ease";
            feedback.style.opacity = "0";

            setTimeout(function () {
                feedback.remove();
            }, 500);

        }, 3000);

    }

});


//dashboard motorista

const titulosDashMotorista = {
    disponiveis: ['FRETES DISPONÍVEIS', 'Fretes próximos de você. Clique para ver detalhes e aceitar. '],
    aceitos: ['MEUS FRETES ACEITOS', 'Fretes que você aceitou e está realizando.'],
    carteira: ['CARTEIRA', 'Seu saldo, pagamentos recebidos e histórico']
};

function trocarAbaMotorista(aba, btn){
    
    document.querySelectorAll('.aba').forEach(function(el){
        el.classList.add('escondido');
    });

    document.querySelectorAll('.nav-item').forEach(function(el){
        el.classList.remove('ativo');
    });

    document.getElementById('aba-' + aba).classList.remove('escondido');
    btn.classList.add('ativo');

    const pageTitle = document.getElementById('page-title');
    const pageSub = document.getElementById('page-sub');

    if (pageTitle){
        pageTitle.textContent = titulosDashMotorista[aba][0];
    
}
    
    if (pageSub){
        pageSub.textContent = titulosDashMotorista[aba][1];
    }
}

function filtrarFretes(){

    const input = document.getElementById('filtro-cidade');
    if (!input) return;

    const termo = input.value.toLowerCase().trim();

    document.querySelectorAll('.frete-card.disponivel').forEach(function(card){
        const origem = card.getAttribute('data-origem');

        if (origem.includes(termo) || termo === ''){
            card.classList.remove('oculto');
        
        } else {
            card.classList.add('oculto');
        }
    });
}

//modal + google maps

let freteAtual = {};

function abrirModal(idFrete, origem, ufOrigem, destino, ufDestino, valor, meta, desc){

    freteAtual = { idFrete, origem, ufOrigem, destino, ufDestino, valor, meta, desc };

    const modalRota = document.getElementById('modal-rota');
    const modalMeta = document.getElementById('modal-meta');
    const modalValor = document.getElementById('modal-valor');
    const modalDesc = document.getElementById('modal-desc');
    const modalIdFrete = document.getElementById('modal-id-frete');

    if (modalRota) modalRota.textContent = origem + ' → ' + destino;
    if (modalMeta) modalMeta.textContent = meta;
    if (modalValor) modalValor.textContent = valor;
    if (modalDesc) modalDesc.textContent = desc;
    if (modalIdFrete) modalIdFrete.value = idFrete;

    const overlay = document.getElementById('modal-overlay');
    if (overlay){
        overlay.classList.remove('escondido');
        document.body.style.overflow = 'hidden';
    }

    carregarMapa(origem, ufOrigem, destino, ufDestino);
}

function fecharModal(){

    const overlay = document.getElementById('modal-overlay');
    if(overlay) overlay.classList.add('escondido');

    const mapaDiv = document.getElementById('mapa-div');
    if (mapaDiv) mapaDiv.innerHTML = '';

    document.body.style.overflow = '';

}

const overlay = document.getElementById('modal-overlay');
if (overlay) {
    overlay.addEventListener('click', function(e){
        if (e.target === this) fecharModal();
    });
}

function aceitarFrete() {
    fecharModal();

    const aviso = document.createElement('div');
    aviso.className = 'form-feedback sucesso';
    aviso.style.cssText = 'position:fixed; top:24px;z-index:999;max-width:360px;display:block;';
    aviso.textContent =  '✅ Frete aceito! ' + freteAtual.origem + ' → ' + freteAtual.destino;

    document.body.appendChild(aviso);

    setTimeout(function() { aviso.remove(); }, 3000);


}

//leaflet e openrouteservice
// Chave pública usada somente para demonstração do projeto técnico.
// Para produção, o ideal é proteger essa chave no servidor.
const ORS_API_KEY = 'eyJvcmciOiI1YjNjZTM1OTc4NTExMTAwMDFjZjYyNDgiLCJpZCI6ImFkNjU4NmJkMTFlNTRiMmNhOWRkOWVjYmU3YWZkYmM1IiwiaCI6Im11cm11cjY0In0=';

let mapaLeaflet = null;

async function carregarMapa(origem, ufOrigem, destino, ufDestino) {

    const mapaDiv = document.getElementById('mapa-div');
    if (!mapaDiv) return;


    mapaDiv.innerHTML = `
    <div style="
        height:100%;
        display:flex;
        justify-content:center;
        align-items:center;
        color:#ccc;
        font-size:16px;
    ">
        🗺️ Carregando mapa...
    </div>
    `;

    if (mapaLeaflet) {
        mapaLeaflet.remove();
        mapaLeaflet = null;
    }

    try{

        const [coordOrigem, coordDestino] = await Promise.all([
            buscarCoordenadas(origem, ufOrigem),
            buscarCoordenadas(destino, ufDestino)
        ]);

        if (!coordOrigem || !coordDestino) {
            mapaDiv.innerHTML = '<p style="padding:20px; color:var(--muted);">Não foi possível localizar a rota.</p>';
            return;
        }

        mapaLeaflet= L.map(mapaDiv).setView([
            (coordOrigem.lat + coordDestino.lat) / 2,
            (coordOrigem.lon + coordDestino.lon) / 2],
            8

        );

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
            attribution: '&copy OpenStreetMap contributors',
            maxZoom: 18
        }).addTo(mapaLeaflet);

        // garante que o Leaflet recalcule o tamanho do container
        // (o modal pode acabar de se tornar visível nesse mesmo frame)
        setTimeout(function(){
            if (mapaLeaflet) mapaLeaflet.invalidateSize();
        }, 100);



        L.marker([coordOrigem.lat, coordOrigem.lon]).addTo(mapaLeaflet).bindPopup('📍 Origem: ' + origem);
        
        L.marker([coordDestino.lat, coordDestino.lon]).addTo(mapaLeaflet).bindPopup('🏁 Destino: ' + destino);

        const rota = await buscarRota(coordOrigem, coordDestino);

        if (rota){

            L.polyline(rota.coordenadas, {
                color: '#F26419',
                weight: 5
            }).addTo(mapaLeaflet);

            mapaLeaflet.fitBounds(rota.coordenadas);

            atualizarInfoRota(rota.distancia, rota.duracao);
        } else {
            // Fallback para a banca: se a API de rota falhar, o mapa não quebra.
            // Mostra uma linha simples entre origem e destino e informa distância aproximada.
            const linhaFallback = [
                [coordOrigem.lat, coordOrigem.lon],
                [coordDestino.lat, coordDestino.lon]
            ];

            L.polyline(linhaFallback, {
                color: '#F26419',
                weight: 3,
                dashArray: '6, 10'
            }).addTo(mapaLeaflet);

            mapaLeaflet.fitBounds(linhaFallback);

            const distanciaAproximada = calcularDistanciaAproximada(coordOrigem, coordDestino);
            atualizarInfoRota(distanciaAproximada + ' km aprox.', 'rota detalhada indisponível');
        }

    } catch (erro){
        console.error('Erro ao carregar o mapa: ', erro);
        mapaDiv.innerHTML = '<p style="padding:20px;color:var(--red);">Erro ao carregar o mapa.</p>';
    }
}

async function buscarCoordenadas(cidade, uf){

    const estados = {
        AC: 'Acre', AL: 'Alagoas', AM: 'Amazonas', AP: 'Amapá',
        BA: 'Bahia', CE: 'Ceará', DF: 'Distrito Federal', ES: 'Espírito Santo',
        GO: 'Goiás', MA: 'Maranhão', MG: 'Minas Gerais', MS: 'Mato Grosso do Sul',
        MT: 'Mato Grosso', PA: 'Pará', PB: 'Paraíba', PE: 'Pernambuco',
        PI: 'Piauí', PR: 'Paraná', RJ: 'Rio de Janeiro', RN: 'Rio Grande do Norte',
        RO: 'Rondônia', RR: 'Roraima', RS: 'Rio Grande do Sul', SC: 'Santa Catarina',
        SE: 'Sergipe', SP: 'São Paulo', TO: 'Tocantins'
    };

    const nomeEstado = estados[uf] || uf || '';
    const textoBusca = nomeEstado
        ? `${cidade}, ${nomeEstado}, Brasil`
        : `${cidade}, Brasil`;

    const url = 'https://api.openrouteservice.org/geocode/search?' +
        new URLSearchParams({
            api_key: ORS_API_KEY,
            text: textoBusca,
            'boundary.country': 'BR',
            layers: 'locality,region',
            size: '1'
        }).toString();

    try {
        const resposta = await fetch(url);

        if (!resposta.ok) {
            console.error('Erro da API ORS geocode:', resposta.status);
            return null;
        }

        const dados = await resposta.json();

        if (dados.features && dados.features.length > 0){
            const [lon, lat] = dados.features[0].geometry.coordinates;
            return { lat, lon };
        }
    } catch (erro) {
        console.error('Falha ao buscar coordenadas direto pelo navegador:', erro);
    }

    return null;
}

async function buscarRota(origem, destino){

    try {
        const resposta = await fetch('https://api.openrouteservice.org/v2/directions/driving-car/geojson', {
            method: 'POST',
            headers: {
                'Authorization': ORS_API_KEY,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                coordinates: [
                    [origem.lon, origem.lat],
                    [destino.lon, destino.lat]
                ]
            })
        });

        if (!resposta.ok) {
            console.error('Erro da API ORS rota:', resposta.status);
            return null;
        }

        const dados = await resposta.json();

        if(dados.features && dados.features.length){

            const rota = dados.features[0];

            const coordenadas = rota.geometry.coordinates.map(function(c){
                return [c[1], c[0]];
            });

            const duracaoSegundos = rota.properties.summary.duration;
            const horas = Math.floor(duracaoSegundos / 3600);
            const minutos = Math.round((duracaoSegundos % 3600) / 60);

            let duracaoFormatada = '';

            if (horas > 0) {
                duracaoFormatada = minutos > 0
                    ? `${horas}h ${minutos}min`
                    : `${horas}h`;
            } else {
                duracaoFormatada = `${minutos} min`;
            }

            return {
                coordenadas: coordenadas,
                distancia: (rota.properties.summary.distance / 1000).toFixed(1) + ' km',
                duracao: duracaoFormatada
            };
        }
    } catch (erro) {
        console.error('Falha ao buscar rota direto pelo navegador:', erro);
    }

    return null;
}

function calcularDistanciaAproximada(origem, destino) {
    const raioTerraKm = 6371;
    const lat1 = origem.lat * Math.PI / 180;
    const lat2 = destino.lat * Math.PI / 180;
    const deltaLat = (destino.lat - origem.lat) * Math.PI / 180;
    const deltaLon = (destino.lon - origem.lon) * Math.PI / 180;

    const a = Math.sin(deltaLat / 2) * Math.sin(deltaLat / 2) +
        Math.cos(lat1) * Math.cos(lat2) *
        Math.sin(deltaLon / 2) * Math.sin(deltaLon / 2);

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return (raioTerraKm * c).toFixed(1);
}


function atualizarInfoRota(distancia, duracao) {
    const infoEl = document.getElementById('modal-rota-info');

    if (!infoEl) return;

    if (!distancia || !duracao) {
        infoEl.textContent = '';
        infoEl.style.display = 'none';
        return;
    }

    infoEl.textContent = `🛣️ ${distancia} · ⏱️ ${duracao}`;
    infoEl.style.display = 'block';
}

function abrirConfirmacao(idFrete, acao){

    document.getElementById("id-frete-confirmacao").value = idFrete;
    document.getElementById("acao-confirmacao").value = acao;

    if(acao == "iniciar"){

        document.getElementById("titulo-confirmacao").textContent =
            "Iniciar viagem";

        document.getElementById("texto-confirmacao").textContent =
            "Deseja iniciar esta viagem?";

    }else{

        document.getElementById("titulo-confirmacao").textContent =
            "Solicitar conclusão";

        document.getElementById("texto-confirmacao").textContent =
            "Confirma que a entrega foi concluída?";

    }

    document
        .getElementById("modal-confirmacao")
        .classList.remove("escondido");

}

function fecharConfirmacao(){

    document
        .getElementById("modal-confirmacao")
        .classList.add("escondido");

}