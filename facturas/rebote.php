<?php
// Inicia la sesiÃ³n
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
      position: relative;
      width: 100%;
      height: 100vh;
    }
    
    /* Estilos para el texto "iContel" */
    .cargando .texto {
      background: linear-gradient(to right, #ff0000, #ffa500, #ffff00, #008000);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      position: absolute;
      font-size: 48px;
      font-weight: bold;
      animation: flotar 6s linear infinite;
    }
    
    /* Estilos para ocultar la imagen de espera cuando la pÃ¡gina estÃ© cargada */
    .cargando.ocultar {
      display: none;
    }
    
    /* AnimaciÃ³n para hacer flotar el texto */
    @keyframes flotar {
      0% {
        top: 10%;
        left: 20%;
        transform: translate(0%, 0%) rotate(10deg);
      }
      25% {
        top: 5%;
        left: 90%;
        transform: translate(-10%, 0%) rotate(-20deg);
      }
      50% {
        top: 95%;
        left: 80%;
        transform: translate(-20%, -10%) rotate(30deg);
      }
      75% {
        top: 80%;
        left: 10%;
        transform: translate(10%, -20%) rotate(-10deg);
      }
      100% {
        top: 10%;
        left: 20%;
        transform: translate(0%, 0%) rotate(10deg);
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
  
  <!-- Script para ocultar la imagen de espera cuando la pÃ¡gina estÃ© cargada -->
  <script>
    // Oculta la imagen de espera y muestra el contenido
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelector('.cargando').classList.add('ocultar');
      document.querySelector('.contenido').style.display = 'block';
    });
  </script>
</body>
</html>