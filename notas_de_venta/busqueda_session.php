<?php
session_start();
session_unset();

// include_once("../../meta_data/meta_data.html");

if (isset($_POST['numero']))     $numero  = $_POST['numero'];    
if (isset($_POST['titulo']))     $titulo  = $_POST['titulo'];    
if (isset($_POST['cliente']))    $cliente = $_POST['cliente'];    
if (isset($_POST['usuario']))    $usuario = $_POST['usuario'];        

$cuales = "";
if (!empty($numero))   $cuales .= " AND aic.num_nota_venta1_c = ".$numero;
if (!empty($titulo))   $cuales .= " AND ai.name LIKE '%".$titulo."%'";
if (!empty($cliente))  $cuales .= " AND ac.name LIKE '%".$cliente."%'";

if (isset($_POST['tipofac']) && !empty($_POST['tipofac'])) {
    $tipofac = $_POST['tipofac'];
    $cuales .= " AND (ai.status LIKE '".$tipofac[0]."'";
    for ($i = 1; $i < count($tipofac); $i++) {
        $cuales .= " OR ai.status LIKE '".$tipofac[$i]."'";
    }
    $cuales .= ")";
}

if (isset($_POST['usuario']) && !empty($_POST['usuario'])) {
    $usuario = $_POST['usuario'];
    $cuales .= " AND (us.user_name LIKE '".$usuario[0]."'";
    for ($i = 1; $i < count($usuario); $i++) {
        $cuales .= " OR us.user_name LIKE '".$usuario[$i]."'";
    }
    $cuales .= ")";
}

$sql = "SELECT 
    concat('https://sweet.icontel.cl/index.php?module=AOS_Invoices&action=DetailView&record=', ai.id) AS fac_url,
    concat('https://sweet.icontel.cl/index.php?module=AOS_Quotes&action=DetailView&record=', aq.id) AS cot_url,
    concat('https://sweet.icontel.cl/index.php?module=Accounts&action=DetailView&record=', ac.id) AS cli_url, 
    aic.num_nota_venta1_c AS nv_num,
    ai.number             AS fac_num,
    ai.quote_number       AS cot_num,
    ai.invoice_date       AS fac_fecha,
    ai.name               AS descripcion,
    ac.name               AS cliente,
    CASE 
        WHEN ai.status = 'Facturado' THEN 'UNICA'
        WHEN ai.status = 'vigente' THEN 'MENSUAL'
        WHEN ai.status = 'Vigente_Anual' THEN 'ANUAL'
        WHEN ai.status = 'vigente_bienal' THEN 'BIENAL'
        WHEN ai.status = 'de_baja' THEN 'DE BAJA'
        WHEN ai.status = 'pendiente' THEN 'PENDIENTE'
        ELSE ai.status
    END AS fac_tipo,
    CASE 
        WHEN aqc.etapa_cotizacion_c = 'cerrado_aceptado' THEN 'UNICA'
        WHEN aqc.etapa_cotizacion_c = 'cerrado_aceptado_cot' THEN 'MENSUAL'
        WHEN aqc.etapa_cotizacion_c = 'cerrado_aceptado_cli' THEN 'BIENAL'
        WHEN aqc.etapa_cotizacion_c = 'cerrado_aceptado_anual_cot' THEN 'ANUAL'
        ELSE aqc.etapa_cotizacion_c
    END AS cot_estado,
    CASE 
        WHEN ai.currency_id = '-99' THEN 'UF'
        ELSE cu.symbol
    END AS moneda,
    ai.subtotal_amount    AS monto, 
    CONCAT_WS(' ', us.first_name, us.last_name) AS usuario
FROM tnasolut_sweet.aos_invoices_cstm           AS aic
JOIN tnasolut_sweet.aos_invoices                AS ai ON ai.id = aic.id_c
JOIN tnasolut_sweet.accounts                    AS ac ON ac.id = ai.billing_account_id
JOIN tnasolut_sweet.users                       AS us ON us.id = ai.assigned_user_id
JOIN tnasolut_sweet.aos_quotes                  AS aq ON aq.number = ai.quote_number
JOIN tnasolut_sweet.aos_quotes_cstm				AS aqc ON aqc.id_c = aq.id
LEFT JOIN currencies                            AS cu ON cu.id = ai.currency_id
WHERE ai.deleted = 0
      AND aq.deleted = 0
      AND aqc.etapa_cotizacion_c like '%cerrado_aceptado%'
";

$_SESSION["query"] = $sql . $cuales;

// REDIRECCIÓN ANTES DE IMPRIMIR CUALQUIER COSA
header('Location: ./sort/index.php');
exit(); // muy importante
?>