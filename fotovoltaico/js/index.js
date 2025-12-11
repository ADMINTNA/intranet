// JavaScript Document
let calculo = "";

document.addEventListener('DOMContentLoaded', function () {
    const ids = ['consumoMensual', 'facturacionMensual', 'cantidadInversores', 'kWhPlanta', 'cantidadPaneles', 'calculo', 'cliente', 'direccion'];
    ids.forEach(function(item) {
        const elemento = document.getElementById(item);
        if (sessionStorage.getItem(item) && elemento) {
            elemento.value = sessionStorage.getItem(item);
        }
    });
});
function cargaInicial() {
    togleVisible('detalle');
    validaSEC();
    calcularSolucion();
    
}
function tipoCalculo(id) {
    calculo = id;
    calcularSolucion();
}

function calcularSolucion() {
    if (!calculo) { calculo = "consumoMensual"; }
    checked(calculo);
    const valores = obtenerValores();
    guardarEnSessionStorage(valores);
    switch (calculo) {
        case 'consumoMensual':
            calcularPorConsumoMensual(valores);
            break;
        case 'cantidadPaneles':
            guardarCantidadPaneles(valores);
            break;
        case 'm2Paneles':
            calcularPorM2Paneles(valores);
            break;
        case 'kWhPlanta':
            calcularPorKWhPlanta(valores);
            break;
    }

    actualizarValoresCalculados(valores);
    actualizarTablasComparativas(valores);
    mostrarBotonExportar();
    logearValores(valores);
}

function obtenerValores() {
    return {
        cliente: document.getElementById('cliente').value,
        direccion: document.getElementById('direccion').value,
        consumoMensual: parseFloat(document.getElementById('consumoMensual').value),
        facturacionMensual: parseFloat(document.getElementById('facturacionMensual').value),
        horasSol: parseFloat(document.getElementById('horasSol').value),
        potenciaPanel: parseFloat(document.getElementById('potenciaPanel').value),
        rendimientoPanel: parseFloat(document.getElementById('rendimientoPanel').value),
        tamanoPanel: parseFloat(document.getElementById('tamanoPanel').value),
        tramiteSec: parseFloat(document.getElementById('tramiteSec').value),
        sec: document.getElementById("sec").checked,
        kWPanel: (parseFloat(document.getElementById('potenciaPanel').value) * (1 - (parseFloat(document.getElementById('rendimientoPanel').value) / 100))).toFixed(3),   
        tipoCalculo: document.getElementById('calculo').value,
        cantidadInversores: document.getElementById('cantidadInversores').value,
        estructura: parseFloat(document.getElementById('estructura').value) / 100,
        costoInversor: parseFloat(document.getElementById('costoInversor').value),        
     };
}

function guardarEnSessionStorage(valores) {
    Object.keys(valores).forEach(key => {
        sessionStorage.setItem(key, valores[key]);
    });
}

function calcularPorConsumoMensual(valores) {
    const kWDia = (valores.consumoMensual / 30).toFixed(2);
    const kWhPlanta_tmp = (kWDia / valores.horasSol).toFixed(2);
    const cantidadPaneles = Math.ceil(kWhPlanta_tmp / valores.kWPanel);
    document.getElementById('kWDia').value = kWDia;
    document.getElementById('cantidadPaneles').value = cantidadPaneles;
}

function guardarCantidadPaneles(valores) {
    const cantidadPaneles = parseFloat(document.getElementById('cantidadPaneles').value);
    sessionStorage.setItem('cantidadPaneles', cantidadPaneles);
}

function calcularPorM2Paneles(valores) {
//    const m2Paneles = parseFloat(document.getElementById('m2Paneles').value).toFixed(2);
    const cantidadPaneles = Math.ceil(parseFloat(document.getElementById('m2Paneles').value) / valores.tamanoPanel);
    sessionStorage.setItem('cantidadPaneles', cantidadPaneles);
    document.getElementById('cantidadPaneles').value = cantidadPaneles;
}

