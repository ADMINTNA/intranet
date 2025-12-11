<?php
// File: /api_sweet/lead.php
include_once('/includes/utils.php'); // archivo con funciones comunes como autenticación

header('Content-Type: application/json');

// Verifica método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Captura input
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['name'], $input['email'], $input['company'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan campos obligatorios: name, email, company']);
    exit;
}

// Logging básico
if (!is_dir('logs')) mkdir('logs');
$logEntry = date('c') . " | GPT | Create Lead | " . json_encode($input) . "\n";
file_put_contents('logs/activity.log', $logEntry, FILE_APPEND);

// Obtener token CRM
$token = get_crm_token();
if (!$token) {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo autenticar con SuiteCRM']);
    exit;
}

// Crear lead en SuiteCRM
$payload = [
    'data' => [
        'type' => 'Leads',
        'attributes' => [
            'first_name' => $input['name'],
            'email1' => $input['email'],
            'phone_work' => $input['phone'] ?? '',
            'description' => $input['requirement'] ?? '',
            'account_name' => $input['company'],
            'status' => 'New'
        ]
    ]
];

$ch = curl_init('https://sweet.icontel.cl/Api/V8/module');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($httpCode);
echo $response;
