<?php
$url = 'https://api.bsale.io/v1/';
$token = '65d4fcedb5a2ce6d2dcb6f74d0bbea72918dbc81';

$document_type = 4; // Nota de Venta
$limit = 50;
$offset = 0;

$urlSearch = $url . "documents.json?document_type=$document_type&limit=$limit&start=$offset";

$ch = curl_init($urlSearch);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "access_token: $token"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo "Error HTTP $httpCode: $response\n";
    exit;
}

$data = json_decode($response, true);

if (empty($data['items'])) {
    echo "No se encontraron Notas de Venta.\n";
    exit;
}

foreach ($data['items'] as $item) {
    $fecha = date('Y-m-d', $item['emissionDate']);
    $cliente = isset($item['client']['name']) ? $item['client']['name'] : '(sin cliente)';
    echo "--------------------------------------\n";
    echo "ID interno: " . $item['id'] . "\n";
    echo "Número NV: " . $item['number'] . "\n";
    echo "Fecha: " . $fecha . "\n";
    echo "Estado: " . $item['state'] . "\n";
    echo "Cliente: " . $cliente . "\n";
}
?>