function calcularPorKWhPlanta(valores) {
   // const kWhPlanta_tmp = parseFloat(document.getElementById('kWhPlanta').value);
    const cantidadPaneles = Math.ceil(parseFloat(document.getElementById('kWhPlanta').value) / valores.kWPanel);
    sessionStorage.setItem('cantidadPaneles', cantidadPaneles);
    document.getElementById('cantidadPaneles').value = cantidadPaneles;
}

function actualizarValoresCalculados(valores) {
    const cantidadPaneles = parseFloat(document.getElementById('cantidadPaneles').value);
    const kWDia = parseFloat(document.getElementById('kWDia').value);
    const kWhPlanta = Math.ceil(cantidadPaneles * valores.kWPanel);
    const kWMes = kWDia * 30;
    const m2Paneles = Math.ceil(cantidadPaneles * valores.tamanoPanel);
    const cumplimiento = (((kWhPlanta*valores.horasSol*30) / valores.consumoMensual) * 100).toFixed(2);
    if(cumplimiento >= 100) {
         document.getElementById("cumplimiento").style.color = '#38b6b6'; 
    } else {
        document.getElementById("cumplimiento").style.color = 'orangered'; 
    }
    document.getElementById('m2Paneles').value = m2Paneles;
    document.getElementById('kWhPlanta').value = kWhPlanta;
    document.getElementById('kWMes').value = kWMes;
    document.getElementById('cumplimiento').value = cumplimiento + "%";
    sessionStorage.setItem('kWhPlanta', kWhPlanta);
}

function actualizarTablasComparativas(valores) {
    const nivelesCobertura = [0.10, 0.25, 0.50, 0.75, 1.00];
    const resumenHtml = generarTablaResumen(nivelesCobertura, valores);
    const detalleHtml = generarTablaDetalle(nivelesCobertura, valores);
    document.getElementById('resumenComparativo').innerHTML = resumenHtml;
    document.getElementById('detalleComparativo').innerHTML = detalleHtml;
}

function generarTablaResumen(nivelesCobertura, valores) {
     let resumenHtml = '<tr><th>Cobertura</th><th>kWh Plta.</th><th>Precio Venta</th><th>Panel</th><th>m² Superficie</th><th>Nueva Factura</th><th>Ahorro Mensual</th><th>Ahorro Anual</th><th>Retrorno</th></tr>';
     nivelesCobertura.forEach(cobertura => {
        const fila = generarFilaResumen(cobertura, valores);
        resumenHtml += fila;
     });
     return resumenHtml;
}

function generarTablaDetalle(nivelesCobertura, valores) {
    let detalleHtml = '<tr><th>Cobert.</th><th>Paneles</th><th> m² </th><th style="text-align: right;">Valor Paneles</th><th style="text-align: right;">Inversor</th><th style="text-align: right;">M.F. EPM</th><th style="text-align: right;">Total Equipos</th><th style="text-align: right;">M y M de O</th><th style="text-align: right;">$ Colchon</th><th style="text-align: right;">$ Margen</th><th style="text-align: right;">$ Estructura</th><th style="text-align: right;">$ SEC</th></tr>';
    nivelesCobertura.forEach(cobertura => {
        const fila = generarFilaDetalle(cobertura, valores);
        detalleHtml += fila;
    });
    return detalleHtml;
}

function generarFilaResumen(cobertura, valores) {
    const { kwCubrirCobertura, cantidadPanelesCobertura, superficieNecesaria, precioVenta, nuevaFactura, ahorroMensual, ahorroAnual, periodoRecuperacion } = calcularValoresPorCobertura(cobertura, valores);
    return `
        <tr>
            <td style="text-align: center;">${(cobertura * 100).toFixed(0)}%</td>
            <td style="text-align: center;">${kwCubrirCobertura}</td>
            <td style="text-align: right;">$${precioVenta.toLocaleString()}</td>
            <td style="text-align: center;">${cantidadPanelesCobertura}</td>
            <td style="text-align: right;">${superficieNecesaria.toFixed(0)} m²</td>
            <td style="text-align: right;">$${nuevaFactura.toLocaleString()}</td>
            <td style="text-align: right;">$${ahorroMensual.toLocaleString()}</td>
            <td style="text-align: right;">$${ahorroAnual.toLocaleString()}</td>
            <td style="text-align: center;">${periodoRecuperacion} año</td>
        </tr>`;
}

