<?php
/**
 * Cron de sincronización de documentos Bsale (TODOS los registros)
 * Inserta o actualiza en tabla `bsale`
 */

ini_set('memory_limit', '512M');
set_time_limit(0);

// Credenciales Bsale
$url = 'https://api.bsale.io/v1/';
$token = '65d4fcedb5a2ce6d2dcb6f74d0bbea72918dbc81';

// Conexión BD
$conn = new mysqli('localhost', 'data_studio', '1Ngr3s0.,', 'tnasolut_sweet');
if ($conn->connect_error) {
    die("❌ Error de conexión BD: " . $conn->connect_error);
}

// Inicializar variables
$page = 1;
$perPage = 100;
$totalProcessed = 0;
$totalIterated = 0;

// Mapeo de tipos de documento
$tiposDocumento = [
    2 => 'Boleta Electrónica',
    3 => 'Nota de Crédito Electrónica',
    4 => 'Nota de Venta',
    5 => 'Factura Electrónica',
    7 => 'Factura Exportación Electrónica',
    17 => 'Guía de Despacho Electrónica',
    22 => 'Factura Afecta',
    27 => 'Factura de Compra Electrónica',
];

// Mapeo de estado
$estadoTexto = [
    0 => 'Emitido',
    1 => 'Anulado',
    2 => 'Borrador',
    3 => 'Rechazado por SII',
    4 => 'Aceptado por SII',
    5 => 'Rechazado por Cliente',
];

echo "🔄 Iniciando sincronización de TODOS los documentos...\n";

