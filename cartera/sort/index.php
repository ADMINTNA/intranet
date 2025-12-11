<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
<?PHP include_once("../../meta_data/meta_data.html"); ?>   
  <title>Buscador Cartera iConTel</title>
   <style type="text/css">
   	html, body, div {
   		margin:0;
   		padding:0;
   		height:100%;
   		margin-left: 0px;
   		margin-top: 0px;
   		margin-right: 0px;
   		margin-bottom: 0px;
   	}
    table{
           border: none;
           color: #1F1D3E;
           color: white;
           font-size: 15px;
           border-collapse: collapse;
           background-color: #19173C;
           border-collapse: collapse;

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
	  color: gray;
	}
	/* visited link */
	a:visited {
	  color: gray;
	}
	/* mouse over link */
	a:hover {
	  color: darkgrey;
	  font-size: 20px;
	  font-weight: bold;
	}
	/* selected link */
	a:active {
	  color: blue;
	}		
  </style>	 
  </head>
  <body>
   <table align="center" border="0" width="100%">
        <tr align="center" style="color: white;background-color: #1F1D3E;">
          <th width="200" height="130" valign="top" align="left"><img src="../images/logo_icontel_azul.jpg"  height="115" alt=""/></th>
          <td>
              <table width="100%" height="100%">
                  <tr height="90">
                      <th align="center" style="font-size: 20px;">Buscador de Cartera en Sweet<br />
						  <span style="font-size: 12px;">(click sobre los títulos de las Columnas para ordenar)</span>
					 </th>
                  </tr>
              </table>
          </td>    
        </tr>
    </table>
  <iframe style="margin: 0 0 0 0;" src="tabla.php" ></iframe>
   </body>
<footer align="center">
℗®™&copy; Copyright <span id="Year"></span><b> iConTel </b>- <a href="tel:228409988">â˜Ž+56 2 2840 9988</a> - <a href="mailto: contacto@tnasolutions.cl?subject=Contacto desde Intranet iConTel.">ðŸ“§contacto@tnasolutions.cl</a> - ðŸ Badajoz 45, piso 17, Las Condes, Santiago, Chile.<br> 
	<script type="text/javascript"	>
		var d = new Date(); 
		document.getElementById("Year").innerHTML = d.getFullYear();
	</script>	
</footer>	
</html>
