<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$perfil = $_SESSION['perfil'];
$user = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal</title>
    <link rel="stylesheet" href="./css/index.css">
</head>
<body>
    <div class="menu-container">
        <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?> (<?php echo htmlspecialchars($perfil); ?>)</h1>
        <nav>
            <ul>
                <li><a href="#">Opción común a todos</a></li>
                <?php if ($perfil === 'administrador'): ?>
                    <li><a href="#">Gestión de Usuarios</a></li>
                    <li><a href="#">Reportes Administrativos</a></li>
                <?php elseif ($perfil === 'supervisor'): ?>
                    <li><a href="#">Supervisión de Actividades</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </div>
</body>
</html>
