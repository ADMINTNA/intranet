<?php
// Script to check which users are in the Soporte security group
include "config.php";

$sg_id = "a03a40e8-bda8-0f1b-b447-58dcfb6f5c19"; // Soporte group ID

$query = "SELECT 
    u.id,
    u.first_name, 
    u.last_name, 
    u.user_name, 
    sg.name as group_name,
    sgu.date_modified as membership_date
FROM users u
JOIN securitygroups_users sgu ON sgu.user_id = u.id
JOIN securitygroups sg ON sg.id = sgu.securitygroup_id
WHERE sg.id = '$sg_id'
AND !u.deleted
AND !sg.deleted
ORDER BY u.last_name, u.first_name";

$conn = DbConnect("tnasolut_sweet");
$result = mysqli_query($conn, $query);

echo "<h2>Usuarios en el grupo Soporte</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Nombre</th><th>Apellido</th><th>Username</th><th>Grupo</th><th>Fecha Modificación</th></tr>";

$found_vicente = false;
while($row = mysqli_fetch_array($result)) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['first_name'] . "</td>";
    echo "<td>" . $row['last_name'] . "</td>";
    echo "<td>" . $row['user_name'] . "</td>";
    echo "<td>" . $row['group_name'] . "</td>";
    echo "<td>" . $row['membership_date'] . "</td>";
    echo "</tr>";
    
    if (stripos($row['first_name'], 'vicente') !== false || 
        stripos($row['last_name'], 'vicente') !== false ||
        stripos($row['user_name'], 'vicente') !== false) {
        $found_vicente = true;
    }
}

echo "</table>";

if ($found_vicente) {
    echo "<p style='color: red; font-weight: bold;'>⚠️ VICENTE ESTÁ EN EL GRUPO SOPORTE</p>";
} else {
    echo "<p style='color: green; font-weight: bold;'>✓ Vicente NO está en el grupo Soporte</p>";
}

// Now check tasks assigned to Vicente
echo "<h2>Tareas asignadas a Vicente (si existe)</h2>";

$query2 = "SELECT 
    t.id,
    t.name as tarea,
    t.status,
    t.date_entered,
    u.first_name,
    u.last_name,
    u.user_name
FROM tasks t
JOIN users u ON u.id = t.assigned_user_id
WHERE (u.first_name LIKE '%vicente%' OR u.last_name LIKE '%vicente%' OR u.user_name LIKE '%vicente%')
AND t.status != 'Completed'
AND !t.deleted
AND !u.deleted
ORDER BY t.date_entered DESC
LIMIT 20";

$result2 = mysqli_query($conn, $query2);

if (mysqli_num_rows($result2) > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID Tarea</th><th>Asunto</th><th>Estado</th><th>Fecha Creación</th><th>Usuario</th></tr>";
    
    while($row = mysqli_fetch_array($result2)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['tarea'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "<td>" . $row['date_entered'] . "</td>";
        echo "<td>" . $row['first_name'] . " " . $row['last_name'] . " (" . $row['user_name'] . ")</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No se encontraron tareas para Vicente</p>";
}

mysqli_close($conn);
?>
