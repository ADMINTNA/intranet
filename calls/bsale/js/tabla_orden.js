// tabla_orden.js
// Módulo TNA: permite ordenar columnas de una tabla HTML por click

function initOrdenColumnas(tablaId) {
  const tabla = document.getElementById(tablaId);
  if (!tabla) return;

  let sortDir = {}; // guarda el orden de cada columna

  const filas = tabla.tBodies[0].rows;
  const headers = tabla.querySelectorAll("th.sortable");

  headers.forEach((th, index) => {
    th.style.cursor = "pointer";
    th.addEventListener("click", () => {
      const asc = !sortDir[index]; // alternar dirección
      sortDir[index] = asc;

      // Detectar tipo de dato
      const isNumeric = [...filas].some(r => !isNaN(parseFloat(r.cells[index].innerText.replace(/,/g, ''))));

      // Convertir filas en array y ordenar
      const sorted = [...filas].sort((a, b) => {
        let x = a.cells[index].innerText.trim();
        let y = b.cells[index].innerText.trim();

        if (isNumeric) {
          x = parseFloat(x.replace(/,/g, '')) || 0;
          y = parseFloat(y.replace(/,/g, '')) || 0;
        }

        if (x < y) return asc ? -1 : 1;
        if (x > y) return asc ? 1 : -1;
        return 0;
      });

      // Reinsertar filas ordenadas
      sorted.forEach(r => tabla.tBodies[0].appendChild(r));

      // Actualizar ícono visual ▲ ▼
      headers.forEach(h => h.querySelector("span.icon")?.remove());
      const icon = document.createElement("span");
      icon.className = "icon";
      icon.innerHTML = asc ? " ▲" : " ▼";
      th.appendChild(icon);
    });
  });
}
// JavaScript Document