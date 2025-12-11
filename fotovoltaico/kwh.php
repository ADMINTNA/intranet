<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solución Fotovoltaica</title>
    <script type="text/javascript" src="./js/html2pdf.js"></script>
    <script>
// JavaScript Document

document.addEventListener('DOMContentLoaded', function () {
    // Restaurar los valores guardados en sessionStorage
    ['consumoMensual', 'facturacionMensual', 'cantidadPlantas', 'kWhPlanta'].forEach(function(item) {
        if (sessionStorage.getItem(item)) {
            document.getElementById(item).value = sessionStorage.getItem(item);
        }
    });
});
function checker(cual){
	switch(cual) {
		case 'ckWh'	:
			document.getElementById("ckWh").checked = true;
			document.getElementById("ccp").checked = false;
			document.getElementById("cm2").checked = false;
			break;
		case 'ccp'	:
			document.getElementById("ckWh").checked = false;
			document.getElementById("ccp").checked = true;
			document.getElementById("cm2").checked = false;
			break;
		case 'cm2'	:
			document.getElementById("ckWh").checked = false;
			document.getElementById("ccp").checked = false;
			document.getElementById("cm2").checked = true;
			break;
	}			
}

function recalcularSolucion() {
    calcularSolucion();
}

function calcularSolucion() {
    // Limpiar mensajes de error anteriores
    ocultarMensajeError();
	// actualiza checkbox
	document.getElementById("ckWh").checked = true;
	document.getElementById("ccp").checked = false;
	document.getElementById("cm2").checked = false;

    // Validar entradas individuales
    const esConsumoMensualValido = validarEntrada('consumoMensual', 'Consumo mensual es requerido y debe ser un número válido.');
    const esFacturacionMensualValido = validarEntrada('facturacionMensual', 'Facturación mensual es requerida y debe ser un número válido.');

    if (!esConsumoMensualValido || !esFacturacionMensualValido) {
        console.error("Hay errores de validación en los campos de entrada.");
        return; // No seguir si hay errores
    }

    // Obtener valores de entrada
    const consumoMensual = parseFloat(document.getElementById('consumoMensual').value);
    const facturacionMensual = parseFloat(document.getElementById('facturacionMensual').value);
 
    console.log("Valores ingresados:");
    console.log("Consumo Mensual:", consumoMensual);
    console.log("Facturación Mensual:", facturacionMensual);

    var btnexportar = document.getElementById('botonExportar');
    btnexportar.style.display = 'inline';

    // Guardar los valores en sessionStorage
    sessionStorage.setItem('consumoMensual', consumoMensual);
    sessionStorage.setItem('facturacionMensual', facturacionMensual);
 
    // Obtener más valores de entrada y realizar cálculos
    const horasSol = parseFloat(document.getElementById('horasSol').value);
    const potenciaPanel = parseFloat(document.getElementById('potenciaPanel').value);
    const rendimientoPanel = parseFloat(document.getElementById('rendimientoPanel').value);
    const tamanoPanel = parseFloat(document.getElementById('tamanoPanel').value);
    const costoPanel = parseFloat(document.getElementById('costoPanel').value);
    const cantidadSistemaProteccion = parseInt(document.getElementById('cantidadSistemaProteccion').value);
    const sistemaProteccion = parseFloat(document.getElementById('sistemaProteccion').value);
    const cantidadMedidorFlujoEpm = parseInt(document.getElementById('cantidadMedidorFlujoEpm').value);
    const medidorFlujoEpm = parseFloat(document.getElementById('medidorFlujoEpm').value);
    const margenComercial = parseFloat(document.getElementById('margenComercial').value) / 100;
    const materialesManoObra = parseFloat(document.getElementById('materialesManoObra').value) / 100;
    const costoInversor = parseFloat(document.getElementById('costoInversor').value);
    const rendimientoInversor = parseFloat(document.getElementById('rendimientoInversor').value);
    const cantidadInversores = parseInt(document.getElementById('cantidadInversores').value);
    const cantidadPlantas = parseInt(document.getElementById('cantidadPlantas').value);
    // calculos
    const kWDia = consumoMensual / 30;
    const kWHora = kWDia / 24;
    const kWhPlanta = Math.ceil(kWDia / horasSol);
    const kWPanel = potenciaPanel * (1 - (rendimientoPanel/100));
    const cantidadPaneles = Math.ceil(kWhPlanta / kWPanel);
	const m2paneles= cantidadPaneles*tamanoPanel;
	// actualización de valores calculados a formulario
    document.getElementById('cantidadPaneles').value = cantidadPaneles;
    document.getElementById('m2paneles').value = m2paneles;
    document.getElementById('kWhPlanta').value = kWhPlanta;
	
	let texto = "CD="+kWDia+" kwPanel="+kWPanel+" paneles="+ cantidadPaneles;
	let x = muestra("mensajes",texto);     
		
    // Actualizar las tablas de resumen comparativo y detalle comparativo
    const nivelesCobertura = [0.10, 0.25, 0.50, 0.75, 1.00];
    document.getElementById('resumenComparativo').innerHTML = '';
    document.getElementById('detalleComparativo').innerHTML = '';

    let resumenHtml = '<tr><th>Cobertura</th><th>kWh Planta</th><th>Precio Venta</th><th>m² Superficie</th><th>Nueva Facturación</th><th>Ahorro Mensual</th><th>Ahorro Anual</th><th>Período Recuperación</th></tr>';
    let detalleHtml = '<tr><th>Cobertura</th><th>Costo Total</th><th>Cantidad Paneles</th><th>m² Superficie</th><th>Costo Paneles</th><th>Costo Inversor</th><th>Sistema Protección</th><th>Medidor Flujo EPM</th><th>Costo Total Equipamiento</th><th>Materiales y Mano de Obra</th></tr>';

    nivelesCobertura.forEach(function (cobertura) {
        const kwCubrirCobertura = Math.ceil(kWhPlanta * cobertura);
        const cantidadPanelesCobertura = Math.ceil(kwCubrirCobertura / kWPanel);
        const superficieNecesaria = cantidadPanelesCobertura * parseFloat(document.getElementById('tamanoPanel').value);
        const costoPaneles = cantidadPanelesCobertura * parseFloat(document.getElementById('costoPanel').value);
        const costoInversores = parseInt(document.getElementById('cantidadInversores').value) * parseFloat(document.getElementById('costoInversor').value);
        const costoSistemaProteccion = parseInt(document.getElementById('cantidadSistemaProteccion').value) * parseFloat(document.getElementById('sistemaProteccion').value);
        const costoMedidorFlujoEpm = parseInt(document.getElementById('cantidadMedidorFlujoEpm').value) * parseFloat(document.getElementById('medidorFlujoEpm').value);
        const costoTotalEquipamiento = costoPaneles + costoInversores + costoSistemaProteccion + costoMedidorFlujoEpm;
        const materialesManoObra = parseFloat(document.getElementById('materialesManoObra').value) / 100;
        const margenComercial = parseFloat(document.getElementById('margenComercial').value) / 100;
        const costoTotal = Math.ceil(costoTotalEquipamiento * (1 + materialesManoObra));
        const precioVenta = Math.ceil(costoTotal * (1 + margenComercial));
        const nuevaFactura = Math.ceil(facturacionMensual * (1 - cobertura));
        const ahorroMensual = Math.ceil(facturacionMensual * cobertura);
        const ahorroAnual = ahorroMensual * 12;
        const periodoRecuperacion = ahorroAnual > 0 ? (precioVenta / ahorroAnual).toFixed(2) : 'N/A';

        resumenHtml += `
            <tr>
                <td style="text-align: center;">${(cobertura * 100).toFixed(0)}%</td>
                <td style="text-align: center;">${kwCubrirCobertura}</td>
                <td style="text-align: right;">$${precioVenta.toLocaleString()}</td>
                <td style="text-align: right;">${superficieNecesaria.toFixed(0)} m²</td>
                <td style="text-align: right;">$${nuevaFactura.toLocaleString()}</td>
                <td style="text-align: right;">$${ahorroMensual.toLocaleString()}</td>
                <td style="text-align: right;">$${ahorroAnual.toLocaleString()}</td>
                <td style="text-align: center;">${periodoRecuperacion} años</td>
            </tr>`;

        detalleHtml += `
            <tr>
                <td style="text-align: center;">${(cobertura * 100).toFixed(0)}%</td>
                <td style="text-align: right;">$${costoTotal.toLocaleString()}</td>
                <td style="text-align: center;">${cantidadPanelesCobertura}</td>
                <td style="text-align: center;">${superficieNecesaria.toFixed(0)} m²</td>
                <td style="text-align: right;">$${costoPaneles.toLocaleString()}</td>
                <td style="text-align: right;">$${costoInversores.toLocaleString()}</td>
                <td style="text-align: right;">$${costoSistemaProteccion.toLocaleString()}</td>
                <td style="text-align: right;">$${costoMedidorFlujoEpm.toLocaleString()}</td>
                <td style="text-align: right;">$${costoTotalEquipamiento.toLocaleString()}</td>
                <td style="text-align: right;">$${(costoTotalEquipamiento * materialesManoObra).toLocaleString()}</td>
            </tr>`;
    });

    // Mostrar las tablas
    document.getElementById('resumenComparativo').innerHTML = resumenHtml;
    document.getElementById('detalleComparativo').innerHTML = detalleHtml;
}

