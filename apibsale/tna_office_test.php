<?php
// Token de TNA Office
$token = '9c54f0b10d15274ec67b08363e6964a9e3474543';

// 1. Obtener los 10 registros de stock con mayor cantidad
$endpoint_stocks = "/stocks.json?limit=10";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.bsale.io/v1" . $endpoint_stocks);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["access_token: $token", "Accept: application/json"]);
$res_stocks = json_decode(curl_exec($ch), true);
curl_close($ch);

$top_stocks = [];
foreach ($res_stocks['items'] ?? [] as $si) {
    if ($si['quantity'] > 0) {
        $vid = $si['variant']['id'];
        // Obtener nombre de la variante
        $vch = curl_init();
        curl_setopt($vch, CURLOPT_URL, "https://api.bsale.io/v1/variants/$vid.json?expand=[product]");
        curl_setopt($vch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($vch, CURLOPT_HTTPHEADER, ["access_token: $token", "Accept: application/json"]);
        $v_data = json_decode(curl_exec($vch), true);
        curl_close($vch);
        
        $top_stocks[] = [
            'variant_id' => $vid,
            'name' => ($v_data['product']['name'] ?? 'S/N') . " " . ($v_data['description'] ?? ''),
            'quantity' => $si['quantity']
        ];
    }
}

header('Content-Type: application/json');
echo json_encode([
    'products_with_real_stock' => $top_stocks
], JSON_PRETTY_PRINT);
?>
