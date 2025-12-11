<?PHP
/**
 * Obtiene URLs de visualización y PDF de Bsale por número y tipo de documento
 * @param string $access_token
 * @param string $numeroDocumento
 * @param int $documentTypeId (por ejemplo: 4 para Nota de Venta)
 * @return array|null
 */
function obtenerUrlsBsalePorNumero($access_token, $numeroDocumento, $documentTypeId) {
    $url = "https://api.bsale.io/v1/documents.json?number={$numeroDocumento}&document_type={$documentTypeId}";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["access_token: {$access_token}"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        error_log("Error HTTP $httpCode al consultar documento Bsale: $response");
        return null;
    }

    $data = json_decode($response, true);

    if (!isset($data["items"]) || count($data["items"]) === 0) {
        // No encontró documento con ese número y tipo
        return null;
    }

    // Por seguridad, validamos que efectivamente sea el tipo correcto
    $document = $data["items"][0];
    if (!isset($document["document_type"]["id"]) || intval($document["document_type"]["id"]) !== intval($documentTypeId)) {
        // Tipo de documento distinto
        return null;
    }

    return [
        "urlPublicView" => $document["urlPublicView"] ?? null,
        "urlPdf" => $document["urlPdf"] ?? null,
        "urlPublicViewOriginal" => $document["urlPublicViewOriginal"] ?? null,
        "urlPdfOriginal" => $document["urlPdfOriginal"] ?? null,
        "urlXml" => $document["urlXml"] ?? null
    ];
}
?>