function exportarAPdf() {
    const contenido = document.querySelector('.container').cloneNode(true);
    const botonExportar = contenido.querySelector('#botonExportar');
    const limpiar = contenido.querySelector('#limpiar');
    const calcular = contenido.querySelector('#calcular');
    // ocultamos datos 
    botonExportar.remove();
    limpiar.remove();
    calcular.remove();

    html2pdf().from(contenido).save('Solucion_Fotovoltaica.pdf');

    location.reload();
}  

function limpiar() {
    sessionStorage.clear();
    location.reload();
}

function mostrarMensajeError(mensaje) {
    let contenedorError = document.getElementById('mensajeError');

    if (!contenedorError) {
        // Crear contenedor de error si no existe
        contenedorError = document.createElement('div');
        contenedorError.id = 'mensajeError';
        contenedorError.style.color = 'red';
        contenedorError.style.fontWeight = 'bold';
        document.body.insertBefore(contenedorError, document.body.firstChild);
    }

    // Actualizar el contenido con el mensaje de error
    contenedorError.textContent = mensaje;

    // Hacer visible el mensaje si estaba oculto
    contenedorError.style.display = 'block';
}

function ocultarMensajeError() {
    const contenedorError = document.getElementById('mensajeError');
    if (contenedorError) {
        contenedorError.style.display = 'none';
    }
}

