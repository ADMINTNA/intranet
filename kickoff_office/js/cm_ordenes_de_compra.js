// ==========================================================
// Órdenes de Compra Pendientes – Eventos Inline Edit
// /kickoff_icontel/js/cm_ordenes_de_compra.js
// Autor: Mauricio Araneda (mAo) - 2025-12
// Codificación: UTF-8 sin BOM
// ==========================================================

console.log("cm_ordenes_de_compra.js loaded");

// ------------------------------
// Evento para Estado Factura (select)
// ------------------------------
function bindEventosEstadoFactura() {
    const selects = document.querySelectorAll('select.estado-factura');

    selects.forEach(select => {
        // Evitar duplicar eventos
        if (select.dataset.bound) return;
        select.dataset.bound = "true";

        select.addEventListener('change', function () {
            const cotiId = this.dataset.cotiId;
            const valor = this.value;
            const container = this.closest('.estado-container');
            const icono = container ? container.querySelector('.estado-icono') : null;

            // Mostrar estado de carga
            if (icono) {
                icono.textContent = "⏳";
                icono.style.color = "#888";
            }

            // Ruta base para AJAX
            const basePath = (window.KICKOFF_BASE_PATH || '/kickoff_icontel/').replace(/\/$/, '') + '/';

            fetch(basePath + 'update_cotizacion.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `coti_id=${encodeURIComponent(cotiId)}&campo=invoice_status&valor=${encodeURIComponent(valor)}`
            })
                .then(r => r.json())
                .then(d => {
                    if (icono) {
                        icono.textContent = d.success ? "✅" : "❌";
                        icono.style.color = d.success ? "limegreen" : "red";

                        // Ocultar ícono después de 2 segundos
                        setTimeout(() => {
                            icono.textContent = "";
                        }, 2000);
                    }

                    if (!d.success) {
                        console.error("Error actualizando estado:", d.msg);
                    }
                })
                .catch(err => {
                    if (icono) {
                        icono.textContent = "❌";
                        icono.style.color = "red";
                    }
                    console.error("Error actualizando estado:", err);
                });
        });
    });
}


// ------------------------------
// Evento para N° DTE (input)
// ------------------------------
function bindEventosNumeroDte() {
    const inputs = document.querySelectorAll('input.numero-dte-input');

    inputs.forEach(input => {
        // Evitar duplicar eventos
        if (input.dataset.bound) return;
        input.dataset.bound = "true";

        input.addEventListener('change', function () {
            const cotiId = this.dataset.cotiId;
            const valor = this.value;
            const container = this.closest('.dte-container');
            const icono = container ? container.querySelector('.dte-icono') : null;

            // Mostrar estado de carga
            if (icono) {
                icono.textContent = "⏳";
                icono.style.color = "#888";
            }

            // Ruta base para AJAX
            const basePath = (window.KICKOFF_BASE_PATH || '/kickoff_icontel/').replace(/\/$/, '') + '/';

            fetch(basePath + 'update_cotizacion.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `coti_id=${encodeURIComponent(cotiId)}&campo=num_dte__compra_c&valor=${encodeURIComponent(valor)}`
            })
                .then(r => r.json())
                .then(d => {
                    if (icono) {
                        icono.textContent = d.success ? "✅" : "❌";
                        icono.style.color = d.success ? "limegreen" : "red";

                        // Ocultar ícono después de 2 segundos
                        setTimeout(() => {
                            icono.textContent = "";
                        }, 2000);
                    }

                    if (!d.success) {
                        console.error("Error actualizando N° DTE:", d.msg);
                    }
                })
                .catch(err => {
                    if (icono) {
                        icono.textContent = "❌";
                        icono.style.color = "red";
                    }
                    console.error("Error actualizando N° DTE:", err);
                });
        });
    });
}


