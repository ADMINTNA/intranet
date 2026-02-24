// ========================================================
// Kickoff AJAX – cm_tareas_pendientes.js
// Guarda cada cambio vía AJAX con íconos  ✅ ❌
// ========================================================

console.log("cm_tareas_pendientes.js (AJAX + ICONOS) cargado");

// -----------------------------------------------
// Insertar una celda para mostrar el estado AJAX
// -----------------------------------------------
function prepararFila(fila) {

    // Evitar duplicar
    if (fila.querySelector(".estado-ajax")) return;

    const td = document.createElement("td");
    td.className = "estado-ajax";
    td.style.width = "22px";
    td.style.textAlign = "center";
    td.style.fontSize = "18px";
    td.style.color = "#666";

    // Insertar como primer td
    fila.insertBefore(td, fila.firstElementChild);
}

// -----------------------------------------------
// Mostrar resultado  ✅ o ❌
// -----------------------------------------------
function mostrarIconoOK(fila) {
    const celda = fila.querySelector(".estado-ajax");
    if (!celda) return;

    celda.textContent = " ✅";
    celda.style.color = "limegreen";

    fila.classList.add("tarea-ok");
    setTimeout(() => {
        fila.classList.remove("tarea-ok");
    }, 800);
}

function mostrarIconoError(fila) {
    const celda = fila.querySelector(".estado-ajax");
    if (!celda) return;

    celda.textContent = "❌";
    celda.style.color = "red";

    fila.classList.add("tarea-error");
    setTimeout(() => {
        fila.classList.remove("tarea-error");
    }, 1200);
}

// -----------------------------------------------
// AJAX: guardar fila
// -----------------------------------------------
function guardarFila(fila) {

    const id = fila.dataset.id;
    if (!id) return;

    let datos = new URLSearchParams();
    datos.append("id", id);

    fila.querySelectorAll("[data-campo]").forEach(c => {
        datos.append(c.dataset.campo, c.value);
    });

    fetch("ajax/update_tarea.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: datos
    })
        .then(r => r.json())
        .then(j => {
            if (j.success) {
                mostrarIconoOK(fila);
                if (j.mail_info) {
                    alert("📧 " + j.mail_info);
                }
            } else {
                mostrarIconoError(fila);
                console.error("Error al guardar:", j.error);
            }
        })
        .catch(err => {
            mostrarIconoError(fila);
            console.error("Error AJAX:", err);
            alert("Error AJAX: " + err); // <--- DEBUG VISTO POR EL USUARIO
        });
}

// -----------------------------------------------
// Inicialización
// -----------------------------------------------
// Inicialización inmediata (AJAX)
// No usamos DOMContentLoaded porque al cargar vía AJAX el evento ya pasó.

// Preparar filas y asignar eventos
document.querySelectorAll("tr[data-id]").forEach(fila => {

    prepararFila(fila); // añade columna de icono

    fila.querySelectorAll("[data-campo]").forEach(campo => {
        campo.addEventListener("change", () => {
            guardarFila(fila);
        });
    });

});


