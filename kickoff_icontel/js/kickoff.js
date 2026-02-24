// ==========================================================
// 🧩 KICKOFF – Control de Capas, Reloj y Ordenamiento
// Autor: mAo / TNA Group
// ==========================================================

var ptr = 0;
var timeout = false;

// ==========================================================
// 🔒 AUTO REFRESH CONTROL (desactivado por defecto)
// ==========================================================
var refreshId = setInterval('window.location.reload()', 60000);
clearInterval(refreshId);

// ==========================================================
// 🚀 Auto submit para select
// ==========================================================
function autoSubmit() {
  var formObject = document.forms['form_select'];
  if (formObject) formObject.submit();
}

// ==========================================================
// 🎚️ Control de capas (visibilidad con persistencia)
// ==========================================================
function capa(capaId) {
  const el = document.getElementById(capaId);
  if (!el) {
    console.log("Ã¢Å¡Â Ã¯Â¸Â No se encontrÃƒÂ³ la capa:", capaId);
    return;
  }

  const currentDisplay = window.getComputedStyle(el).display;
  if (currentDisplay === "none") {
    el.style.display = "block";
    localStorage.setItem("capa_" + capaId, "visible");
    console.log("Ã°Å¸Å¸Â¢ Capa '" + capaId + "' Ã¢â€ â€™ visible");
  } else {
    el.style.display = "none";
    localStorage.setItem("capa_" + capaId, "oculto");
    console.log("Ã°Å¸â€Â´ Capa '" + capaId + "' Ã¢â€ â€™ oculta");
  }
}

function capa_new(nombre) {
  const capas = ['capa_casos', 'capa_iconos', 'capa_buscadores'];
  capas.forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;

    if (id === nombre) {
      el.hidden = false;
      el.style.display = 'block';
      // Reinicializa sort al mostrar la capa
      const tablas = el.querySelectorAll('table');
      tablas.forEach(tbl => {
        if (typeof inicializarOrdenamiento === 'function') {
          inicializarOrdenamiento(tbl);
        }
      });
    } else {
      el.hidden = true;
      el.style.display = 'none';
    }
  });
}

// Oculta capas principales al iniciar
function ocultarCapasPrincipales() {
  const capas = ['capa_casos', 'capa_iconos', 'capa_buscadores'];
  capas.forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.hidden = true;
      el.style.display = 'none';
      console.log(`Ã°Å¸â€â€™ ${id} iniciada oculta`);
    }
  });
}

function carga_capas() {
  capa('capa_casos');
  capa('capa_iconos');
  capa('capa_buscadores');
}

// Restaura visibilidad de todas las capas internas
function restaurarTodasLasCapas() {
  const capas = [
    'casos_abiertos', 'casos_congelados', 'casos_abiertos_debaja',
    'casos_en_seguimiento', 'casos_sujeto_a_cobro', 'casos_debaja',
    'clientes_potenciales', 'cobranza', 'oportunidades',
    'oportunidades_archivadas', 'demo', 'Ordenes_de_Compra',
    'tareas', 'cotizaciones'
  ];

  capas.forEach(function (capaId) {
    const estado = localStorage.getItem("capa_" + capaId);
    const el = document.getElementById(capaId);

    if (el) {
      if (estado === "oculto") {
        el.style.display = "none";
        console.log("Ã°Å¸â€Â¸ Restaurando '" + capaId + "' Ã¢â€ â€™ oculto");
      } else {
        el.style.display = "block";
        console.log("Ã¢Å“â€¦ Restaurando '" + capaId + "' Ã¢â€ â€™ visible");
        void el.offsetWidth;
      }
    } else {
      console.log("Ã¢Å¡Â Ã¯Â¸Â No se encontrÃƒÂ³ el elemento con id:", capaId);
    }
  });
}

// Mostrar todas las capas internas
function mostrarTodasLasCapas() {
  const capas = [
    'casos_abiertos', 'casos_congelados', 'casos_abiertos_debaja',
    'casos_en_seguimiento', 'casos_sujeto_a_cobro', 'casos_debaja',
    'clientes_potenciales', 'cobranza', 'oportunidades',
    'oportunidades_archivadas', 'demo', 'Ordenes_de_Compra',
    'tareas', 'cotizaciones'
  ];

  capas.forEach(function (capaId) {
    const el = document.getElementById(capaId);
    if (el) {
      el.style.display = "block";
      el.offsetWidth; // reflow
      localStorage.setItem("capa_" + capaId, "visible");
      console.log("Ã°Å¸Å¸Â© Capa '" + capaId + "' ahora visible");
    } else {
      console.log("Ã¢Å¡Â Ã¯Â¸Â No se encontrÃƒÂ³ la capa:", capaId);
    }
  });
}

