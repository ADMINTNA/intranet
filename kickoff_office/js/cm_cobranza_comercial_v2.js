// ==========================================================
// Cobranza Comercial – Eventos Estado y Comentario
// /kickoff/js/cm_cobranza_comercial.js
// Autor: Mauricio Araneda
// Fecha: 2025-11-20
// Versión: Dinámico + UTF-8 + Sin Bordes
// Codificación: UTF-8 sin BOM
// ==========================================================

console.log("cm_cobranza_comercial.js v8 loaded");

// ------------------------------
// Evento para Estado Sweet
// ------------------------------
function bindEventosEstado() {
    // Buscar todos los selects en celdas con clase estado-sweet-cell
    const cells = document.querySelectorAll('td.estado-sweet-cell');

    cells.forEach(cell => {
        const select = cell.querySelector('select');
        if (!select) return;

        // Evitar duplicar eventos
        if (select.dataset.bound) return;
        select.dataset.bound = "true";

        select.addEventListener('change', function () {
            const rut = cell.dataset.rut;
            const valor = this.value;
            const icono = cell.querySelector('.estado-icono');

            // Usar ruta absoluta para que funcione cuando se carga via AJAX
            const basePath = (window.KICKOFF_BASE_PATH || '/kickoff_icontel/').replace(/\/$/, '') + '/';
            fetch(basePath + 'update_estado_sweet.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `rut=${encodeURIComponent(rut)}&estado=${encodeURIComponent(valor)}`
            })
                .then(r => r.json())
                .then(d => {
                    if (icono) {
                        icono.textContent = d.success ? "✅" : "❌";
                        icono.style.color = d.success ? "limegreen" : "red";
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
// Evento para Comentario
// ------------------------------
function bindEventosComentario() {
    // Buscar todos los inputs en celdas con clase comentario-cell
    const cells = document.querySelectorAll('td.comentario-cell');

    cells.forEach(cell => {
        const input = cell.querySelector('input');
        if (!input) return;

        // Evitar duplicar eventos
        if (input.dataset.bound) return;
        input.dataset.bound = "true";

        input.addEventListener('change', function () {
            const rut = cell.dataset.rut;
            const valor = this.value;
            const icono = cell.querySelector('.comentario-icono');

            // Usar ruta absoluta para que funcione cuando se carga via AJAX
            const basePath = (window.KICKOFF_BASE_PATH || '/kickoff_icontel/').replace(/\/$/, '') + '/';
            fetch(basePath + 'update_comentario_sweet.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `rut=${encodeURIComponent(rut)}&comentario=${encodeURIComponent(valor)}`
            })
                .then(r => r.json())
                .then(d => {
                    if (icono) {
                        icono.textContent = d.success ? "✅" : "❌";
                        icono.style.color = d.success ? "limegreen" : "red";
                    }
                })
                .catch(err => {
                    if (icono) {
                        icono.textContent = "❌";
                        icono.style.color = "red";
                    }
                    console.error("Error actualizando comentario:", err);
                });
        });
    });
}


// ------------------------------
// Función para Recarga + Actualización Automática
// Procesa cuentas con docs vencidos > 60 días
// Llamar via onclick="procesarCobranzaComercial(this)"
// ------------------------------
function procesarCobranzaComercial(btn) {
    // Si no se pasa el botón, buscarlo
    const reloadBtn = btn || document.getElementById('reloadModulo') || document.getElementById('actualiza');

    if (!reloadBtn) {
        console.warn("⚠️ Ícono de recarga no encontrado");
        return;
    }

    console.log("🔄 Iniciando proceso de actualización de cobranza comercial...");

    // Guardar ícono original y mostrar estado de carga
    const iconoOriginal = reloadBtn.textContent;
    reloadBtn.textContent = "⏳";
    reloadBtn.style.cursor = "wait";
    reloadBtn.style.opacity = "0.6";

    // Ejecutar proceso AJAX
    const basePath = (window.KICKOFF_BASE_PATH || '/kickoff_icontel/').replace(/\/$/, '') + '/';
    fetch(basePath + 'ajax/procesar_cobranza_comercial.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
        .then(response => response.json())
        .then(data => {
            console.log("📊 Resultado:", data);

            if (data.ok) {
                // Mostrar resultado brevemente
                reloadBtn.textContent = "✅";
                reloadBtn.style.color = "limegreen";
                reloadBtn.style.opacity = "1";
                reloadBtn.style.cursor = "pointer";

                // Mensaje en consola con detalles
                console.log(`✅ Proceso completado: ${data.procesadas} actualizadas, ${data.omitidas} omitidas, ${data.errores || 0} errores`);

                // Recargar módulo después de 1 segundo
                setTimeout(() => {
                    location.reload();
                }, 1000);

            } else {
                // Error en el proceso
                reloadBtn.textContent = "❌";
                reloadBtn.style.color = "red";
                console.error("❌ Error en proceso:", data.msg);

                // Restaurar ícono después de 2 segundos
                setTimeout(() => {
                    reloadBtn.textContent = iconoOriginal;
                    reloadBtn.style.color = "";
                    reloadBtn.style.cursor = "pointer";
                    reloadBtn.style.opacity = "1";
                }, 2000);
            }
        })
        .catch(err => {
            console.error("❌ Error de conexión:", err);
            reloadBtn.textContent = "❌";
            reloadBtn.style.color = "red";

            // Restaurar ícono después de 2 segundos
            setTimeout(() => {
                reloadBtn.textContent = iconoOriginal;
                reloadBtn.style.color = "";
                reloadBtn.style.cursor = "pointer";
                reloadBtn.style.opacity = "1";
            }, 2000);
        });
}


// =======================================================
// FILTRADO DE TABLA (Estado Sweet multi-select + inputs)
// =======================================================
function aplicarFiltros() {
    const tabla = document.getElementById("cobranza");
    if (!tabla) return;

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

    // Obtener filas de datos (excluir títulos, subtítulos, filtros y totales)
    const filas = tabla.querySelectorAll("tr:not(.subtit):not(.subtitulo):not(.filtros-row)");

    let visibles = 0;

    filas.forEach((fila, idx) => {
        // Saltar fila de título (primera fila) y fila de totales (última generalmente)
        if (idx === 0 || fila.querySelector("td[colspan]")) {
            return;
        }

        const celdas = fila.querySelectorAll("td");
        if (celdas.length < 10) return;

        let mostrar = true;

        // Filtro por Estado Sweet (columna 3, índice 3)
        if (selectedEstados.length > 0) {
            const selectEstado = celdas[3]?.querySelector("select");
            const estadoActual = selectEstado ? selectEstado.value.toLowerCase() : "";
            if (!selectedEstados.includes(estadoActual)) {
                mostrar = false;
            }
        }

        // Filtros de texto
        for (let col in filtrosTexto) {
            const idx = parseInt(col);
            const celda = celdas[idx];
            if (!celda) continue;

            let textocelda = celda.textContent.toLowerCase();
            // Para inputs/selects, obtener el valor
            const input = celda.querySelector("input, select");
            if (input) textocelda = (input.value || input.textContent).toLowerCase();

            const filtroVal = filtrosTexto[col];

            // Filtro numérico con > o <
            if (filtroVal.startsWith(">")) {
                const num = parseFloat(filtroVal.substring(1));
                const numCelda = parseFloat(textocelda.replace(/\D/g, ""));
                if (isNaN(num) || isNaN(numCelda) || numCelda <= num) {
                    mostrar = false;
                }
            } else if (filtroVal.startsWith("<")) {
                const num = parseFloat(filtroVal.substring(1));
                const numCelda = parseFloat(textocelda.replace(/\D/g, ""));
                if (isNaN(num) || isNaN(numCelda) || numCelda >= num) {
                    mostrar = false;
                }
            } else {
                // Filtro de texto normal
                if (!textocelda.includes(filtroVal)) {
                    mostrar = false;
                }
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

// Mostrar/ocultar panel de filtros
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

// Cerrar panel sin aplicar
function cerrarPanelFiltros() {
    const panel = document.getElementById("panel-filtros");
    const overlay = document.getElementById("panel-overlay");
    const btn = document.getElementById("btn-filtrar");

    panel.classList.add("panel-filtros-hidden");
    overlay.classList.remove("visible");
    btn.textContent = "🔍 Filtrar";
    btn.classList.remove("activo");
}

// Aplicar filtros y cerrar panel
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

    localStorage.setItem("cobranza_filtros", JSON.stringify(filtros));
    console.log("💾 Filtros guardados:", filtros);
}

// Restaurar filtros desde localStorage
function restaurarFiltros() {
    const saved = localStorage.getItem("cobranza_filtros");
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

// Limpiar todos los filtros
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
    localStorage.removeItem("cobranza_filtros");

    console.log("🧹 Filtros limpiados y eliminados de localStorage");
}

// Enlazar eventos de filtrado
function bindFiltros() {
    // Checkboxes de Estado Sweet
    const checksEstado = document.querySelectorAll(".filtro-estado-check");
    checksEstado.forEach(check => {
        check.addEventListener("change", () => {
            aplicarFiltros();
            actualizarBotonEstado();
            // Cerrar dropdown después de seleccionar
            const dropdown = document.getElementById("filtro-estado-dropdown");
            if (dropdown) {
                dropdown.classList.add("dropdown-hidden");
                dropdown.classList.remove("dropdown-visible");
            }
        });
    });

    // Inputs de texto
    const filtrosInput = document.querySelectorAll(".filtro-input");
    filtrosInput.forEach(input => {
        input.addEventListener("input", aplicarFiltros);
    });

    console.log("🔍 Filtros enlazados correctamente");

    // Restaurar filtros guardados en localStorage
    restaurarFiltros();
}

// Actualizar texto del botón según selección
function actualizarBotonEstado() {
    const btn = document.getElementById("btn-filtro-estado");
    const checks = document.querySelectorAll(".filtro-estado-check:checked");

    if (checks.length === 0) {
        btn.textContent = "🔽 Estado";
    } else if (checks.length === 1) {
        btn.textContent = "✓ " + checks[0].parentElement.textContent.trim();
    } else {
        btn.textContent = "✓ " + checks.length + " seleccionados";
    }
}


// =======================================================
// ORDENAMIENTO DEFINITIVO (SELECT + INPUT + TEXTO + NÚMEROS)
// =======================================================

// Función de inicialización principal
function initCobranzaComercial() {
    const table = document.getElementById("cobranza");
    if (!table) return;

    // Evitar inicialización duplicada
    if (table.dataset.initialized === "true") return;
    table.dataset.initialized = "true";

    console.log("🚀 Inicializando Cobranza Comercial...");

    const headers = table.querySelectorAll("th.sortable");

    headers.forEach(th => {
        th.addEventListener("click", function () {
            const colType = th.dataset.col;

            // Calcular el índice correcto basado en el tipo de columna
            let colIndex;
            switch (colType) {
                case "razon": colIndex = 1; break;
                case "estado": colIndex = 2; break;
                case "comentario": colIndex = 3; break;
                case "monto": colIndex = 4; break;
                case "docs": colIndex = 5; break;
                case "dias_venc": colIndex = 6; break;
                case "ejecutivo": colIndex = 7; break;
                case "fecha": colIndex = 8; break;
                case "dias": colIndex = 9; break;
                default: colIndex = th.cellIndex;
            }

            // Determinar orden actual
            const isAsc = !th.classList.contains("asc");

            // Limpiar clases e indicadores de otros headers
            headers.forEach(h => {
                h.classList.remove("asc", "desc");
                const indicator = h.querySelector(".order-indicator");
                if (indicator) indicator.textContent = "";
            });

            // Aplicar clase e indicador al header actual
            th.classList.toggle("asc", isAsc);
            th.classList.toggle("desc", !isAsc);
            const indicator = th.querySelector(".order-indicator");
            if (indicator) indicator.textContent = isAsc ? "↑" : "↓";

            // Obtener filas (excluyendo encabezados)
            const tbody = table.querySelector("tbody") || table;
            const rows = Array.from(tbody.querySelectorAll("tr:not(.subtitulo)"));

            // Ordenar filas
            rows.sort((rowA, rowB) => {
                const valA = getCellValue(rowA, colIndex, colType);
                const valB = getCellValue(rowB, colIndex, colType);
                return compareValues(valA, valB, isAsc);
            });

            // Reinsertar filas ordenadas
            rows.forEach(row => tbody.appendChild(row));

            console.log(`Ordenado por ${colType} (${isAsc ? 'ASC' : 'DESC'}), columna índice ${colIndex}`);
        });
    });

    // Ejecutar enlaces
    bindEventosEstado();
    bindEventosComentario();
    bindFiltros();

    console.log("✅ Cobranza Comercial inicializada correctamente");
}

// Ejecutar inicialización: si DOM ya está listo, ejecutar inmediatamente
// Si no, esperar al DOMContentLoaded (para carga directa)
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initCobranzaComercial);
} else {
    // DOM ya está listo (carga via AJAX), ejecutar inmediatamente
    initCobranzaComercial();
}

/**
 * Obtiene el valor de una celda según el tipo de columna
 */
function getCellValue(row, index, type) {
    const cell = row.cells[index];
    if (!cell) {
        console.warn(`Celda ${index} no encontrada en fila`);
        return "";
    }

    switch (type) {
        case "estado":
            // Buscar select dentro de la celda (SIN data-campo)
            const select = cell.querySelector("select");
            if (select && select.selectedIndex >= 0) {
                const selectedOption = select.options[select.selectedIndex];
                const text = selectedOption ? selectedOption.text.trim().toLowerCase() : "";
                return text;
            }
            return "";

        case "comentario":
            // Buscar input dentro de la celda (SIN data-campo)
            const input = cell.querySelector("input");
            if (input) {
                const value = input.value.trim().toLowerCase();
                return value;
            }
            return "";

        case "monto":
        case "docs":
        case "dias_venc":
        case "dias":
            // Obtener texto y limpiar
            let text = cell.innerText.trim();
            // Remover símbolo de peso y espacios
            text = text.replace(/[$\s]/g, "");
            // Remover puntos (separadores de miles)
            text = text.replace(/\./g, "");
            // Mantener solo números y signo negativo
            text = text.replace(/[^0-9-]/g, "");

            const numValue = text === "" || text === "-" ? 0 : parseInt(text, 10);
            return numValue;

        case "razon":
        case "ejecutivo":
        case "fecha":
        default:
            // Texto normal - tomar el innerText completo
            return cell.innerText.trim().toLowerCase();
    }
}

/**
 * Compara dos valores para el ordenamiento
 */
function compareValues(a, b, isAsc) {
    // Si ambos son números
    if (typeof a === "number" && typeof b === "number") {
        return isAsc ? a - b : b - a;
    }

    // Manejo de valores vacíos
    if (a === "" && b === "") return 0;
    if (a === "") return isAsc ? 1 : -1;
    if (b === "") return isAsc ? -1 : 1;

    // Comparación de cadenas
    return isAsc
        ? a.toString().localeCompare(b.toString(), "es", { numeric: true })
        : b.toString().localeCompare(a.toString(), "es", { numeric: true });
}