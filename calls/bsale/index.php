<?php
// /home/icontel/public_html/intranet/calls/bsale/index.php
// Contenedor principal del módulo Buscador Bsale (Facturas / Notas de Venta)
//=====================================================
// /bsale/index.php
// 
// Autor: Mauricio Araneda
// Actualizado: 08-11-2025
//=====================================================

include_once(__DIR__ . '/../includes/security_check.php');
header('Content-Type: text/html; charset=UTF-8');

$fecha_hoy = date("d-m-Y H:i:s");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Buscador Bsale (NV y Facturas) - TNA Solutions</title>

<style>
/* ====== ESTILOS GENERALES ====== */
html, body {
  height: 100%;
  margin: 0;
  padding: 0;
  font-family: Arial, sans-serif;
  background: #fafafa;
  color: #1F1D3E;
  overflow: hidden;
}

/* ====== ENCABEZADO ====== */
header {
  background: #1F1D3E;
  color: #fff;
  padding: 12px 18px;
  font-size: 18px;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 2px 6px rgba(0,0,0,0.3);
}

header small {
  font-weight: normal;
  font-size: 13px;
  color: #ddd;
}

/* ====== CONTENEDOR PRINCIPAL ====== */
.container {
  display: flex;
  flex-direction: column;
  height: calc(100vh - 50px);
}

/* ====== IFRAMES ====== */
iframe {
  width: 100%;
  border: none;
  outline: none;
}

#frm {
  height: 130px;
  border-bottom: 2px solid #1F1D3E;
  background: #fff;
}

#res {
  flex: 1;
  height: calc(100% - 130px);
  background: #fafafa;
}

/* ====== FOOTER ====== */
footer {
  background: #1F1D3E;
  color: #fff;
  text-align: center;
  font-size: 12px;
  padding: 8px 0;
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  box-shadow: 0 -2px 6px rgba(0,0,0,0.2);
  z-index: 1000;
}
</style>
</head>
<body>

<!-- ENCABEZADO -->
<header>
  <div>🔎 Buscador Bsale – Facturas y Notas de Venta</div>
  <small>Actualizado: <?= $fecha_hoy ?></small>
</header>

<!-- CONTENIDO PRINCIPAL -->
<div class="container">
  <!-- Formulario de búsqueda -->
  <iframe id="frm" src="buscador.php" title="Formulario de Búsqueda"></iframe>

  <!-- Resultados -->
  <iframe id="res" name="informe" src="informe.php" title="Resultados"></iframe>
</div>

<!-- FOOTER -->
<footer>
  © <?= date("Y") ?> TNA Solutions SpA — Módulo Bsale Integrado con SweetCRM
</footer>

</body>
</html>
