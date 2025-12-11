// JavaScript Document
let calculo = "";

document.addEventListener('DOMContentLoaded', function () {
    ['consumoMensual', 'facturacionMensual', 'cantidadPlantas', 'kWhPlanta', 'cantidadPaneles', 'calculo', 'cliente', 'direccion'].forEach(function(item) {
        const elemento = document.getElementById(item);
        if (sessionStorage.getItem(item) && elemento) {
            elemento.value = sessionStorage.getItem(item);
        }
    });
});

function tipoCalculo(id) {
    calculo = id;
    calcularSolucion();
}

function calcularSolucion() {
    if (!calculo) { calculo = "consumoMensual"; }
    const cliente = document.getElementById('cliente').value;
    const direccion = document.getElementById('direccion').value;
    const consumoMensual = parseFloat(document.getElementById('consumoMensual').value);
    const facturacionMensual = parseFloat(document.getElementById('facturacionMensual').value);
    const horasSol = parseFloat(document.getElementById('horasSol').value);
    const potenciaPanel = parseFloat(document.getElementById('potenciaPanel').value);
    const rendimientoPanel = parseFloat(document.getElementById('rendimientoPanel').value);
    const tamanoPanel = parseFloat(document.getElementById('tamanoPanel').value);
    const kWPanel = (potenciaPanel * (1 - (rendimientoPanel / 100))).toFixed(3);

    // Guardar los valores en sessionStorage
    sessionStorage.setItem('cliente', cliente);
    sessionStorage.setItem('direccion', direccion);
    sessionStorage.setItem('consumoMensual', consumoMensual);
    sessionStorage.setItem('facturacionMensual', facturacionMensual);
    checked(calculo);

    if (calculo === 'consumoMensual') {
        // calculos
        const kWDia = (consumoMensual / 30).toFixed(2);
        const kWhPlanta_tmp = (kWDia / horasSol).toFixed(2);
        const cantidadPaneles = (kWhPlanta_tmp / kWPanel).toFixed(2);
        // actualización de valores calculados a formulario
        document.getElementById('kWDia').value = kWDia;
        document.getElementById('cantidadPaneles').value = cantidadPaneles;
    } else if (calculo === 'cantidadPaneles') {
        // Obtener valores de entrada
        const cantidadPaneles = parseFloat(document.getElementById('cantidadPaneles').value);
        // Guardar los valores en sessionStorage y en formulario
        sessionStorage.setItem('cantidadPaneles', cantidadPaneles);
    } else if (calculo === "m2Paneles") {
        // Obtener valores de entrada
        const m2Paneles = parseFloat(document.getElementById('m2Paneles').value).toFixed(2);
        // calculos
        const cantidadPaneles = (m2Paneles / tamanoPanel).toFixed(2);
        // Guardar los valores en sessionStorage y en formulario
        sessionStorage.setItem('cantidadPaneles', cantidadPaneles);
        document.getElementById('cantidadPaneles').value = cantidadPaneles;
    } else if (calculo === "kWhPlanta") {
        // Obtener valores de entrada
        const kWhPlanta_tmp = parseFloat(document.getElementById('kWhPlanta').value);
        // calculos
        const cantidadPaneles = (kWhPlanta_tmp / kWPanel).toFixed(2);
        // Guardar los valores en sessionStorage y en formulario
        sessionStorage.setItem('cantidadPaneles', cantidadPaneles);
        document.getElementById('cantidadPaneles').value = cantidadPaneles;
    }

    const cantidadPaneles = parseFloat(document.getElementById('cantidadPaneles').value);
    const kWDia = parseFloat(document.getElementById('kWDia').value);
    const kWhPlanta = (cantidadPaneles * kWPanel).toFixed(2);
    const kWMes = kWDia * 30;
    const m2Paneles = (cantidadPaneles * tamanoPanel).toFixed(2);
    const cumplimiento = (kWMes / consumoMensual) * 100;
    const temp = ((kWhPlanta*horasSol*30) / consumoMensual) * 100;
    document.getElementById('m2Paneles').value = m2Paneles;
    document.getElementById('kWhPlanta').value = kWhPlanta;
    document.getElementById('kWMes').value = kWMes;
    document.getElementById('cumplimiento').value = cumplimiento.toFixed(2) + "%";
    sessionStorage.setItem('kWhPlanta', kWhPlanta);

    // logear valores para seguimiento
    console.log("----------------------------------------------");
    console.log("Valores ingresados para cálculo por", calculo);
    console.log('Cliente antes de guardar:', cliente);
    console.log('Dirección antes de guardar:', direccion);
    console.log("Horas de Sol:", horasSol);
    console.log("Consumo Mensual:", consumoMensual);
    console.log("Factura Mensual:", facturacionMensual);
    console.log("KW Día:", kWDia);
    console.log("KW mes:", kWMes);
    console.log("Tamaño Panel:", tamanoPanel);
    console.log("Potencia Panel:", potenciaPanel);
    console.log("Pérdida Panel:", rendimientoPanel);
    console.log("kWh Panel:", kWPanel);
    console.log("Cantidad de Paneles:", cantidadPaneles);
    console.log("kWh Planta:", kWhPlanta);
    console.log("temo:", temp);
    console.log("kWh cumpliento:", cumplimiento);
    console.log("kWh Plantasin:", (cantidadPaneles * kWPanel).toFixed(2));
    console.log("m2 Paneles:", m2Paneles);

    // Boton Exportar
    const btnexportar = document.getElementById('botonExportar');
    btnexportar.style.display = 'inline';

    // Actualizar las tablas de resumen comparativo y detalle comparativo
    const nivelesCobertura = [0.10, 0.25, 0.50, 0.75, 1.00];
    document.getElementById('resumenComparativo').innerHTML = '';
    document.getElementById('detalleComparativo').innerHTML = '';

    let resumenHtml = '<tr><th>Cobertura</th><th>kWh Planta</th><th>Precio Venta</th><th>Paneles</th><th>m² Superficie</th><th>Nueva Facturación</th><th>Ahorro Mensual</th><th>Ahorro Anual</th><th>Recuperación</th></tr>';
    let detalleHtml = '<tr><th>Cobertura</th><th>Paneles</th><th>m² Superficie</th><th>Precio Paneles</th><th>Inversor</th><th>M.F. EPM</th><th>Equipamiento</th><th>MyM de Obra</th><th>$ Colchon</th><th>$ Margen</th><th>$ SEC</th></tr>';

    nivelesCobertura.forEach(function (cobertura) {
        const kwCubrirCobertura = (kWhPlanta * cobertura).toFixed(2);
        const cantidadPanelesCobertura = (kwCubrirCobertura / kWPanel).toFixed(2);
        const superficieNecesaria = cantidadPanelesCobertura * parseFloat(document.getElementById('tamanoPanel').value);
        const costoPaneles = cantidadPanelesCobertura * parseFloat(document.getElementById('costoPanel').value);
        const tramiteSec = parseFloat(document.getElementById('tramiteSec').value);
        const costoInversores = parseInt(document.getElementById('cantidadInversores').value) * parseFloat(document.getElementById('costoInversor').value);
        const costoMedidorFlujoEpm = parseInt(document.getElementById('cantidadMedidorFlujoEpm').value) * parseFloat(document.getElementById('medidorFlujoEpm').value);
        const costoTotalEquipamiento = costoPaneles + costoInversores + costoMedidorFlujoEpm;
        const materialesManoObra = parseFloat(document.getElementById('materialesManoObra').value) / 100;
        const mmPesos = Math.ceil(costoTotalEquipamiento * materialesManoObra);
        const margenComercial = parseFloat(document.getElementById('margenComercial').value) / 100;
        const mCPesos = Math.ceil(costoTotalEquipamiento * margenComercial);
        const colchon = parseFloat(document.getElementById('colchon').value) / 100;
        const cPesos = Math.ceil(costoTotalEquipamiento * colchon);
        const precioVenta = costoTotalEquipamiento + mmPesos + mCPesos + cPesos + tramiteSec;
        const nuevaFactura = Math.ceil(facturacionMensual * (1 - cobertura));
        const ahorroMensual = Math.ceil(facturacionMensual * cobertura);
        const ahorroAnual = ahorroMensual * 12;
        const periodoRecuperacion = ahorroAnual > 0 ? (precioVenta / ahorroAnual).toFixed(2) : 'N/A';

        resumenHtml += `
            <tr>
                <td style="text-align: center;">${(cobertura * 100).toFixed(0)}%</td>
                <td style="text-align: center;">${kwCubrirCobertura}</td>
                <td style="text-align: right;">$${precioVenta.toLocaleString()}</td>
                <td style="text-align: center;">${cantidadPanelesCobertura}</td>
                <td style="text-align: right;">${superficieNecesaria.toFixed(0)} m²</td>
                <td style="text-align: right;">$${nuevaFactura.toLocaleString()}</td>
                <td style="text-align: right;">$${ahorroMensual.toLocaleString()}</td>
                <td style="text-align: right;">$${ahorroAnual.toLocaleString()}</td>
                <td style="text-align: center;">${periodoRecuperacion} años</td>
            </tr>`;

        detalleHtml += `
            <tr>
                <td style="text-align: center;">${(cobertura * 100).toFixed(0)}%</td>
                <td style="text-align: center;">${cantidadPanelesCobertura}</td>
                <td style="text-align: center;">${superficieNecesaria.toFixed(0)} m²</td>
                <td style="text-align: right;">$${costoPaneles.toLocaleString()}</td>
                <td style="text-align: right;">$${costoInversores.toLocaleString()}</td>
                <td style="text-align: right;">$${costoMedidorFlujoEpm.toLocaleString()}</td>
                <td style="text-align: right;">$${costoTotalEquipamiento.toLocaleString()}</td>
                <td style="text-align: right;">$${mmPesos.toLocaleString()}</td>
                <td style="text-align: right;">$${cPesos.toLocaleString()}</td>
                <td style="text-align: right;">$${mCPesos.toLocaleString()}</td>
                <td style="text-align: right;">$${tramiteSec.toLocaleString()}</td>
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
    const detalle = contenido.querySelector('#detalle');

    // ocultamos datos
    botonExportar.remove();
    limpiar.remove();
    calcular.remove();
    detalle.remove();

    html2pdf().from(contenido).save('Solucion_Fotovoltaica.pdf');

    location.reload();
}

function muestra_oculta() {
    const x = document.getElementById('ocultar');
    x.style.display = x.style.display === "none" ? "block" : "none";
}

function muestra(id, valor) {
    document.getElementById(id).value = valor; // Asigna el nuevo valor
}

function checked(calculo) {
    document.getElementById("ckWh").checked = false;
    document.getElementById("ccp").checked = false;
    document.getElementById("cm2").checked = false;
    document.getElementById("kWhP").checked = false;
    switch (calculo) {
        case "cantidadPaneles":
            document.getElementById("ccp").checked = true;
            break;
        case "m2Paneles":
            document.getElementById("cm2").checked = true;
            break;
        case "kWhPlanta":
            document.getElementById("kWhP").checked = true;
            break;
        default:
            document.getElementById("ckWh").checked = true;
    }
}

function guardar_presupuesto() {
    const cliente = document.getElementById('cliente').value;
    const direccion = document.getElementById('direccion').value;
    const consumoMensual = parseFloat(document.getElementById('consumoMensual').value);
    const facturacionMensual = parseFloat(document.getElementById('facturacionMensual').value);
    const horasSol = parseFloat(document.getElementById('horasSol').value);
    const potenciaPanel = parseFloat(document.getElementById('potenciaPanel').value);
    const rendimientoPanel = parseFloat(document.getElementById('rendimientoPanel').value);
    const tamanoPanel = parseFloat(document.getElementById('tamanoPanel').value);
    const m2Paneles = parseFloat(document.getElementById('m2Paneles').value);
    const costoPanel = parseFloat(document.getElementById('costoPanel').value);
    const cantidadPaneles = parseFloat(document.getElementById('cantidadPaneles').value);
    const cantidadMedidorFlujoEpm = parseInt(document.getElementById('cantidadMedidorFlujoEpm').value);
    const medidorFlujoEpm = parseFloat(document.getElementById('medidorFlujoEpm').value);
    const margenComercial = parseFloat(document.getElementById('margenComercial').value) / 100;
    const materialesManoObra = parseFloat(document.getElementById('materialesManoObra').value) / 100;
    const costoInversor = parseFloat(document.getElementById('costoInversor').value);
    const cantidadInversores = parseInt(document.getElementById('cantidadInversores').value);
    const cantidadPlantas = parseInt(document.getElementById('cantidadPlantas').value);
    const kWhPlanta = parseFloat(document.getElementById('kWhPlanta').value);

    // Cálculos adicionales
    const kWPanel = (potenciaPanel * (1 - (rendimientoPanel / 100))).toFixed(3);
    const kWMes = (horasSol * kWPanel * cantidadPaneles).toFixed(2); // Ejemplo de cómo calcular la energía generada por mes
    const cumplimiento = ((kWMes / consumoMensual) * 100).toFixed(2); // Porcentaje de cobertura del consumo

    // Creación del objeto de datos
    const array_datos = {
        cliente,
        direccion,
        consumoMensual,
        facturacionMensual,
        horasSol,
        tamanoPanel,
        cantidadPaneles,
        m2Paneles,
        potenciaPanel,
        costoPanel,
        rendimientoPanel,
        kWPanel,
        cantidadMedidorFlujoEpm,
        medidorFlujoEpm,
        cantidadPlantas,
        cantidadInversores,
        costoInversor,
        kWhPlanta,
        kWMes,
        cumplimiento,
        margenComercial,
        materialesManoObra,
    };

    // Hacer la solicitud AJAX usando fetch
    fetch('guardar_presupuesto.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(array_datos)
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);
        alert('Presupuesto guardado exitosamente.');
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Hubo un error al guardar el presupuesto.');
    });
}
