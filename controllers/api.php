<?php

header("Content-Type: application/json");

$ORS_KEY = "eyJvcmciOiI1YjNjZTM1OTc4NTExMTAwMDFjZjYyNDgiLCJpZCI6ImFkNjU4NmJkMTFlNTRiMmNhOWRkOWVjYmU3YWZkYmM1IiwiaCI6Im11cm11cjY0In0=";


// =========================
// GEOCODIFICAÇÃO
// =========================

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (!isset($_GET['acao'])) {
        echo json_encode(["erro" => "Ação não informada"]);
        exit;
    }

    if ($_GET['acao'] === 'geocode') {

        $cidade = $_GET['cidade'] ?? '';

        $url =
        "https://api.openrouteservice.org/geocode/search?" .
        http_build_query([
            "api_key" => $ORS_KEY,
            "text" => $cidade . ", Brasil",
            "size" => 1
        ]);

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $resposta = curl_exec($curl);

        curl_close($curl);

        echo $resposta;

        exit;
    }
}



// =========================
// ROTA
// =========================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dados = json_decode(file_get_contents("php://input"), true);

    if (!$dados || $dados["acao"] != "rota") {
        echo json_encode(["erro" => "Requisição inválida"]);
        exit;
    }

    $origem = $dados["origem"];
    $destino = $dados["destino"];

    $body = json_encode([
        "coordinates" => [
            [$origem["lon"], $origem["lat"]],
            [$destino["lon"], $destino["lat"]]
        ]
    ]);

    $curl = curl_init("https://api.openrouteservice.org/v2/directions/driving-car");

    curl_setopt_array($curl, [

        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_POST => true,

        CURLOPT_HTTPHEADER => [

            "Authorization: $ORS_KEY",
            "Content-Type: application/json"

        ],

        CURLOPT_POSTFIELDS => $body

    ]);

    $resposta = curl_exec($curl);

    curl_close($curl);

    echo $resposta;

    exit;

}