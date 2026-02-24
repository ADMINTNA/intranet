<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Debug Error 500</h1>";

// Simular sesión
session_name('icontel_intranet_sess');
session_start();

// Definir variables globales si faltan
if (!isset($_SESSION['sg_id'])) {
    echo "<p>⚠️ No session sg_id, using fallback for test</p>";
    $_SESSION['sg_id'] = '64e05205-3a75-84cd-6aee-692234692c9f'; // ID del log del usuario
    $_SESSION['sg_name'] = 'soporte';
}

echo "<p>Intentando cargar cm_tareas_pendientes.php...</p>";

try {
    include "cm_tareas_pendientes.php";
} catch (Throwable $t) {
    echo "<h2>🔥 EXCEPTION FATAL:</h2>";
    echo "<pre>" . $t->getMessage() . "\n" . $t->getTraceAsString() . "</pre>";
}

echo "<p>Fin del script.</p>";
?>
