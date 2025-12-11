<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
        <!--link rel="stylesheet" type="text/css" href="../css/style.css"/-->
        <title>Calls TNA</title>
		<link rel="stylesheet" href="busca.css">
     </head>
    <body bgcolor="#FFFFFF" text="#1F1D3E" link="#E95300" >
  <?php
    include_once("config.php");
    echo '<div class="contenedor-scroll-empresa">';
        include_once("busca_empresa.php");
    echo '</div>';
    include_once("busca_duemint.php");
    include_once("informe.php");
  ?>
</body>
</html>
