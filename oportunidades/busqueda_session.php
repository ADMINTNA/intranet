<?php
// ==========================================================
// /intranet/tareas/sort/index.php
// Muestra el resultado de la busqueda de tareas
// Autor: Mauricio Araneda
// Fecha: 2025-11-18
// Codificación: UTF-8 sin BOM
// ==========================================================

// ⚠️ IMPORTANTE: NADA DE HTML ANTES DE ESTO
session_name('icontel_intranet_sess');
session_start();

header('Content-Type: text/html; charset=utf-8');
require_once "includes/functions.php";
gestionarDebugMode();

if (isset($_POST['numero'])) {
    $numero = $_POST['numero'];
}

$cuales = '';

if (!empty($numero)) {
    $cuales = " && oc.numero_oportunidad_c = '" . $numero . "'";
} else {
    if (isset($_POST['asunto'])) $asunto = $_POST['asunto'];
    if (isset($_POST['cliente'])) $cliente = $_POST['cliente'];
    if (isset($_POST['ejecutivo'])) $ejecutivo = $_POST['ejecutivo'];
    if (isset($_POST['estado'])) $estado = $_POST['estado'];

    if (!empty($asunto)) $cuales .= " && op.name like '%" . $asunto . "%'";
    if (!empty($cliente)) $cuales .= " && ac.name like '%" . $cliente . "%'";
    if (!empty($ejecutivo)) $cuales .= " && us.user_name like '%" . $ejecutivo . "%'";
    if (!empty($estado)) $cuales .= " && op.sales_stage like '%" . $estado . "%'";
}

$sql = "SELECT 
        oc.numero_oportunidad_c AS op_numero,
        op.name AS op_nombre,
        us.first_name AS u_nombre,
        us.last_name AS u_apellido,
        ac.name AS op_cliente,
        op.date_entered AS op_fecha,
        op.id AS op_id,
        CASE
            WHEN op.sales_stage = 'Prospecting' THEN '00 Prospecto'
            WHEN op.sales_stage = 'Levantamiento' THEN '01 Levantamiento'
            WHEN op.sales_stage = 'proyectodemo' THEN '02 Proyecto DEMO'
            WHEN op.sales_stage = 'esperafact' THEN '02 Esperando Factibilidad'
            WHEN op.sales_stage = 'Value Proposition' THEN '03 Cotizar'
            WHEN op.sales_stage = 'ReCotizar' THEN '03 Recotizar'
            WHEN op.sales_stage = 'Proposal/Price Quote' THEN '04 Seguimiento'
            WHEN op.sales_stage = 'Facturarprepago' THEN '05 Facturar Abono/Prepago'
            WHEN op.sales_stage = 'Estatusfinanciero' THEN '05 Verificar Estatus Financiero'
            WHEN op.sales_stage = 'Firmar_Contrato' THEN '05 Firmar Contrato o Anexo'
            WHEN op.sales_stage = 'AceptadoCliente' THEN '05 Aceptado Cliente'
            WHEN op.sales_stage = 'Escalado' THEN '05 ESCALADO URGENTE'
            WHEN op.sales_stage = 'Pre_Instalacion' THEN '06 Pre-Instalación'
            WHEN op.sales_stage = 'pendiente_enlace' THEN '06 Pendiente Enlace / Proveedor'
            WHEN op.sales_stage = 'Proyecto' THEN '07 Instalación'
            WHEN op.sales_stage = 'Renovacion' THEN '07 Renovación'
            WHEN op.sales_stage = 'Recepcion' THEN '07 Solicitar recepción conforme'
            WHEN op.sales_stage = 'Cerrar_a_Fin_de_Mes' THEN '08 Cerrar a Fin de Mes'
            WHEN op.sales_stage = 'Facturacion' THEN '09 Instalado - Generar NV'
            WHEN op.sales_stage = 'facturar' THEN '10 Listo para Facturar'
            WHEN op.sales_stage = 'Facturado_bienvenida' THEN '11 Facturado + Bienvenida'
            WHEN op.sales_stage = 'Facturado' THEN '11 Facturado/Cerrado'
            WHEN op.sales_stage = 'Waiting' THEN '12 PAUSA En Espera de Cliente'
            WHEN op.sales_stage = 'Archivado_Ventas' THEN '12 Archivado Ventas'
            WHEN op.sales_stage = 'Needs Analysis' THEN '12 Pausa en Análisis'
            WHEN op.sales_stage = 'duplicada_reemplazada' THEN '13 Duplicada / Reemplazada'
            WHEN op.sales_stage = 'Closed Lost' THEN '13 Perdido o Descartado'
            WHEN op.sales_stage = 'Dado_de_Baja' THEN '14 Servicio Dado de Baja'
            ELSE 'Error en Estado'
        END AS op_estado
        FROM opportunities AS op
        LEFT JOIN opportunities_cstm AS oc ON oc.id_c = op.id
        LEFT JOIN users AS us ON us.id = op.assigned_user_id
        LEFT JOIN accounts_opportunities AS ao ON op.id = ao.opportunity_id
        LEFT JOIN accounts AS ac ON ac.id = ao.account_id
        WHERE 1
        AND NOT op.deleted
        AND NOT us.deleted
        AND NOT ao.deleted
        AND NOT ac.deleted";

session_start();
session_unset();
$_SESSION["query_op"] = $sql . $cuales;

// Redirección limpia sin output previo
header('Location: ./sort/index.php');
exit;
