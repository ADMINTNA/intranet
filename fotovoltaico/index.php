<?php require_once './includes/config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solución Fotovoltaica <?PHP echo $user; ?></title>
    <script type="text/javascript" src="./js/html2pdf.bundle.min.js"></script>
    <!-- script type="text/javascript" src="./js/html2pdf.js"></script -->
    <!-- script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script -->    
    <script type="text/javascript" src="./js/index.js"></script>
    <link rel="stylesheet" type="text/css" href="./css/index.css">
</head>
<body onLoad="cargaInicial();">
    <div class="container">
        <table  id="formulario" align="center" style="width: 700px; align-content: center;  border-collapse: collapse; margin-top: 3px; font-size: 13px;">
            <tr>
                <td colspan="4">
                    <table style="width:100%; align-content: center; background-color: #38b6b6; color: #fff;">
                        <tr>
                            <td style="width: 65px; text-align: center;">
                                <img src="../kickoff/images/Robot_Cool_01.png" width="71" height="84" alt="" onClick="muestra_oculta()">
                            </td>
                            <td colspan="2" style="text-align: center;">
                                <h1 style="margin: 0; font-size: 24px; color: white; font-weight: bold;">Calculadora Fotovoltaica</h1>
                            </td>
                            <td style="width: 150px; text-align: right; vertical-align: top; font-size: 20px; font-weight: bold; font-family: Gotham, 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #ffffff;">
                                #JuntosSomosMás
                            </td>
                        </tr>
                    </table>    
                </td>
            </tr>
            <tr>
                <td height="20px" colspan="4" style="padding: 0px; font-weight: bold;"><h2 style="color: #38b6b6;"> </h2></td>
            </tr>
            <tr>
                <td  colspan="4" style="padding: 0px; font-weight: bold;"><h2 style="color: #38b6b6;">Planta de: <input style="color: orangered; font-weight:bold; font-size: 18px; width: 50px" type="number" id="kWhPlanta"  value="0" step="1" onchange="tipoCalculo(this.id)"> (kWh)&nbsp; <input type="checkbox" id="kWhP" onclick="return false;">,  <input  type="text" size="7" id="cumplimiento" style="color: #38b6b6; font-weight: bold; font-size: 20px; border: none; " readonly>, sobre su consumo mensual.</h2></td>
            </tr> 
            <tr>
                <td height="20px" colspan="4" style="padding: 0px; font-weight: bold;"><h2 style="color: #38b6b6;"> </h2></td>
            </tr>
            <tr>
                <td colspan="2" style="width: 100px; font-weight: bold;">
					Cliente: <input type="text" id="cliente" size="35" required>
				</td>
                <td colspan="2" style="width: 100px; font-weight: bold;">Dirección: <input type="text" id="direccion" size="30"  required>
				</td>
            </tr>
            <tr>
                <td style="width: 100px; font-weight: bold;">
					Consumo Mensual:</td>
                <td style="width: 80px">
					<input style="color: orangered; font-weight: bold" type="number" id="consumoMensual" value="719" step="1" required onchange="tipoCalculo(this.id)"> (kWh) <input type="checkbox"   id="ckWh" onclick="return false;">
				</td>
                <td style=" width: 100px; font-weight: bold;">Facturación Mensual:</td>
                <td style="width: 50px; text-align: left;"><input type="number" id="facturacionMensual" value="142272" step="1000"  required> (CLP)</td>
            </tr>
            <tr>
                 <td style="font-weight: bold;">Cantidad Paneles:</td>
                <td >
					<input style="color: orangered; font-weight: bold" type="number" id="cantidadPaneles" value="1" step="1" min="1" onchange="tipoCalculo(this.id)">
					&nbsp;<input type="checkbox" id="ccp" onclick="return false;">
				</td>
               <td style="font-weight: bold;">m<sup>2</sup> Paneles:</td>
                <td style="text-align: left;"><input style="color: orangered; font-weight: bold" type="number" id="m2Paneles" value="" step="0.5" onchange="tipoCalculo(this.id)">
					  (m²) <input type="checkbox" id="cm2" onclick="return false;"> </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">m<sup>2</sup> Panel:</td>
                <td ><input type="number" id="tamanoPanel" value="3.36" step="0.1" onchange="calcularSolucion()"> (m²)</td>
                <td style="font-weight: bold;">Potencia Panel:</td>
                <td ><input type="number" id="potenciaPanel" value="0.625" step="0.01" onchange="calcularSolucion()"> (kWh)</td>
             </tr>
            <tr>
                <td style="font-weight: bold;">Inversores:</td>
				<td ><input type="number" id="cantidadInversores" value="1" step="1" min="1" onchange="calcularSolucion()"></td>
               <td style="font-weight: bold;">Pérdida Panel:</td>
                <td style="text-align: left;"><input type="number" id="rendimientoPanel" value="22.3" step="0.1" min="0" max="100" onchange="calcularSolucion()"> (%)</td>
             </tr>
			<tr>
                <td style="font-weight: bold;">Tramite SEC (NetBilling): </td>
                <td colspan="3"><input type="checkbox" id="sec" onclick="validaSEC();"> Incluye: Revisiones, Observaciones, Mano de Obra y Materiales<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; de Modificaciones SEC.</td>
			</tr>
  			<tr>
                <td colspan="4">
                    <table  id="ocultar" align="center" style="width: 100%; align-content: center;  border-collapse: collapse; margin-top: 3px; font-size: 13px;">					
                        <tr>
                            <td style="width: 190px; font-weight: bold;">Margen_Comercial:</td>
                            <td style="width: 190px;"><input type="number" id="margenComercial" value="30" step="1" min="10" max="100" onchange="calcularSolucion()"> (%)</td>
                            <td style="width: 190px; font-weight: bold;">Materiales y M/O:</td>
                            <td style="width: 190px; text-align: left;"><input type="number" id="materialesManoObra" value="30" step="1" min="10" max="100" onchange="calcularSolucion()"> (%)</td>
                        </tr>
                        <tr>
                             <td style="font-weight: bold;">Costo Panel:</td>
                             <td style="text-align: left;"><input type="number" id="costoPanel" value="165000" step="1000" onchange="calcularSolucion()"> (CLP)</td>
                             <td style="font-weight: bold;">Colchon:</td>
                             <td style="width: 190px;"><input type="number" id="colchon" value="30" step="1" min="0" max="100" onchange="calcularSolucion()"> (%)</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Costo Inversor:</td>
                            <td style="text-align: left;"><input type="number" id="costoInversor" value="1020000" step="1000" onchange="calcularSolucion()"> (CLP)</td>
                             <td style="font-weight: bold;">Estructura Paneles:</td>
                             <td style="width: 190px;"><input type="number" id="estructura" value="45" step="1" min="0" max="100" onchange="calcularSolucion()"> (%)</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Meter / EPM:</td>
                            <td ><input type="number" id="cantidadMedidorFlujoEpm" value="1" step="1" min="1" onchange="calcularSolucion()"></td>
                            <td style="font-weight: bold;"> Costo Meter / EPM:</td>
                            <td style="text-align: left;"><input type="number" id="medidorFlujoEpm" value="380000" step="1000" onchange="calcularSolucion()"> (CLP)</td>
                        </tr>         
                        <tr>
                            <td style="font-weight: bold;">Tramite SEC ( NetBilling): </td>
                            <td> <input type="number" id="tramiteSec" value="1143000" step="10000" min="0" onchange="calcularSolucion()"></td>
                            <td style="font-weight: bold;">Horas Sol:</td>
                            <td ><input type="number" id="horasSol" value="5" step="1" min="1" max="24" onchange="calcularSolucion()"> (hrs)</td>
                        </tr>
                        <input type="hidden" id="kWDia">
                        <input type="hidden" id="kWMes">
                        <input type="hidden" id="calculo">
                    </table>
                    <script>muestra_oculta();</script>
                </td>    
			</tr>
 			<tr>
				<td  colspan="1" style="text-align: center; padding: 1px;">
					<button id="limpiar" style="background-color: #38b6b6; color: #fff;" type="button" onclick="togleVisible('detalle');">Limpiar</button>
				</td>
				<td>
                    <button id="botonExportar" type="button" onclick="exportarAPdf()">Exportar a PDF</button>
                </td>
				<td colspan="1" style="text-align: center; padding: 1px;">
					<button id="calcular" type="button" onclick="calcularSolucion()">Calcular</button>
				</td>
				<td colspan="1" style="text-align: center; padding: 1px;">
					<button id="botonGuardar" type="button" onclick="enviarSolucion()">Guardar</button>
				</td>
			</tr>
            <tr>
                <td colspan="4">
                    <br>
                    <h2 style="text-align: left;">Resumen Comparativo</h2>
                    <table width="100%" id="resumenComparativo"></table>
                    <br>
                    <div id="detalle">
                        <h2 style="text-align: left;">Detalle Comparativo</h2>
                        <table id="detalleComparativo"></table>
                    </div>        
                </td>
            </tr>           
        </table>
    </div>  
    <br><br><br>
         <!-- Listado de soluciones -->
        <?PHP $soluciones = listarSoluciones(); ?>
        <div id="listado" align="center">
            <h2 style="text-align: center;">Listado de Soluciones Guardadas  <input type="text" id="buscarSolucion" placeholder="Buscar solución..." onkeyup="filtrarSoluciones()"></h2>
            <table style="width: 1000px;" align="center" id="listadosoluciones">
            <thead>
                <tr>
                    <th width="1%">ID</th>
                    <th width="25%" >Cliente</th>
                    <th>Dirección</th>
                    <th width="80px" >Fecha</th>
                    <th>Precio Venta</th> <!-- Nueva columna -->
                    <th colspan="2">
                        <div align="center"> Acciones </div></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($soluciones as $solucion): ?>
                    <tr>
                        <td><?= $solucion['id'] ?></td>
                        <td><?= $solucion['cliente'] ?></td>
                        <td><?= $solucion['direccion'] ?></td>
                        <td><?= date("d-m-Y", strtotime($solucion['fecha_creacion'])); ?></td>
                        <td>$<?= number_format($solucion['precioVenta'], 0) ?></td> <!-- Mostrar precio de venta -->
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="id" value="<?= $solucion['id'] ?>">
                                <button class="btnEliminar" data-id="<?= $solucion['id'] ?>" style="width: 70px;">Eliminar</button>
                            </form>
                        </td>
                        <td>
                            <button style="width: 70px;" type="button" data-id="<?= $solucion['id'] ?>" onclick="cargarProyecto(event)"> Cargar </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>      
            </table>
            <br><br>
        </div>
</body>
</html>
