<?php
//=====================================================
// /intranet/casos/busqueda_casos.php
// Recibe datos desde formulario y redirige con query
// Autor: Mauricio Araneda
// Actualizado: 10-11-2025
//=====================================================

session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

// --- Iniciar armado del WHERE dinámico ---
$cuales = "";

// --- Campos individuales ---
if (!empty($_POST['numero']))        $cuales .= " && c.case_number = '" . addslashes($_POST['numero']) . "'";
if (!empty($_POST['categoria']))     $cuales .= " && cc.tipo_caso_c LIKE '%" . addslashes($_POST['categoria']) . "%'";
if (!empty($_POST['empresa']))       $cuales .= " && a.name LIKE '%" . addslashes($_POST['empresa']) . "%'";
if (!empty($_POST['codservicio']))   $cuales .= " && cc.codigo_servicio_c LIKE '%" . addslashes($_POST['codservicio']) . "%'";

// --- Estado (radio) ---
if (!empty($_POST['estado'])) {
    if ($_POST['estado'] === "cerrados") {
        $cuales .= " && c.state LIKE '%closed%'";
    } elseif ($_POST['estado'] === "abiertos") {
        $cuales .= " && c.state NOT LIKE '%closed%'";
    }
}

// --- Avances / descripción ---
if (!empty($_POST['texto_avance'])) {
    $texto_avance = htmlspecialchars(trim(str_replace("'", "", $_POST['texto_avance'])));
    $cuales .= " && (
        cc.avances_1_c LIKE '%$texto_avance%' OR
        cc.avances_2_c LIKE '%$texto_avance%' OR
        cc.avances_3_c LIKE '%$texto_avance%' OR
        cc.avances_4_c LIKE '%$texto_avance%' OR
        c.description   LIKE '%$texto_avance%'
    )";
}

// --- Query base ---
echo $sql = "
    SELECT 
        c.id as id,
        cc.responsable_c as responsable,
        cc.categoria_c as categoria,
        cc.proveedor_c as proveedor,
        c.case_number as numero,
        c.name as asunto,
        c.state as estado,
        a.name as cliente,
        c.date_entered as f_creacion,
        cc.codigo_servicio_c as codigo_servicio,
        IF(ISNULL(uu.first_name), uu.last_name, CONCAT(uu.first_name,' ',uu.last_name)) as creado_por,
        c.created_by as u_creation,
        c.date_modified as f_modifica,
        IF(c.state = 'Closed', TIMEDIFF(c.date_modified ,c.date_entered), TIMEDIFF(NOW(), c.date_entered)) as antiguedad,
        cc.horas_sin_servicio_c as horas,
        IF(ISNULL(u.first_name), u.last_name, CONCAT(u.first_name,' ',u.last_name)) as usuario
    FROM cases c
    JOIN tnasolut_sweet.cases_cstm cc ON cc.id_c = c.id
    JOIN tnasolut_sweet.accounts a ON a.id = c.account_id
    JOIN tnasolut_sweet.users u ON u.id = c.assigned_user_id
    JOIN tnasolut_sweet.users uu ON uu.id = c.created_by
    WHERE c.deleted = 0 
      AND a.deleted = 0 
      AND cc.categoria_c != 'Soporte_contrato_mensual'
      $cuales
";

// --- Guardar en sesión y redirigir ---
$_SESSION['query'] = $sql;
header("Location: ./sort/index.php");
exit;
?>