function generarFilaDetalle(cobertura, valores) {
    const { cantidadPanelesCobertura, superficieNecesaria, costoPaneles, costoInversores, costoMedidorFlujoEpm, costoTotalEquipamiento, mmPesos, mCPesos, cPesos, ePesos, tramiteSec } = calcularValoresPorCobertura(cobertura, valores);
    return `
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
            <td style="text-align: right;">$${ePesos.toLocaleString()}</td>
            <td style="text-align: right;">$${tramiteSec.toLocaleString()}</td>
        </tr>`;
}

function calcularPrecioVenta($datos) {
    // Ejemplo de cálculo de precioVenta
    const $costoTotalEquipamiento = ($datos['cantidadPaneles'] * $datos['potenciaPanel']) + ($datos['cantidadInversores'] * $datos['costoInversor']);
    const $margenComercial = $datos['estructura'] / 100;
    const $precioVenta = $costoTotalEquipamiento * (1 + $margenComercial);

    return $precioVenta;
}

function calcularValoresPorCobertura(cobertura, valores) {
 //   const kWhPlanta = parseFloat(document.getElementById('kWhPlanta').value);
    const kwCubrirCobertura = Math.ceil(parseFloat(document.getElementById('kWhPlanta').value) * cobertura);
    const cantidadPanelesCobertura = Math.ceil(parseFloat(document.getElementById('cantidadPaneles').value) * cobertura);
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
    const estructura = parseFloat(document.getElementById('estructura').value) / 100;
    const ePesos = Math.ceil(costoPaneles * estructura); 
    const precioVenta = costoTotalEquipamiento + mmPesos + mCPesos + cPesos + ePesos + parseFloat(document.getElementById('tramiteSec').value);
    const nuevaFactura = Math.ceil(valores.facturacionMensual * (1 - cobertura));
    const ahorroMensual = Math.ceil(valores.facturacionMensual * cobertura);
    const ahorroAnual = ahorroMensual * 12;
    const periodoRecuperacion = ahorroAnual > 0 ? (precioVenta / ahorroAnual).toFixed(2) : 'N/A';
    return { kwCubrirCobertura, cantidadPanelesCobertura, superficieNecesaria, costoPaneles, costoInversores, costoMedidorFlujoEpm, costoTotalEquipamiento, mmPesos, mCPesos, cPesos, ePesos, tramiteSec, precioVenta, nuevaFactura, ahorroMensual, ahorroAnual, periodoRecuperacion };
}

function mostrarBotonExportar() {
    const btnexportar = document.getElementById('botonExportar');
    btnexportar.style.display = 'inline';
}

function logearValores(valores) {
    console.log("----------------------------------------------");
    console.log("Valores ingresados para cálculo por", calculo);
    console.log('Cliente:', valores.cliente);
    console.log('Dirección:', valores.direccion);
    console.log("Horas de Sol:", valores.horasSol);
    console.log("Consumo Mensual:", valores.consumoMensual);
    console.log("Factura Mensual:", valores.facturacionMensual);
    console.log("KW Día:", document.getElementById('kWDia').value);
    console.log("Cumplimiento:", document.getElementById('cumplimiento').value);
    console.log("Tamaño Panel:", valores.tamanoPanel);
    console.log("Potencia Panel:", valores.potenciaPanel);
    console.log("Pérdida Panel:", valores.rendimientoPanel);
    console.log("kWh Panel:", valores.kWPanel);
    console.log("Cantidad de Paneles:", document.getElementById('cantidadPaneles').value);
    console.log("kWh Planta:", document.getElementById('kWhPlanta').value);
    console.log("kWh Plantasin:", (document.getElementById('cantidadPaneles').value * valores.kWPanel).toFixed(2));
    console.log("m2 Paneles:", document.getElementById('m2Paneles').value);
    console.log("Tramite SEC:", document.getElementById('tramiteSec').value);
    console.log("Tipo Cálculo:", valores.tipoCalculo);
    console.log("Cantidad Inversor:", valores.cantidadInversores);
    console.log("Costo Inversor:", valores.costoInversor);
}

