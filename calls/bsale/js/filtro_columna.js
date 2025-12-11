// filtro_columna.js
// Módulo TNA: Filtros dinámicos por columna con botón "Borrar filtros" (combinables tipo AND)

function initFiltrosPorColumna(tablaId) {
  const table = document.getElementById(tablaId);
  if (!table) return;

  const header = table.querySelector("thead");
  if (!header) return;

  // Evita duplicar filtros si ya existen
  if (header.querySelector(".filter-row")) return;

  const firstRow = header.rows[0];
  const numCols = firstRow.cells.length;

  const filterRow = document.createElement("tr");
  filterRow.classList.add("filter-row");

  for (let i = 0; i < numCols; i++) {
    const th = document.createElement("th");
    const input = document.createElement("input");
    input.type = "text";
    input.placeholder = "Filtrar...";
    input.dataset.colIndex = i;
    input.style.width = "95%";
    input.style.fontSize = "11px";
    input.addEventListener("keyup", function () {
      applyAllFilters(table);
    });
    th.appendChild(input);
    filterRow.appendChild(th);
  }

  header.appendChild(filterRow);

  // Crear botón borrar filtros debajo de la tabla
  createClearButton(table);
}

function applyAllFilters(table) {
  const filters = Array.from(table.querySelectorAll(".filter-row input"));
  const rows = table.tBodies[0].rows;

  for (let i = 0; i < rows.length; i++) {
    let visible = true;

    for (const inp of filters) {
      const colIndex = parseInt(inp.dataset.colIndex);
      const val = inp.value.trim().toLowerCase();

      if (val !== "") {
        const cell = rows[i].cells[colIndex];
        if (!cell) continue;

        const txt = (cell.textContent || cell.innerText || "").toLowerCase();
        if (!txt.includes(val)) {
          visible = false;
          break;
        }
      }
    }

    rows[i].style.display = visible ? "" : "none";
  }
}

function clearAllFilters(table) {
  const inputs = table.querySelectorAll('.filter-row input');
  inputs.forEach(inp => inp.value = '');
  const rows = table.getElementsByTagName('tr');
  for (let i = 2; i < rows.length; i++) rows[i].style.display = '';
}

function createClearButton(table) {
  if (document.querySelector(".clear-filters-btn")) return; // evita duplicar

  const wrapper = document.createElement("div");
  wrapper.style.textAlign = "right";
  wrapper.style.marginTop = "8px";

  const btn = document.createElement("button");
  btn.textContent = "🧹 Borrar filtros";
  btn.className = "clear-filters-btn";
  btn.style.cssText = `
    background: #c0392b;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 6px 12px;
    cursor: pointer;
    font-weight: bold;
    font-size: 13px;
  `;
  btn.addEventListener("click", () => clearAllFilters(table));

  wrapper.appendChild(btn);
  table.parentNode.appendChild(wrapper);
}
