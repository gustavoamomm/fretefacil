<?php

session_start();

if (
    !isset($_SESSION['tipo']) ||
    $_SESSION['tipo'] !== 'empresa'
) {

    header('Location: login.php');
    exit;

}

$bodyClass = 'dashboard';

include __DIR__ . '/../components/header.php';
require_once __DIR__ . "/../models/FreteDAO.php";
require_once __DIR__ . "/../models/PagamentoDAO.php";

$freteDAO = new FreteDAO();
$fretes = $freteDAO->listarPorEmpresa($_SESSION['id']);

$pagamentoDAO = new PagamentoDAO();
$pagamentos = $pagamentoDAO->listarPorEmpresa($_SESSION["id"]);
$totalPagoMes = $pagamentoDAO->totalPagoMes($_SESSION["id"]);

?>
<head>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="../static/pagamento-simulacao.js" defer></script>
</head>
<aside class="sidebar">

    <a
        href="../index.php"
        class="sidebar-logo">

        🚚 Frete<em>Fácil</em>

    </a>

    <div class="sidebar-user">

        <div class="user-avatar">
            🏢
        </div>

        <div>

            <div class="user-name">
                <?= htmlspecialchars($_SESSION['nome']) ?>
            </div>

            <div class="user-role">
                Empresa
            </div>

        </div>

    </div>

    <div class="sidebar-divider"></div>

    <nav class="sidebar-nav">

        <button
            class="nav-item ativo"
            onclick="trocarAba('publicar', this)">

            <span class="nav-icon">📦</span>

            Publicar Frete

        </button>

        <button
            class="nav-item"
            onclick="trocarAba('fretes', this)">

            <span class="nav-icon">📋</span>

            Fretes Publicados

        </button>

        <button
            class="nav-item"
            onclick="trocarAba('pagamentos', this)">

            <span class="nav-icon">💳</span>

            Pagamentos

            <span
                class="nav-badge"
                id="badge-pendentes">

                2

            </span>

        </button>

    </nav>

    <botton class="nav-item" id="btn-tema">
        <span class="nav-icon">
            🌙
        </span>
        <span id="texto=tema">
            Alterar Tema
        </span>
    </botton>

    <div class="sidebar-bottom">

        <div class="sidebar-divider"></div>

        <a href="../logout.php" class="nav-item sair">
            <span class="nav-icon">🚪</span>
            Sair
        </a>

    </div>