function exportarAPdf() {
    const contenido = document.querySelector('.container').cloneNode(true);

    // Ocultar botones y elementos no necesarios
    const elementosAOcultar = ['botonExportar', 'limpiar', 'calcular', 'botonGuardar'];
    elementosAOcultar.forEach(id => {
        const elemento = contenido.querySelector(`#${id}`);
        if (elemento) {
            elemento.remove();
        }
    });

    // Configurar opciones de PDF con márgenes adecuados
    const opciones = {
        margin: [0.5, 0.5, 0.5, 0.5], // Márgenes en pulgadas (arriba, derecha, abajo, izquierda)
        filename: 'Solucion_Fotovoltaica.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 1.2, scrollX: 0 }, // Ajuste de escala para evitar cortes
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' } // Formato carta
    };

    html2pdf().set(opciones).from(contenido).save();
    location.reload();
}
    



function exportarAPdf_era() {
    const contenido = document.querySelector('.container').cloneNode(true);
    const elementosAOcultar = ['botonExportar', 'limpiar', 'calcular', 'detalle', 'botonGuardar'];
    elementosAOcultar.forEach(id => {
       const elemento = contenido.querySelector(`#${id}`);
        if (elemento) {
            elemento.remove();
        }

    });
    html2pdf().from(contenido).save('Solucion_Fotovoltaica.pdf');
    location.reload();
}

function muestra_oculta() {
    const x = document.getElementById('ocultar');
    x.style.display = x.style.display === "none" ? "block" : "none";
}

function muestra(id, valor) {
    document.getElementById(id).value = valor;
}

function checked(calculo) {
    document.getElementById("ckWh").checked = false;
    document.getElementById("ccp" ).checked = false;
    document.getElementById("cm2" ).checked = false;
    document.getElementById("kWhP").checked = false;
    document.getElementById("calculo").value = calculo;
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
function validaSEC() {
    if(document.getElementById("sec").checked == true) {
       document.getElementById('tramiteSec').value = 1143000; 
    } else {
        document.getElementById('tramiteSec').value = 0;       
    }
    calcularSolucion();
}
function oculta(id) {
    const togle = document.getElementById(id);
    togle.style.display = 'none';
}
function muestra(id) {
    const togle = document.getElementById(id);
    togle.style.display = 'block';
}
function togleVisible(id) {
  const togle = document.getElementById(id);
  if (togle.style.display === 'none') {
    togle.style.display = 'block';
  } else {
    togle.style.display = 'none';
  }
}

function enviarSolucion() {
    const valores = {
        cliente: document.getElementById('cliente').value,
        direccion: document.getElementById('direccion').value,
        consumoMensual: parseFloat(document.getElementById('consumoMensual').value),
        facturacionMensual: parseFloat(document.getElementById('facturacionMensual').value),
        horasSol: parseFloat(document.getElementById('horasSol').value),
        potenciaPanel: parseFloat(document.getElementById('potenciaPanel').value),
        rendimientoPanel: parseFloat(document.getElementById('rendimientoPanel').value),
        tamanoPanel: parseFloat(document.getElementById('tamanoPanel').value),
        tramiteSec: parseFloat(document.getElementById('tramiteSec').value),
        sec: document.getElementById('sec').checked ? 1 : 0,
        tipoCalculo: document.getElementById('calculo').value,
        cantidadInversores: parseInt(document.getElementById('cantidadInversores').value, 10),
        estructura: parseFloat(document.getElementById('estructura').value),
        costoInversor: parseFloat(document.getElementById('costoInversor').value),
        cantidadPaneles: parseInt(document.getElementById('cantidadPaneles').value, 10),
        kWhPlanta: parseFloat(document.getElementById('kWhPlanta').value),
        kWDia: parseFloat(document.getElementById('kWDia').value),
        cumplimiento: parseFloat(document.getElementById('cumplimiento').value),
        precioVenta: calcularPrecioVentaFrontend(), // Agregar precioVenta
    };

    fetch('./includes/guardar_solucion.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(valores),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                alert('Solución guardada con éxito.');
                location.reload();
            } else {
                alert('Error al guardar la solución: ' + data.message);
            }
        })
        .catch((error) => {
            console.error('Error al enviar la solución:', error);
        });

}

