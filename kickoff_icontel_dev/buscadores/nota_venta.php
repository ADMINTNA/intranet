<?PHP 
// ==========================================================
// intranet/kickoff/buscadores/nota_venta.php
// Descripción: buscador de Notas de Venta en Sweet
// Autor: Mauricio Araneda
// Fecha: 2025-11-17
// Codificación: UTF-8 sin BOM
// ==========================================================

    header('Content-Type: text/html; charset=utf-8');
    include_once("funciones.php");
    $usuarios   = busca_columna("CALL `facturas_usuarios`()");
    $tipofac    = busca_columna("CALL `facturas_tipo_factura`()");
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

<form action="../../notas_de_venta/busqueda_session.php" method="post" target="_blank" name="clientes" id="clientes">
	<table height="100%"  style="background-color: #1F1D3E;color: white;  border-style: solid; border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px;  border-color: dimgrey;">
		  <tbody>
				<tr>
					<td colspan="2" align="center"><strong style="font-size: 20px;">Notas de Venta Sweet</strong></td>
				</tr>
				<tr>
					<td width="90px" style="background-color: #1F1D3E;color: white;"> 
						<label>N°:</label></td>
		   			<td style="background-color: #1F1D3E;color: white;">
						<input style="background-color: lightgray" name="numero" type="text" id="numero"  value="">
					</td>
				</tr>
				<tr>
					<td style="background-color: #1F1D3E;color: white;"> 
						<label>Título:</label></td>
					<td style="background-color: #1F1D3E;color: white;">
						<input style="background-color: lightgray" name="titulo" type="text" id="titulo" value="">
					</td>
				</tr>
				<tr>
					<td style="background-color: #1F1D3E;color: white;"> 
						<label>Cliente:</label>
					</td>
					<td style="background-color: #1F1D3E;color: white;">
						<input style="background-color: lightgray" name="cliente" type="text" id="cliente" value="">
					</td>
				</tr>
				<tr>
					<td  style="background-color: #1F1D3E;color: white;"><label>Tipo Factura</label>:</td>
					<td>
						<?php crea_select($tipofac, "tipofac", 0, 3); ?>
					</td>
				</tr>
				<tr>
					<td style="background-color: #1F1D3E;color: white;"> 
						<label>Usuario:</label>
					</td>
					<td>
						<?php crea_select($usuarios, "usuario", 0, 3); ?>
					</td>
				</tr>
				<tr>
					<td align="center" style="background-color: #1F1D3E;color: white;">
						
                        <button type="button" onclick="resetForm('clientes')" style="background-color: lightgray; color: gray; font-size: 12px; cursor: pointer;">Limpiar</button>					
                    </td>
					<td align="center" >
						<input style="background-color: lightgray; font-size: 12px; cursor: pointer;" name="submit" type="submit" value="Buscar Notas en Venta" />
					</td>
				</tr>
			</form>
		  </tbody>
		</table>
	                             
