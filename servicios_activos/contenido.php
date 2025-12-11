     <div class='container'>
      <table align="center" border="0" id="tblData" name="tblData">
        <tr align="center" style="color: white; background-color: #1F1D3E;">
          <th colspan = "2" width="200" height="130" valign="top" align="left"><img src="./images/logo_icontel_azul.jpg"  height="115" alt=""/></th>
          <td colspan="2">
              <table width="100%" height="100%">
                  <tr height="90">
                      <th align="right" valign="bottom" style="font-size: 20px;">Informe de Servicios Activos por Cliente<br><br>
                      	<span align="right" style="color: white; font-size: 12px;"><a href="serviciosxfamilia.php">Recurrencias x Familia Precio de Costo y Venta</a></span><br>
                      	<span align="right" style="color: white; font-size: 12px;"><a href="serviciosxfamiliasincosto.php">Recurrencias x Familia solo Precio de Venta </a></span>
					  </th>
              </table>
          </td>
        </tr>
        <form  method = 'post' action="informe.php" target="_blank" >
            <tr>
                <td><?php crea_select($recurrencias, "Recurrencia"); ?></td>              
                <td><?php crea_select($categorias, "Categoria"); ?></td>              
                <td><?php crea_select($productos, "Producto"); ?></td>              
                <td><?php crea_select($codigo, "Codigo"); ?></td>              
                <td>
                    <table border="0" width="100%" height="100%">
                      <tr height="33%">
                        <td align="center">
                            <input type = 'submit' name = 'submit' value = 'Generar Informe'><br><br><br>
                        </td>
                      </tr>
                      <tr height="33%" valign="center">
                        <td align="center">
                            <input type="reset" name='reset' value = 'RESET'>
                        </td>
                      </tr>
                      </tr>
                      <tr height="33%" valign="center">
                        <td align="center">
                            <a href="#" onclick="genera_base()">Re-Generar los Datos</a>
                        </td>
                      </tr>
                    </table>
                </td>
            </tr>
        </form>       
      </table>    
    </div>
