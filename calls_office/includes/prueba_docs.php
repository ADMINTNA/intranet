<?php
// Incluye tu archivo de conexión y funciones
include_once("config.php");

// Conexión a la BD Sweet
$conn = DbConnect("tnasolut_sweet");

// Probar con la cotización N° 7273
$docs_info = busca_doc_de_contrato($conn, '7273');

// Mostrar resultado
if ($docs_info) {
    echo "<h3>✅ Documentos encontrados:</h3>";
    $i = 1;
    foreach ($docs_info as $doc) {
        echo "<div style='margin-bottom:10px;'>";
        echo "📄 <strong>Documento #$i</strong><br>";
        echo "Contrato: {$doc['contrato_nombre']}<br>";
        echo "Archivo: {$doc['archivo_nombre']}<br>";
        echo "Documento ID: {$doc['documento_id']}<br>";
        echo "URL descarga: <a href='{$doc['url_documento']}' target='_blank'>Descargar PDF</a>";
        echo "</div>";
        $i++;
    }
} else {
    echo "❌ No se encontraron documentos adjuntos a contratos para esta cotización.";
}

// Cerrar conexión
$conn->close();
?>