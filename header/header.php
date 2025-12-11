<style type="text/css">

body,td,th {
    font-family: Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;
}
a {
    font-family: Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;
    color: #000000;
}
</style>
	<table width="100%" >
		<tbody>
		<tr >
		<td valign="top" style="padding: 0px; margin: 0px; height: 5px; border: none; border-spacing: 0px;" >
			<table width="100%" border="0" >
				<tbody>
				<tr style="color: white; background-color: #64C2C8;" >
				  <td width="6%" rowspan="2" align="center" valign="bottom" widh="40%"><p><strong><img src="../kickoff/images/Robot_Cool_01.png" width="71" height="84" alt=""/></strong><br>
				  </p></td>
				  <td width="15%" rowspan="2" align="center" valign="middle" widh="40%"><p><em>&quot;Cuando todos vendemos, el éxito es inevitable&quot;</em></p></td>
				  <td height="30" colspan="6" align="right" valign="top"><h1>				    #JuntosSomosMás</h1></td>
				  </tr>
				<tr style="color: white; background-color: #64C2C8;" >
				    <td style="vertical-align: bottom">
					<select style="color: white; font-weight: bold; text-decoration: underline; background-color: #64C2C8; border: none; vertical-align: bottom;" name="calculadoras" id="calculadoras" onchange="manejarCambio(this)">
						<option value="">Calculadoras</option>	
						<option value="https://intranet.icontel.cl/fotovoltaico/">Fotovoltaica</option>
					</select>
				  </td>
			 	  <td style="vertical-align: bottom">
					<select style="color: white; font-weight: bold; text-decoration: underline; background-color: #64C2C8; border: none; vertical-align: bottom;" name="formularios" id="formulario" onchange="manejarCambio(this)">
						<option value="">Formularios</option>	
						<option value="https://forms.gle/3eamUcgVp7zFx2Gw6">Referidos</option>
						<option value="https://forms.gle/KckuqAywwEk755KU9"> Fotovoltaico </option>
					</select>
				  </td>
				  <td width="11%" align="center" valign="bottom"><p><a style="color: white; font-weight: bold;" href="https://drive.google.com/drive/folders/1_v-uzpOmaLP9lyLO3onpdzTg29PXskLZ?usp=sharing" target="_blank">Material Campaña</a></p></td>
				  <td width="15%" align="center" valign="bottom"><p><a style="color: white; font-weight: bold;" href="https://calendar.app.google/9oeDbrLGtM3Gzhn28" target="_blank">Agendamiento Comercial</a></p></td>
				  <td  colspan="3"align="center" valign="bottom"><p><a style="color: white; font-weight: bold;" href="https://www.tnagroup.cl/presentacion" target="_blank">Presentación Empresa</a></p></td>
			    </tr>
				<tr style="background-color: #1F1D3E;" >
					<td colspan="2">
					  <p class="infoheader2">UF <?php echo $UF; ?>&nbsp;&nbsp;&nbsp;
					  US <?php echo $USD; ?>&nbsp;&nbsp;
					  Al &nbsp;<?PHP echo $UF_Fecha; ?>&nbsp;
				    </p></td>
					<td align="center">
						<p class="botonheader2">
						  <text onclick="capa('capa_casos')" onMouseOver="this.style.cursor='pointer'"/>
						  <b>Casos</b>
						  </text>
				    </p></td>
					<td align="center">
							<p class="botonheader2">
						  <text onclick="capa('capa_iconos')" onMouseOver="this.style.cursor='pointer'"/>
						  <b>Favoritos</b>
						  </text>
				    </p></td>
					<td align="center">
						<p class="botonheader2">
						  <text onclick="capa('capa_buscadores')" onMouseOver="this.style.cursor='pointer'"/> 
						  <b>Buscadores</b>
						  </text>
				    </p></td>
					<td align="center">
						<p class="infoheader2" id="reloj"> Aqui va la hora</p>
						<script type="application/x-javascript">mueveReloj();</script>
					</td>
					<td align="Center">
						<p class="infoheader2">Cuadro de Mando de &nbsp;&nbsp;<?PHP echo  $sg_name; ?></p></td>
						<td width="11%" align="right" style="padding: 0px; margin: 0px; border: none; border-spacing: 0px;">
						<form  action="" method="post" name="form_select" id="form_select">
						  <p><?PHP echo $select; ?></p>
						</form>
					</td>
				</tr>
			  </tbody>
			</table>
		  </td>				
		</tr>
	</tbody>
	</table>
