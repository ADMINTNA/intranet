<?php
/**
 * ==========================================================
 * Editor de Listas SuiteCRM 7.x  – MAO + ChatGPT
 * Ubicación recomendada: /kickoff/tools/dropdown_editor.php
 * ==========================================================
 */

header('Content-Type: text/html; charset=utf-8');

// ----------------------------------------------------------
// 1) Sweet exige sugarEntry
// ----------------------------------------------------------
if (!defined('sugarEntry')) define('sugarEntry', true);

// ----------------------------------------------------------
// 2) Cambiar a la raíz REAL de tu SuiteCRM
// ----------------------------------------------------------
//   sweet/  está en: /home/icontel/public_html/intranet/sweet/
chdir('/home/icontel/public_html/intranet/sweet/');

// ----------------------------------------------------------
// 3) Cargar entryPoint del CRM
// ----------------------------------------------------------
require_once('include/entryPoint.php');

global $app_list_strings;

// ----------------------------------------------------------
// 4) Cargar archivos de idioma (STÁNDAR SweetCRM)
// ----------------------------------------------------------

// idioma principal español
if (file_exists('include/language/es_ES.lang.php')) {
    include 'include/language/es_ES.lang.php';
}

// idioma español personalizado (CUSTOM)
foreach (glob('custom/include/language/*.lang.php') as $f) {
    include $f;
}

// extensiones del idioma
foreach (glob('custom/Extension/application/Ext/Language/*.php') as $f) {
    include $f;
}


// ----------------------------------------------------------
// 5) Archivo donde se guardará la lista editada
// ----------------------------------------------------------
$save_lang = 'custom/include/language/es_ES.lang.php';


// ==========================================================
// GUARDAR LISTA
// ==========================================================
if (isset($_POST['save']) && isset($_POST['lista'])) {

    $lista = $_POST['lista'];
    $keys  = $_POST['key'] ?? [];
    $vals  = $_POST['value'] ?? [];

    $nuevo = [];

    for ($i = 0; $i < count($keys); $i++) {
        $k = trim($keys[$i]);
        $v = trim($vals[$i] ?? '');
        if ($k !== '') {
            $nuevo[$k] = $v;
        }
    }

    asort($nuevo, SORT_STRING | SORT_FLAG_CASE);

    // Hacer backup
    if (file_exists($save_lang)) {
        copy($save_lang, $save_lang . '.bak_' . date('Ymd_His'));
    }

    // Cargar el archivo actual
    $contenido = file_exists($save_lang) ? file_get_contents($save_lang) : "<?php\n\n";

    // Eliminar definición previa
    $pattern = "/\\\$app_list_strings\\['" . preg_quote($lista, '/') . "'\\] = array\\((.*?)\\);/s";
    $contenido = preg_replace($pattern, '', $contenido);

    // Nueva definición
    $txt = "\$app_list_strings['$lista'] = array(\n";
    foreach ($nuevo as $k => $v) {
        $txt .= "    '" . addslashes($k) . "' => '" . addslashes($v) . "',\n";
    }
    $txt .= ");\n\n";

    // Guardar
    file_put_contents($save_lang, $contenido . $txt);

    echo "<h2 style='color:green'>✔ Lista '$lista' actualizada</h2>";
    echo "<a href='dropdown_editor.php'>← Volver</a>";
    exit;
}


// ==========================================================
// LISTADO DE TODAS LAS LISTAS
// ==========================================================
if (!isset($_GET['lista'])) {

    echo "<h1>Listas disponibles en SuiteCRM</h1>";

    $list_names = array_keys($app_list_strings);
    sort($list_names, SORT_STRING | SORT_FLAG_CASE);

    echo "<ul>";
    foreach ($list_names as $name) {
        $safe = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        echo "<li><a href='dropdown_editor.php?lista=$safe'>$safe</a></li>";
    }
    echo "</ul>";
    exit;
}


// ==========================================================
// EDITAR UNA LISTA ESPECÍFICA
// ==========================================================
$lista = $_GET['lista'];
$data  = $app_list_strings[$lista] ?? [];

asort($data, SORT_STRING | SORT_FLAG_CASE);

$safe_lista = htmlspecialchars($lista, ENT_QUOTES, 'UTF-8');

echo "<h1>Editando lista: <b>$safe_lista</b></h1>";
echo "<form method='post'>";

echo "<table border='1' cellpadding='6' cellspacing='0'>";
echo "<tr style='background:#DDD'>
        <th>Clave</th>
        <th>Etiqueta</th>
        <th>Acción</th>
      </tr>";

foreach ($data as $k => $v) {
    echo "<tr>
        <td><input name='key[]' value='" . htmlspecialchars($k) . "'></td>
        <td><input name='value[]' value='" . htmlspecialchars($v) . "'></td>
        <td><button type='button' onclick='this.closest(\"tr\").remove()'>❌</button></td>
    </tr>";
}

// fila vacía
echo "<tr>
        <td><input name='key[]' placeholder='nueva_clave'></td>
        <td><input name='value[]' placeholder='Nueva etiqueta'></td>
        <td><button type='button' onclick='this.closest(\"tr\").remove()'>❌</button></td>
     </tr>";

echo "</table><br><br>";

echo "<input type='hidden' name='lista' value='$safe_lista'>";
echo "<button type='submit' name='save'>💾 Guardar cambios</button>";

echo "</form>";
?>