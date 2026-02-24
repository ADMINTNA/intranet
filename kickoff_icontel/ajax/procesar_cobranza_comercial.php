<?php
// ==============================================================
// /kickoff/ajax/procesar_cobranza_comercial.php
// Ejecuta SP + actualiza estatusfinanciero_c automáticamente
// Se llama desde el icono 🔄 en Cobranza Comercial
// Autor: Mauricio Araneda
// Fecha: 2025-12 (Actualizado)
// ==============================================================

header("Content-Type: application/json; charset=UTF-8");
mb_internal_encoding("UTF-8");

require_once "../config.php";

// ==========================================================
// CONEXIONES A BASE DE DATOS
// ==========================================================

// Conexión a icontel_clientes (para el SP)
$connClientes = DbConnect($db_clientes);
if (!$connClientes) {
    echo json_encode(["ok" => false, "msg" => "Error de conexión con icontel_clientes"]);
    exit;
}
$connClientes->set_charset("utf8mb4");

// Conexión a Sweet (para las actualizaciones)
$connSweet = DbConnect($db_sweet);
if (!$connSweet) {
    $connClientes->close();
    echo json_encode(["ok" => false, "msg" => "Error de conexión con Sweet"]);
    exit;
}
$connSweet->set_charset("utf8mb4");

// ==========================================================
// ESTADOS QUE **NO DEBEN SER CAMBIADOS**
// Según requisitos: "Suspendido" y "Acuerdo cobranza comercial"
// ==========================================================
$estadosBloqueados = [
    "suspendido",
    "Suspendido",
    "acuerdo_cobranza_comer"
];

// ==========================================================
// 1. Ejecutar SP desde icontel_clientes
// CALL icontel_clientes.search_by_status_min_docs(3, 0, '', '', 60)
// Retorna clientes con facturas vencidas >= 60 días
// ==========================================================
$sql = "CALL icontel_clientes.search_by_status_min_docs(3, 0, '', '', 60)";
$res = $connClientes->query($sql);

if (!$res) {
    echo json_encode([
        "ok" => false,
        "msg" => "Error al ejecutar SP: " . $connClientes->error
    ]);
    $connClientes->close();
    $connSweet->close();
    exit;
}

$procesadas = 0;
$omitidas   = 0;
$errores    = 0;

while ($row = $res->fetch_assoc()) {

    // El SP ya retorna id_cuenta directamente
    $idCuenta = $row["id_cuenta"] ?? "";
    if (!$idCuenta) continue;

    // El SP también retorna el estado financiero actual
    $estadoActual = strtolower(trim($row["estado_financiero_sweet"] ?? ""));

    // ------------------------------------------------------
    // Verificar si el estado está bloqueado
    // Estados que NO deben cambiarse: Suspendido, acuerdo_cobranza_comer
    // ------------------------------------------------------
    $bloqueado = false;
    foreach ($estadosBloqueados as $eb) {
        if (strtolower($eb) === $estadoActual) {
            $bloqueado = true;
            break;
        }
    }

    if ($bloqueado) {
        $omitidas++;
        continue;
    }

    // Evitar actualización innecesaria si ya está en cobranza_comercial
    if ($estadoActual === "cobranza_comercial") {
        $omitidas++;
        continue;
    }

    // ------------------------------------------------------
    // Actualizar estado a cobranza_comercial
    // ------------------------------------------------------
    $sqlUpd = "
        UPDATE tnasolut_sweet.accounts AS ac
        JOIN tnasolut_sweet.accounts_cstm AS acc ON acc.id_c = ac.id
        SET 
            acc.estatusfinanciero_c = 'cobranza_comercial',
            ac.date_modified = NOW()
        WHERE ac.id = ?
    ";
    
    $stmtUpd = $connSweet->prepare($sqlUpd);
    if (!$stmtUpd) {
        $errores++;
        continue;
    }
    
    $stmtUpd->bind_param("s", $idCuenta);
    $ok = $stmtUpd->execute();
    $stmtUpd->close();

    if ($ok) {
        $procesadas++;
    } else {
        $errores++;
    }
}

$connClientes->close();
$connSweet->close();

// ==========================================================
// RESPUESTA JSON
// ==========================================================
echo json_encode([
    "ok"         => true,
    "procesadas" => $procesadas,
    "omitidas"   => $omitidas,
    "errores"    => $errores
]);
exit;
