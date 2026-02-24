<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
<?PHP include_once("../meta_data/meta_data.html"); ?>   
    <title>Servicios Activos por Cliente iContel</title>
    <meta http-equiv="refresh" content="300"> 
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
   	iframe {
        border: none;
        border-collapse: collapse;
        padding: 0;
        margin: 0;
        display:block; 
        width:100%;  
        height: 80%;
    }        
        footer {
          background-color: white;
          position: absolute;
          bottom: 0;
          width: 100%;
          height: 25px;
          color: gray;
          font-size: 12px;
        }
        /* unvisited link */
        a:link {
          color: darkslategrey;
        }

        /* visited link */
        a:visited {
          color: white;
        }
    </style>
    </head>
<!--body onload="mueveReloj()"> solo para mostrar reloj cada segundo --> 
<body>
    <?php
         include_once("includes/config.php");    
      //  include_once("includes/tabla.html");    
        include_once("includes/buscasincosto.php");
    ?>
</body>   
</html>
