// ==========================================================
// Cobranza Comercial – Eventos Estado y Comentario
// /kickoff_icontel/js/cm_cobranza_comercial.js
// Autor: Mauricio Araneda (mAo)
// Fecha: 2025-12
// Versión: Dinámico + UTF-8 + Sin Bordes
// Codificación: UTF-8 sin BOM
// ==========================================================

console.log("cm_cobranza_comercial.js v10 loaded");

// ==========================================================
// 🎨 COLORES POR ESTADO SWEET
// ==========================================================
const coloresEstado = {
    "Al día":          "#5cb85c",
    "Contacto":        "#0275d8",
    "Convenio":        "#7952b3",
    "Judicial":        "#d9534f",
    "Moroso":          "#f0ad4e",
    "Sin información": "#6c757d"
};

function aplicarColorEstado(selectEl) {
    const estado = selectEl.value.trim();
    const color = coloresEstado[estado] || "#ffffff";

    selectEl.style.backgroundColor = color;
    selectEl.style.color = (color === "#ffffff") ? "#000" : "#fff";
}


// ==========================================================
// 🟩 EVENTOS PARA ESTADO SWEET (GUARDA + COLOR)
// ==========================================================
function bindEventosEstado() {

    const cells = document.querySelectorAll("td.estado-sweet-cell");

    cells.forEach(cell => {
        const select = cell.querySelector("select");
        if (!select) return;

        // Aplicar color inicial
        aplicarColorEstado(select);

        // Evitar doble binding
        if (select.dataset.bound) return;
        select.dataset.bound = "true";

        select.addEventListener("change", function () {

            aplicarColorEstado(this);

            const rut = cell.dataset.rut;
            const valor = this.value;
            const icono = cell.querySelector(".estado-icono");

            fetch("update_estado_sweet.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `rut=${encodeURIComponent(rut)}&estado=${encodeURIComponent(valor)}`
            })
            .then(r => r.json())
            .then(d => {

                if (d.success) {
                    if (icono) { icono.textContent = "✔"; icono.style.color = "limegreen"; }
                    console.log("✔ Estado actualizado:", rut, valor);
                } else {
                    if (icono) { icono.textContent = "❌"; icono.style.color = "red"; }
                    console.warn("❌ Error actualizando estado:", d.msg);
                }

            })
            .catch(err => {
                if (icono) { icono.textContent = "❌"; icono.style.color = "red"; }
                console.error("❌ Error AJAX actualizando estado:", err);
            });

        });
    });
}


// ==========================================================
// 🟧 EVENTOS PARA COMENTARIO (GUARDA SIEMPRE)
// ==========================================================
function bindEventosComentario() {

    const cells = document.querySelectorAll("td.comentario-cell");

    cells.forEach(cell => {
        const input = cell.querySelector("input");
        if (!input) return;

        if (input.dataset.bound) return;
        input.dataset.bound = "true";

        input.addEventListener("change", function () {

            const rut = cell.dataset.rut;
            const valor = this.value;
            const icono = cell.querySelector(".comentario-icono");

            fetch("update_comentario_sweet.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `rut=${encodeURIComponent(rut)}&comentario=${encodeURIComponent(valor)}`
            })
            .then(r => r.json())
            .then(d => {

                if (d.success) {
                    if (icono) { icono.textContent = "✔"; icono.style.color = "limegreen"; }
                    console.log("✔ Comentario actualizado:", rut);
                } else {
                    if (icono) { icono.textContent = "❌"; icono.style.color = "red"; }
                    console.warn("❌ Error actualizando comentario:", d.msg);
                }

            })
            .catch(err => {
                if (icono) { icono.textContent = "❌"; icono.style.color = "red"; }
                console.error("❌ Error AJAX actualizando comentario:", err);
            });

        });
    });
}


// ==========================================================
// 🟦 ORDENAMIENTO GENERAL (SELECT + INPUT + TEXTO + NÚMEROS)
// ==========================================================
document.addEventListener("DOMContentLoaded", function () {

    const table = document.getElementById("cobranza");
    if (!table) return;

    const headers = table.querySelectorAll("th.sortable");

    headers.forEach(th => {

        th.addEventListener("click", function () {

            const colType = th.dataset.col;

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

            const isAsc = !th.classList.contains("asc");

            headers.forEach(h => {
                h.classList.remove("asc", "desc");
                const ind = h.querySelector(".order-indicator");
                if (ind) ind.textContent = "";
            });

            th.classList.toggle("asc", isAsc);
            th.classList.toggle("desc", !isAsc);

            const indicator = th.querySelector(".order-indicator");
            if (indicator) indicator.textContent = isAsc ? "↑" : "↓";

            const tbody = table.querySelector("tbody") || table;
            const rows = Array.from(tbody.querySelectorAll("tr:not(.subtitulo)"));

            rows.sort((rowA, rowB) => {
                return compareValues(
                    getCellValue(rowA, colIndex, colType),
                    getCellValue(rowB, colIndex, colType),
                    isAsc
                );
            });

            rows.forEach(row => tbody.appendChild(row));

            console.log(`Ordenado por ${colType} (${isAsc ? "ASC" : "DESC"})`);
        });
    });

    // Enlazar eventos principales
    bindEventosEstado();
    bindEventosComentario();
});


// ==========================================================
// 📌 Funciones auxiliares de ordenamiento
// ==========================================================
function getCellValue(row, index, type) {
    const cell = row.cells[index];
    if (!cell) return "";

    switch (type) {
        case "estado":
            const sel = cell.querySelector("select");
            return sel ? sel.value.toLowerCase() : "";

        case "comentario":
            const inp = cell.querySelector("input");
            return inp ? inp.value.toLowerCase() : "";

        case "monto":
        case "docs":
        case "dias_venc":
        case "dias":
            let text = cell.innerText.trim();
            text = text.replace(/[$\s]/g, "").replace(/\./g, "").replace(/[^0-9-]/g, "");
            return text === "" ? 0 : parseInt(text);

        default:
            return cell.innerText.trim().toLowerCase();
    }
}

function compareValues(a, b, isAsc) {

    if (typeof a === "number" && typeof b === "number")
        return isAsc ? a - b : b - a;

    if (a === "" && b === "") return 0;
    if (a === "") return isAsc ? 1 : -1;
    if (b === "") return isAsc ? -1 : 1;

    return isAsc
        ? a.localeCompare(b, "es", { numeric: true })
        : b.localeCompare(a, "es", { numeric: true });
}


// ==========================================================
// 🔄 BOTÓN RELOAD (PROCESO AUTOMÁTICO)
// ==========================================================
document.addEventListener("DOMContentLoaded", function () {

    const btnReload = document.getElementById("reloadModulo");
    if (!btnReload) return;

    btnReload.addEventListener("click", function () {

        btnReload.classList.remove("reload-off");
        btnReload.classList.add("reload-on");

        fetch("ajax/procesar_cobranza_comercial.php")
            .then(r => r.json())
            .then(d => {

                if (!d.ok) {
                    alert("❌ Error: " + d.msg);
                    btnReload.classList.add("reload-error");
                    return;
                }

                alert(
                    "✔ Proceso completado\n\n" +
                    "Actualizadas: " + d.procesadas + "\n" +
                    "Omitidas: " + d.omitidas
                );

                location.reload();
            })
            .catch(err => {
                alert("❌ Error AJAX ejecutando proceso automático");
                console.error(err);
            });
    });
});
