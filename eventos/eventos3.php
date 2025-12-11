<?php
//include_once("../includes/session.php")
 //ini_set('display_errors', 1);
 //ini_set('display_startup_errors', 1);
 //error_reporting(E_ALL);
//////// Funciones //////////////
function DbConnect($dbname){ // se conecta a base de datos y devuelve $conn
    $server   = "localhost";
//    $user     = "tnasolut_data_studio";
//    $password = "P3rf3ct0.,";
    $user     = "data_studio";
    $password = "1Ngr3s0.,";
    // me conecto a la Base de Datos
    $conn = new mysqli($server, $user, $password);
    if ($conn->connect_error) { die("No me pude conectar a servidor localhost: " . $conn->connect_error); }
    $dummy = mysqli_set_charset ($conn, "utf8");    
    $bd_seleccionada = mysqli_select_db($conn, $dbname);
    if (!$bd_seleccionada) { die ('No se puede usar '.$dbname.' : ' . mysql_error()); }
    return($conn);
}
function Valor_Diario_Moneda($cual){  // Dolar = 2, EURO=3, UF=6  
	$moneda = array();
	$conn = DbConnect("tnasolut_monedas");
		$sql = "CALL moneda_ultimo_valor_num_moneda({$cual})";                    
		$result = $conn->query($sql);
		$ptr=0;
		 if($result->num_rows > 0)  { 
			while($row = $result->fetch_assoc()) {
			  $ptr ++; 
			  switch ($cual){    
			  case "2":
				$moneda[0] = "USD";
				break;
			  case "3":
				 $moneda[0] = "EURO";
				break;
			  case "6":
				 $moneda[0] = "UF";
				break;
			   default:
				 $moneda[0] = "Error Moneda desconocida.";                     
			  }  
			  $moneda[1] = $row["fecha"];				  
			  $moneda[2] = $row["valor"];
			}
		} 
	$conn->close();
	unset($result);
	unset($conn);
	return $moneda;
} 
    $tmp      = Valor_Diario_Moneda(6);  // Dolar = 2, EURO=3, UF=6
	$UF    	  = $tmp[2];
    $UF_Fecha = date("d-m-Y", strtotime($tmp[1]));
	$tmp      = Valor_Diario_Moneda(2);  // Dolar = 2, EURO=3, UF=6
	$USD   	  = $tmp[2];
	$USD_Fecha= date("d-m-Y", strtotime($tmp[1]));
?>

<html>
<head>
<meta charset="UTF-8">
<?PHP include_once("../meta_data/meta_data.html"); ?>       
<title>Eventos</title>
    <script type='text/javascript'>
  
        // JavaScript anonymous function
        (() => {
            if (window.localStorage) {
  
                // If there is no item as 'reload'
                // in localstorage then create one &
                // reload the page
                if (!localStorage.getItem('reload')) {
                    localStorage['reload'] = true;
                    window.location.reload();
                } else {
  
                    // If there exists a 'reload' item
                    // then clear the 'reload' item in
                    // local storage
                    localStorage.removeItem('reload');
                }
            }
        })(); // Calling anonymous function here only
    </script>
    
<style type="text/css">
.iframe_ev { 
    margin:0;
    padding:0;
    height:230px;
    width:100%;
    margin-left: 0px;
    margin-top: 0px;
    margin-right: 0px;
    margin-bottom: 0px;
    display:block; width:100%;  
    border: none; 
    border-color: red;
    background-color: #15173C;
}    

.table_mao{
    border: thin;
    border-color: white;
    border-collapse: collapse;
    background-color: #15163C;
    }  
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
    font-family: "Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", "DejaVu Sans", "Verdana", "sans-serif";
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
h1 { font-size: 36px; }
h2 { font-size: 24px; }
h3 { font-size: 18px; }
h4 { font-size: 16px; }
h5 { font-size: larger; }
h6 { font-size: smaller; }
</style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<table border='0' id="eventos" width="100%">
		<tr>
		  <td colspan="1" align="left" style="font-size: 20px" height="30"><b>&nbsp; #somosIconTel</b</td>
		  <td align="right">Valor UF <?php echo $UF; ?> USD <?php echo $USD; ?> al  <?PHP echo $UF_Fecha;?>&nbsp;&nbsp;</td>	  
	    </tr>
		<tr>
		  <td colspan="1" width="35%" align="left" bgcolor="#CF6F2A" style="font-size: 16px;" height="30">&nbsp;TRABAJANDO JUNTOS PARA DAR EL MEJOR SERVICIO !!</td>
          <td colspan="1" rowspan="2">
	           <table width="100%" border='0' id="iframes">
                    <tr>
                        <td width="37%" align="center">
                            <iframe class="iframe_ev" src="/eventos/busca.html"></iframe>
                        </td>
                        <td align="center" >
                            <iframe class="iframe_ev" src="../casos/index.php"></iframe>
                        </td>                                                
                   </tr>
              </table>
          </td>
		</tr>
		<tr>
		  <td colspan="1" align="left" bgcolor="#FFFFFF" scope="row"><img src="/eventos/images/logo_icontel_no_bg.png" width="" height="180" alt=""/></td>
		</tr>
		<tr>
		  <td colspan="2" height="4"  align="left" ></td>
		</tr>		
		<tr align='center'>
            <td colspan="2" id="informe">
                <table border="0" width="100%" style="border-color: yellow;">
                    <tr>
                        <td><iframe frameborder="0" width="100%" height="200px" src="/eventos/datos.php"></iframe></td>
                    </tr>
                </table>
            </td>
        </tr>    
    </table>
</body>
</html>