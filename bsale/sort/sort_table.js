// ===== sort_table.js =====
// Módulo de ordenamiento AJAX con overlay animado y compatibilidad en iframes

/******************************
 * FUNCION PRINCIPAL DE ORDENAMIENTO
 ******************************/
function sortTable(columnName) {
  const sort = $("#sort").val() || "asc";
  const tbody = document.querySelector("#empTable tbody") || document.querySelector("#tablaDocs tbody");

  if (!tbody) {
    console.warn("[sortTable] No se encontrÃ³ el tbody.");
    return;
  }

  // ðŸ•“ Mostrar overlay
  showLoadingOverlay(`Ordenando por ${columnName.toUpperCase()} (${sort === "asc" ? "descendente" : "ascendente"})...`);

  // ðŸ‘€ Observa cambios en el DOM (para cerrar el spinner cuando se actualicen filas)
  const observer = new MutationObserver((mutations, obs) => {
    hideLoadingOverlay();
    obs.disconnect();
  });
  observer.observe(tbody, { childList: true, subtree: false });

  // âš™ï¸ Llamada AJAX
  $.ajax({
    url: 'fetch_details.php',
    type: 'post',
    data: { columnName: columnName, sort: sort },
    success: function(response){
      // Reemplaza filas completas
      tbody.innerHTML = response;
      $("#sort").val(sort === "asc" ? "desc" : "asc");
      // Reaplica filtros si existen
      if (typeof reapplyClientFilters === "function") reapplyClientFilters();
    },
    error: function(xhr){
      console.error("âŒ Error AJAX sortTable:", xhr?.responseText);
      hideLoadingOverlay();
    }
  });
}

/******************************
 * OVERLAY GLOBAL (SPINNER)
 ******************************/
function showLoadingOverlay(text = "Cargando...") {
  hideLoadingOverlay(); // limpia previos

  const parentDoc = window.top?.document || document;
  if (parentDoc.getElementById("global-loading-overlay")) return; // evita duplicados

  const overlay = parentDoc.createElement("div");
  overlay.id = "global-loading-overlay";
  overlay.innerHTML = `
    <div style="
      position: fixed;
      top: 0; left: 0;
      width: 100vw; height: 100vh;
      background: rgba(0,0,0,0.45);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 999999;
    ">
      <div style="
        background: white;
        border-radius: 10px;
        padding: 30px 40px;
        box-shadow: 0 0 20px rgba(0,0,0,0.3);
        text-align: center;
        font-family: Arial, sans-serif;
        min-width: 200px;
      ">
        <div class="spinner" style="
          width: 40px;
          height: 40px;
          border: 5px solid #ccc;
          border-top: 5px solid #1F1D3E;
          border-radius: 50%;
          margin: 0 auto 15px;
          animation: spin 1s linear infinite;
        "></div>
        <div style="color:#1F1D3E;font-weight:bold;">${text}</div>
      </div>
    </div>
  `;

  parentDoc.body.appendChild(overlay);

  // Agregar animaciÃ³n si no existe
  if (!parentDoc.getElementById("spin-style")) {
    const style = parentDoc.createElement("style");
    style.id = "spin-style";
    style.textContent = `
      @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
      }
    `;
    parentDoc.head.appendChild(style);
  }
}

function hideLoadingOverlay() {
  const parentDoc = window.top?.document || document;
  const overlay = parentDoc.getElementById("global-loading-overlay");
  if (overlay) {
    overlay.style.transition = "opacity 0.3s ease";
    overlay.style.opacity = "0";
    setTimeout(() => overlay.remove(), 400);
  }
}