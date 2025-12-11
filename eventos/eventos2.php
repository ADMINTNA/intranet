<?php
include_once("session.php");
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>
<html>
<head>
<meta charset="UTF-8">
<title>Eventos</title>
<style type="text/css">
.iframe_ev { 
    margin:0;
    padding:0;
    height:218px;
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
		  <td colspan="8" align="left" style="font-size: 20px" height="30"><b>&nbsp; #somosIconTel</b</td>
	    </tr>
		<tr>
		  <td colspan="4" align="left" bgcolor="#CF6F2A" style="font-size: 18px;" height="30">&nbsp;TRABAJANDO JUNTOS PARA DAR EL MEJOR SERVICIO !!</td>
          <td colspan="4" rowspan="2">
	           <table width="100%" border='0' id="iframes">
                    <tr>
                        <td width="35%" align="center">
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
		  <td colspan="5" align="left" bgcolor="#FFFFFF" scope="row"><img src="/eventos/images/logo_icontel_no_bg.png" width="" height="180" alt=""/></td>
		</tr>
		<tr>
		  <td colspan="8" height="4"  align="left" ></td>
		</tr>		
		<tr align='center'>
            <td colspan="8" id="informe">
                <table border="0" width="100%" style="border-color: yellow;">
                    <tr>
                        <td><iframe frameborder="0" width="100%" height="232px" src="/eventos/datos.php"></iframe></td>
                    </tr>
                </table>
            </td>
        </tr>    
    </table>
</body>
</html>