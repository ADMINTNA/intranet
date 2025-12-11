<?php
require_once 'db_connection.php';

try {
    $pdo = getConnection();
    echo "Conexión exitosa a la base de datos.";
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}
?>