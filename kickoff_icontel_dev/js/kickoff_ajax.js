// ==========================================================
// KickOff AJAX – Motor Dinámico de Módulos
// Autor: Mauricio Araneda (mAo)
// Fecha: 2025
// Codificación: UTF-8 sin BOM
// ==========================================================

console.log("🔎 KickOff AJAX — SESIÓN:");
console.log("sg_id  =", typeof sg_id !== "undefined" ? sg_id : "(no definido)");
console.log("sg_name=", typeof sg_name !== "undefined" ? sg_name : "(no definido)");

console.log("kickoff_icontel.js cargado correctamente");

// ----------------------------------------------------------
// Función principal para cargar módulos dentro del contenedor
// ----------------------------------------------------------
function loadModulo(ruta) {

    const cont = document.getElementById("modulo-contenedor");
    if (!cont) {
        console.error("❌ No se encontró el contenedor #modulo-contenedor");
        return;
    }

    // Fade out antes de cargar
    cont.classList.add("fade-out");

    // Evitar caché
    const noCache = `?_=${Date.now()}`;

    fetch(ruta + noCache, {
        method: "GET",
        credentials: "include"
    })
        .then(r => {
            if (!r.ok) throw new Error("Error HTTP " + r.status);
            return r.text();
        })
        .then(html => {

            // 1. Verificar si la respuesta es un JSON de error de sesión
            try {
                // Intentamos parsear por si viene un JSON de error
                if (html.trim().startsWith("{") && html.includes('"redirect"')) {
                    const json = JSON.parse(html);
                    if (json.success === false && json.redirect) {
                        console.warn("⛔ Sesión expirada. Redirigiendo...", json.redirect);
                        window.top.location.href = json.redirect;
                        return;
                    }
                }
            } catch (e) {
                // No es JSON, continuamos normal
            }

            // Limpiar clases previas de ordenamiento
            cont.querySelectorAll("th").forEach(th => {
                th.classList.remove("asc", "desc", "active", "sortable");
            });

            // Insertar HTML
            cont.innerHTML = html;
            cont.classList.remove("fade-out");
            cont.classList.add("fadein");

            console.log("🔹 Módulo cargado:", ruta);

            // Ejecutar scripts inline dentro del módulo cargado
            const scripts = cont.querySelectorAll("script");
            scripts.forEach(oldScript => {
                const newScript = document.createElement("script");

                // Copiar atributos
                Array.from(oldScript.attributes).forEach(attr => {
                    newScript.setAttribute(attr.name, attr.value);
                });

                // Copiar contenido
                newScript.textContent = oldScript.textContent;

                // Reemplazar script
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });

            console.log("✅ Scripts del módulo ejecutados");

            // Activar sort después del render del nuevo módulo
            setTimeout(() => {
                if (typeof activarSortEnTablas === "function") {
                    activarSortEnTablas();
                }
                // Inicializar columnas redimensionables
                if (typeof window.initResizableColumns === "function") {
                    window.initResizableColumns();
                }
            }, 80);

            // Extra fallback opcional
            setTimeout(() => {
                if (typeof activarSortEnTablas === "function") {
                    activarSortEnTablas();
                }
                // Reinicializar columnas redimensionables
                if (typeof window.initResizableColumns === "function") {
                    window.initResizableColumns();
                }
            }, 250);
        })
        .catch(err => {
            cont.innerHTML = `
            <div class="error-modulo">
                ❌ Error cargando el módulo<br>
                <small>${ruta}</small>
            </div>`;
            console.error("❌ Error AJAX:", err);
        });
}

// ----------------------------------------------------------
// Ocultar animación inicial cuando carga la página
// ----------------------------------------------------------
document.addEventListener("DOMContentLoaded", () => {
    const cargando = document.querySelector(".cargando");
    if (cargando) cargando.classList.add("ocultar");
});

// ----------------------------------------------------------
// Establece el botón activo del menú estilo macOS
// ----------------------------------------------------------
function selectMenu(btn) {
    document.querySelectorAll('#menu-ajax .toolbar-btn')
        .forEach(b => b.classList.remove('active'));

    btn.classList.add('active');
}

function activarSortEnTablas() {
    if (typeof initLocalSort === "function") {
        initLocalSort();   // cm_sort.js → activa sort
    }
}
