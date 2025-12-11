<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
<?PHP include_once("../../meta_data/meta_data.html"); ?>   
    <title>Casos iConTel</title>
    <style type="text/css">
        table {
               border: none;
               color: #1F1D3E;
               color: black;
               font-size: 12px;
               border-collapse: collapse;
           }   
          th, td {
              padding: 2px;
              font-size: 14px;
         }
         th {
            background-color: #1F1D3E; 
            color: white;
         }
         body{
            margin:0;
            padding:0;
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
    <meta http-equiv="refresh" content="300"> 
    <!-- script language="JavaScript">
        var ptr=0   

        function mueveReloj(){
            if (ptr==0){
                ptr=1
                reload = new Date()
                reload.setMinutes ( reload.getMinutes() + 5 )
                h_reload = reload.getHours()
                if(h_reload<10) h_reload = "0" + h_reload
                m_reload = reload.getMinutes()
                if(m_reload<10) m_reload ="0"+m_reload
                s_reload = reload.getSeconds()
                if(s_reload<10) s_reload ="0"+s_reload        
                hora_reload = h_reload + " : " + m_reload + " : " + s_reload
            }
            momentoActual = new Date()
            hora = momentoActual.getHours()
            if(hora<10) hora ="0"+hora      
            minuto = momentoActual.getMinutes()
            if(minuto<10) minuto ="0"+minuto
            segundo = momentoActual.getSeconds()
            if(segundo<10) segundo ="0"+segundo
            horaImprimible = hora + " : " + minuto + " : " + segundo        
            document.form_reloj.reloj.value = horaImprimible
            document.form_reloj.reload.value = hora_reload
            setTimeout("mueveReloj()",1000)
        }
    </script-->        
    </head>
    <!--body bgcolor="#FFFFFF" text="#1F1D3E" link="#E95300" onload="mueveReloj()"-->
    <body bgcolor="#FFFFFF" text="#1F1D3E" link="#E95300">
    <?php 
        include_once("tabla.php");      
        if($categoria == "kickoff") {
            include_once("tabla_ultimos_casos.php");
            include_once("tabla_tareas.php");
        }
    ?>

    </body> 
</html>

        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
  