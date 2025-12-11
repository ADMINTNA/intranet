<?php
//=====================================================
// /bsale/buscador.php
// Cierre de sesión seguro del sistema KickOff
// Autor: Mauricio Araneda
// Actualizado: 08-11-2025
//=====================================================

include_once(__DIR__ . '/../includes/security_check.php');
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Buscador Bsale</title>
<style>
body { font-family: Arial, sans-serif; font-size: 14px; margin: 15px; background: #fafafa; }
h2 { color: #1F1D3E; margin-top: 0; }
form {
  background: #f7f7f7;
  padding: 12px;
  border-radius: 8px;
  border: 1px solid #ccc;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 10px;
}
label { margin-right: 4px; }
input, select, button {
  padding: 4px 6px;
  font-size: 13px;
}
button {
  background: #1F1D3E;
  color: #fff;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}
button:hover { background: #29255d; }
</style>
</head>
<body>

<h2>🔎 Buscador de Documentos Bsale</h2>

<form id="frmBuscador" method="get" action="informe.php" target="informe">
  <label for="tipo">Tipo:</label>
  <select name="tipo" id="tipo">
    <option value="">Todos</option>
    <option value="fac">Factura</option>
    <option value="nv">Nota de Venta</option>
  </select>

  <label for="num_doc">N° Documento:</label>
  <input type="text" name="num_doc" id="num_doc" placeholder="Ej: 14055">

  <button type="submit">Buscar</button>
</form>

<script>
// 🔁 Enviar resultados al iframe "informe" que está en index.php
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('frmBuscador');
  form.addEventListener('submit', function(e) {
    e.preventDefault();

    // Ubicar iframe de resultados en el documento padre (index.php)
    const iframe = parent.document.querySelector('iframe[name="informe"]');
    if (iframe) {
      const params = new URLSearchParams(new FormData(form)).toString();
      iframe.src = form.action + '?' + params;
    }
  });
});
</script>

</body>
</html>
