<?php 
	session_start();
	include "config.php"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
    <?PHP include_once("../../meta_data/meta_data.html"); ?>
    <title>Servicios Activos por Cliente</title>
        <link href='style.css' rel='stylesheet' type='text/css'>
        <script src='jquery-3.3.1.min.js' type='text/javascript'></script>
        <script src='script.js' type='text/javascript'></script>
     <style type="text/css">
        table {
               border: none;
               color: #1F1D3E;
               color: black;
               font-size: 10px;
               border-collapse: collapse;
           }   
          th, td {
              padding: 4px;
              font-size: 12px;
         }
         th {
            background-color: #1F1D3E; 
            color: white;
         }
         body{
            margin:0;
            padding:0px;
            margin-left: 0px;
            margin-top: 0px;
            margin-right: 0px;
            margin-bottom: 0px;
            font-size: 10px;
            background-color: #FFFFFF;
            color: #1F1D3E;
        }
        table tbody tr:nth-child(odd) {
            background: #F6F9FA;
        }
        table tbody tr:nth-child(even) {
            background: #FFFFFF;
        }
        table thead {
          background: #444;
          color: #fff;
          font-size: 18px;
        }
        table {
          border-collapse: collapse;
        }            
    </style>
    <script type="text/javascript">
        function exportToExcel(tableId){
            let tableData = document.getElementById(tableId).outerHTML;
            tableData = tableData.replace(/<A[^>]*>|<\/A>/g, ""); //remove if u want links in your table
            tableData = tableData.replace(/<input[^>]*>|<\/input>/gi, ""); //remove input params

            let a = document.createElement('a');
            a.href = `data:application/vnd.ms-excel, ${encodeURIComponent(tableData)}`
            a.download = 'Contactos_Clientes_' + getRandomNumbers() + '.xls'
            a.click()
        }
        function getRandomNumbers() {
            let dateObj = new Date()
            let dateTime = `${dateObj.getHours()}${dateObj.getMinutes()}${dateObj.getSeconds()}`

            return `${dateTime}${Math.floor((Math.random().toFixed(2)*100))}`
        }        
    </script>     
    </head>
    <body bgcolor="#FFFFFF" text="#1F1D3E" link="#E95300" >
        <div class='container'>
            <input type='hidden' id='sort' value='asc'>			
            <table id="empTable" name="empTable" width='100%' border='1' cellpadding='10'>
                <tr>
                    <th>#</span></th>
                    <th><span onclick='sortTable("empresa");'>Cliente</span></th>
                    <th><span onclick='sortTable("office_tel");'>Fono</span></th>
                    <th><span onclick='sortTable("contacto");'>Contacto</span></th>
                    <th><span onclick='sortTable("cargo");'>Cargo</span></th>
                    <th width="7%"><span onclick='sortTable("tipo_contacto");'>Tipo Contacto</span></th>
                    <th><span onclick='sortTable("celular");'>Celular</span></th>
                    <th><span onclick='sortTable("telefono");'>Tel&eacute;fono</span></th>
                    <th><span onclick='sortTable("email");'>eMail&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" onClick="exportToExcel('empTable')" value="Export to Excel" /></span></th>
               </tr>
               <?php 
					include_once("tabla_datos.php"); // muestra datos
			   ?>
            </table><br><br>
             <br><br>     
        </div>
    </body>
</html>