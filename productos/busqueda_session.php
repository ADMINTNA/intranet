<?php
// ==========================================================
// /intranet/productos/busqueda_session.php
// Buscador → Construye la query y redirige sin enviar HTML
// Autor: Mauricio Araneda
// Fecha: 2025-11-18
// Codificación: UTF-8 sin BOM
// ==========================================================

// ⚠️ IMPORTANTE: NADA DE HTML ANTES DE ESTO
session_name('icontel_intranet_sess');
session_start();

header('Content-Type: text/html; charset=utf-8');

// --------------------
// 🔍 Captura variables
// --------------------
$nombre      = isset($_POST['nombre'])      ? trim($_POST['nombre']) : "";
$variante    = isset($_POST['variante'])    ? trim($_POST['variante']) : "";
$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : "";
$categoria   = isset($_POST['categoria'])   ? $_POST['categoria'] : [];

// --------------------
// 🔎 Filtros dinámicos
// --------------------
$cuales = "";

if (!empty($nombre))
    $cuales .= " AND pr.name LIKE '%" . addslashes($nombre) . "%'";

if (!empty($variante))
    $cuales .= " AND pr.part_number LIKE '%" . addslashes($variante) . "%'";

if (!empty($descripcion))
    $cuales .= " AND pr.description LIKE '%" . addslashes($descripcion) . "%'";

if (!empty($categoria) && is_array($categoria)) {
    $sqlCats = [];
    foreach ($categoria as $cat) {
        $sqlCats[] = "ca.name LIKE '" . addslashes($cat) . "'";
    }
    $cuales .= " AND (" . implode(" OR ", $sqlCats) . ")";
}

// --------------------
// 🧱 Query base
// --------------------
$sql = "
    SELECT 
        pr.id            AS id,
        pr.name          AS producto,
        pr.description   AS descripcion,
        pr.part_number   AS numero_parte,
        pr.price         AS valor,
        ca.name          AS categoria,
        ca.description   AS categ_descrip,
        CASE
            WHEN pr.`type` = 'Good'    THEN 'Equipo'
            WHEN pr.`type` = 'Service' THEN 'Servicio'
            ELSE 'SIN INFORMACION'
        END AS tipo
    FROM aos_products AS pr
    LEFT JOIN aos_product_categories AS ca 
        ON pr.aos_product_category_id = ca.id
    WHERE pr.deleted = 0
      AND ca.deleted = 0
";

// Guardar query final en sesión
 $_SESSION['query'] = $sql . $cuales;
// 💣 ELIMINA CUALQUIER SALIDA DEL BUFFER
ob_clean();

// --------------------
// 🔁 Redirección limpia
// --------------------
header("Location: ./sort/index.php");
exit; // 👈 NADA VA DESPUÉS