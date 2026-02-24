// ===========================================================
// /intranet/kickoff_icontel/js/cm_casos_abiertos.js
// JavaScript para casos abiertos editables con feedback MacOS (✅ ❌)
// Autor: mAo
// Codificación: UTF-8 sin BOM
// ===========================================================

console.log("🚀 cm_casos_abiertos.js v8 cargando (Email Notify)...");

// -----------------------------------------------------------
// 1. Funciones de Feedback Visual
// -----------------------------------------------------------

function mostrarIconoOK(fila) {
    const celda = fila.querySelector(".estado-ajax");
    if (!celda) {
        console.error("❌ ERROR: No se encontró la celda .estado-ajax");
        return;
    }

    const originalText = celda.textContent.trim();
    // No queremos reemplazar si ya hay un emoji activo
    if (originalText === "\u2705" || originalText === "\u274C") return;

    console.log(`✨ Ejecutando feedback ✅ en fila: ${fila.dataset.id} (Valor actual: ${originalText})`);

    // Guardar número original en un atributo temporal por si acaso
    celda.dataset.oldNum = originalText;

    // Feedback visual
    celda.textContent = "\u2705"; // ✅
    celda.style.color = "limegreen";
    celda.style.fontSize = "18px";
    celda.style.fontWeight = "bold";

    fila.style.backgroundColor = "#d4edda";

    // Restaurar después de 2.5 seg
    setTimeout(() => {
        fila.style.backgroundColor = "";
        const restoredNum = celda.dataset.oldNum || originalText;
        console.log(`🔄 Restaurando número: ${restoredNum}`);
        celda.textContent = restoredNum;
        celda.style.color = "";
        celda.style.fontSize = "";
        celda.style.fontWeight = "";
    }, 2500);
}

function mostrarIconoError(fila, errorMsg) {
    const celda = fila.querySelector(".estado-ajax");
    if (!celda) return;

    const originalText = celda.textContent.trim();
    celda.dataset.oldNum = originalText;

    celda.textContent = "\u274C"; // ❌
    celda.style.color = "red";
    celda.style.fontSize = "18px";
    celda.title = errorMsg || "Error al guardar";

    fila.style.backgroundColor = "#f8d7da";
    setTimeout(() => {
        fila.style.backgroundColor = "";
        celda.textContent = celda.dataset.oldNum || originalText;
        celda.style.color = "";
        celda.style.fontSize = "";
    }, 4000);
}

// -----------------------------------------------------------
// 2. Lógica de Guardado
// -----------------------------------------------------------

function guardarCambioCaso(id, campo, valor, fila, elemento) {
    console.log(`💾 Intentando guardar caso ${id}: ${campo} = "${valor}"`);

    elemento.style.backgroundColor = "#ffffcc"; // Amarillo aviso

    const params = new URLSearchParams();
    params.append("id", id);
    params.append("campo", campo);
    params.append("valor", valor);

    fetch("cm_casos_abiertos_save.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        credentials: "include",
        body: params
    })
        .then(response => {
            if (response.redirected) {
                window.top.location.href = response.url;
                return;
            }
            return response.json();
        })
        .then(data => {
            elemento.style.backgroundColor = "";
            if (!data) return;

            if (data.success) {
                console.log("✅ Servidor respondió éxito.");
                mostrarIconoOK(fila);

                // Si el servidor indica que se envió un mail, avisar al usuario
                if (data.mail) {
                    console.log("📧 Notificación de mail: " + data.mail);
                    // Usar un ligero delay para no interrumpir la animación del ✅
                    setTimeout(() => {
                        alert("📧 " + data.mail);
                    }, 500);
                }
            } else {
                console.error("❌ Error devuelto por el servidor:", data.error);
                mostrarIconoError(fila, data.error);
                alert("Error al guardar: " + (data.error || "Desconocido"));
            }
        })
        .catch(error => {
            elemento.style.backgroundColor = "";
            console.error("❌ Error en la llamada FETCH:", error);
            mostrarIconoError(fila, "Error de red");
        });
}

// -----------------------------------------------------------
// 3. Inicialización
// -----------------------------------------------------------

function initCasosEditables() {
    const tablas = document.querySelectorAll("#casos_abiertos, #casos_sujeto_a_cobro, #casos_congelados");

    if (tablas.length === 0) {
        setTimeout(initCasosEditables, 200);
        return;
    }

    tablas.forEach(tabla => {
        const filas = tabla.querySelectorAll("tr[data-id]");
        filas.forEach(fila => {
            // Aseguramos que la primera celda sea identificable
            if (fila.cells[0] && !fila.cells[0].classList.contains("estado-ajax")) {
                fila.cells[0].classList.add("estado-ajax");
            }

            fila.querySelectorAll("[data-campo]").forEach(campo => {
                if (campo.dataset.listenerOk) return;

                campo.addEventListener("change", () => {
                    const row = campo.closest("tr");
                    const id = row.dataset.id;
                    const field = campo.dataset.campo;
                    const value = campo.value;
                    guardarCambioCaso(id, field, value, row, campo);
                });

                campo.dataset.listenerOk = "true";
            });
        });
    });

    console.log("✅ initCasosEditables v7 (Final) completada.");
}

initCasosEditables();
