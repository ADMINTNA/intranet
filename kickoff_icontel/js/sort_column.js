// ==========================================================
// /kickoff/js/sort_columna.js
// Autor: Mauricio Araneda
// Fecha: 2025-11-05
// ==========================================================
// ======================================================
// ORDENAR TABLAS POR COLUMNA (Kickoff - soporte total para <select>)
// ======================================================
function inicializarOrdenamiento(context = document) {
  const tables = context.querySelectorAll("table");

  tables.forEach((table) => {
    if (table.closest("#header") || table.classList.contains("no-sort")) return;

    const headerRow = table.querySelectorAll("tr")[1];
    if (!headerRow) return;
    const headerCells = headerRow.querySelectorAll("th, td");
    if (headerCells.length === 0) return;

    const toNumber = (s) => {
      if (!s) return NaN;
      const norm = s.replace(/\s/g, "").replace(/\./g, "").replace(/[%$CLP]/gi, "").replace(",", ".");
      return parseFloat(norm);
    };

    const toDateKey = (s) => {
      if (!s) return null;
      const m = s.match(/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})$/);
      return m ? parseInt(`${m[3]}${m[2]}${m[1]}`, 10) : null;
    };

    const compare = (a, b) => {
      const dA = toDateKey(a);
      const dB = toDateKey(b);
      if (dA && dB) return dA - dB;

      const nA = toNumber(a);
      const nB = toNumber(b);
      if (!isNaN(nA) && !isNaN(nB)) return nA - nB;

      return a.localeCompare(b);
    };

    const sortState = new Map();

    headerCells.forEach((cell, colIndex) => {
      cell.classList.add("sortable");
      cell.style.cursor = "pointer";
      cell.title = "Ordenar por esta columna";

      cell.addEventListener("click", () => {
        const asc = sortState.get(colIndex) !== "asc";
        sortState.set(colIndex, asc ? "asc" : "desc");

        headerCells.forEach((c) => c.classList.remove("asc", "active"));
        cell.classList.add("active");
        if (asc) cell.classList.add("asc");

        const tbody = table.tBodies[0];
        if (!tbody) return;
        const rows = Array.from(tbody.querySelectorAll("tr"));

        const getCellValue = (tr) => {
          const td = tr.children[colIndex];
          if (!td) return "";
          const sel = td.querySelector("select.estado-sweet, select");
          if (sel) {
            const opt = sel.selectedOptions[0];
            return (opt?.textContent || sel.value || "").trim().toLowerCase();
          }
          return (td.innerText || "").trim().toLowerCase();
        };

        rows.sort((a, b) => {
          const valA = getCellValue(a);
          const valB = getCellValue(b);
          const result = compare(valA, valB);
          return asc ? result : -result;
        });

        rows.forEach((r) => tbody.appendChild(r));
        console.log(`✅ Ordenado por columna ${colIndex}: ${asc ? "ASC" : "DESC"}`);
      });
    });
  });
}

window.addEventListener("load", () => {
  setTimeout(() => inicializarOrdenamiento(), 500);
});