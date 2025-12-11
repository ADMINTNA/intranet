<?php include "config.php";
    // activo mostrar errores
   //  error_reporting(E_ALL);
   //  ini_set('display_errors', '1');
session_start();
$sql = "
SELECT 
	CONCAT('https://sweet.icontel.cl/index.php?module=AOS_Quotes&action=DetailView&record=',aq.id) as url_coti,
    CONCAT('https://sweet.icontel.cl/index.php?module=Opportunities&action=DetailView&record=', op.id) AS url_op,
    CONCAT('https://sweet.icontel.cl/index.php?module=Cases&action=DetailView&record=', cs.id) AS url_caso,
    aq.id AS cot_id,
    aq.number AS cot_numero,
    aq.name AS titulo,
    u.user_name AS asignado_a,
    ac.name AS cliente,
    aq.date_entered AS fecha_creacion,
    aqc.num_dte__compra_c AS dte,
    op.id AS op_id,
    opc.numero_oportunidad_c AS op_numero,
    cs.id AS caso_id,
    cs.case_number AS caso_numero,
    apq.name AS producto,
    CASE 
        WHEN aq.opportunity_id IS NOT NULL AND aq.opportunity_id != '' THEN 'Oportunidad'
        WHEN aqc.acase_id_c IS NOT NULL AND aqc.acase_id_c != '' THEN 'Caso'
        ELSE 'Sin relación'
    END AS modulo_origen,
    CASE
        WHEN op.sales_stage = 'Prospecting' THEN 'Prospecto'
        WHEN op.sales_stage = 'Levantamiento' THEN 'Levantamiento'
        WHEN op.sales_stage = 'Pre_venta' THEN 'Pre Venta'
        WHEN op.sales_stage = 'proyectodemo' THEN 'Proyecto Demo'
        WHEN op.sales_stage = 'esperafact' THEN 'Esperando Factibilidad'
        WHEN op.sales_stage = 'Value Proposition' THEN 'Cotizar'
        WHEN op.sales_stage = 'ReCotizar' THEN 'Re Cotizar'
        WHEN op.sales_stage = 'Proposal/Price Quote' THEN 'Seguimiento'
        WHEN op.sales_stage = 'Facturarprepago' THEN 'Facturar Abono/PrePago'
        WHEN op.sales_stage = 'Estatusfinanciero' THEN 'Verificar estatus Financiero'
        WHEN op.sales_stage = 'Firmar_Contrato' THEN 'Firmar Contrato'
        WHEN op.sales_stage = 'AceptadoCliente' THEN 'Aceptado Cliente'
        WHEN op.sales_stage = 'Escalado' THEN 'Escalado Urgente'
        WHEN op.sales_stage = 'Pre_Instalacion' THEN 'Pre Instalación Cliente Nuevo'
        WHEN op.sales_stage = 'Pre_Instalacioncliente' THEN 'Pre Instalación Cliente Existente'
        WHEN op.sales_stage = 'pendiente_enlace' THEN 'Pendiente de Enlace'
        WHEN op.sales_stage = 'Proyecto' THEN 'En Instalación'
        WHEN op.sales_stage = 'Renovacion' THEN 'Renovación'
        WHEN op.sales_stage = 'Recepcion' THEN 'Solicitar Recepción Conforme'
        WHEN op.sales_stage = 'Cerrar_a_Fin_de_Mes' THEN 'Cerrar a Fin de Mes'
        WHEN op.sales_stage = 'Facturacion' THEN 'Generar Nota de Venta'
        WHEN op.sales_stage = 'facturar' THEN 'Lista para Facturar'
        WHEN op.sales_stage = 'Facurado' THEN 'Facturado Cerrado'
        WHEN op.sales_stage = 'Waiting' THEN 'En Espera de CLIENTE'
        WHEN op.sales_stage = 'dupliacada_reemplazada' THEN 'Duplicada / Reemplazada'
        WHEN op.sales_stage = 'Closed Lost' THEN 'Perdido / Descartado'
        WHEN op.sales_stage = 'Dado_de_Baja' THEN 'Servicio Dado de Baja'
        ELSE op.sales_stage
    END AS etapa_venta,
    CASE
        WHEN aqc.etapa_cotizacion_c = 'Borrador_cot' THEN 'Borrador'
        WHEN aqc.etapa_cotizacion_c = 'negociacion_cot' THEN 'Negociación'
        WHEN aqc.etapa_cotizacion_c = 'esperando_prov_cot' THEN 'Esperando Proveedor'
        WHEN aqc.etapa_cotizacion_c = 'entregada_cot' THEN 'Cotización Entregada'
        WHEN aqc.etapa_cotizacion_c = 'espera_cliente_cot' THEN 'En Espera de Cliente'
        WHEN aqc.etapa_cotizacion_c = 'remplazada_cot' THEN 'Reemplazada'
        WHEN aqc.etapa_cotizacion_c = 'reemplazada_final' THEN 'Reemplazada Final'
        WHEN aqc.etapa_cotizacion_c = 'cerrado_aceptado_cot' THEN 'Cerrado Recurrente Mensual'
        WHEN aqc.etapa_cotizacion_c = 'Cerrado_aceptado_anual_cot' THEN 'Cerrado Recurrente Anual'
        WHEN aqc.etapa_cotizacion_c = 'cerrado_aceptado_cli' THEN 'Cerrado Recurrente Bienal' 
        WHEN aqc.etapa_cotizacion_c = 'cerrado_aceptado' THEN 'Cerrado Aceptado Única'
        WHEN aqc.etapa_cotizacion_c = 'posible_traslado' THEN 'Posible Traslado'
        WHEN aqc.etapa_cotizacion_c = 'en_traslado' THEN 'En Traslado'
        WHEN aqc.etapa_cotizacion_c = 'Suspender' THEN 'Suspender'
        WHEN aqc.etapa_cotizacion_c = 'gasto' THEN 'Gasto'
        WHEN aqc.etapa_cotizacion_c = 'generar_baja' THEN 'Generar Baja'
        WHEN aqc.etapa_cotizacion_c = 'cambio_razon_social' THEN 'Cambio Razón Social'
        WHEN aqc.etapa_cotizacion_c = 'no_renovado' THEN 'No Renovado'
        WHEN aqc.etapa_cotizacion_c = 'cerrado_perdido_cot' THEN 'Cerrado Perdido / Descartado'
        WHEN aqc.etapa_cotizacion_c = 'suspendido' THEN 'Suspendido'
        WHEN aqc.etapa_cotizacion_c = 'de_baja' THEN 'De Baja'
        WHEN aqc.etapa_cotizacion_c = 'guia_oc_cli' THEN 'Guía de Despacho/Recepción'
        WHEN aqc.etapa_cotizacion_c = 'orden_compra' THEN 'Orden de Compra'
        ELSE aqc.etapa_cotizacion_c
    END AS etapa_cot,
    CASE
        WHEN cu.symbol = 'USD' THEN 'USD'
        WHEN cu.symbol = '$' THEN '$'
        ELSE 'UF'
    END AS moneda,

    ROUND(aq.subtotal_amount, 2) AS neto

