<?php
// ==========================================================
// KickOff AJAX – Oportunidades en DEMO
// /kickoff_icontel/cm_oportunidades_en_Demo.php
// Autor: Mauricio Araneda (mAo)
// Fecha: 2025-12
// Codificación: UTF-8 sin BOM
// ==========================================================

mb_internal_encoding("UTF-8");

// ------------------------------------------------------
// Cargar bootstrap AJAX (sesión + $sg_id + DbConnect)
// ------------------------------------------------------
require_once __DIR__ . "/ajax_bootstrap.php";

// Seguridad mínima
if ($sg_id === "" || $sg_name === "") {
    echo "<div style='padding:20px; color:red;'>❌ Error: sesión inválida.</div>";
    exit;
}

// ------------------------------------------------------
// Conexión a Sweet
// ------------------------------------------------------
$conn = DbConnect($db_sweet);
$conn->set_charset("utf8mb4");

// Procedimiento almacenado
$sql = "CALL Oportunidades_en_Demo()";
$resultado = $conn->query($sql);

$ptr       = 0;
$contenido = "";
$muestra   = ($resultado && $resultado->num_rows > 0);

// ------------------------------------------------------
// Procesar resultados
// ------------------------------------------------------
if ($muestra) {

    while ($lin = $resultado->fetch_assoc()) {

        $ptr++;

        // ===============================
        //  DEFINICIÓN DE COLOR
        // ===============================
        switch ($lin["estado"]) {

            case "1 Escalado Urgente":
            case "Proyecto Demo":
                $color = "color:red;";
                break;

            case "2 Aceptadado, listo para Instalar":
            case "4 Pre Instalación":
                $color = "color:orangered;";
                break;

            case "3 Generar NV":
                $color = "color:orange;";
                break;

            case "Cotizar":
                $color = "color:green;";
                break;

            default:
                $color = "color:#333;";
        }

        // ===============================
        //  FILA – Seguro + Limpio
        // ===============================
        $contenido .= "<tr style='{$color}'>";

        $contenido .= "<td>{$ptr}</td>";

        $contenido .= '<td colspan="2"><a target="_blank" href="' .
            htmlspecialchars($lin["url_opor"]) . '">' .
            htmlspecialchars($lin["nombre"]) . '</a></td>';

        $contenido .= "<td>" . htmlspecialchars($lin["numero"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["cliente"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["estado"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["asignado"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["f_creacion"]) . "</td>";
        $contenido .= "<td>" . htmlspecialchars($lin["f_modifica"]) . "</td>";
        $contenido .= "<td align='right'>" . htmlspecialchars($lin["dias"]) . "&nbsp;&nbsp;</td>";

        $contenido .= "</tr>";
    }

} else {

    $contenido = "
        <tr>
            <td colspan='10' style='text-align:center; padding:12px; color:#666;'>
                ⚠️ No se encontraron Oportunidades en Demo.
            </td>
        </tr>";
}

$conn->close();
unset($resultado);
unset($conn);

// URL nueva oportunidad
$url_nueva_oportunidad = "https://sweet.icontel.cl/index.php?module=Opportunities&action=EditView";

?>

<!-- ======================================================= -->
<!--  TABLA OPORTUNIDADES EN DEMO — AJAX VERSION             -->
<!-- ======================================================= -->

<style>
#demo tr.subtit th { background:#512554 !important; color:#fff !important; }
</style>

<div class="tabla-scroll">
<table id="demo" align="center" width="100%" cellspacing="0" cellpadding="0" border="0">

    <tr style="background:#512554; color:white;">
        <td colspan="9" class="titulo" style="padding:8px;">
            &nbsp;&nbsp;💼 Oportunidades en DEMO
        </td>

        <td align="right" style="padding-right:15px; background:#512554;">
            <a href="<?= $url_nueva_oportunidad ?>"
               target="new"
               title="Crear Nueva Oportunidad"
               style="color:white; font-size:22px; text-decoration:none;">
               +
            </a>
        </td>
    </tr>

    <tr class="subtit" style="background:#512554; color:white;">
        <th width="1%">#</th>
        <th colspan="2" width="30%" style="white-space:nowrap">
            Asunto&nbsp;<input id="filtro-demo-asunto"
                type="text" placeholder="🔍"
                oninput="demoFilterAsunto(this.value)"
                style="width:80px!important;padding:2px 5px!important;border:1px solid rgba(255,255,255,0.6)!important;border-radius:4px;background:rgba(255,255,255,0.2)!important;color:#fff!important;font-size:11px;font-weight:400;outline:none;vertical-align:middle"><span
                id="filtro-demo-asunto-x"
                onclick="document.getElementById('filtro-demo-asunto').value='';demoFilterAsunto('')"
                title="Quitar filtro"
                style="display:none;cursor:pointer;color:#ffd600;font-weight:bold;font-size:13px;vertical-align:middle;margin-left:2px">✕</span>
        </th>
        <th width="5%">Número</th>
        <th width="13%">Cliente</th>
        <th>Estado</th>
        <th width="11%">Asignado a</th>
        <th width="11%">Fecha Creación</th>
        <th width="11%">Fecha Modificación</th>
        <th width="5%" align="right">Días&nbsp;&nbsp;</th>
    </tr>

    <?= $contenido ?>

</table>
</div>

<script>
function demoFilterAsunto(q){
    q=q.toLowerCase();
    var x=document.getElementById('filtro-demo-asunto-x');
    if(x) x.style.display=q?'inline':'none';
    document.querySelectorAll('#demo tr').forEach(function(r){
        if(!r.querySelector('td')) return;
        var tds=r.querySelectorAll('td'), txt='';
        for(var i=1;i<Math.min(5,tds.length);i++){
            if(tds[i]&&tds[i].textContent.trim()){txt=tds[i].textContent.toLowerCase();break;}
        }
        r.style.display=(!q||txt.includes(q))?'':'none';
    });
}
</script>