// ==========================================================
// 🕑 Reloj seguro (evita error si el header aún no está cargado)
// ==========================================================
function mueveReloj() {
  const reloj = document.getElementById("reloj");

  if (!reloj) {
    setTimeout(mueveReloj, 1000);
    return;
  }

  const momentoActual = new Date();
  const hora = momentoActual.getHours().toString().padStart(2, "0");
  const minuto = momentoActual.getMinutes().toString().padStart(2, "0");
  const segundo = momentoActual.getSeconds().toString().padStart(2, "0");

  const fecha = momentoActual.toLocaleDateString("es-CL", {
    day: "2-digit", month: "2-digit", year: "numeric"
  });

  const horaImprimible = `⏰ Hora: ${hora}:${minuto}:${segundo} — ${fecha}`;
  reloj.innerHTML = horaImprimible;

  setTimeout(mueveReloj, 1000);
}

// 🌐 Navegación por selects del header

// ==========================================================
// Ã°Å¸Å’Â NavegaciÃƒÂ³n por selects del header
// ==========================================================
function abrirNuevaVentana(url) {
  if (url) {
    window.open(url, '_blank');
  } else {
    alert('Por favor, proporciona una URL vÃƒÂ¡lida.');
  }
}

function manejarCambio(selectElement) {
  const urlSeleccionada = selectElement.value;
  if (urlSeleccionada !== "") {
    abrirNuevaVentana(urlSeleccionada);
  }
}

// ==========================================================
// 📊 Ordenamiento de tablas dinámico
// ==========================================================
function inicializarOrdenamiento(context = document) {
  const tables = context.querySelectorAll("table");
  tables.forEach((table) => {
    if (table.closest("#header") || table.classList.contains("no-sort")) return;

    const allRows = Array.from(table.querySelectorAll("tr"));
    const headerRow = allRows.find(r => r.querySelector("th"));
    if (!headerRow) return;

    const headerCells = Array.from(headerRow.querySelectorAll("th,td"));
    if (!headerCells.length) return;

    headerCells.forEach(cell => {
      cell.classList.add("sortable");
      cell.style.cursor = "pointer";
      cell.title = "Ordenar por esta columna";
    });

    const toNumber = (s) => {
      if (!s) return null;
      const n = parseFloat(s.replace(/[^\d.-]/g, '').replace(",", "."));
      return isNaN(n) ? null : n;
    };

    const parseTime = (timePart) => {
      if (!timePart) return "000000";
      const tm = timePart.match(/^(\d{1,2}):(\d{1,2})(?::(\d{1,2}))?/);
      if (!tm) return "000000";
      return tm.slice(1).map(v => v ? v.padStart(2, "0") : "00").join("");
    };

    const toDateKey = (s) => {
      if (!s) return null;
      let [datePart, timePart] = s.trim().split(/\s+/);
      if (!datePart) return null;
      let m = datePart.match(/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})$/);
      if (m) {
        let [_, d, mo, y] = m;
        if (y.length === 2) y = (parseInt(y) < 50 ? "20" : "19") + y;
        return parseInt(`${y}${mo.padStart(2, "0")}${d.padStart(2, "0")}${parseTime(timePart)}`, 10);
      }
      m = datePart.match(/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/);
      if (m) {
        let [_, y, mo, d] = m;
        return parseInt(`${y}${mo.padStart(2, "0")}${d.padStart(2, "0")}${parseTime(timePart)}`, 10);
      }
      return null;
    };

    const isDateHeader = (cell) => {
      const txt = (cell.innerText || "").toLowerCase();
      return txt.includes("fecha") || txt.includes("modificada") || txt.includes("creada");
    };

    const sortState = new Map();

    if (!table.dataset.sortBound) {
      table.addEventListener("click", (ev) => {
        const cell = ev.target.closest(".sortable");
        if (!cell) return;
        const cells = Array.from(headerRow.children);
        const colIndex = cells.indexOf(cell);
        if (colIndex < 0) return;

        const currentDir = sortState.get(colIndex) || "desc";
        const asc = currentDir === "desc";
        sortState.set(colIndex, asc ? "asc" : "desc");

        cells.forEach(c => c.classList.remove("asc", "active"));
        cell.classList.add("active");
        if (asc) cell.classList.add("asc");

        const allData = allRows.slice(allRows.indexOf(headerRow) + 1)
          .filter(tr => tr.querySelectorAll("td").length > 0);

        const forceDate = isDateHeader(cell);

        allData.sort((a, b) => {
          const ra = (a.children[colIndex]?.innerText || "").trim();
          const rb = (b.children[colIndex]?.innerText || "").trim();
          const da = toDateKey(ra);
          const db = toDateKey(rb);
          if (forceDate || (da && db)) return (da - db) * (asc ? 1 : -1);
          const na = toNumber(ra);
          const nb = toNumber(rb);
          if (na !== null && nb !== null) return (na - nb) * (asc ? 1 : -1);
          return ra.localeCompare(rb) * (asc ? 1 : -1);
        });

        allData.forEach(tr => table.tBodies[0].appendChild(tr));
      });

      table.dataset.sortBound = "1";
    }
  });
}

