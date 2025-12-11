<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Documento sin título</title>
<!--script language="JavaScript">
    function mueveReloj(){
        momentoActual = new Date()
        hora = momentoActual.getHours()
        minuto = momentoActual.getMinutes()
        segundo = momentoActual.getSeconds()
        if(segundo<10) { segundo="0"+segundo; }
        if(minuto<10) { minuto="0"+minuto; }
        if(hora<10) { hora="0"+hora; }
        horaImprimible = "Hora:"+hora + " : " + minuto + " : " + segundo
        document.getElementById("reloj").innerHTML = horaImprimible
        setTimeout("mueveReloj()",1000)
    }
</script-->
<style type="text/css">
table{
    border: thin;
    border-color: white;
    border-collapse: collapse;
    background-color: #15163C;
    color: white;
    }  
td {
  padding: 0px;
}
    
body,td,th {
    color: #FFFFFF;
    font-family: "Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", "DejaVu Sans", Verdana, sans-serif;
    font-size: 12px;
    padding: 0px;
}
a:link {
    color: #CF6F2A;
}
body {
    margin-left: 0px;
    margin-top: 0px;
    margin-right: 0px;
    margin-bottom: 0px;
}
h1 {
    font-size: 36px;
}
h2 {
    font-size: 24px;
}
h3 {
    font-size: 18px;
}
h4 {
    font-size: 16px;
}
h5 {
    font-size: larger;
}
h6 {
    font-size: smaller;
}
</style>
    
</head>

<body>
 <table border="0" style="border-color: red;" width="100%">   
    <tr align='center'>
        <th align='center'><a href='/eventos/mantencion.php' target="_blank">#</a></th>
        <th align="left">SERVICIO</th>
        <th>EVENTOS</th>
        <th align='center'>ULTIMA REPOSICION</th>
        <th>DURACION</th>
        <th align="left">DESCRIPCION</th>
        <th>DIAS SIN<br>EVENTOS</th>
        <th rowspan='9' align='center' ><div id='grafico'>aqui<br>va<br>el<br>gráfico</div></th>
    </tr>
        <?php
            function horacl($date) {
                global $userTimeZone;
                $dateNeeded = new DateTime($date); 
                $dateNeeded->setTimeZone($userTimeZone);
                return($dateNeeded->format('d-m-Y H:i:s')) ;
            }     
            $desde = new DateTime("2021-01-01");
            $hoy   = new DateTime(NOW);
            $diff = $desde->diff($hoy);
            $db_host="localhost";
            $port=3306;
            $socket="";
            $db_user="tnasolut_data_studio";
            $db_pwd="P3rf3ct0.,";
            $db_name="tnasolut_eventos";
            $con = new mysqli($db_host, $db_user, $db_pwd);
            if (!$con) die("No se conecta a servidor");
            mysqli_select_db($con,$db_name);
            mysqli_set_charset($con, 'utf8');
            $query = "SELECT e1.servicio,
                       (select count(servicio)-1 FROM eventos as e3 where e3.servicio=e1.servicio) as eventos,      
                       e1.fecha_fin,
                       TIMEDIFF(e1.fecha_fin, e1.fecha_ini) as duracion,
                       e1.desc_evento,       
                       DATEDIFF(now(), e1.fecha_fin) as dias_up      
                FROM eventos as e1 INNER JOIN ( SELECT servicio, MAX(fecha_fin) as fecha_fin FROM eventos GROUP BY servicio ) as e2
                ON (e1.fecha_fin = e2.fecha_fin AND e1.servicio = e2.servicio)
                ORDER BY e1.fecha_fin desc; ";
            $result = mysqli_query($con,$query);
            $servicios = mysqli_num_rows($result);
            if (!$result) { die("Error para mostrar los datos."); }
            $fields_num = mysqli_num_fields($result);
            $linea = 0;	
            $data  = array(); // array a graficar
            while($row = mysqli_fetch_row($result)) {
                $linea ++; ?>
                <tr>
                <td bgcolor="#15173C" align='center' style='width: 2%'><?PHP echo $linea;?></td>	
                <?PHP													 
                $col = 0; 
                $servicio = $row[0];
                $data[$servicio] = $row[5]; // datos a graficar
                foreach($row as $cell) {
                    $col ++;
                    switch ($col) {
                        case 1: // Servicio
                            ?> <td align='left'><?PHP echo ucwords($cell); ?></td><?php
                            break;
                        case 3: //Fecha
                            ?> <td align='center'><?PHP echo horacl($cell); ?></td><?php 
                            break;
                        case 5: //Descripción
                            ?> <td align='left'><?PHP echo ucwords($cell); ?></td><?php 
                            break;
                        case 6: // Días sin evento
                            ?> <td bgcolor="#CF6F29" align='center'><?PHP echo $cell; ?></td><?PHP
                            break;
                        default:
                            ?> <td align='center'><?PHP echo $cell; ?></td><?PHP
                    }
     /*               if($col==1){ //servicio 
                         ?> <td align='left'><?PHP echo ucwords($cell); ?></td><?php
                    } elseif($col==5){ //descripcion  
                         ?> <td align='left'><?PHP echo ucwords($cell); ?></td><?php 
                    } elseif($col==6){ // Dias sin evento 
                         ?> <td bgcolor="#CF6F29" align='center'><?PHP echo $cell; ?></td><?PHP
                    } else {
                        ?> <td align='center'><?PHP echo $cell; ?></td><?PHP 
                    } */
                } // fin de foreach ?> 
                </tr>
                <?PHP } // fin while ?> 
                <tr>
                    <td height="5"  bgcolor="#CF6F29" colspan=8 align='center' >Nota: Datos de Monitoreo desde el 01/01/2021 00:00:00 Hrs.(Hace <?PHP echo $diff->days . ' dias'; ?>)</td>
                </tr>
                <tr>
                  <td  height="4" colspan=8  align="left"></td>
                </tr>		
                </table>

            <?PHP
            // agrego gráfico en google
            include( 'GoogChart.class.php' );
            $chart = new GoogChart();
            //$color = array('#99C754','#54C7C5','#999999',);
            $color = array('#e0440e', '#e6693e', '#ec8f6e', '#f3b49f', '#f6c7b6');
            //$color = array ('#95b645', '#7498e9', '#999999', '#FFFF00', '#FF0000', '#00FFFF', '#996633', '#FF00FF', '#006633');
            $chart->setChartAttrs( array(
                                    'type' => 'pie',
                                    'title' => 'Estabilidad de Servicio',
                                    'data' => $data,
                                    'size' => array( 360, 180 ), // ancho, alto
                                    'color' => $color,
                                    'background' => '#F1F1F1'
                                    ));
            echo  "<script>document.getElementById('grafico').innerHTML = '".$chart."'</script>";
            if(!$_SESSION['loggedin']) {
                    echo  "<script>document.getElementById('eventos').style.visibility = \"hidden\";</script>";
            } else {
                    echo  "<script>document.getElementById('eventos').style.visibility = \"visible\";</script>";
            }
?>
</body>
</html>