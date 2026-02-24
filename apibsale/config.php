<?php
/**
 * Configuración para el módulo de BSale API
 */

// Configuración de empresas BSale
define('BSALE_COMPANIES', [
    'tna_office' => [
        'name' => 'TNA Office',
        'token' => '9c54f0b10d15274ec67b08363e6964a9e3474543',
        'price_list_id' => 6
    ],
    'tna_solutions' => [
        'name' => 'TNA Solutions',
        'token' => '21e9099aa5876d8159f54ec103335973b7b8146b',
        'price_list_id' => 4 // Lista de Precios Base
    ]
]);

define('BSALE_API_URL', 'https://api.bsale.io/v1');

// Configuración general
date_default_timezone_set('America/Santiago');

/**
 * Función para realizar peticiones a la API de BSale
 * 
 * @param string $endpoint El endpoint de la API (ej: '/products.json')
 * @param string $method Método HTTP (GET, POST, PUT)
 * @param array|null $data Datos para enviar en el cuerpo de la petición
 * @param string|null $company_key Key de la empresa en BSALE_COMPANIES
 * @return array Respuesta decodificada de la API
 */
function bsale_api_request($endpoint, $method = 'GET', $data = null, $company_key = 'tna_office') {
    $company = BSALE_COMPANIES[$company_key] ?? BSALE_COMPANIES['tna_office'];
    $url = BSALE_API_URL . $endpoint;
    
    $ch = curl_init($url);
    
    $headers = [
        'access_token: ' . $company['token'],
        'Accept: application/json',
        'Content-Type: application/json'
    ];
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    if ($error) {
        return [
            'success' => false,
            'error' => 'cURL Error: ' . $error
        ];
    }
    
    $result = json_decode($response, true);
    
    if ($http_code >= 400) {
        return [
            'success' => false,
            'error' => $result['error'] ?? 'API Error status code: ' . $http_code,
            'http_code' => $http_code
        ];
    }
    
    return [
        'success' => true,
        'data' => $result,
        'http_code' => $http_code
    ];
}

/**
 * Formatea un precio como moneda chilena
 */
function format_price($amount) {
    return '$' . number_format($amount, 0, ',', '.');
}
?>
