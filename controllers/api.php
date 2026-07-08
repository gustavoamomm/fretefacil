<?php

// =========================
// Blindagem: essa rota só deve responder JSON.
// Se algo der errado (extensão faltando, warning do PHP, etc.),
// nunca deixamos HTML de erro vazar pro front-end.
// =========================

ini_set('display_errors', '0'); // não imprime erro/warning como HTML
error_reporting(E_ALL);         // mas continua registrando no log do servidor

header("Content-Type: application/json");

function responderErro($mensagem, $statusCode = 500) {
    http_response_code($statusCode);
    echo json_encode(["erro" => $mensagem]);
    exit;
}

// Captura qualquer erro fatal (ex: função inexistente) e converte em JSON
register_shutdown_function(function () {
    $erro = error_get_last();
    if ($erro && in_array($erro['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        if (!headers_sent()) {
            header("Content-Type: application/json");
            http_response_code(500);
        }
        echo json_encode([
            "erro" => "Erro interno no servidor.",
            "detalhe" => $erro['message'] // pode remover essa linha em produção
        ]);
    }
});

if (!function_exists('curl_init')) {
    responderErro("A extensão cURL do PHP não está habilitada no servidor.");
}

$ORS_KEY = "eyJvcmciOiI1YjNjZTM1OTc4NTExMTAwMDFjZjYyNDgiLCJpZCI6ImFkNjU4NmJkMTFlNTRiMmNhOWRkOWVjYmU3YWZkYmM1IiwiaCI6Im11cm11cjY0In0=";

// Certificado CA para o cURL validar SSL corretamente mesmo se o servidor
// (comum em XAMPP/WAMP locais) não tiver um CA bundle configurado no php.ini
define('CACERT_PATH', __DIR__ . '/ssl/cacert.pem');


// =========================
// GEOCODIFICAÇÃO
// =========================

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (!isset($_GET['acao'])) {
        responderErro("Ação não informada", 400);
    }

    if ($_GET['acao'] === 'geocode') {

        $cidade = $_GET['cidade'] ?? '';
        $uf = $_GET['uf'] ?? '';

        $estados = [
            'AC' => 'Acre', 'AL' => 'Alagoas', 'AM' => 'Amazonas', 'AP' => 'Amapá',
            'BA' => 'Bahia', 'CE' => 'Ceará', 'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo',
            'GO' => 'Goiás', 'MA' => 'Maranhão', 'MG' => 'Minas Gerais', 'MS' => 'Mato Grosso do Sul',
            'MT' => 'Mato Grosso', 'PA' => 'Pará', 'PB' => 'Paraíba', 'PE' => 'Pernambuco',
            'PI' => 'Piauí', 'PR' => 'Paraná', 'RJ' => 'Rio de Janeiro', 'RN' => 'Rio Grande do Norte',
            'RO' => 'Rondônia', 'RR' => 'Roraima', 'RS' => 'Rio Grande do Sul', 'SC' => 'Santa Catarina',
            'SE' => 'Sergipe', 'SP' => 'São Paulo', 'TO' => 'Tocantins'
        ];

        $nomeEstado = $estados[$uf] ?? '';
        $textoBusca = $nomeEstado ? "$cidade, $nomeEstado, Brasil" : "$cidade, Brasil";

        $url =
        "https://api.openrouteservice.org/geocode/search?" .
        http_build_query([
            "api_key" => $ORS_KEY,
            "text" => $textoBusca,
            "boundary.country" => "BR",
            "layers" => "locality,region",
            "size" => 1
        ]);

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        if (file_exists(CACERT_PATH)) {
            curl_setopt($curl, CURLOPT_CAINFO, CACERT_PATH);
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);

        $resposta = curl_exec($curl);

        if ($resposta === false) {
            $erroCurl = curl_error($curl);
            curl_close($curl);
            responderErro("Falha ao consultar o serviço de geocodificação: " . $erroCurl, 502);
        }

        $codigoHttp = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($codigoHttp >= 400) {
            responderErro("O serviço de geocodificação retornou erro (HTTP $codigoHttp).", 502);
        }

        echo $resposta;
        exit;
    }

    responderErro("Ação inválida", 400);
}


// =========================
// ROTA
// =========================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dados = json_decode(file_get_contents("php://input"), true);

    if (!$dados || !isset($dados["acao"]) || $dados["acao"] !== "rota") {
        responderErro("Requisição inválida", 400);
    }

    if (!isset($dados["origem"]["lat"], $dados["origem"]["lon"], $dados["destino"]["lat"], $dados["destino"]["lon"])) {
        responderErro("Coordenadas de origem/destino ausentes", 400);
    }

    $origem = $dados["origem"];
    $destino = $dados["destino"];

    $body = json_encode([
        "coordinates" => [
            [$origem["lon"], $origem["lat"]],
            [$destino["lon"], $destino["lat"]]
        ]
    ]);

    $curl = curl_init("https://api.openrouteservice.org/v2/directions/driving-car/geojson");

    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: $ORS_KEY",
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS => $body
    ]);

    if (file_exists(CACERT_PATH)) {
        curl_setopt($curl, CURLOPT_CAINFO, CACERT_PATH);
    }

    $resposta = curl_exec($curl);

    if ($resposta === false) {
        $erroCurl = curl_error($curl);
        curl_close($curl);
        responderErro("Falha ao consultar o serviço de rotas: " . $erroCurl, 502);
    }

    $codigoHttp = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($codigoHttp >= 400) {
        responderErro("O serviço de rotas retornou erro (HTTP $codigoHttp).", 502);
    }

    echo $resposta;
    exit;
}

responderErro("Método não suportado", 405);
