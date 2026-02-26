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
// Carga asíncrona de badges del menú
// Se llama desde icontel.php al cargar la página.
// Los badges se obtienen de ajax/badges.php (con caché 3min).
// ----------------------------------------------------------
function loadBadges(force) {
    // Construir URL absoluta relativa al script de kickoff
    const base = (function() {
        const scripts = document.querySelectorAll('script[src]');
        for (const s of scripts) {
            if (s.src && s.src.includes('kickoff_ajax.js')) {
                return s.src.replace(/js\/kickoff_ajax\.js.*$/, '');
            }
        }
        return window.location.href.replace(/[^/]*$/, '');
    })();

    const url = base + 'ajax/badges.php' + (force ? '?force=1' : '');
    console.log('🔖 Cargando badges desde:', url);

    fetch(url, { credentials: 'include' })
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(data => {
            if (!data.success || !data.badges) {
                console.warn('⚠️ Respuesta inesperada de badges:', data);
                return;
            }
            const b = data.badges;
            let painted = 0;
            Object.entries(b).forEach(([key, val]) => {
                const el = document.getElementById('badge-' + key);
                if (!el) return;
                if (val > 0) {
                    el.textContent = val;
                    el.style.display = 'inline-block';
                    el.style.animation = 'none';
                    el.offsetHeight;
                    el.style.animation = 'badgePop .25s ease';
                    painted++;
                } else {
                    el.style.display = 'none';
                }
            });
            console.log('✅ Badges pintados: ' + painted + (data.cached ? ' (caché)' : ' (frescos)'));
        })
        .catch(err => console.warn('⚠️ Error cargando badges:', err));
}

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

            // Ejecutar scripts inline y externos dentro del módulo cargado
            const scripts = cont.querySelectorAll("script");
            scripts.forEach(oldScript => {
                const newScript = document.createElement("script");

                // Copiar atributos
                Array.from(oldScript.attributes).forEach(attr => {
                    newScript.setAttribute(attr.name, attr.value);
                });

                // Copiar contenido
                newScript.textContent = oldScript.textContent;

                // Si tiene src, forzar recarga eliminando el script previo si existe
                if (newScript.src) {
                    const oldOne = document.querySelector(`script[src="${newScript.src}"]`);
                    if (oldOne) oldOne.remove();
                    document.head.appendChild(newScript);
                } else {
                    // Reemplazar script inline en su lugar original
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                }
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