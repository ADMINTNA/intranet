<?PHP
   header ("Content-type: image/png");
   
// Calcular ángulos
   // $votos1 = $_REQUEST['votos1'];
   // $votos2 = $_REQUEST['votos2'];
   // $votos3 = $_REQUEST['votos3'];
$votos1 = 120;
$votos2 = 360;
$votos3 = 45;


   $totalVotos = $votos1 + $votos2 + $votos3;
 
   $porcentaje1 = round (($votos1/$totalVotos)*100,2);
   $angulo1 = 3.6 * $porcentaje1;
   $porcentaje2 = round (($votos2/$totalVotos)*100,2);
   $angulo2 = 3.6 * $porcentaje2;
   $porcentaje3 = round (($votos3/$totalVotos)*100,2);
   $angulo3 = 3.6 * $porcentaje3;
 
 
// Crear imagen
   $imagen = imagecreate (300, 300);
   $colorfondo = imagecolorallocate ($imagen, 203, 203, 203); // CCCCCC color gris de fondo de tarta
   $color1 = imagecolorallocate ($imagen, 255, 0, 255); // FF0000 Color rojo de tarta
   $color2 = imagecolorallocate ($imagen, 0, 255, 0); // 00FF00 Color verde de tarta
   $color3 = imagecolorallocate ($imagen, 0, 0, 255); // 0000FF Color azul de tarta
   $colortexto = imagecolorallocate ($imagen, 0, 0, 0); // 000000 color negro de letra
  
 
// Mostrar tarta
   imagefilledrectangle ($imagen, 0, 0, 300, 300, $colorfondo);
    imagefilledarc ($imagen, 150, 120, 200, 200, 0, $angulo1, $color1, IMG_ARC_PIE);
    imagefilledarc ($imagen, 150, 120, 200, 200, $angulo1, $angulo2, $color2, IMG_ARC_PIE);
    imagefilledarc ($imagen, 150, 120, 200, 200, $angulo2, 360, $color3, IMG_ARC_PIE);
 
// Mostrar leyenda
   imagefilledrectangle ($imagen, 60, 250, 80, 260, $color1);
   $texto1 = "Total Notas 1: " . $votos1 . " (" . $porcentaje1 . "%)";
   imagestring ($imagen, 3, 90, 220, $texto1, $colortexto);
   imagefilledrectangle ($imagen, 60, 270, 80, 280, $color2);
   $texto2 = "Total Notas 2: " . $votos2 . " (" . $porcentaje2 . "%)";
   imagestring ($imagen, 3, 90, 250, $texto2, $colortexto);
   imagefilledrectangle ($imagen, 60, 270, 80, 280, $color3);
   $texto3 = "Total Notas 3: " . $votos3 . " (" . $porcentaje3 . "%)";
   imagestring ($imagen, 3, 90, 270, $texto3, $colortexto);
 
   imagepng ($imagen);
   imagedestroy ($imagen);
?>
 