// =======================================================
// FILTRADO DE TABLA (Estado + Prioridad + texto)
// =======================================================
function aplicarFiltros() {
    const tabla = document.getElementById("tareas");
    if (!tabla) {
        console.error("❌ Tabla tareas no encontrada");
        return;
    }

    // Obtener estados seleccionados
    const checksEstado = document.querySelectorAll(".filtro-estado-check:checked");
    const selectedEstados = Array.from(checksEstado).map(chk => chk.value.toLowerCase());

    // Obtener prioridades seleccionadas
    const checksPrioridad = document.querySelectorAll(".filtro-prioridad-check:checked");
    const selectedPrioridades = Array.from(checksPrioridad).map(chk => chk.value.toLowerCase());

    // Obtener categorías seleccionadas
    const checksCategoria = document.querySelectorAll(".filtro-categoria-check:checked");
    const selectedCategorias = Array.from(checksCategoria).map(chk => chk.value.toLowerCase());

    // Obtener inputs de texto
    const filtrosInput = document.querySelectorAll(".filtro-input");
    const filtrosTexto = {};

    filtrosInput.forEach(input => {
        const col = input.dataset.col;
        const val = input.value.trim().toLowerCase();
        if (val) filtrosTexto[col] = val;
    });

    console.log("🔍 Aplicando filtros - Estados:", selectedEstados, "Prioridades:", selectedPrioridades, "Categorías:", selectedCategorias, "Textos:", filtrosTexto);

    const filas = tabla.querySelectorAll("tr");
    let visibles = 0;

    filas.forEach((fila, idx) => {
        // Saltar fila de título 
        if (idx === 0) return;

        // Saltar fila de encabezado
        if (fila.classList.contains("subtit")) return;

        // Solo procesar filas de datos (con data-id)
        if (!fila.dataset.id) return;

        const celdas = fila.querySelectorAll("td");
        if (celdas.length < 5) return;

        let mostrar = true;

        // Filtro por Estado (columna 5 o 6 dependiendo de si hay celda estado-ajax)
        if (selectedEstados.length > 0) {
            const selectEstado = fila.querySelector("select[data-campo='estado']");
            const estadoActual = selectEstado ? selectEstado.value.toLowerCase() : "";
            if (!selectedEstados.includes(estadoActual)) {
                mostrar = false;
            }
        }

        // Filtro por Prioridad
        if (selectedPrioridades.length > 0 && mostrar) {
            const selectPrioridad = fila.querySelector("select[data-campo='prioridad']");
            const prioridadActual = selectPrioridad ? selectPrioridad.value.toLowerCase() : "";
            if (!selectedPrioridades.includes(prioridadActual)) {
                mostrar = false;
            }
        }

        // Filtro por Categoría
        if (selectedCategorias.length > 0 && mostrar) {
            const selectCategoria = fila.querySelector("select[data-campo='categoria']");
            const categoriaActual = selectCategoria ? selectCategoria.value.toLowerCase() : "";
            if (!selectedCategorias.includes(categoriaActual)) {
                mostrar = false;
            }
        }

        // Filtros de texto
        for (let col in filtrosTexto) {
            if (!mostrar) break;
            const colIdx = parseInt(col);
            const celda = celdas[colIdx];
            if (!celda) continue;

            let textocelda = celda.textContent.toLowerCase();
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

    const btn = document.getElementById("btn-filtrar");
    const checksEstado = document.querySelectorAll(".filtro-estado-check:checked");
    const checksPrioridad = document.querySelectorAll(".filtro-prioridad-check:checked");
    const checksCategoria = document.querySelectorAll(".filtro-categoria-check:checked");
    const inputs = document.querySelectorAll(".filtro-input");
    let filtrosActivos = checksEstado.length + checksPrioridad.length + checksCategoria.length;

    inputs.forEach(inp => { if (inp.value.trim()) filtrosActivos++; });

    if (filtrosActivos > 0) {
        btn.textContent = `🔍 Filtros (${filtrosActivos})`;
        btn.classList.add("activo");
    } else {
        btn.textContent = "🔍 Filtrar";
        btn.classList.remove("activo");
    }

    guardarFiltrosTareas();
}

function guardarFiltrosTareas() {
    const filtros = {
        estados: [],
        prioridades: [],
        categorias: [],
        inputs: {}
    };

    document.querySelectorAll(".filtro-estado-check:checked").forEach(chk => {
        filtros.estados.push(chk.value);
    });
    document.querySelectorAll(".filtro-prioridad-check:checked").forEach(chk => {
        filtros.prioridades.push(chk.value);
    });
    document.querySelectorAll(".filtro-categoria-check:checked").forEach(chk => {
        filtros.categorias.push(chk.value);
    });
    document.querySelectorAll(".filtro-input").forEach(inp => {
        const col = inp.dataset.col;
        if (inp.value.trim()) {
            filtros.inputs[col] = inp.value.trim();
        }
    });

    localStorage.setItem("tareas_pendientes_filtros", JSON.stringify(filtros));
    console.log("💾 Filtros guardados:", filtros);
}

function restaurarFiltrosTareas() {
    const saved = localStorage.getItem("tareas_pendientes_filtros");
    if (!saved) return;

    try {
        const filtros = JSON.parse(saved);

        if (filtros.estados?.length > 0) {
            document.querySelectorAll(".filtro-estado-check").forEach(chk => {
                chk.checked = filtros.estados.includes(chk.value);
            });
        }
        if (filtros.prioridades?.length > 0) {
            document.querySelectorAll(".filtro-prioridad-check").forEach(chk => {
                chk.checked = filtros.prioridades.includes(chk.value);
            });
        }
        if (filtros.categorias?.length > 0) {
            document.querySelectorAll(".filtro-categoria-check").forEach(chk => {
                chk.checked = filtros.categorias.includes(chk.value);
            });
        }
        if (filtros.inputs) {
            document.querySelectorAll(".filtro-input").forEach(inp => {
                const col = inp.dataset.col;
                if (filtros.inputs[col]) {
                    inp.value = filtros.inputs[col];
                }
            });
        }

        aplicarFiltros();

        const checksActivos = document.querySelectorAll(".filtro-estado-check:checked").length +
            document.querySelectorAll(".filtro-prioridad-check:checked").length +
            document.querySelectorAll(".filtro-categoria-check:checked").length;
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
    document.querySelectorAll(".filtro-estado-check").forEach(chk => chk.checked = false);
    document.querySelectorAll(".filtro-prioridad-check").forEach(chk => chk.checked = false);
    document.querySelectorAll(".filtro-categoria-check").forEach(chk => chk.checked = false);
    document.querySelectorAll(".filtro-input").forEach(inp => inp.value = "");

    aplicarFiltros();

    const btn = document.getElementById("btn-filtrar");
    btn.textContent = "🔍 Filtrar";
    btn.classList.remove("activo");

    localStorage.removeItem("tareas_pendientes_filtros");
    console.log("🧹 Filtros limpiados");
}

// Enlazar eventos de filtrado
(function bindFiltrosTareas() {
    document.querySelectorAll(".filtro-estado-check").forEach(check => {
        check.addEventListener("change", () => {
            aplicarFiltros();
            guardarFiltrosTareas();
        });
    });
    document.querySelectorAll(".filtro-prioridad-check").forEach(check => {
        check.addEventListener("change", () => {
            aplicarFiltros();
            guardarFiltrosTareas();
        });
    });
    document.querySelectorAll(".filtro-categoria-check").forEach(check => {
        check.addEventListener("change", () => {
            aplicarFiltros();
            guardarFiltrosTareas();
        });
    });
    document.querySelectorAll(".filtro-input").forEach(input => {
        input.addEventListener("input", () => {
            aplicarFiltros();
            guardarFiltrosTareas();
        });
    });

    console.log("🔍 Filtros de tareas enlazados");
    restaurarFiltrosTareas();
})();
