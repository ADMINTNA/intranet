<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Documento sin título</title>
</head>

<body>
<?php
function generarSelectVendedores() {
    // Conexión a la base de datos (ajusta según tu configuración)
    $host = "localhost"; // Cambia esto según tu configuración
    $user = "tnasolut_data_studio"; // Usuario de la base de datos
    $password = "P3rf3ct0.,"; // Contraseña
    $database = "tnasolut_sweet"; // Nombre de la base de datos

    // Conectar a MySQL
    $conn = new mysqli($host, $user, $password, $database);

    // Verificar conexión
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Forzar UTF-8 para evitar problemas con caracteres especiales
    $conn->set_charset("utf8mb4");

    // Llamar al procedimiento almacenado
    $sql = "CALL ventas_ejecutivos_de_cuentas()";
    $result = $conn->query($sql);

    // Verificar si hay resultados
    if ($result && $result->num_rows > 0) {
        echo '<select multiple name="vendedores[]" id="vendedores" size="5">';
        while ($row = $result->fetch_assoc()) {
            // Limpiar y verificar datos
            $ejecutivo = trim($row["ejecutivo"] ?? '');
            $id = $row["id"] ?? '';

            // Validación extra: asegurarse de que no sea vacío ni contenga solo espacios
            if (!empty($ejecutivo) && strlen($ejecutivo) > 1 && !empty($id)) {
                echo '<option value="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($ejecutivo, ENT_QUOTES, 'UTF-8') . '</option>';
            }
        }
        echo '</select>';
    } else {
        echo "<p>No hay vendedores disponibles.</p>";
    }

    // Cerrar conexión
    $conn->close();
}

// Llamar a la función para mostrar el <select>
generarSelectVendedores();
?>




    
</body>
</html>