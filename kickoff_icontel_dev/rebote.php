require_once __DIR__ . '/session_core.php';

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Imagen de espera</title>
  <link href="./css/rebote.css" rel="stylesheet" type="text/css" />
</head>
<body>
  <!-- Imagen de espera -->
  <div class="cargando">
    <span class="texto">iContel</span>
  </div>
  
  <!-- Contenedor para el contenido -->
  <div class="contenido" style="display:none;">
    <?php include_once 'index.php'; ?>
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