do {
    echo "📄 Solicitando página $page...\n";
    $offset = ($page - 1) * $perPage;
    $urlPage = $url . "documents.json?size=$perPage&offset=$offset&order=Id%20asc";
    $ch = curl_init($urlPage);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["access_token: $token"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        echo "❌ Error HTTP $httpCode: $response\n";
        break;
    }

    $data = json_decode($response, true);

    $numItems = isset($data['items']) ? count($data['items']) : 0;
    echo "ℹ️ Página $page contiene $numItems documentos.\n";

    if ($numItems === 0) {
        echo "✅ No hay más documentos.\n";
        break;
    }

    if ($page === 1) {
        $totalPages = $data['count'] > 0 ? ceil($data['count'] / $perPage) : 1;
        echo "ℹ️ Total estimado de páginas: $totalPages\n";
    }

    foreach ($data['items'] as $item) {
        $totalIterated++;

        $docId = $item['id'];
        $docNumber = $item['number'];
        $docTypeId = $item['document_type']['id'] ?? null;
        $docTypeName = $tiposDocumento[$docTypeId] ?? '(desconocido)';
        $state = $item['state'];
        $stateDesc = $estadoTexto[$state] ?? '(desconocido)';

        // Obtener detalle completo del documento
        $detailUrl = $url . "documents/$docId.json";
        $chDetail = curl_init($detailUrl);
        curl_setopt($chDetail, CURLOPT_HTTPHEADER, ["access_token: $token"]);
        curl_setopt($chDetail, CURLOPT_RETURNTRANSFER, true);
        $responseDetail = curl_exec($chDetail);
        curl_close($chDetail);
        $detail = json_decode($responseDetail, true);

        // Fechas
        $emissionDate = !empty($detail['emissionDate']) ? date('Y-m-d H:i:s', $detail['emissionDate']) : null;
        $expirationDate = !empty($detail['expirationDate']) ? date('Y-m-d H:i:s', $detail['expirationDate']) : null;
        $generationDate = !empty($detail['generationDate']) ? date('Y-m-d H:i:s', $detail['generationDate']) : null;

        // Cliente
        $clientId = isset($detail['client']['id']) ? (string)$detail['client']['id'] : null;

        echo "🔍 DEBUG CLIENTE ARRAY:\n";
        print_r($detail['client']);

        // Por defecto
        $clientName = '(sin cliente)';
        $clientRut = '(sin rut)';

        if (!empty($detail['client']['href'])) {
            // Hacemos un curl adicional al endpoint del cliente
            $clientUrl = $detail['client']['href'];
            $chClient = curl_init($clientUrl);
            curl_setopt($chClient, CURLOPT_HTTPHEADER, ["access_token: $token"]);
            curl_setopt($chClient, CURLOPT_RETURNTRANSFER, true);
            $responseClient = curl_exec($chClient);
            curl_close($chClient);
            $clientData = json_decode($responseClient, true);

            $clientName = $clientData['company'] ?? ($clientData['firstName'] ?? '(sin cliente)');
            $clientRut = $clientData['code'] ?? '(sin rut)';
        }

        // Montos
        $totalAmount = $detail['totalAmount'] ?? 0;
        $netAmount = $detail['netAmount'] ?? 0;
        $taxAmount = $detail['taxAmount'] ?? 0;
        $exemptAmount = $detail['exemptAmount'] ?? 0;
        $notExemptAmount = $detail['notExemptAmount'] ?? 0;

        // Ubicación
        $address = $detail['address'] ?? '';
        $municipality = $detail['municipality'] ?? '';
        $city = $detail['city'] ?? '';

        // URLs
        $urlPublicView = $detail['urlPublicView'] ?? '';
        $urlPdf = $detail['urlPdf'] ?? '';

        echo "--------------------------------------\n";
        echo "🔢 Iteración #: $totalIterated\n";
        echo "ID interno: $docId\n";
        echo "Número documento: $docNumber\n";
        echo "Tipo documento: $docTypeName (ID: $docTypeId)\n";
        echo "Estado: $state ($stateDesc)\n";
        echo "Fecha emisión: " . ($emissionDate ?: '(sin fecha)') . "\n";
        echo "Cliente: $clientName ($clientRut)\n";

        $stmt = $conn->prepare("
            INSERT INTO bsale (
                doc_id, num_doc, tipo_doc, tipo_doc_desc,
                estado, estado_desc,
                fecha_emision, fecha_expiracion, fecha_generacion,
                cliente_id, cliente_nombre, cliente_rut,
                direccion, comuna, ciudad,
                monto_total, monto_neto, monto_iva, monto_exento, monto_no_afecto,
                urlPublicView, urlPdf, fecha_actualizacion
            ) VALUES (
                ?, ?, ?, ?,
                ?, ?,
                ?, ?, ?,
                ?, ?, ?,
                ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, NOW()
            )
            ON DUPLICATE KEY UPDATE
                estado = VALUES(estado),
                estado_desc = VALUES(estado_desc),
                fecha_emision = VALUES(fecha_emision),
                fecha_expiracion = VALUES(fecha_expiracion),
                fecha_generacion = VALUES(fecha_generacion),
                cliente_id = VALUES(cliente_id),
                cliente_nombre = VALUES(cliente_nombre),
                cliente_rut = VALUES(cliente_rut),
                direccion = VALUES(direccion),
                comuna = VALUES(comuna),
                ciudad = VALUES(ciudad),
                monto_total = VALUES(monto_total),
                monto_neto = VALUES(monto_neto),
                monto_iva = VALUES(monto_iva),
                monto_exento = VALUES(monto_exento),
                monto_no_afecto = VALUES(monto_no_afecto),
                urlPublicView = VALUES(urlPublicView),
                urlPdf = VALUES(urlPdf),
                fecha_actualizacion = NOW()
        ");

        if (!$stmt) {
            echo "❌ Error prepare: " . $conn->error . "\n";
            continue;
        }

        $stmt->bind_param(
            "iiisisssssssssssddddss",
            $docId,
            $docNumber,
            $docTypeId,
            $docTypeName,
            $state,
            $stateDesc,
            $emissionDate,
            $expirationDate,
            $generationDate,
            $clientId,
            $clientName,
            $clientRut,
            $address,
            $municipality,
            $city,
            $totalAmount,
            $netAmount,
            $taxAmount,
            $exemptAmount,
            $notExemptAmount,
            $urlPublicView,
            $urlPdf
        );

        if (!$stmt->execute()) {
            echo "❌ Error insert/update: " . $stmt->error . "\n";
        } else {
            echo "✅ Documento $docNumber insertado o actualizado.\n";
            $totalProcessed++;
        }

        $stmt->close();
    }

    $page++;
} while (true);

$conn->close();
echo "✅ Sincronización COMPLETA.\n";
echo "🔢 Total documentos recorridos: $totalIterated\n";
echo "✅ Total insertados/actualizados: $totalProcessed\n";
?>