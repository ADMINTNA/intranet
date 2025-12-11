<?php
// Función para conectar a la base de datos
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function conectarDB() {
    $host = 'localhost';
    $dbname = 'fotovoltaicos';
    $user = 'data_studio';
    $password = '1Ngr3s0.,';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Error en la conexión: " . $e->getMessage());
    }
}




// Guardar solución desde formulario
function guardarSolucion($datos) {
    $pdo = conectarDB(); // ✅ Conectar a la base de datos

    try {
        $stmt = $pdo->prepare("INSERT INTO proyectos (cliente, direccion, consumoMensual, facturacionMensual, horasSol, potenciaPanel, 
                                rendimientoPanel, tamanoPanel, tramiteSec, sec, tipoCalculo, cantidadInversores, estructura, 
                                costoInversor, cantidadPaneles, kWhPlanta, kWDia, cumplimiento, precioVenta) 
                               VALUES (:cliente, :direccion, :consumoMensual, :facturacionMensual, :horasSol, :potenciaPanel, 
                                :rendimientoPanel, :tamanoPanel, :tramiteSec, :sec, :tipoCalculo, :cantidadInversores, :estructura, 
                                :costoInversor, :cantidadPaneles, :kWhPlanta, :kWDia, :cumplimiento, :precioVenta)");

        $resultado = $stmt->execute([
            ':cliente' => $datos['cliente'],
            ':direccion' => $datos['direccion'],
            ':consumoMensual' => $datos['consumoMensual'],
            ':facturacionMensual' => $datos['facturacionMensual'],
            ':horasSol' => $datos['horasSol'],
            ':potenciaPanel' => $datos['potenciaPanel'],
            ':rendimientoPanel' => $datos['rendimientoPanel'],
            ':tamanoPanel' => $datos['tamanoPanel'],
            ':tramiteSec' => $datos['tramiteSec'],
            ':sec' => $datos['sec'],
            ':tipoCalculo' => $datos['tipoCalculo'],
            ':cantidadInversores' => $datos['cantidadInversores'],
            ':estructura' => $datos['estructura'],
            ':costoInversor' => $datos['costoInversor'],
            ':cantidadPaneles' => $datos['cantidadPaneles'],
            ':kWhPlanta' => $datos['kWhPlanta'],
            ':kWDia' => $datos['kWDia'],
            ':cumplimiento' => $datos['cumplimiento'],
            ':precioVenta' => $datos['precioVenta']
        ]);

        if (!$resultado) {
            $errorInfo = $stmt->errorInfo();
            file_put_contents('debug.log', "Error al ejecutar SQL: " . implode(" | ", $errorInfo) . PHP_EOL, FILE_APPEND);
            return false;
        }

        return true;
    } catch (PDOException $e) {
        file_put_contents('debug.log', "Error SQL: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
        return false;
    }
}
// Listar todas las soluciones
function listarSoluciones() {
    $pdo = conectarDB();
    $sql = "SELECT * FROM proyectos ORDER BY fecha_creacion DESC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Eliminar solución por ID
function eliminarSolucion($id) {
    $pdo = conectarDB(); // Conectar a la base de datos
    
    try {
        $stmt = $pdo->prepare("DELETE FROM proyectos WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true; // ✅ Eliminación exitosa
        } else {
            return false; // ❌ No se encontró el registro
        }
    } catch (PDOException $e) {
        file_put_contents('debug.log', "Error al eliminar solución: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
        return false; // ❌ Error en la consulta
    }
}

function cargarProyecto($id) {
    $pdo = conectarDB();
    $sql = "SELECT * FROM proyectos WHERE id = :id";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die(json_encode([
            'success' => false,
            'message' => 'Error en la consulta: ' . $e->getMessage()
        ]));
    }
}




?>
