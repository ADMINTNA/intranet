// JavaScript Document

document.addEventListener('DOMContentLoaded', function () {
    // Restaurar los valores guardados en sessionStorage
    ['consumoMensual', 'facturacionMensual', 'horasCubrir', 'cantidadPlantas', 'kwhPlanta'].forEach(function(item) {
        if (sessionStorage.getItem(item)) {
            document.getElementById(item).value = sessionStorage.getItem(item);
        }
    });
});

function recalcularSolucion() {
    calcularSolucion();
}

function calcularSolucion() {
    // Limpiar mensajes de error anteriores
    ocultarMensajeError();

    // Validar entradas individuales
    const esConsumoMensualValido = validarEntrada('consumoMensual', 'Consumo mensual es requerido y debe ser un número válido.');
    const esFacturacionMensualValido = validarEntrada('facturacionMensual', 'Facturación mensual es requerida y debe ser un número válido.');
    const esHorasCubrirValido = validarEntrada('horasCubrir', 'Horas a cubrir es requerido y debe ser un número entero.');

    if (!esConsumoMensualValido || !esFacturacionMensualValido || !esHorasCubrirValido) {
        return; // No seguir si hay errores
    }

    const consumoMensual = parseFloat(document.getElementById('consumoMensual').value);
    const facturacionMensual = parseFloat(document.getElementById('facturacionMensual').value);
    const horasCubrir = parseInt(document.getElementById('horasCubrir').value);

    var btnexportar = document.getElementById('botonExportar');
    btnexportar.style.display = 'inline';

    // Guardar los valores en sessionStorage
    sessionStorage.setItem('consumoMensual', consumoMensual);
    sessionStorage.setItem('facturacionMensual', facturacionMensual);
    sessionStorage.setItem('horasCubrir', horasCubrir);

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
    document.getElementById('cantidadPaneles').value = cantidadPaneles;
    document.getElementById('m2paneles').value = cantidadPaneles;
    document.getElementById('kWhPlanta').value = kWhPlanta;
	let texto = "CD="+kWDia+" kwPanel="+kWPanel+" paneles="+ cantidadPaneles;
	let x = muestra("mensajes",texto);     
	
    const nivelesCobertura = [0.10, 0.25, 0.50, 0.75, 1.00];
    document.getElementById('resumenComparativo').innerHTML = '';
    document.getElementById('detalleComparativo').innerHTML = '';

    let resumenHtml = '<tr><th>Cobertura</th><th>kWh Planta</th><th>Precio Venta</th><th>m² Superficie</th><th>Nueva Facturación</th><th>Ahorro Mensual</th><th>Ahorro Anual</th><th>Período Recuperación</th></tr>';
    let detalleHtml = '<tr><th>Cobertura</th><th>Costo Total</th><th>Cantidad Paneles</th><th>m² Superficie</th><th>Costo Paneles</th><th>Costo Inversor</th><th>Sistema Protección</th><th>Medidor Flujo EPM</th><th>Costo Total Equipamiento</th><th>Materiales y Mano de Obra</th></tr>';

    let kwhPlanta100 = 0;

    nivelesCobertura.forEach(function (cobertura) {
        const kwCubrirCobertura = Math.ceil(kWhPlanta * cobertura);
        const kwhPlantaCobertura = Math.ceil((kwCubrirCobertura / horasSol) * cantidadPlantas);
        if (cobertura === 1.00) {
            kwhPlanta100 = kWhPlanta;
        }
        const cantidadPanelesCobertura = Math.ceil(kwCubrirCobertura / kWPanel);
        const costoPaneles = cantidadPanelesCobertura * costoPanel;
        const superficieNecesaria = cantidadPanelesCobertura * tamanoPanel;
        const costoInversores = cantidadInversores * costoInversor;
        const costoSistemaProteccion = cantidadSistemaProteccion * sistemaProteccion;
        const costoMedidorFlujoEpm = cantidadMedidorFlujoEpm * medidorFlujoEpm;
        const costoTotalEquipamiento = costoPaneles + costoInversores + costoSistemaProteccion + costoMedidorFlujoEpm;
        const costoTotal = Math.ceil(costoTotalEquipamiento * (1 + materialesManoObra));
        const precioVenta = Math.ceil(costoTotal * (1 + margenComercial));
        const nuevaFactura = Math.ceil(facturacionMensual * (1 - cobertura));
        const ahorroMensual = Math.ceil(facturacionMensual * cobertura);
        const ahorroAnual = ahorroMensual * 12;
        const periodoRecuperacion = ahorroAnual > 0 ? (precioVenta / ahorroAnual).toFixed(2) : 'N/A';

        resumenHtml += `
            <tr>
                <td style="text-align: center;">${(cobertura * 100).toFixed(0)}%</td>
                <td style="text-align: center;">${kwhPlantaCobertura}</td>
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

        // Actualizar los inputs con la cantidad de paneles e inversores calculados para la cobertura del 100%
        if (cobertura === 1.00) {
            document.getElementById('cantidadPaneles').value = cantidadPanelesCobertura;
            document.getElementById('cantidadInversores').value = cantidadInversores;
        }
    });

    // Guardar el valor calculado de kWh Planta para la cobertura del 100%
    sessionStorage.setItem('kWhPlanta', kWhPlanta);
    sessionStorage.setItem('cantidadPlantas', cantidadPlantas);

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
    var campo = document.getElementById(id).value;
    alert(campo); // Muestra el valor actual
    document.getElementById(id).value = valor; // Asigna el nuevo valor
}