// =======================================================
// FILTRADO DE TABLA (Estado multi-select + inputs)
// =======================================================
function aplicarFiltros() {
    const tabla = document.getElementById("Ordenes_de_Compra");
    if (!tabla) {
        console.error("❌ Tabla Ordenes_de_Compra no encontrada");
        return;
    }

    // Obtener estados seleccionados de los checkboxes
    const checksEstado = document.querySelectorAll(".filtro-estado-check:checked");
    const selectedEstados = Array.from(checksEstado).map(chk => chk.value.toLowerCase());
    const filtrosInput = document.querySelectorAll(".filtro-input");
    const filtrosTexto = {};

    filtrosInput.forEach(input => {
        const col = input.dataset.col;
        const val = input.value.trim().toLowerCase();
        if (val) filtrosTexto[col] = val;
    });

    console.log("🔍 Aplicando filtros - Estados:", selectedEstados, "Textos:", filtrosTexto);

    // Obtener TODAS las filas de la tabla
    const filas = tabla.querySelectorAll("tr");
    console.log(`📊 Total de filas en tabla: ${filas.length}`);

    let visibles = 0;

    filas.forEach((fila, idx) => {
        const celdas = fila.querySelectorAll("td");
        const hasColspan = fila.querySelector("td[colspan]");
        const isSubtit = fila.classList.contains("subtit");

        // Debug todas las filas
        if (idx <= 3) {
            console.log(`📍 Fila ${idx}: celdas=${celdas.length}, subtit=${isSubtit}, colspan=${!!hasColspan}`);
        }

        // Saltar fila de título (primera fila con background morado)
        if (idx === 0) return;

        // Saltar fila de encabezado (subtit)
        if (isSubtit) return;

        // Saltar filas de título/totales (colspan grande, más de 6 columnas)
        const bigColspan = fila.querySelector("td[colspan='10'], td[colspan='11'], td[colspan='12']");
        if (bigColspan) return;

        // Si tiene muy pocas celdas, saltar
        if (celdas.length < 5) return;

        let mostrar = true;

        // Filtro por Estado (columna 3 - después de #, N°, Asunto)
        if (selectedEstados.length > 0) {
            const selectEstado = celdas[3]?.querySelector("select");
            const estadoActual = selectEstado ? selectEstado.value.toLowerCase() : "";
            if (!selectedEstados.includes(estadoActual)) {
                mostrar = false;
            }
        }

        // Filtros de texto
        for (let col in filtrosTexto) {
            const colIdx = parseInt(col);
            const celda = celdas[colIdx];
            if (!celda) continue;

            let textocelda = celda.textContent.toLowerCase();
            // Para inputs/selects, obtener el valor
            const input = celda.querySelector("input, select");
            if (input) textocelda = (input.value || input.textContent).toLowerCase();

            const filtroVal = filtrosTexto[col];

            if (!textocelda.includes(filtroVal)) {
                mostrar = false;
            }
        }

        fila.style.display = mostrar ? "" : "none";
        if (mostrar) visibles++;
    });

    console.log(`📋 Filtros aplicados: ${visibles} filas visibles`);
}

// =======================================================
// PANEL FLOTANTE DE FILTROS
// =======================================================

function togglePanelFiltros() {
    const panel = document.getElementById("panel-filtros");
    const overlay = document.getElementById("panel-overlay");
    const btn = document.getElementById("btn-filtrar");

    if (panel.classList.contains("panel-filtros-hidden")) {
        // Mover al body para evitar recorte por contenedor padre
        document.body.appendChild(overlay);
        document.body.appendChild(panel);

        panel.classList.remove("panel-filtros-hidden");
        overlay.classList.add("visible");
        btn.textContent = "✅ Aplicar";
        btn.classList.add("activo");
    } else {
        aplicarYCerrar();
    }
}

function cerrarPanelFiltros() {
    const panel = document.getElementById("panel-filtros");
    const overlay = document.getElementById("panel-overlay");
    const btn = document.getElementById("btn-filtrar");

    panel.classList.add("panel-filtros-hidden");
    overlay.classList.remove("visible");
    btn.textContent = "🔍 Filtrar";
    btn.classList.remove("activo");
}

function aplicarYCerrar() {
    aplicarFiltros();
    cerrarPanelFiltros();

    // Mostrar estado del filtro en el botón
    const btn = document.getElementById("btn-filtrar");
    const checksEstado = document.querySelectorAll(".filtro-estado-check:checked");
    const inputs = document.querySelectorAll(".filtro-input");
    let filtrosActivos = checksEstado.length;

    inputs.forEach(inp => { if (inp.value.trim()) filtrosActivos++; });

    if (filtrosActivos > 0) {
        btn.textContent = `🔍 Filtros (${filtrosActivos})`;
        btn.classList.add("activo");
    } else {
        btn.textContent = "🔍 Filtrar";
        btn.classList.remove("activo");
    }

    // Guardar filtros en localStorage
    guardarFiltros();
}

