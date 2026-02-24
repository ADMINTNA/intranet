<?php
// Script to remove Vicente Acevedo from the Soporte security group
// This version finds Vicente by name to ensure we get the correct user
include "config.php";

$sg_id = "a03a40e8-bda8-0f1b-b447-58dcfb6f5c19"; // Soporte group ID

echo "<h2>Removiendo Vicente Acevedo del grupo Soporte</h2>";

$conn = DbConnect("tnasolut_sweet");

// First, find Vicente's user ID by name
$query_find = "SELECT id, first_name, last_name, user_name 
               FROM users 
               WHERE (first_name LIKE '%vicente%' OR last_name LIKE '%acevedo%')
               AND !deleted
               LIMIT 5";

$result_find = mysqli_query($conn, $query_find);

if (mysqli_num_rows($result_find) > 0) {
    echo "<h3>Usuarios encontrados con nombre Vicente/Acevedo:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Apellido</th><th>Username</th><th>Acción</th></tr>";
    
    while ($user = mysqli_fetch_array($result_find)) {
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . $user['first_name'] . "</td>";
        echo "<td>" . $user['last_name'] . "</td>";
        echo "<td>" . $user['user_name'] . "</td>";
        
        // Check if this user is in the Soporte group
        $query_check = "SELECT COUNT(*) as count
                        FROM securitygroups_users
                        WHERE user_id = '" . $user['id'] . "'
                        AND securitygroup_id = '$sg_id'";
        
        $result_check = mysqli_query($conn, $query_check);
        $check = mysqli_fetch_array($result_check);
        
        if ($check['count'] > 0) {
            echo "<td style='color: orange;'>EN GRUPO SOPORTE - Removiendo...</td>";
            
            // Remove from group
            $query_delete = "DELETE FROM securitygroups_users 
                             WHERE user_id = '" . $user['id'] . "' 
                             AND securitygroup_id = '$sg_id'";
            
            if (mysqli_query($conn, $query_delete)) {
                echo "</tr><tr><td colspan='5' style='color: green; font-weight: bold;'>";
                echo "✅ " . $user['first_name'] . " " . $user['last_name'] . " removido del grupo Soporte";
                echo " (Filas afectadas: " . mysqli_affected_rows($conn) . ")";
                echo "</td>";
            } else {
                echo "</tr><tr><td colspan='5' style='color: red;'>";
                echo "❌ Error al remover: " . mysqli_error($conn);
                echo "</td>";
            }
        } else {
            echo "<td>No está en grupo Soporte</td>";
        }
        
        echo "</tr>";
    }
    
    echo "</table>";
    
} else {
    echo "<p style='color: red;'>❌ No se encontró ningún usuario con nombre Vicente/Acevedo</p>";
}

// Final verification
echo "<h3>Verificación Final</h3>";
$query_verify = "SELECT 
    u.id,
    u.first_name, 
    u.last_name, 
    u.user_name
FROM users u
JOIN securitygroups_users sgu ON sgu.user_id = u.id
WHERE sgu.securitygroup_id = '$sg_id'
AND (u.first_name LIKE '%vicente%' OR u.last_name LIKE '%acevedo%')
AND !u.deleted";

$result_verify = mysqli_query($conn, $query_verify);

if (mysqli_num_rows($result_verify) == 0) {
    echo "<p style='color: green; font-weight: bold; font-size: 18px;'>✅ CONFIRMADO: Ningún usuario Vicente/Acevedo está en el grupo Soporte</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ ADVERTENCIA: Todavía hay usuarios Vicente/Acevedo en el grupo:</p>";
    echo "<ul>";
    while ($row = mysqli_fetch_array($result_verify)) {
        echo "<li>" . $row['first_name'] . " " . $row['last_name'] . " (" . $row['user_name'] . ") - ID: " . $row['id'] . "</li>";
    }
    echo "</ul>";
}

mysqli_close($conn);

echo "<br><br>";
echo "<p><a href='check_soporte_users.php'>← Verificar usuarios del grupo Soporte</a></p>";
echo "<p><a href='index.php'>← Volver a tareas</a></p>";
?>
