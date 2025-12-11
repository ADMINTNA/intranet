<?php 
$tmp = busca_casos_creados("a"); 
$tmp = busca_casos_creados("12h");
$tmp = busca_casos_creados("24h");
$tmp = busca_casos_creados("48h");
$tmp = busca_casos_creados("1s");
$tmp = busca_casos_creados("15d");
$tmp = busca_casos_creados("1m");
$tmp = busca_casos_creados("6m");
$tmp = busca_casos_creados("1a");

function busca_casos_creados($periodo){
  switch($periodo) {
      case "a":
        $sql = "CALL searchopencases()";       
        break;
      case "12h":
        $sql = "CALL searchcasescreatedbyhours('12')";       
        break;
      case "24h":
        $sql = "CALL searchcasescreatedbyhours('24')";       
        break;
      case "48h":
        $sql = "CALL searchcasescreatedbyhours('48')";       
        break;
      case "1s":
        $sql = "CALL searchopencasesbydays('7')";       
        //$sql = "CALL searchcasescreatedbyhours('168')";       
        break;
      case "15d":
        $sql = "CALL searchopencasesbydays('15')";       
        //$sql = "CALL searchcasescreatedbyhours('360')";       
        break;
      case "1m":
        $sql = "CALL searchopencasesbymonth('1')";       
        //$sql = "CALL searchcasescreatedbyhours('720')";       
        break;
      case "6m":
        $sql = "CALL searchopencasesbymonth('6')";       
        // $sql = "CALL searchcasescreatedbyhours('4380')";       
       break;
      case "1a":
        $sql = "CALL searchopencasesbymonth('12')";       
       // $sql = "CALL searchcasescreatedbyhours('8760')";       
        break;
      default:
    } 
    // me conecto a la Base de Datos
    $conn = DbConnect("tnasolut_sweet");
    $result = $conn->query($sql);
    if ($result->num_rows > 0)   { 
        $total_abierto=0;       
        $total_cerrado=0;
        while($row = $result->fetch_assoc()) {
          switch(strtolower($row["categoria"])) {
              case "cableado":
                $id = "cableado".$periodo; 
                break;
              case "enlace":
                $id = "enlace".$periodo;     
                break;
              case "enlace_caido":
                $id = "caido".$periodo; 
                break;
              case "facturacion":
                $id = "factura".$periodo; 
                break;
              case "fuera_de_horario":
                $id = "fuera".$periodo; 
                break;
              case "hosting":
                $id = "hosting".$periodo; 
                break;
              case "nuevo_requerimiento":
                $id = "nuevo".$periodo; 
                break;
              case "otros":
                $id = "otros".$periodo; 
                break;
              case "soporte":
                $id = "soporte".$periodo; 
                break;
              case "telefonia":
                $id = "telefonia".$periodo; 
                break;
              case "termino_contrato":
                $id = "termino".$periodo; 
                break;
              default:
          } 
          $linea ="document.getElementById('".$id."').innerHTML = '".$row["cuantos"]."'";     
    
          if($row["state"] != "Closed") {
              $total_abierto += $row["cuantos"]; 
          } else {
              $total_cerrado += $row["cuantos"];
          }
          echo '<script type="text/javascript">';
          echo $linea;   
          echo '</script>';
        }
        $total = $total_abierto."/".$total_cerrado;
        $linea ="document.getElementById('total".$periodo."').innerHTML = '".$total."'";     
        
        echo '<script type="text/javascript">';
        echo $linea;   
        echo '</script>';
        // echo $datos_completos;
    } 
    $conn->close(); 
}
?>
