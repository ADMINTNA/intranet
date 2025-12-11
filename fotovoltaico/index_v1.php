<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solución Fotovoltaica</title>
    <script type="text/javascript" src="./js/html2pdf.js"></script>
    <script type="text/javascript" src="./js/index.js"></script>
    <link rel="stylesheet" type="text/css" href="./css/index.css">
</head>
<body>
    <div class="container">
        <table style="width: 100%; background-color: #38b6b6; color: #fff; padding: 10px;">
            <tr>
                <td style="width: 75px; text-align: center;"><img src="../kickoff/images/Robot_Cool_01.png" width="71" height="84" alt=""></td>
                <td style="width: 20%; text-align: center; font-size: 18px; font-style: italic;">"Cuando todos vendemos, el éxito es inevitable"</td>
                <td style="width: 50%; text-align: center;"><h1 style="margin: 0; font-size: 24px; color: white; font-weight: bold;">Calculadora Fotovoltaica</h1></td>
                <td style="width: 10%; text-align: right; vertical-align: top; font-size: 20px; font-weight: bold; font-family: Gotham, 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #ffffff;">#JuntosSomosMás</td>
            </tr>
        </table>
        <table align="center" style="width: 550px; border-collapse: collapse; margin-top: 3px; font-size: 13px;">
            <tr>
                <td  colspan="4" style="padding: 1px; font-weight: bold;"><h2 style="color: #38b6b6;">Ingreso de Datos</h2></td>
            </tr>
            <tr>
                <td style="padding: 1px; font-weight: bold;">Consumo Mensual:</td>
                <td style="padding: 1px;"><input type="number" id="consumoMensual" value="70200" step="100" required> (kWh)</td>
				<td colspan="2"></td>
            </tr>
            <tr>
                <td style="padding: 1px; font-weight: bold;">Facturación Mensual:</td>
                <td style="padding: 1px;"><input type="number" id="facturacionMensual" value="7512397" step="1000" required> (CLP)</td>
				<td colspan="2"></td>
            </tr>
            <tr>
                <td style="padding: 1px; font-weight: bold;">Horas a Cubrir:</td>
                <td style="padding: 1px;"><input type="number" id="horasCubrir" value="9" step="1" min="1" max="24" onchange="recalcularSolucion()" required> (hrs)</td>
				<td colspan="2"></td>
            </tr>
            <tr>
                <td  colspan="4" style="padding: 1px; font-weight: bold;"><h2 style="color: #38b6b6;">Variables en Uso</h2></td>
            </tr>
            <tr>
                <td style="padding: 1px; font-weight: bold;">Margen Comercial:</td>
                <td style="padding: 1px;"><input type="number" id="margenComercial" value="30" step="1" min="10" max="100" onchange="recalcularSolucion()"> (%)</td>
            </tr>
            <tr>
                <td style="padding: 1px; font-weight: bold;">Materiales y M/O:</td>
                <td style="padding: 1px;"><input type="number" id="materialesManoObra" value="15" step="1" min="10" max="100" onchange="recalcularSolucion()"> (%)</td>
            </tr>
            <tr>
                <td style="padding: 1px; font-weight: bold;">Días del Mes:</td>
                <td style="padding: 1px;"><input type="number" id="diasMes" value="30" step="1" min="1" max="31" onchange="recalcularSolucion()"> (dias</td>
				<td colspan="2"></td>
            </tr>
            <tr>
                <td style="padding: 1px; font-weight: bold;">Horas del Día:</td>
                <td style="padding: 1px;"><input type="number" id="horasDia" value="24" step="1" min="1" max="24" onchange="recalcularSolucion()"> (hrs)</td>
				<td colspan="2"></td>
            </tr>
            <tr>
                <td style="padding: 1px; font-weight: bold;">Horas Sol:</td>
                <td style="padding: 1px;"><input type="number" id="horasSol" value="5" step="1" min="1" max="24" onchange="recalcularSolucion()"> (hrs)</td>
				<td colspan="2"></td>
            </tr>
            <tr>
                <td style="padding: 1px; font-weight: bold;">Potencia Panel:</td>
                <td style="padding: 1px;"><input type="number" id="potenciaPanel" value="0.625" step="0.01" onchange="recalcularSolucion()"> (kWh)</td>
				<td colspan="2"></td>
            </tr>
            <tr>
                <td style="padding: 1px; font-weight: bold;">Tamaño Panel:</td>
                <td style="padding: 1px;"><input type="number" id="tamanoPanel" value="3.36" step="0.1" onchange="recalcularSolucion()"> (m²)</td>
				<td colspan="2"></td>
            </tr>
            <tr>
                <td style="padding: 1px; font-weight: bold;">CPaneles:</td>
                <td style="padding: 1px;"><input type="number" id="cantidadPaneles" value="1" step="1" min="1" onchange="recalcularSolucion()"></td>
				<td style="padding: 1px; font-weight: bold;">Costo Panel:</td>
				<td><input type="number" id="costoPanel" value="170000" step="1000" onchange="recalcularSolucion()"> (CLP)</td>
            </tr>
            <tr>
                <td style="padding: 1px; font-weight: bold;">Inversores:</td>
                <td style="padding: 1px;"><input type="number" id="cantidadInversores" value="1" step="1" min="1" onchange="recalcularSolucion()"></td>
				<td style="padding: 1px; font-weight: bold;">Costo Inversor:</td>
				<td><input type="number" id="costoInversor" value="450000" step="1000" onchange="recalcularSolucion()"> (CLP)</td>
            </tr>
            <tr>
                <td style="padding: 1px; font-weight: bold;">Sistema Protección:</td>
                <td style="padding: 1px;"><input type="number" id="cantidadSistemaProteccion" value="1" step="1" min="1" onchange="recalcularSolucion()"></td>
				<td style="padding: 1px; font-weight: bold;">Sistema Protección:</td>
				<td><input type="number" id="sistemaProteccion" value="390000" step="1000" onchange="recalcularSolucion()"> (CLP)</td>
            </tr>           
            <tr>
                <td style="padding: 1px; font-weight: bold;">Medidor Flujo EPM:</td>
                <td style="padding: 1px;"><input type="number" id="cantidadMedidorFlujoEpm" value="1" step="1" min="1" onchange="recalcularSolucion()"></td>
				<td style="padding: 1px; font-weight: bold;">Medidor Flujo EPM:</td>
				<td><input type="number" id="medidorFlujoEpm" value="380000" step="1000" onchange="recalcularSolucion()"> (CLP)</td>
            </tr>           
            <tr>
                <td style="padding: 1px; font-weight: bold;">Plantas:</td>
                <td style="padding: 1px;"><input type="number" id="cantidadKwhPlanta" value="1" step="1" min="1" onchange="recalcularSolucion()"></td>
				<td style="padding: 1px; font-weight: bold;">kWh Planta:</td>
				<td><input type="number" id="kwhPlanta" value="0" step="1" onchange="recalcularSolucion()"> (kWh)</td>
            </tr>
			<tr>
				<td  colspan="2" style="text-align: center; padding: 1px;">
					<button id="limpiar" style="background-color: #38b6b6; color: #fff;" type="button" onclick="limpiar();">Limpiar</button>
				</td>
                <td colspan="2" style="text-align: center; padding: 1px;">
					<button id="calcular" type="button" onclick="calcularSolucion()">Calcular</button>
				</td>
            </tr>
        </table>
        <br>
        <h2 style="text-align: left;">Resumen Comparativo</h2>
        <table id="resumenComparativo"></table>
        <br>
        <div id="detalle">
            <h2 style="text-align: left;">Detalle Comparativo</h2>
            <table id="detalleComparativo"></table>
        </div>
        <br>
		<div align="center">
 	       <button id="botonExportar" style="display: none;" type="button" onclick="exportarAPdf()">Exportar a PDF</button>
		</div>
    </div>
</body>
</html>
