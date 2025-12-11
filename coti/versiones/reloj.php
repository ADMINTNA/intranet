<?php
// Inicia la sesión
	session_start();
	ini_set('log_errors', 'On');
	ini_set('error_log', './error.log');
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Imagen de espera</title>
  <style>
    /* Estilos para la imagen de espera */
    .cargando {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 50px;
      height: 50px;
      border-radius: 50%;
      border: 4px solid #ccc;
      border-top-color: #007bff;
      animation: girar 1s linear infinite;
    }
    
    /* Estilos para ocultar la imagen de espera cuando la página esté cargada */
    .cargando.ocultar {
      display: none;
    }
    
    /* Animación para hacer girar la imagen de espera */
    @keyframes girar {
      0% {
        transform: translate(-50%, -50%) rotate(0deg);
      }
      100% {
        transform: translate(-50%, -50%) rotate(360deg);
      }
    }
  </style>
</head>
<body>
  <!-- Imagen de espera -->
  <div class="cargando"></div>
  
  <!-- Contenedor para el contenido -->
  <div class="contenido" style="display:none;">
    <?php include_once 'coti.php'; ?>
  </div>
  
  <!-- Script para ocultar la imagen de espera cuando la página esté cargada -->
  <script>
    // Oculta la imagen de espera y muestra el contenido
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelector('.cargando').classList.add('ocultar');
      document.querySelector('.contenido').style.display = 'block';
    });
  </script>
</body>
</html>