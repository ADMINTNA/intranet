<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h3>🔍 Debug AJAX</h3>";

// 1. Test Session
session_name('icontel_intranet_sess');
session_start();

echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "Cookie: " . print_r($_COOKIE, true) . "\n";
echo "Session Vars:\n";
print_r($_SESSION);
echo "</pre>";

// 2. Test Bootstrap Logic
$sg_id = $_SESSION['sec_id_office'] ?? $_SESSION['sg_id'] ?? '';
echo "<p>Calculated sg_id: <strong>$sg_id</strong></p>";

if (empty($sg_id)) {
    echo "<p style='color:red;'>❌ ERROR: sg_id is empty!</p>";
} else {
    echo "<p style='color:green;'>✅ sg_id found.</p>";
}

// 3. Test Include Config
if (file_exists("config.php")) {
    require_once "config.php";
    echo "<p>✅ config.php included.</p>";
} else {
    echo "<p style='color:red;'>❌ config.php NOT found.</p>";
}

// 4. Test DB Connection
if (function_exists('DbConnect')) {
    echo "<h3>Testing DB Connect...</h3>";
    $conn = DbConnect("tnaoffice_suitecrm");
    
    if ($conn) {
        echo "<p style='color:green;'>✅ DB Connect Success</p>";
        
        // 5. Test Query
        if (!empty($sg_id)) {
            echo "<h4>Testing Tareas Query for sg_id: $sg_id</h4>";
            
            // Limpiar resultados anteriores
            while ($conn->more_results()) $conn->next_result();
            
            $sql = "CALL Kick_Off_Operaciones_Tareas_Abiertas('" . $conn->real_escape_string($sg_id) . "')";
            echo "<pre>SQL: $sql</pre>";
            
            $res = $conn->query($sql);
            if ($res) {
                $num = $res->num_rows;
                echo "<p style='color:green;'>✅ Query Executed. Rows: <strong>" . $num . "</strong></p>";
                
                if ($num > 0) {
                    echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
                    echo "<tr><th>ID</th><th>Asunto</th><th>Estado</th><th>Prioridad</th><th>Asignado</th></tr>";
                    while ($row = $res->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . ($row['id'] ?? 'N/A') . "</td>";
                        echo "<td>" . ($row['tarea'] ?? 'N/A') . "</td>";
                        echo "<td>" . ($row['estado'] ?? 'N/A') . "</td>";
                        echo "<td>" . ($row['prioridad'] ?? 'N/A') . "</td>";
                        echo "<td>" . ($row['asignado'] ?? $row['usuario'] ?? 'N/A') . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>⚠️ No rows returned (check if user has assigned tasks)</p>";
                }
            } else {
                echo "<p style='color:red;'>❌ Query Failed: " . $conn->error . "</p>";
            }
        } else {
             echo "<p style='color:orange;'>⚠️ sg_id is empty, skipping query</p>";
        }
    } else {
         echo "<p style='color:red;'>❌ DB Connect Failed</p>";
    }
} else {
    echo "<p style='color:red;'>❌ DbConnect function not found.</p>";
}
?>
