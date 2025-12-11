<?PHP
// ==========================================================
// /intranet/calls/includes/informe.php
// Genera el informe consolidado de empresa, contactos,
// portal de pago y servicios activos.
// Autor: Mauricio Araneda (mAo)
// Empresa: TNA Group / iConTel
// Última actualización: 12-11-2025
// ==========================================================
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8">
<title>Datos iConTel</title>
<link rel="stylesheet" href="../css/informe.css">
</head>

<body>
<div class="contenido-principal">
  <table class="tablaClientes" align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
    <tbody>

    <!-- Contactos -->
    <tr style="background-color: #1F1D3E; color: white;">
      <td colspan="8" style="padding:0;">
        <div class="contenedor-scroll-empresa">
          <table>
            <thead>
              <tr>
                <th width="25%">Empresa</th>
                <th width="10%">Ejecutiv@</th>
                <th width="15%">Contacto</th>
                <th width="13%">Teléfono</th>
                <th width="10%">eMail</th>
                <th width="10%">Tipo</th>
                <!--th>Rut Empresa</th-->
                <th width="5%">Estado</th>
              </tr>
            </thead>
            <tbody>
              <?php echo $datos_completos; ?>
            </tbody>
          </table>
        </div>
      </td>
    </tr>

    <!-- Portal de pago -->
    <tr>
      <td colspan="9" style="background-color: #1F1D3E; color:white; text-align:center;">
        PORTAL DE PAGO
      </td>
    </tr>

    <!-- Vencimientos -->
    <tr>
      <td colspan="2" style="background-color:orange; color:white; text-align:center;">Por Vencer</td>
      <td colspan="2" style="background-color:orangered; color:white; text-align:center;">Vencido</td>
      <td colspan="5" rowspan="2" style="text-align:center;">
        <a href="<?php echo $dumit_portal; ?>" target="_blank">Ver detalle de Cuenta Corriente.
          <?php if (!$endummit) echo "NO EN DUEMIT !!!"; ?>
        </a>
      </td>
    </tr>
    <tr>
      <td colspan="2" style="background-color:orange; color:white; text-align:center;">
        <?php echo number_format($dumit_por_vencer); ?>
      </td>
      <td colspan="2" style="background-color:orangered; color:white; text-align:center;">
        <?php echo number_format($dumit_vencida); ?>
      </td>
    </tr>

    <!-- Servicios activos -->
    <tr>
      <td colspan="9" style="padding:0;">
        <div class="contenedor-scroll">
          <table>
            <thead>
              <tr>
                <th>&nbsp;</th>
                <th>Cant.</th>
                <th width="6%">Estado</th>
                <th width="15%">Servicio</th>
                <th>Contrato Cliente</th>
                <th width="15%">Detalles de instalación</th>
                <th align="left">Proveedor</th>
                <th width="15%">Cód.Servicio</th>
                <th>Fecha</th>
                <th>Plazo</th>
                <th width="6%">Meses</th>
                <th>NV</th>
                <th>Coti_#</th>
                <th>Opor_#</th>
                <th>Valor</th>
              </tr>
            </thead>
            <tbody>
              <?php include_once("busca_servicios_activos.php"); ?>
            </tbody>
          </table>
        </div>
      </td>
    </tr>
    </tbody>
  </table>
</div>
</body>
</html>