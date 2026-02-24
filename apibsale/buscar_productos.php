<?php
/**
 * Backend para buscar productos en BSale
 */

header('Content-Type: application/json; charset=utf-8');
require_once "config.php";

$search = $_GET['search'] ?? '';
$offset = $_GET['offset'] ?? 0;
$limit = $_GET['limit'] ?? 50;
$company_key = $_GET['company'] ?? 'tna_office';

// Validar empresa
if (!isset(BSALE_COMPANIES[$company_key])) {
    $company_key = 'tna_office';
}

$company_config = BSALE_COMPANIES[$company_key];
$price_list_id = $company_config['price_list_id'];

$variants_items = [];
$total = 0;

if (!empty($search)) {
    $variants_data = [];
    
    // 1. Buscar por SKU (code) - exacto
    $res_code = bsale_api_request("/variants.json?code=" . urlencode($search) . "&expand=[product,stocks]", 'GET', null, $company_key);
    if ($res_code['success']) {
        foreach ($res_code['data']['items'] ?? [] as $v) {
            $variants_data[$v['id']] = $v;
        }
    }

    // 2. Buscar por nombre de producto - coincidencia parcial
    // BSale no busca variantes por nombre directamente, usamos products.json
    $res_name = bsale_api_request("/products.json?name=" . urlencode($search) . "&expand=[variants,variants.stocks]", 'GET', null, $company_key);
    if ($res_name['success']) {
        foreach ($res_name['data']['items'] ?? [] as $p) {
            if (isset($p['variants']['items'])) {
                foreach ($p['variants']['items'] as $v) {
                    if (!isset($variants_data[$v['id']])) {
                        // Inyectar producto padre para consistencia
                        $v['product'] = [
                            'id' => $p['id'],
                            'name' => $p['name'],
                            'href' => $p['href']
                        ];
                        $variants_data[$v['id']] = $v;
                    }
                }
            }
        }
    }
    
    $variants_items = array_values($variants_data);
    $total = count($variants_items);
} else {
    // Listado normal paginado si no hay búsqueda
    $response = bsale_api_request("/variants.json?limit={$limit}&offset={$offset}&expand=[product,stocks]", 'GET', null, $company_key);
    $variants_items = $response['data']['items'] ?? [];
    $total = $response['data']['count'] ?? 0;
}

// 1. Obtener información de la MONEDA de la lista de precios
$pl_response = bsale_api_request("/price_lists/{$price_list_id}.json", 'GET', null, $company_key);
$coin_symbol = '$';
$coin_name = 'CLP';
if ($pl_response['success'] && isset($pl_response['data']['coin'])) {
    $coin_id = $pl_response['data']['coin']['id'];
    $pl_name = $pl_response['data']['name'] ?? '';
    
    // Heurística: Si el nombre de la lista contiene "UF", forzamos UF (Incluso si BSale dice que es $)
    if (stripos($pl_name, 'UF') !== false) {
        $coin_symbol = 'UF';
        $coin_name = 'UF';
    } else {
        $coin_res = bsale_api_request("/coins/{$coin_id}.json", 'GET', null, $company_key);
        if ($coin_res['success']) {
            $coin_symbol = $coin_res['data']['symbol'] ?? '$';
            $coin_name = $coin_res['data']['name'] ?? 'CLP';
        }
    }
}

// 2. Mapeo de PRECIOS (Paginado porque BSale limita a 50)
$price_map = [];
$p_offset = 0;
while (true) {
    $price_response = bsale_api_request("/price_lists/{$price_list_id}/details.json?limit=50&offset={$p_offset}", 'GET', null, $company_key);
    if (!$price_response['success'] || empty($price_response['data']['items'])) break;
    
    foreach ($price_response['data']['items'] as $pi) {
        $vid = $pi['variant']['id'];
        $price_map[$vid] = $pi['variantValue'];
    }
    
    if (count($price_response['data']['items']) < 50) break;
    $p_offset += 50;
    if ($p_offset > 2000) break; // Seguridad
}

// 3. Mapeo de STOCK (Paginado)
$stock_map = [];
$s_offset = 0;
while (true) {
    $stock_response = bsale_api_request("/stocks.json?limit=50&offset={$s_offset}", 'GET', null, $company_key);
    if (!$stock_response['success'] || empty($stock_response['data']['items'])) break;
    
    foreach ($stock_response['data']['items'] as $si) {
        $vid = $si['variant']['id'];
        if (!isset($stock_map[$vid])) $stock_map[$vid] = 0;
        $stock_map[$vid] += $si['quantity'];
    }
    
    if (count($stock_response['data']['items']) < 50) break;
    $s_offset += 50;
    if ($s_offset > 2000) break; // Seguridad
}

// 4. Mapeo de TIPOS DE PRODUCTO (Para mostrar "Líquidos y alimentos", etc)
$type_map = [];
$types_res = bsale_api_request("/product_types.json", 'GET', null, $company_key);
if ($types_res['success'] && isset($types_res['data']['items'])) {
    foreach ($types_res['data']['items'] as $type) {
        $type_map[$type['id']] = $type['name'];
    }
}

$formatted_products = [];

foreach ($variants_items as $variant) {
    $product = $variant['product'] ?? [];
    $vid = $variant['id'];
    
    // Usar el mapa de stock en lugar de la expansión
    $final_stock = $stock_map[$vid] ?? 0;
    $final_price = $price_map[$vid] ?? 0;
    
    // Obtener nombre del tipo
    $type_id = $product['product_type']['id'] ?? 0;
    $type_name = $type_map[$type_id] ?? 'Sin Tipo';
    
    $formatted_products[] = [
        'id' => $vid,
        'product_id' => $product['id'] ?? 0,
        'name' => $product['name'] ?? 'S/N',
        'variant_description' => $variant['description'] ?? '',
        'full_name' => ($product['name'] ?? '') . ' ' . ($variant['description'] ?? ''),
        'code' => $variant['code'] ?? 'N/A',
        'price' => $final_price,
        'tax_included_price' => $final_price * 1.19,
        'stock' => $final_stock,
        'state' => $variant['state'] == 0 ? 'Activo' : 'Inactivo',
        'link' => "https://app.bsale.cl/products/edit/" . ($product['id'] ?? 0),
        'classification' => $product['classification'] ?? 0, // 0: Prod, 1: Serv, 2: Pack
        'type_name' => $type_name
    ];
}

echo json_encode([
    'success' => true,
    'products' => $formatted_products,
    'total' => $total,
    'count' => count($formatted_products),
    'currency' => [
        'symbol' => $coin_symbol,
        'name' => $coin_name
    ]
]);
?>