// Guardar filtros en localStorage
function guardarFiltros() {
    const filtros = {
        estados: [],
        inputs: {}
    };

    // Guardar checkboxes
    document.querySelectorAll(".filtro-estado-check:checked").forEach(chk => {
        filtros.estados.push(chk.value);
    });

    // Guardar inputs
    document.querySelectorAll(".filtro-input").forEach(inp => {
        const col = inp.dataset.col;
        if (inp.value.trim()) {
            filtros.inputs[col] = inp.value.trim();
        }
    });

    localStorage.setItem("ordenes_compra_filtros", JSON.stringify(filtros));
    console.log("💾 Filtros guardados:", filtros);
}

// Restaurar filtros desde localStorage
function restaurarFiltros() {
    const saved = localStorage.getItem("ordenes_compra_filtros");
    if (!saved) return;

    try {
        const filtros = JSON.parse(saved);

        // Restaurar checkboxes
        if (filtros.estados && filtros.estados.length > 0) {
            document.querySelectorAll(".filtro-estado-check").forEach(chk => {
                chk.checked = filtros.estados.includes(chk.value);
            });
        }

        // Restaurar inputs
        if (filtros.inputs) {
            document.querySelectorAll(".filtro-input").forEach(inp => {
                const col = inp.dataset.col;
                if (filtros.inputs[col]) {
                    inp.value = filtros.inputs[col];
                }
            });
        }

        // Aplicar filtros automáticamente
        aplicarFiltros();

        // Actualizar botón
        const checksActivos = document.querySelectorAll(".filtro-estado-check:checked").length;
        const inputsActivos = Object.keys(filtros.inputs || {}).length;
        const btn = document.getElementById("btn-filtrar");

        if (checksActivos + inputsActivos > 0) {
            btn.textContent = `🔍 Filtros (${checksActivos + inputsActivos})`;
            btn.classList.add("activo");
        }

        console.log("♻️ Filtros restaurados:", filtros);
    } catch (e) {
        console.error("Error restaurando filtros:", e);
    }
}

function limpiarFiltros() {
    // Limpiar checkboxes
    document.querySelectorAll(".filtro-estado-check").forEach(chk => chk.checked = false);

    // Limpiar inputs
    document.querySelectorAll(".filtro-input").forEach(inp => inp.value = "");

    // Aplicar (mostrar todas las filas)
    aplicarFiltros();

    // Actualizar botón
    const btn = document.getElementById("btn-filtrar");
    btn.textContent = "🔍 Filtrar";
    btn.classList.remove("activo");

    // Eliminar de localStorage
    localStorage.removeItem("ordenes_compra_filtros");

    console.log("🧹 Filtros limpiados y eliminados de localStorage");
}

function bindFiltros() {
    // Checkboxes de Estado
    const checksEstado = document.querySelectorAll(".filtro-estado-check");
    checksEstado.forEach(check => {
        check.addEventListener("change", () => {
            aplicarFiltros();
            guardarFiltros();
        });
    });

    // Inputs de texto
    const filtrosInput = document.querySelectorAll(".filtro-input");
    filtrosInput.forEach(input => {
        input.addEventListener("input", () => {
            aplicarFiltros();
            guardarFiltros();
        });
    });

    console.log("🔍 Filtros enlazados correctamente");

    // Restaurar filtros guardados en localStorage
    restaurarFiltros();
}


// ------------------------------
// Función de inicialización
// ------------------------------
function initOrdenesDeCompra() {
    const table = document.getElementById("Ordenes_de_Compra");
    if (!table) return;

    // Evitar inicialización duplicada
    if (table.dataset.initialized === "true") return;
    table.dataset.initialized = "true";

    console.log("🚀 Inicializando Órdenes de Compra...");

    bindEventosEstadoFactura();
    bindEventosNumeroDte();
    bindFiltros();

    console.log("✅ Órdenes de Compra inicializada correctamente");
}

// Ejecutar inicialización
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initOrdenesDeCompra);
} else {
    // DOM ya está listo (carga via AJAX)
    initOrdenesDeCompra();
}