function calcularPrecioVentaFrontend() {
    const cantidadPaneles = parseInt(document.getElementById('cantidadPaneles').value, 10);
    const costoInversor = parseFloat(document.getElementById('costoInversor').value);
    const potenciaPanel = parseFloat(document.getElementById('potenciaPanel').value);
    const estructura = parseFloat(document.getElementById('estructura').value) / 100;

    const costoTotalEquipamiento = cantidadPaneles * potenciaPanel + costoInversor;
    return (costoTotalEquipamiento * (1 + estructura)).toFixed(2);
}

function cargarProyecto(event) {
    const id = event.target.getAttribute('data-id');

    if (!id) {
        alert('No se encontró el ID del proyecto.');
        return;
    }

    fetch(`./includes/cargar_proyecto.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta de la API:', data); // Verifica la respuesta

            if (data.success) {
                const proyecto = data.data;

                document.getElementById('cliente').value = proyecto.cliente;
                document.getElementById('direccion').value = proyecto.direccion;
                document.getElementById('consumoMensual').value = proyecto.consumoMensual;
                document.getElementById('facturacionMensual').value = proyecto.facturacionMensual;
                document.getElementById('horasSol').value = proyecto.horasSol;
                document.getElementById('potenciaPanel').value = proyecto.potenciaPanel;
                document.getElementById('rendimientoPanel').value = proyecto.rendimientoPanel;
                document.getElementById('tamanoPanel').value = proyecto.tamanoPanel;
                document.getElementById('tramiteSec').value = proyecto.tramiteSec;
                document.getElementById('sec').checked = proyecto.sec === 1;
                document.getElementById('cantidadInversores').value = proyecto.cantidadInversores;
                document.getElementById('estructura').value = proyecto.estructura;
                document.getElementById('costoInversor').value = proyecto.costoInversor;
                document.getElementById('cantidadPaneles').value = proyecto.cantidadPaneles;
                document.getElementById('kWhPlanta').value = proyecto.kWhPlanta;
                document.getElementById('kWDia').value = proyecto.kWDia;
                document.getElementById('cumplimiento').value = `${proyecto.cumplimiento}%`;
                calcularSolucion()
           //     alert('Proyecto cargado exitosamente.');
            } else {
                alert('Error al cargar el proyecto: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error al cargar el proyecto:', error);
            alert('Ocurrió un error al cargar el proyecto.');
        });
   
}
function filtrarSoluciones() {
    const termino = document.getElementById("buscarSolucion").value.toLowerCase().trim();
    const filas = document.querySelectorAll("#listadosoluciones tbody tr");

    filas.forEach(fila => {
        const cliente = fila.cells[1].textContent.toLowerCase();
        const direccion = fila.cells[2].textContent.toLowerCase();

        if (cliente.includes(termino) || direccion.includes(termino)) {
            fila.style.display = ""; // Mostrar fila si coincide con la búsqueda
        } else {
            fila.style.display = "none"; // Ocultar fila si no coincide
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    document.body.addEventListener("click", function (event) {
        if (event.target.classList.contains("btnEliminar")) {
            const id = event.target.getAttribute("data-id");

            if (confirm(`¿Estás seguro de que deseas eliminar la solución con ID ${id}?`)) {
                fetch("./includes/eliminar_solucion.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `id=${id}`
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error HTTP: ${response.status}`);
                    }
                    return response.text(); // ⚠️ Leer como texto primero
                })
                .then(text => {
                    try {
                        return JSON.parse(text); // ⚠️ Convertir manualmente a JSON
                    } catch (error) {
                        throw new Error("Respuesta del servidor no es JSON válido: " + text);
                    }
                })
                .then(data => {
                    if (data.success) {
                        alert("Solución eliminada correctamente.");
                        location.reload(); // Recargar la página para actualizar la tabla
                    } else {
                  //      alert("Error al eliminar: " + data.message);
                        console.error("Error en la respuesta del servidor:", data);
                    }
                })
                .catch(error => {
                   // console.error("Error en la solicitud de eliminación:", error);
                    alert("Error al comunicarse con el servidor: " + error.message);
                });
            }
        }
    });
});
