<?php
// Inicia la sesión
session_start();
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
      font-size: 48px;
      font-weight: bold;
      text-align: center;
    }
    
    /* Estilos para el texto "iContel" */
    .cargando .texto {
      background: linear-gradient(to right, #ff0000, #ffa500, #ffff00, #008000);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      animation: girar 2s linear infinite;
      transform-origin: center;
      display: inline-block;
    }
    
    /* Estilos para ocultar la imagen de espera cuando la página esté cargada */
    .cargando.ocultar {
      display: none;
    }
    
    /* Animación para hacer girar el texto */
    @keyframes girar {
      0% {
        transform: rotate(0deg);
      }
      100% {
        transform: rotate(360deg);
      }
    }
  </style>
</head>
<body>
  <!-- Imagen de espera -->
  <div class="cargando">
    <span class="texto">iContel</span>
  </div>
  
  <!-- Contenedor para el contenido -->
  <div class="contenido" style="display:none;">
    <?php include_once 'contenido.php'; ?>
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