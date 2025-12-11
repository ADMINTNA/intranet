<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Llama a la función para conectar a la base de datos
    $pdo = conectarDB();

    // Consulta para validar el usuario
    $stmt = $pdo->prepare("SELECT id, username, password, perfil FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && $password =  $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['perfil'] = $user['perfil'];
        header("Location: ../calculadora.php");
        exit();
    } else {
        header("Location: index.php?error=Usuario o contraseña incorrectos");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
