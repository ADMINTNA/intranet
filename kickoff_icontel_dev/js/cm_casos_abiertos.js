// ===========================================================
// /intranet/kickoff_icontel/js/cm_casos_abiertos.js
// JavaScript para casos abiertos editables
// Autor: mAo
// Codificación: UTF-8 sin BOM
// ===========================================================

document.addEventListener("DOMContentLoaded", () => {
    // Lista de tablas que soportan edición
    const tablasIds = ["casos_abiertos", "casos_sujeto_a_cobro"];

    tablasIds.forEach(id => {
        const tabla = document.getElementById(id);
        if (tabla) {
            setupTablaEditable(tabla);
        }
    });

    console.log("cm_casos_abiertos.js cargado correctamente para tablas:", tablasIds);
});

function setupTablaEditable(tabla) {
    // Escuchar cambios en todos los selects e inputs
    tabla.addEventListener("change", (e) => {
        const elem = e.target;

        // Solo procesar si tiene data-campo
        if (!elem.dataset.campo) return;

        const tr = elem.closest("tr");
        const id = tr.dataset.id;
        const campo = elem.dataset.campo;
        const valor = elem.value;

        if (!id) {
            console.error("No se encontró ID del caso");
            return;
        }

        // Guardar cambio vía AJAX
        guardarCambio(id, campo, valor, elem);
    });
}

// -----------------------------------------------------------
// Guardar cambio en la base de datos
// -----------------------------------------------------------
function guardarCambio(id, campo, valor, elemento) {

    // Mostrar feedback visual
    elemento.style.background = "#ffffcc";

    fetch("cm_casos_abiertos_save.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        credentials: "include",
        body: `id=${encodeURIComponent(id)}&campo=${encodeURIComponent(campo)}&valor=${encodeURIComponent(valor)}`
    })
        .then(response => {
            if (response.redirected) {
                console.warn("⚠️ Redirección detectada:", response.url);
                window.top.location.href = response.url;
                return;
            }
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error("❌ Respuesta no es JSON:", text);
                    throw new Error("La respuesta del servidor no es un JSON válido");
                }
            });
        })
        .then(data => {
            if (!data) return; // Si hubo redirección

            if (data.success) {
                // Éxito - fondo verde temporal
                elemento.style.background = "#d4edda";
                setTimeout(() => {
                    elemento.style.background = "transparent";
                }, 1500);
            } else {
                // Error - fondo rojo
                elemento.style.background = "#f8d7da";
                console.error("❌ Error al guardar:", data.error);
                alert("Error al guardar: " + (data.error || "Desconocido"));
            }
        })
        .catch(error => {
            elemento.style.background = "#f8d7da";
            console.error("❌ Error AJAX:", error);
            alert("Error de conexión: " + error.message);
        });
}
