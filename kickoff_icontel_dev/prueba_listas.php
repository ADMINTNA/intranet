<?php
// =======================================================
// kickoff/prueba_listas.php
// para probar lectura de listas de sweet
// 
// =======================================================

header("Content-Type: text/html; charset=UTF-8");

require_once "config.php";   // OJO: ya incluye sweet_get_dropdown()

$lista = sweet_get_dropdown('categoria_list');

$prueba = sweet_get_dropdown("categoria_list");

/* echo "<pre>";
print_r($prueba);
echo "</pre>";
*/

echo "<h2>Prueba lista categoria_list</h2>";

if (empty($lista)) {
    echo "<p style='color:red'>❌ No se encontró categoria_list</p>";
    exit;
}

echo "<select>";
foreach ($lista as $key => $value) {
    echo "<option value='$key'>$value</option>";
}
echo "</select>";

echo "<hr><pre>";
print_r($lista);
echo "</pre>";
?>