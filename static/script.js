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
    const feedback = document.getElementById('feedback');

    if (feedback) {
        feedback.className = 'form-feedback';
    }

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
// FORMULÁRIO
// =========================

const form = document.getElementById('form-cadastro');

if (form) {

    form.addEventListener('submit', function(e) {

        if (abaAtiva === 'empresa'){
            form.action = '../controllers/EmpresaController.php';
        }

        if (abaAtiva === 'motorista'){
            form.action = '../controllers/MotoristaController.php';
        }


        const feedback = document.getElementById('feedback');

        const senha =
        document.getElementById('senha').value;

        const confirmar =
        document.getElementById('confirmar-senha').value;

        // EMPRESA
        if (abaAtiva === 'empresa') {

            const nome =
            document.getElementById('nome-empresa').value.trim();

            const cnpj =
            document.getElementById('cnpj').value.trim();

            if (!nome || !cnpj) {

                feedback.className = 'form-feedback erro';
                feedback.textContent =
                '⚠️ Preencha nome e CNPJ.';

                return;
            }

        }

        // MOTORISTA
        if (abaAtiva === 'motorista') {

            const nome =
            document.getElementById('nome-motorista').value.trim();

            const cpf =
            document.getElementById('cpf').value.trim();

            const veiculo =
            document.getElementById('veiculo').value;

            const placa =
            document.getElementById('placa').value.trim();

            if (!nome || !cpf || !veiculo || !placa) {

                e.preventDefault();
                feedback.className = 'form-feedback erro';

                feedback.textContent =
                '⚠️ Preencha todos os campos.';

                return;
            }

        }

        // SENHA
        if (senha.length < 8) {

            e.preventDefault();
            feedback.className = 'form-feedback erro';

            feedback.textContent =
            '⚠️ A senha precisa ter pelo menos 8 caracteres.';

            return;
        }

        if (senha !== confirmar) {

            e.preventDefault();
            feedback.className = 'form-feedback erro';

            feedback.textContent =
            '⚠️ As senhas não coincidem.';

            return;
        }

        // SUCESSO
        feedback.className = 'form-feedback sucesso';

        feedback.textContent =
        '✅ Conta criada!';

    });

}


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

function abrirModal(origem, destino, valor, meta, desc){

    freteAtual = { origem, destino, valor, meta, desc };

    const modalRota = document.getElementById('modal-rota');
    const modalMeta = document.getElementById('modal-meta');
    const modalValor = document.getElementById('modal-valor');
    const modalDesc = document.getElementById('modal-desc');

    if (modalRota) modalRota.textContent = origem + ' → ' + destino;
    if (modalMeta) modalMeta.textContent = meta;
    if (modalValor) modalValor.textContent = valor;
    if (modalDesc) modalDesc.textContent = desc;

    carregarMapa(origem, destino);

    const overlay = document.getElementById('modal-overlay');
    if (overlay){
        overlay.classList.remove('escondido');
        document.body.style.overflow = 'hidden';
    }
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


let mapaLeaflet = null;

async function carregarMapa(origem, destino) {

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
            buscarCoordenadas(origem),
            buscarCoordenadas(destino)
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
        }

    } catch (erro){
        console.error('Erro ao carregar o mapa: ', erro);
        mapaDiv.innerHTML = '<p style="padding:20px;color:var(--red);">Erro ao carregar o mapa.</p>';
    }
}

async function buscarCoordenadas(cidade){

    const resposta = await fetch(
        "../controllers/api.php?acao=geocode&cidade=" +
        encodeURIComponent(cidade)
    );

    const dados = await resposta.json();
    
    if (dados.features && dados.features.length > 0){

        const [lon, lat] = dados.features[0].geometry.coordinates;

        return {
            lat, 
            lon
        };
    }

    return null;

}

async function buscarRota(origem, destino){

    const resposta = await fetch("../controllers/api.php",{

        method:"POST",

        headers:{
            "Content-Type":"application/json"
        },

        body:JSON.stringify({

            acao:"rota",

            origem:origem,

            destino:destino

        })

    });

    const dados = await resposta.json();

    if(dados.features && dados.features.length){

        const rota = dados.features[0];

        const coordenadas = rota.geometry.coordinates.map(function(c){

            return [c[1], c[0]];

        });

        return{

            coordenadas:coordenadas,

            distancia:
            (rota.properties.summary.distance/1000).toFixed(1) + " km",

            duracao:
            Math.round(rota.properties.summary.duration/60) + " min"

        };

    }

    return null;

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


