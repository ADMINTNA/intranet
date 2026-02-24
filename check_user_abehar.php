<?php
// Script para verificar usuario abehar en la base de datos
$con = new mysqli("localhost", "tnasolut_app", "1Ngr3s0.,", "tnasolut_app");

if ($con->connect_errno) {
    die("❌ Error MySQL: " . $con->connect_error);
}

echo "=== Buscando usuario 'abehar' ===\n\n";

// Buscar usuario
$username = 'abehar';
$username_clean = mb_strtolower(str_replace(['.', ' ', '-'], '', $username));

$stmt = $con->prepare("
    SELECT id, username, password, razon_social, rut, sec_id, rol, sec_id_office
    FROM clientes
    WHERE LOWER(REPLACE(REPLACE(REPLACE(username,'.',''),' ',''),'-','')) = ?
");
$stmt->bind_param("s", $username_clean);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo "❌ Usuario '$username' NO encontrado en la base de datos\n\n";
    
    // Buscar variaciones
    echo "Buscando variaciones...\n";
    $result = $con->query("SELECT id, username, rol, sec_id, sec_id_office FROM clientes WHERE username LIKE '%behar%' OR username LIKE '%abehar%'");
    
    if ($result && $result->num_rows > 0) {
        echo "\nUsuarios similares encontrados:\n";
        while ($row = $result->fetch_assoc()) {
            echo "  - ID: {$row['id']}, Username: {$row['username']}, Rol: {$row['rol']}, sec_id: {$row['sec_id']}, sec_id_office: {$row['sec_id_office']}\n";
        }
    } else {
        echo "No se encontraron usuarios similares\n";
    }
} else {
    $stmt->bind_result($id, $db_user, $db_pass, $rs, $rut, $sec_id, $rol, $sec_id_office);
    $stmt->fetch();
    
    echo "✅ Usuario encontrado:\n";
    echo "  ID: $id\n";
    echo "  Username: $db_user\n";
    echo "  Razón Social: $rs\n";
    echo "  RUT: $rut\n";
    echo "  Rol: $rol\n";
    echo "  sec_id: $sec_id\n";
    echo "  sec_id_office: $sec_id_office\n\n";
    
    // Verificar si el rol es correcto
    if ($rol !== "Office") {
        echo "⚠️  PROBLEMA: El rol es '$rol' pero debería ser 'Office' para acceder a office_v2.php\n";
    } else {
        echo "✅ El rol es correcto: 'Office'\n";
    }
    
    // Verificar sec_id_office
    if (empty($sec_id_office)) {
        echo "⚠️  PROBLEMA: sec_id_office está vacío o NULL\n";
    } else {
        echo "✅ sec_id_office está configurado: $sec_id_office\n";
    }
}

$con->close();
?>