</aside>
<main class="main-content">

    <div class="page-header">

        <div>

            <h1
                class="page-title"
                id="page-title">

                PUBLICAR FRETE

            </h1>

            <p
                class="page-sub"
                id="page-sub">

                Preencha os dados e motoristas da sua região verão em instantes.

            </p>

        </div>

    </div>

    <div
        class="aba"
        id="aba-publicar">

        <form
            class="dash-form"
            id="form-frete"
            method="POST"
            action="../controllers/FreteController.php">

            <div class="form-row">

                <div class="form-group">

                    <label for="origem">Cidade de origem</label>
                    <input type="text" id="origem" name="origem" placeholder="Ex: Joinville - SC" required>

                    <label for="uf_origem">Estado</label>
                    <select name="uf_origem" id="uf_origem" required>
                        <option value="">UF</option>
                        <option value="AC">AC</option>
                        <option value="AL">AL</option>
                        <option value="AM">AM</option>
                        <option value="BA">BA</option>
                        <option value="CE">CE</option>
                        <option value="DF">DF</option>
                        <option value="ES">ES</option>
                        <option value="GO">GO</option>
                        <option value="MA">MA</option>
                        <option value="MG">MG</option>
                        <option value="MS">MS</option>
                        <option value="MT">MT</option>
                        <option value="PA">PA</option>
                        <option value="PB">PB</option>
                        <option value="PE">PE</option>
                        <option value="PI">PI</option>
                        <option value="PR">PR</option>
                        <option value="RJ">RJ</option>
                        <option value="RN">RN</option>
                        <option value="RO">RO</option>
                        <option value="RR">RR</option>
                        <option value="RS">RS</option>
                        <option value="SC">SC</option>
                        <option value="SE">SE</option>
                        <option value="SP">SP</option>
                        <option value="TO">TO</option>
                    </select>

                </div>

                <div class="form-group">
                    <label for="destino">Cidade de destino</label>
                    <input type="text" id="destino" name="destino" placeholder="Ex: Blumenau - SC" required>
                    <label for="uf_destino">Estado</label>
                    <select name="uf_destino" id="uf_destino" required>
                        <option value="">UF</option>
                        <option value="AC">AC</option>
                        <option value="AL">AL</option>
                        <option value="AM">AM</option>
                        <option value="BA">BA</option>
                        <option value="CE">CE</option>
                        <option value="DF">DF</option>
                        <option value="ES">ES</option>
                        <option value="GO">GO</option>
                        <option value="MA">MA</option>
                        <option value="MG">MG</option>
                        <option value="MS">MS</option>
                        <option value="MT">MT</option>
                        <option value="PA">PA</option>
                        <option value="PB">PB</option>
                        <option value="PE">PE</option>
                        <option value="PI">PI</option>
                        <option value="PR">PR</option>
                        <option value="RJ">RJ</option>
                        <option value="RN">RN</option>
                        <option value="RO">RO</option>
                        <option value="RR">RR</option>
                        <option value="RS">RS</option>
                        <option value="SC">SC</option>
                        <option value="SE">SE</option>
                        <option value="SP">SP</option>
                        <option value="TO">TO</option>
                    </select>

                </div>

            </div>

            <div class="form-row">

                <div class="form-group">

                    <label for="peso">
                        Peso da carga (kg)
                    </label>

                    <input
                        type="number"
                        id="peso"
                        name="peso"
                        placeholder="Ex: 350"
                        required>

                </div>

                <div class="form-group">

                    <label for="valor">
                        Valor do frete (R$)
                    </label>

                    <input
                        type="number"
                        id="valor"
                        name="valor"
                        step="0.01"
                        placeholder="Ex: 280"
                        required>

                </div>

            </div>

            <div class="form-row">

                <div class="form-group">

                    <label for="data_coleta">
                        Data da coleta
                    </label>

                    <input
                        type="date"
                        id="data_coleta"
                        name="data_coleta"
                        required>

                </div>

                <div class="form-group">

                    <label for="hora_coleta">
                        Hora da coleta
                    </label>

                    <input
                        type="time"
                        id="hora_coleta"
                        name="hora_coleta"
                        required>

                </div>

            </div>

            <div class="form-group">

                <label for="carro_necessario">
                    Veículo necessário
                </label>

                <select id="carro_necessario" name="carro_necessario"required>

                    <option value="">
                        Selecione
                    </option>

                    <option value="furgao">
                        Furgão
                    </option>

                    <option value="van">
                        Van
                    </option>

                    <option value="caminhao_pqn">
                        Caminhão pequeno
                    </option>

                </select>

            </div>

            <div class="form-group">

                <label for="descricao">
                    Descrição da carga
                </label>

                <textarea
                    id="descricao"
                    name="descricao"
                    rows="3"
                    placeholder="Ex: Caixas frágeis, necessário cuidado no manuseio."></textarea>

            </div>

            <div
                class="form-feedback"
                id="feedback-frete"></div>

            <button
                type="submit"
                class="btn btn-o">

                Publicar frete →

            </button>

        </form>

    </div>
    <div class="aba escondido" id="aba-fretes">

        <div class="fretes-grid" id="fretes-grid">
            <?php include "ajax/fretes-empresa.php";?>

        </div>

    </div>
    <div class="aba escondido" id="aba-pagamentos">
        <?php include "ajax/pagamentos.php";?>
        
    </div>
</main>

<script>
const btnTema = document.getElementById("btn-tema");

if (localStorage.getItem("tema_empresa") === "light") {
    document.body.classList.add("light");
}

btnTema.addEventListener("click", () => {

    document.body.classList.toggle("light");

    if (document.body.classList.contains("light")) {
        localStorage.setItem("tema_empresa", "light");
    } else {
        localStorage.setItem("tema_empresa", "dark");
    }

});
</script>

<script>


    function trocarAba(aba, botao) {

        document
            .querySelectorAll('.aba')
            .forEach(function (elemento) {

                elemento.classList.add('escondido');

            });

        document
            .querySelectorAll('.nav-item')
            .forEach(function (elemento) {

                elemento.classList.remove('ativo');

            });

        const abaSelecionada =
            document.getElementById('aba-' + aba);

        if (abaSelecionada) {

            abaSelecionada.classList.remove('escondido');

        }

        if (botao) {

            botao.classList.add('ativo');

        }

        const titulos = {

            publicar: [
                'PUBLICAR FRETE',
                'Preencha os dados e motoristas da sua região verão em instantes.'
            ],

            fretes: [
                'FRETES PUBLICADOS',
                'Acompanhe todos os fretes que sua empresa publicou.'
            ],

            pagamentos: [
                'PAGAMENTOS',
                'Gerencie pagamentos pendentes e veja seu histórico.'
            ]

        };

        const titulo =
            document.getElementById('page-title');

        const subtitulo =
            document.getElementById('page-sub');

        if (titulo) {

            titulo.textContent =
                titulos[aba][0];

        }

        if (subtitulo) {

            subtitulo.textContent =
                titulos[aba][1];

        }

    }

    setInterval(() => {

    fetch("ajax/fretes-empresa.php")
        .then(res => res.text())
        .then(html => {

            document.getElementById("fretes-grid").innerHTML = html;

        });

    }, 5000);

    



    console.log("Script carregado");
    console.log(typeof trocarAba);

</script>