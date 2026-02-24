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
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }
    
    /* Estilos para el cÃ­rculo exterior */
    .cargando .circulo-externo {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      border: 4px solid transparent;
      border-top-color: #ff0000;
      border-right-color: #ffa500;
      border-bottom-color: #ffff00;
      border-left-color: #008000;
      animation: girar-externo 1s linear infinite;
    }
    
    /* Estilos para el cÃ­rculo interior */
    .cargando .circulo-interno {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      border: 4px solid transparent;
      border-top-color: #0000ff;
      border-right-color: #800080;
      border-bottom-color: #008080;
      border-left-color: #00ffff;
      animation: girar-interno 1.5s cubic-bezier(0.645, 0.045, 0.355, 1) infinite;
    }
    
    /* Estilos para ocultar la imagen de espera cuando la pÃ¡gina estÃ© cargada */
    .cargando.ocultar {
      display: none;
    }
    
    /* AnimaciÃ³n para hacer girar el cÃ­rculo exterior */
    @keyframes girar-externo {
      0% {
        transform: rotate(0deg);
      }
      100% {
        transform: rotate(360deg);
      }
    }
    
    /* AnimaciÃ³n para hacer girar el cÃ­rculo interior */
    @keyframes girar-interno {
      0% {
        transform: rotate(0deg);
      }
      100% {
        transform: rotate(-360deg);
      }
    }
  </style>
</head>
<body>
  <!-- Imagen de espera -->
  <div class="cargando">
    <div class="circulo-externo"></div>
    <div class="circulo-interno"></div>
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