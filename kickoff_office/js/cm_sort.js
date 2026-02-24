// ===========================================================
// /intranet/kickoff/js/cm_sort.js
// Ordenamiento de columnas para cm_tareas_pendientes.php
// Autor: mAo + ChatGPT
// Codificación: UTF-8 sin BOM
// ===========================================================
function initLocalSort() {

    document.querySelectorAll("table th").forEach(th => {

        // Evitar re-vincular múltiples veces
        th.style.cursor = "pointer";
        th.onclick = function () {

            const table = th.closest("table");
            const index = Array.from(th.parentNode.children).indexOf(th);
            const asc = !th.classList.contains("asc");

            // Quitar estilos anteriores
            table.querySelectorAll("th").forEach(h => h.classList.remove("asc"));

            // Aplicar estilo nuevo
            if (asc) th.classList.add("asc");

            // Ordenar filas
            const rows = Array.from(table.querySelectorAll("tbody tr"))
                .sort((r1, r2) => {
                    const c1 = obtenerValorCelda(r1.children[index]);
                    const c2 = obtenerValorCelda(r2.children[index]);

                    const val1 = parseComparableValue(c1);
                    const val2 = parseComparableValue(c2);

                    if (val1 < val2) return asc ? -1 : 1;
                    if (val1 > val2) return asc ? 1 : -1;
                    return 0;
                });

            // Reinsertar en tbody
            const tbody = table.querySelector("tbody") || table;
            rows.forEach(r => tbody.appendChild(r));
        };
    });
}

document.addEventListener("DOMContentLoaded", () => {
    // Buscar cualquiera de las tablas soportadas
    const tabla = document.getElementById("tareas") ||
        document.getElementById("tareas_delegadas") ||
        document.getElementById("casos_abiertos");

    if (!tabla) {
        console.warn("cm_sort.js: ninguna tabla soportada encontrada");
        return;
    }

    // La segunda fila (después del título) es la fila de <th>
    const headerRow = tabla.querySelectorAll("tr")[1];
    if (!headerRow) {
        console.warn("cm_sort.js: fila de encabezados no encontrada");
        return;
    }

    const headers = headerRow.querySelectorAll("th");
    headers.forEach((th, colIndex) => {
        th.style.cursor = "pointer";
        th.addEventListener("click", () => ordenarColumna(tabla, colIndex, th));
    });

    console.log("cm_sort.js: inicializado correctamente para tabla:", tabla.id);
});

// -----------------------------------------------------------
// Obtiene valor comparable desde una celda
// -----------------------------------------------------------
function obtenerValorCelda(td) {
    if (!td) return "";

    // SELECT → texto visible
    const sel = td.querySelector("select");
    if (sel) {
        const txt = sel.options[sel.selectedIndex]?.text || "";
        return txt.trim();
    }

    // INPUT TEXT
    const inpText = td.querySelector("input[type='text']");
    if (inpText) {
        return inpText.value.trim();
    }

    // INPUT DATE
    const inpDate = td.querySelector("input[type='date']");
    if (inpDate) {
        // formato YYYY-MM-DD → ya es ordenable como string
        return inpDate.value || "";
    }

    // LINK
    const link = td.querySelector("a");
    if (link) return link.textContent.trim();

    // TEXTO PLANO
    return td.textContent.trim();
}

// -----------------------------------------------------------
// Parsea valores para comparación (fechas, números, texto)
// -----------------------------------------------------------
function parseComparableValue(val) {
    if (!val) return "";

    const v = val.toLowerCase().trim();

    // 1. Fechas DD-MM-YYYY o DD/MM/YYYY
    // Regex para DD/MM/YYYY o DD-MM-YYYY (con horas opcionales que ignoramos para sort simple, o incluimos si necesario)
    // Ejemplo: 05-11-2025 o 05/11/2025
    if (/^\d{1,2}[\/-]\d{1,2}[\/-]\d{4}/.test(v)) {
        // Intentar separar
        // Asumimos formato chileno/europeo: DIA-MES-AÑO
        let parts = v.split(/[\/\-]/);
        if (parts.length >= 3) {
            let d = parseInt(parts[0], 10);
            let m = parseInt(parts[1], 10) - 1; // Meses en JS son 0-11
            let y = parseInt(parts[2].substring(0, 4), 10); // Tomar solo los primeros 4 dígitos del año si viene con hora

            // Validar fecha válida
            let dateObj = new Date(y, m, d);
            if (dateObj.getFullYear() === y && dateObj.getMonth() === m && dateObj.getDate() === d) {
                return dateObj.getTime();
            }
        }
    }

    // 2. YYYY-MM-DD
    if (/^\d{4}-\d{2}-\d{2}/.test(v)) {
        return new Date(v).getTime();
    }

    // 3. Números (moneda, etc? Cuidado con puntos de miles)
    // Por ahora, si es simple numérico
    // Eliminar puntos de miles si es formato chileno "1.000" -> "1000", pero cuidado con decimales.
    // Si asumimos punto = miles y coma = decimal (CLP/UF), habría que limpiar. 
    // Pero si el usuario dice "fechas", enfoquémonos en fechas primero.

    // Si se quiere ordenar números simples:
    // if (!isNaN(v) && v !== "") return parseFloat(v);

    return v;
}

// -----------------------------------------------------------
// Ordena filas por columna
// -----------------------------------------------------------
function ordenarColumna(tabla, colIndex, th) {

    // Dirección actual
    const actual = th.dataset.orden === "asc" ? "asc" : "desc";
    const nuevaDireccion = actual === "asc" ? "desc" : "asc";

    // Limpiar estado de otros th
    const allTh = tabla.querySelectorAll("tr:nth-child(2) th");
    allTh.forEach(h => h.dataset.orden = "");

    // Guardar estado en el th clickeado
    th.dataset.orden = nuevaDireccion;

    // Filas de datos - intentar con data-id primero, sino todas las filas después de headers
    let filas = Array.from(tabla.querySelectorAll("tr[data-id]"));

    if (filas.length === 0) {
        // Si no hay data-id, tomar todas las filas excepto las primeras 2 (título + headers)
        const todasFilas = Array.from(tabla.querySelectorAll("tr"));
        filas = todasFilas.slice(2); // Saltar título y headers
    }

    if (filas.length === 0) return;

    filas.sort((a, b) => {
        const rawA = obtenerValorCelda(a.children[colIndex]);
        const rawB = obtenerValorCelda(b.children[colIndex]);

        const A = parseComparableValue(rawA);
        const B = parseComparableValue(rawB);

        if (A < B) return (nuevaDireccion === "asc") ? -1 : 1;
        if (A > B) return (nuevaDireccion === "asc") ? 1 : -1;
        return 0;
    });

    const tbody = tabla.tBodies[0] || tabla;

    // Reinsertar filas en el nuevo orden
    filas.forEach(f => tbody.appendChild(f));
}