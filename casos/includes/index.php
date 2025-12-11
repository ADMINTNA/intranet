<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
<?PHP include_once("../meta_data/meta_data.html"); ?>   
    <title>Casos iContel</title>
    <meta http-equiv="refresh" content="300"> 
    <script language="JavaScript">
        var seconds = 300; //número de segundos a contar
        function secondPassed() {
          var minutes = Math.trunc(seconds/60); //calcula el número de minutos
          var remainingSeconds = seconds % 60; //calcula los segundos restantes
          //si los segundos y minutos usan sólo un dígito, añadimos un cero a la izq
          if (remainingSeconds < 10) remainingSeconds = "0" + remainingSeconds; 
          if (minutes < 10)  minutes = "0" + minutes; 
          document.getElementById('titulo').innerHTML = "Casos x Categoría, actualización en: "+minutes + ":" + remainingSeconds; 
          if (seconds == 0) { 
            clearInterval(countdownTimer); 
            location.reload(true);  
          } else seconds--; 
        } 
        var countdownTimer = setInterval(secondPassed, 1000);
 /*       function mueveReloj(){ reemplaza el campo id=reloj con la hora actual
            momentoActual = new Date()
            hora    = zeroFill(momentoActual.getHours(),2);
            minuto  = zeroFill(momentoActual.getMinutes(),2);
            segundo = zeroFill(momentoActual.getSeconds(),2);
            horaImprimible =  hora + " : " + minuto + " : " + segundo;
            document.getElementById("reloj").innerHTML = horaImprimible;
            setTimeout("mueveReloj()",1000);
        }
        function zeroFill( number, width ) { // rellena ceros a la izquierda
          width -= number.toString().length;
          if ( width > 0 ) return new Array( width + (/\./.test( number ) ? 2 : 1) ).join( '0' ) + number;
          return number + ""; // siempre devuelve tipo cadena
        }
*/        
    </script>        
    <style type="text/css">
        .table_alarmas{
               border: none;
               color: #1F1D3E;
               color: black;
               border-collapse: collapse;
           } 
         .abierto{
            color: orangered;
             font-weight: bold; 
          }
        .tabla_casos {
            width: 100%;
            height: 100%;
            border: 0px;
            background-color: #1F1D3E;
            border-collapse: collapse; 
            font-size: 13px;
        }
         .negro{
             color: black;
             font-weight: bold; 
             background-color:#1F1D3E;
          }
          th {
              padding: 2px;
              background: #1F1D3E;
              color: white;
         }
          th_right {
              padding: 2px;
              background: #1F1D3E;
              color: white;
			  text-align-last: right;
         }
          td {
              padding: 3px;
         }
         body{
            margin:0;
            padding:0;
            margin-left: 0px;
            margin-top: 0px;
            margin-right: 0px;
            margin-bottom: 0px;
            font-size: 14px;
        }
        table tbody tr:nth-child(odd) {
            background: #F6F9FA;
        }
        table tbody tr:nth-child(even) {
            background: #FFFFFF;
        }
    </style>
    </head>
<!--body onload="mueveReloj()"> solo para mostrar reloj cada segundo --> 
<body>
    <?php
		include_once("includes/config.php");    
        include_once("includes/tabla.html");
        include_once("includes/busca.php");
    ?>
</body>        
</html>
