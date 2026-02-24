<?php
$token = '65d4fcedb5a2ce6d2dcb6f74d0bbea72918dbc81';

// URL con dominio .io
$url_io = "https://api.bsale.io/v1/documents.json?limit=1";

// URL con dominio .cl
$url_cl = "https://api.bsale.cl/v1/documents.json?limit=1";

function testEndpoint($url, $token) {
    echo "Probando: $url\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "access_token: $token"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "HTTP: $httpCode\n";
    echo "Respuesta: $response\n\n";
}

testEndpoint($url_io, $token);
testEndpoint($url_cl, $token);
?>