function mostrarSolo(capaId) {
  const capas = ['capa_casos', 'capa_iconos', 'capa_buscadores'];

  capas.forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;

    if (id === capaId) {
      // Si la capa clickeada estÃ¡ visible, ocultarla; si no, mostrarla
      if (el.style.display === 'block') {
        el.style.display = 'none';
        el.hidden = true;
      } else {
        el.style.display = 'block';
        el.hidden = false;
      }
    } else {
      // Ocultar todas las demÃ¡s
      el.style.display = 'none';
      el.hidden = true;
    }
  });
}

// ==========================================================
// 🖱️ Cerrar capas al hacer click fuera de ellas
// ==========================================================
function ocultarCapaSiClickFuera(event) {
  const capas = ['capa_casos', 'capa_iconos', 'capa_buscadores'];
  const botonesHeader = ['botonheader2']; // Clase de los botones del header

  // Verificar si el click fue en un botÃ³n del header (Casos, Favoritos, Buscadores)
  const clickEnBoton = event.target.closest('.botonheader2') ||
    event.target.closest('text[onclick*="mostrarSolo"]') ||
    event.target.closest('b') && event.target.closest('td');

  if (clickEnBoton) {
    // Si clickeÃ³ en un botÃ³n del header, no hacer nada (mostrarSolo se encarga)
    return;
  }

  capas.forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;

    // Si la capa estÃ¡ visible y el click fue fuera de ella
    if (el.style.display === 'block' && !el.contains(event.target)) {
      el.style.display = 'none';
      el.hidden = true;
      console.log(`ðŸ”´ Capa '${id}' cerrada por click externo`);
    }
  });
}


document.addEventListener("DOMContentLoaded", () => {
  inicializarOrdenamiento();

  // Registrar listener para cerrar capas al hacer click fuera
  document.addEventListener('click', ocultarCapaSiClickFuera);
});

// ==========================================================
// ðŸ§© OnLoad principal
// ==========================================================
function BodyOnLoad() {
  ocultarCapasPrincipales();
  mueveReloj();
}
// ==========================================================
// 🔄 Cambio de grupo via AJAX (sin recargar página)
// ==========================================================
function cambiarGrupoAjax(nuevoGrupoId) {
  if (!nuevoGrupoId) return;

  fetch('cambiar_grupo.php', {
    method: 'POST',
    credentials: 'include',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: 'sg=' + encodeURIComponent(nuevoGrupoId)
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Actualizar variables globales
        window.sg_id = data.sg_id;
        window.sg_name = data.sg_name;

        // Recargar el módulo actual
        const moduloActual = document.querySelector('#modulo-contenedor');
        if (moduloActual && typeof loadModulo === 'function') {
          const btnActivo = document.querySelector('.menu-btn.active');
          if (btnActivo) {
            const modulo = btnActivo.getAttribute('onclick').match(/loadModulo\('([^']+)'\)/);
            if (modulo) loadModulo(modulo[1]);
          }
        }

        console.log('✅ Grupo cambiado a:', data.sg_name);
      } else {
        console.error('❌ Error al cambiar grupo:', data.error);
        alert('Error al cambiar de grupo');
      }
    })
    .catch(error => {
      console.error('❌ Error de red:', error);
    });
}
