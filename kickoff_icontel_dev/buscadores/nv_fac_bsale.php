<?PHP include_once("funciones.php"); 
// ==========================================================
// intranet/kickoff/buscadores/nv_fac_base.php
// Descripción: buscador de Notas de Venta en BSale
// Autor: Mauricio Araneda
// Fecha: 2025-11-17
// Codificación: UTF-8 sin BOM
// ==========================================================
   header('Content-Type: text/html; charset=utf-8');

 //    $usuarios   = busca_columna("CALL `facturas_usuarios`()");
//	 $tipofac    = busca_columna("CALL `facturas_tipo_factura`()");
?>

<script>
function resetForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return;

    // Resetea todos los inputs (text, hidden, radio, checkbox, etc.)
    form.reset();

    // Limpia manualmente todos los <select>, incluidos los multiple[]
    const selects = form.querySelectorAll("select");
    selects.forEach(select => {
        for (let i = 0; i < select.options.length; i++) {
            select.options[i].selected = false;
        }
    });
}
</script>

<form action="../../bsale/busqueda_resultado.php" method="post" target="_blank" name="clientes" id="clientes">
	<table height="100%"  style="background-color: #1F1D3E;color: white;  border-style: solid; border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px;  border-color: dimgrey;">
		  <tbody>
				<tr>
					<td colspan="2" align="center"><strong style="font-size: 20px;">NV y Facturas Bsale</strong></td>
				</tr>
				<tr>
					<td width="64" style="background-color: #1F1D3E;color: white;"> 
                          <label for="tipo">Tipo:</label>
					<td style="background-color: #1F1D3E;color: white;">
                          <select name="tipo" id="tipo">
                            <option value="">Todos</option>
                            <option value="fac">Factura</option>
                            <option value="nv">Nota de Venta</option>
                          </select>
					</td>
				</tr>
				<tr>
					<td  style="background-color: #1F1D3E;color: white;"> 
						<label>N°:</label></td>
		   			<td style="background-color: #1F1D3E;color: white;" >
						<input style="background-color: lightgray; width: 100px;" name="numero" type="text" id="numero"  value="">
					</td>
				</tr>
                 <tr><td  height="96" colspan="2" style="background-color: #1F1D3E;; color: #1F1D3E;"></td></tr>
				<tr>
					<td align="center" style="background-color: #1F1D3E;color: white;">
						
                        <button type="button" onclick="resetForm('clientes')" style="background-color: lightgray; color: gray; font-size: 12px; cursor: pointer;">Limpiar</button>					
                    </td>
					<td align="center" >
						<input style="background-color: lightgray; font-size: 12px; cursor: pointer;" name="submit" type="submit" value="Buscar NV y Facturas" />
					</td>
				</tr>
			</form>
		  </tbody>
		</table>
	                             