function validarEntrada(idCampo, mensajeError) {
    const campo = document.getElementById(idCampo);
    const valor = parseFloat(campo.value);

    if (isNaN(valor)) {
        mostrarMensajeErrorCampo(idCampo, mensajeError);
        return false;
    } else {
        ocultarMensajeErrorCampo(idCampo);
        return true;
    }
}

function mostrarMensajeErrorCampo(idCampo, mensaje) {
    let mensajeError = document.getElementById(`error-${idCampo}`);

    if (!mensajeError) {
        mensajeError = document.createElement('span');
        mensajeError.id = `error-${idCampo}`;
        mensajeError.style.color = 'red';
        mensajeError.style.fontSize = '12px';
        document.getElementById(idCampo).parentNode.appendChild(mensajeError);
    }

    mensajeError.textContent = mensaje;
}

function ocultarMensajeErrorCampo(idCampo) {
    const mensajeError = document.getElementById(`error-${idCampo}`);
    if (mensajeError) {
        mensajeError.textContent = '';
    }
}

function muestra(id, valor) {
//    var campo = document.getElementById(id).value;
//    alert(campo); // Muestra el valor actual
    document.getElementById(id).value = valor; // Asigna el nuevo valor
}    
    </script>
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
        <table align="center" style="width:800px; border-collapse: collapse; margin-top: 3px; font-size: 13px;">
            <tr>
                <td  colspan="4" style="padding: 1px; font-weight: bold;"><h2 style="color: #38b6b6;">Ingreso de Datos</h2></td>
            </tr>
            <tr>
                <td style="padding: 1px; font-weight: bold;"><input type="checkbox" id="ckWh" onclick="return false;"> Consumo Mensual:</td>
                <td style="padding: 1px;"><input type="number" id="consumoMensual" value="719" step="100" required> (kWh)</td>
                <td colspan="2"><input type="text" id="mensajes" style="width: 500px; border: white" </td>
            </tr>
            <tr>
                <td style="padding: 1px; font-weight: bold;">Facturación Mensual:</td>
                <td style="padding: 1px;"><input type="number" id="facturacionMensual" value="137000" step="1000" required> (CLP)</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td  colspan="4" style="padding: 1px; font-weight: bold;"><h2 style="color: #38b6b6;">Variables en Uso</h2></td>
            </tr>
            <tr>
                <td style="padding: 1px; font-weight: bold;">Margen Comercial:</td>
                <td style="padding: 1px;"><input type="number" id="margenComercial" value="30" step="1" min="10" max="100" onchange="recalcularSolucion()"> (%)</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td style="padding: 1px; font-weight: bold;">Materiales y M/O:</td>
                <td style="padding: 1px;"><input type="number" id="materialesManoObra" value="30" step="1" min="10" max="100" onchange="recalcularSolucion()"> (%)</td>
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
                <td style="padding: 1px; font-weight: bold;">Pérdida Panel:</td>
                <td style="padding: 1px;"><input type="number" id="rendimientoPanel" value="20" step="1" min="10" max="100" onchange="recalcularSolucion()"> (%)</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td style="padding: 1px; font-weight: bold;">m<sup>2</sup> Panel:</td>
                <td style="padding: 1px;"><input type="number" id="tamanoPanel" value="3.36" step="0.1" onchange="recalcularSolucion()"> (m²)</td>
                 <td style="padding: 1px; font-weight: bold;">Costo Panel:</td>
                <td><input type="number" id="costoPanel" value="170000" step="1000" onchange="recalcularSolucion()"> (CLP)</td>
            </tr>
            <tr>
                <td style="padding: 1px; font-weight: bold;"><input type="checkbox" id="ccp" onclick="return false;"> Cantidad Paneles:</td>
                <td style="padding: 1px;"><input type="number" id="cantidadPaneles" value="1" step="1" min="1" onchange="recalcularSolucion()"></td>
                <td style="padding: 1px; font-weight: bold;"><input type="checkbox" id="cm2" onclick="return false;"> m<sup>2</sup> Paneles:</td>
                <td><input type="number" id="m2paneles" value="" step="1" onchange="recalcularSolucion()"> (CLP)</td>
            </tr>
            <tr>
                <td style="padding: 1px; font-weight: bold;">Inversores:</td>
                <td style="padding: 1px;"><input type="number" id="cantidadInversores" value="1" step="1" min="1" onchange="recalcularSolucion()"></td>
                <td style="padding: 1px; font-weight: bold;">Costo Inversor:</td>
                <td><input type="number" id="costoInversor" value="1100000" step="1000" onchange="recalcularSolucion()"> (CLP)</td>
            </tr>
            <tr>
                <td style="padding: 1px; font-weight: bold;">Rendimiento inversor:</td>
                <td style="padding: 1px;"><input type="number" id="rendimientoInversor" value="30" step="1" min="10" max="100" onchange="recalcularSolucion()"> (%)</td>
                <td colspan="2"></td>
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
                <td style="padding: 1px;"><input type="number" id="cantidadPlantas" value="1" step="1" min="1" onchange="recalcularSolucion()"></td>
                <td style="padding: 1px; font-weight: bold;">kWh Planta:</td>
                <td><input type="number" id="kWhPlanta" value="0" step="1" onchange="recalcularSolucion()"> (kWh)</td>
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
