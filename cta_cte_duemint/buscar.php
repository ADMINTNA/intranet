<?php
// ==========================================================
// /intranet/cta_cte_duemint/buscar.php
// Backend de búsqueda de cuenta corriente en Duemint por RUT
// ==========================================================

header('Content-Type: application/json; charset=utf-8');
require_once "config.php";

// Obtener RUT desde POST o GET
$rut = $_POST['rut'] ?? $_GET['rut'] ?? '';

if (empty($rut)) {
    echo json_encode([
        'success' => false,
        'error' => 'RUT no proporcionado'
    ]);
    exit;
}

// Limpiar RUT
$rut = limpiarRut($rut);

// Conectar a base de datos
$conn = DbConnecta("tnaoffice_clientes");

// Escapar RUT para prevenir inyección SQL
$rutEscaped = $conn->real_escape_string($rut);

// Ejecutar stored procedure
$sql = "CALL tnaoffice_clientes.searchbyrut('{$rutEscaped}')";
$result = $conn->query($sql);

// Inicializar variables de respuesta
$dumit_pagada = 0;
$dumit_por_vencer = 0;
$dumit_vencida = 0;
$dumit_portal = "https://www.duemint.com";
$encontrado = false;

if ($result && $result->num_rows > 0) {
    $encontrado = true;
    
    while ($col = $result->fetch_assoc()) {
        switch ($col["estado"]) {
            case 1:
                $dumit_pagada = $col["monto"];
                break;
            case 2:
                $dumit_por_vencer = $col["monto"];
                break;
            case 3:
                $dumit_vencida = $col["monto"];
                break;
        }
        
        // Obtener URL del portal si existe
        if (isset($col["url_cliente"]) && !empty($col["url_cliente"])) {
            $dumit_portal = $col["url_cliente"];
        }
    }
}

// Cerrar conexión
$conn->close();

// Retornar respuesta JSON
echo json_encode([
    'success' => true,
    'encontrado' => $encontrado,
    'rut' => $rut,
    'datos' => [
        'pagada' => $dumit_pagada,
        'por_vencer' => $dumit_por_vencer,
        'vencida' => $dumit_vencida,
        'portal_url' => $dumit_portal
    ]
]);
?>
