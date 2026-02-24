<?PHP
// ==========================================================
// /intranet/calls/includes/informe.php
// Genera el informe consolidado de empresa, contactos,
// portal de pago y servicios activos.
// Autor: Mauricio Araneda (mAo)
// Última actualización: 07-01-2026 - Diseño modernizado
// ==========================================================
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8">
<title>Ficha de Cliente - TNA Office</title>
<link rel="stylesheet" href="../css/informe_modern.css">
<script src="../js/sort_columns.js"></script>
<script src="../js/resize_columns.js"></script>
<script src="../js/filters.js"></script>
<script src="../js/export.js"></script>
</head>

<body>
<div class="contenido-principal">

  <!-- ========== BARRA DE BÚSQUEDA Y FILTROS ========== -->
  <div class="toolbar">
    <div class="search-box">
      <input type="text" id="search-input" placeholder="🔍 Buscar en toda la ficha..." autocomplete="off">
      <span class="search-icon">🔍</span>
    </div>
    <button class="btn-filtrar" id="btn-filtrar" onclick="togglePanelFiltros()">🔍 Filtrar</button>
    <div style="position:relative;">
      <button class="btn-exportar" id="btn-exportar" onclick="mostrarMenuExportar()">📤 Exportar</button>
      <div id="menu-exportar">
        <div class="export-section">
          <h4>👥 Clientes y Contactos</h4>
          <div class="export-options">
            <button class="export-btn" onclick="exportarSeccion('clientes', 'excel')">📊 Excel</button>
            <button class="export-btn" onclick="exportarSeccion('clientes', 'csv')">📄 CSV</button>
            <button class="export-btn" onclick="exportarSeccion('clientes', 'copiar')">📋 Copiar</button>
          </div>
        </div>
        <div class="export-section">
          <h4>🔧 Servicios Activos</h4>
          <div class="export-options">
            <button class="export-btn" onclick="exportarSeccion('servicios', 'excel')">📊 Excel</button>
            <button class="export-btn" onclick="exportarSeccion('servicios', 'csv')">📄 CSV</button>
            <button class="export-btn" onclick="exportarSeccion('servicios', 'copiar')">📋 Copiar</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Panel de filtros (modal) -->
  <div id="overlay-filtros" onclick="cerrarPanelFiltros()"></div>
  <div id="panel-filtros" class="hidden">
    <div class="panel-header">
      <h3>🎛️ Filtros</h3>
      <button class="btn-cerrar" onclick="cerrarPanelFiltros()">×</button>
    </div>
    <div class="panel-body">
      <div class="filter-group">
        <h4>Estado de Servicio</h4>
        <div class="filter-option">
          <input type="checkbox" id="filter-vigente" class="filter-checkbox" data-tipo="estado" value="Vigente">
          <label for="filter-vigente">✅ Vigente</label>
        </div>
        <div class="filter-option">
          <input type="checkbox" id="filter-vencido" class="filter-checkbox" data-tipo="estado" value="Vencido">
          <label for="filter-vencido">❌ Vencido</label>
        </div>
        <div class="filter-option">
          <input type="checkbox" id="filter-instalacion" class="filter-checkbox" data-tipo="estado" value="En Instalacion">
          <label for="filter-instalacion">🔧 En Instalación</label>
        </div>
        <div class="filter-option">
          <input type="checkbox" id="filter-suspendido" class="filter-checkbox" data-tipo="estado" value="Suspendido">
          <label for="filter-suspendido">⏸️ Suspendido</label>
        </div>
      </div>
      
      <div class="filter-group">
        <h4>Tipo de Contacto</h4>
        <div class="filter-option">
          <input type="checkbox" id="filter-admin" class="filter-checkbox" data-tipo="contacto" value="Admin">
          <label for="filter-admin">👔 Administrativo</label>
        </div>
        <div class="filter-option">
          <input type="checkbox" id="filter-tecnico" class="filter-checkbox" data-tipo="contacto" value="Tecnico">
          <label for="filter-tecnico">🔧 Técnico</label>
        </div>
        <div class="filter-option">
          <input type="checkbox" id="filter-finanzas" class="filter-checkbox" data-tipo="contacto" value="Finanzas">
          <label for="filter-finanzas">💰 Finanzas</label>
        </div>
      </div>
    </div>
    <div class="panel-footer">
      <button class="btn-limpiar" onclick="limpiarFiltros()">🧹 Limpiar</button>
      <button class="btn-aplicar" onclick="cerrarPanelFiltros()">✅ Aplicar</button>
    </div>
  </div>

  <!-- ========== SECCIÓN: INFORMACIÓN DEL CLIENTE ========== -->
  <div class="section-card">
    <div class="section-header">
      👤 Información del Cliente y Contactos
    </div>
    <div class="section-body">
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
              <th width="5%">Estado</th>
            </tr>
          </thead>
          <tbody>
            <?php echo $datos_completos; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ========== SECCIÓN: PORTAL DE PAGO (DUEMINT) ========== -->
  <div class="section-card">
    <div class="section-header">
      💳 Estado de Cuenta - Portal de Pago
    </div>
    <div class="section-body">
      <div class="payment-portal">
        <div class="payment-card warning">
          <div class="payment-label">Por Vencer</div>
          <div class="payment-amount">$<?php echo number_format($dumit_por_vencer, 0, ',', '.'); ?></div>
        </div>
        
        <div class="payment-card danger">
          <div class="payment-label">Vencido</div>
          <div class="payment-amount">$<?php echo number_format($dumit_vencida, 0, ',', '.'); ?></div>
        </div>
        
        <div class="payment-card">
          <div class="payment-label">Cuenta Corriente</div>
          <a href="<?php echo $dumit_portal; ?>" target="_blank" class="payment-link">
            <?php if (!$endummit) { ?>
              ⚠️ NO EN DUEMINT
            <?php } else { ?>
              Ver Detalle Completo →
            <?php } ?>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- ========== SECCIÓN: SERVICIOS ACTIVOS ========== -->
  <div class="section-card">
    <div class="section-header">
      🔧 Servicios Activos
    </div>
    <div class="section-body">
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
    </div>
  </div>

</div>
</body>
</html>