FROM aos_quotes aq
    INNER JOIN aos_products_quotes apq ON apq.parent_id = aq.id
    LEFT JOIN users u ON u.id = aq.assigned_user_id
    LEFT JOIN accounts ac ON ac.id = aq.billing_account_id
    LEFT JOIN aos_quotes_cstm aqc ON aqc.id_c = aq.id
    LEFT JOIN currencies cu ON cu.id = aq.currency_id
    LEFT JOIN opportunities op ON op.id = aq.opportunity_id
    LEFT JOIN opportunities_cstm opc ON opc.id_c = op.id
    LEFT JOIN cases cs ON cs.id = aqc.acase_id_c

WHERE aq.deleted = 0
    AND apq.deleted = 0";   
    $_SESSION["query_cotizacion"] = $sql.$_SESSION["cuales"]." GROUP BY aq.number ";   
?>
<!doctype html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
    <?PHP include_once("../../meta_data/meta_data.html"); ?>
    <title>Buscador Oportunidades iContel</title>
        <link href='style.css' rel='stylesheet' type='text/css'>
        <script src='jquery-3.3.1.min.js' type='text/javascript'></script>
        <script src='script.js' type='text/javascript'></script>
     <style type="text/css">
        table {
               border: none;
               color: #1F1D3E;
               color: black;
               font-size: 10px;
               border-collapse: collapse;
           }   
          th, td {
              padding: 4px;
              font-size: 12px;
         }
         th {
            background-color: #1F1D3E; 
            color: white;
         }
         body{
            margin:0;
            padding:0px;
            margin-left: 0px;
            margin-top: 0px;
            margin-right: 0px;
            margin-bottom: 0px;
            font-size: 10px;
            background-color: #FFFFFF;
            color: #1F1D3E;
        }
        table tbody tr:nth-child(odd) {
            background: #F6F9FA;
        }
        table tbody tr:nth-child(even) {
            background: #FFFFFF;
        }
        table thead {
          background: #444;
          color: #fff;
          font-size: 18px;
        }
        table {
          border-collapse: collapse;
        }            
    </style>
    </head>
    <body bgcolor="#FFFFFF" text="#1F1D3E" link="#E95300" >
        <div class='container'>
            <input type='hidden' id='sort' value='asc'>
            <table width='100%' id='empTable' border='1' cellpadding='10'>
                <tr>
                    <th>#</span></th>
                    <th><span onclick='sortTable("aq.number");'>Coti N°</span></th>
                    <th><span onclick='sortTable("aq.name");'>Titulo</span></th>
                    <th><span onclick='sortTable("ac.name");'>Cliente / Proveedor</span></th>
                    <th><span onclick='sortTable("apq.name");'>Producto</span></th>
                    <th><span onclick='sortTable("aqc.num_dte__compra_c");'>DTE</span></th>
                    <th><span onclick='sortTable("opc.numero_oportunidad_c");'>OP N°</span></th>
                    <th><span onclick='sortTable("op.sales_stage");'>OP Estado</span></th>
                    <th><span onclick='sortTable("cs.case_number");'>Caso N°</span></th>            
                    <th><span onclick='sortTable("cu.symbol");'>Moneda</span></th>
                    <th><span onclick='sortTable("aq.subtotal_amount");'>Neto</span></th>
                    <th><span onclick='sortTable("u.user_name");'>Usuario</span></th>
                    <th><span onclick='sortTable("aqc.etapa_cotizacion_c");'>Etapa de Ventas</span></th>
                    <th><span onclick='sortTable("aq.date_entered");'>Fecha_Creación</a></th>
                </tr>
                <?php 
                   $query =  $_SESSION["query_cotizacion"]." ORDER BY aq.number DESC";
                    $conn = DbConnect("tnasolut_sweet");
                    $result = mysqli_query($conn,$query);
                    $ptr = 0;    
                    while($row = mysqli_fetch_array($result)){
                        $ptr ++; 
						$url_coti 		= $row["url_coti"];
						$url_op 		= $row["url_op"];
                        $url_caso       = $row["url_caso"];
                        $numero         = $row["cot_numero"];
                        $caso_num       = $row["caso_numero"];
                        $titulo         = $row['titulo'];
                        $producto       = $row['producto'];
                        $cliente        = $row['cliente'];
                        $dte       		= $row['dte'];
                        $op_num       	= $row['op_numero'];
                        $etapa_venta    = $row['etapa_venta'];
                        $moneda       	= $row['moneda'];
						$neto			= $row['neto'];
						$asignado_a		= $row['asignado_a'];
                        $etapa 			= $row['etapa_cot']; 
                        $fecha          = fechacl($row['fecha_creacion']);           
                        ?>
                        <tr>
                            <td><?php echo $ptr; ?></td>
                            <td><a target="_blank" href="<?php echo $url_coti; ?>"><?php echo $row['cot_numero']; ?></a></td>
                            <td><?php echo $titulo; ?></td>
                            <td><?php echo $cliente; ?></td>
                            <td><?php echo $producto; ?></td>
                            <td><?php echo $dte; ?></td>
                            <td><a target="_blank" href="<?php echo $url_op; ?>"><?php echo $op_num; ?></a></td>
                            <td><?php echo $etapa_venta; ?></td>
                            <td><a target="_blank" href="<?php echo $url_caso; ?>"><?php echo $caso_num; ?></a></td>
                            <td align="center"><?php echo $moneda; ?></td>
                            <td align="right"><?php echo number_format($neto, 2); ?></td>
                            <td ><?php echo $asignado_a; ?></td>
                            <td><?php echo $etapa; ?></td>
                            <td align="center"><?php echo $fecha; ?></td>
                        </tr>
                <?php
                }
                ?>
            </table><br><br>
        </div>
    </body>
</html>