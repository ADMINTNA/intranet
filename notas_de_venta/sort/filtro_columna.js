// ===== filtro_columna.js =====
// Filtros de columnas + footer con botones (Borrar, Calcular, Ayuda)

(function(global){
  const numericCols = [1,2,5,10];
  const dateCols = [4];

  // --- Conversión de fecha ---
  function dateKey(str){
    if(!str) return null;
    str = str.trim().replace(/\//g,'-');
    let m = str.match(/^(\d{2})-(\d{2})-(\d{4})$/);
    if(m) return parseInt(m[3]+m[2]+m[1]);
    m = str.match(/^(\d{4})-(\d{2})-(\d{2})$/);
    if(m) return parseInt(m[1]+m[2]+m[3]);
    return null;
  }

  // --- Filtro principal ---
  function filterTable(input, colIndex){
    const filter = (input.value || '').trim();
    const table  = input.closest('table');
    const rows   = table.getElementsByTagName('tr');

    for(let i=2; i<rows.length; i++){
      const cell = rows[i].getElementsByTagName('td')[colIndex];
      if(!cell) continue;
      const txt = (cell.textContent || cell.innerText || '').trim();
      let show = true;

      // === NUMÉRICOS ===
      if(numericCols.includes(colIndex) && filter!==''){
        const num = parseFloat(txt.replace(/,/g,''));
        const f = filter.toLowerCase();

        if(f.startsWith('mayor')){
          const comp = parseFloat(f.replace('mayor','').trim());
          show = num > comp;
        }
        else if(f.startsWith('menor')){
          const comp = parseFloat(f.replace('menor','').trim());
          show = num < comp;
        }
        else if(f.startsWith('entre')){
          const parts = f.replace('entre','').split('y');
          if(parts.length===2){
            const a = parseFloat(parts[0].trim());
            const b = parseFloat(parts[1].trim());
            if(!isNaN(a) && !isNaN(b)){
              const min = Math.min(a,b), max = Math.max(a,b);
              show = num >= min && num <= max;
            } else show = false;
          }
        }
        else if(f.startsWith('=') || !isNaN(f)){
          const comp = parseFloat(f.replace('=','').trim());
          show = num === comp;
        }
        else {
          show = txt.toUpperCase().includes(filter.toUpperCase());
        }
      }

      // === FECHAS ===
      else if(dateCols.includes(colIndex) && filter!==''){
        const cellK = dateKey(txt);
        if(cellK===null){ rows[i].style.display='none'; continue; }
        const f = filter.toLowerCase();

        if(f.startsWith('mayor')){
          const fK = dateKey(f.replace('mayor','').trim());
          show = fK!==null && cellK > fK;
        }
        else if(f.startsWith('menor')){
          const fK = dateKey(f.replace('menor','').trim());
          show = fK!==null && cellK < fK;
        }
        else if(f.startsWith('entre')){
          const parts = f.replace('entre','').split('y');
          if(parts.length===2){
            const k1 = dateKey(parts[0].trim()), k2 = dateKey(parts[1].trim());
            if(k1 && k2){
              const a=Math.min(k1,k2), b=Math.max(k1,k2);
              show = (cellK >= a && cellK <= b);
            } else show=false;
          }
        } else {
          const fK = dateKey(f);
          show = (fK!==null && cellK===fK);
        }
      }

      // === TEXTO ===
      else if(filter!==''){
        show = txt.toUpperCase().includes(filter.toUpperCase());
      }

      rows[i].style.display = show ? '' : 'none';
    }
  }

  // --- Limpia todos los filtros ---
  function clearAllFilters(table){
    const inputs = table.querySelectorAll('.filter-row input');
    inputs.forEach(inp => inp.value = '');
    const rows = table.getElementsByTagName('tr');
    for(let i=2; i<rows.length; i++){
      rows[i].style.display = '';
    }
  }

  // --- Ventana de ayuda ---
  function showHelpOverlay(){
    if (document.querySelector('.help-overlay')) return;

    const overlay = document.createElement('div');
    overlay.className = 'help-overlay';
    overlay.innerHTML = `
      <div class="help-content">
        <h3>🧭 Guía rápida de uso</h3>
        <p><b>🔹 Ordenar columnas:</b><br>
        Clic en el nombre de la columna → ▲ ascendente, ▼ descendente.</p>

        <p><b>🔹 Filtros:</b><br>
        Escribe en los campos bajo el título.<br>
        Ejemplos:<br>
        <code>mayor 100</code> &nbsp; <code>menor 500</code><br>
        <code>entre 100 y 2000</code><br>
        <code>entre 01-01-2024 y 31-03-2024</code></p>

        <p><b>🔹 Selección y suma:</b><br>
        - Arrastra verticalmente para seleccionar una columna.<br>
        - ⌘ (Mac) / Ctrl (Windows) + clic = celdas no contiguas.<br>
        - Botón <b>“Calcular selección”</b> → sumar o contar.</p>

        <button class="close-help">Cerrar</button>
      </div>
    `;
    document.body.appendChild(overlay);
    document.querySelector('.close-help').addEventListener('click', () => overlay.remove());
  }

  // --- Barra fija en el footer de la página ---
  function createFooterBar(table){
    if(document.querySelector('.page-footer-bar')) return;

    const bar = document.createElement('div');
    bar.className = 'page-footer-bar';
    bar.style.cssText = `
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 20px;
      background: #f8f8f8;
      border-top: 1px solid #ccc;
      box-shadow: 0 -2px 6px rgba(0,0,0,0.1);
      font-size: 14px;
      z-index: 9999;
    `;

    const leftGroup = document.createElement('div');
    const rightGroup = document.createElement('div');

    // 🧹 Borrar filtros
    const btnClear = document.createElement('button');
    btnClear.textContent = '🧹 Borrar filtros';
    btnClear.style.cssText = `
      background: #c0392b;
      color: #fff;
      border: none;
      border-radius: 6px;
      padding: 6px 12px;
      cursor: pointer;
      font-weight: bold;
    `;
    btnClear.addEventListener('click', () => clearAllFilters(table));
    leftGroup.appendChild(btnClear);

    // ❓ Ayuda
    const helpBtn = document.createElement('button');
    helpBtn.textContent = '❓ Ayuda';
    helpBtn.style.cssText = `
      background: #3498db;
      color: #fff;
      border: none;
      border-radius: 6px;
      padding: 6px 12px;
      cursor: pointer;
      font-weight: bold;
      margin-left: 10px;
    `;
    helpBtn.addEventListener('click', showHelpOverlay);
    leftGroup.appendChild(helpBtn);

    // 🧮 Calcular selección
    const calcBtn = document.querySelector('.ssc-btn');
    if (calcBtn) {
      calcBtn.style.position = 'static';
      calcBtn.style.marginRight = '10px';
      rightGroup.appendChild(calcBtn);
    } else {
      const btnCalc = document.createElement('button');
      btnCalc.textContent = '🧮 Calcular selección';
      btnCalc.style.cssText = `
        background: #1F1D3E;
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 6px 12px;
        cursor: pointer;
        font-weight: bold;
      `;
      rightGroup.appendChild(btnCalc);
    }

    bar.appendChild(leftGroup);
    bar.appendChild(rightGroup);
    document.body.appendChild(bar);
  }

  // --- Inicialización ---
  function initFiltroColumna(selector, options = {}){
    const table = document.querySelector(selector || '#empTable');
    if(!table){ console.warn('[filtro_columna] No se encontró la tabla:', selector); return; }

    const header = table.querySelector('thead');
    if(!header) return;

    const excludeFirst = !!options.excludeFirstColumn;
    let filterRow = header.querySelector('.filter-row');
    if(!filterRow){
      const firstRow = header.rows[0];
      filterRow = header.insertRow(1);
      filterRow.classList.add('filter-row');

      for(let i=0; i<firstRow.cells.length; i++){
        const th = document.createElement('th');
        if(excludeFirst && i === 0){
          th.innerHTML = '&nbsp;';
          filterRow.appendChild(th);
          continue;
        }
        const input = document.createElement('input');
        input.type = 'text';
        input.placeholder = 'Filtrar...';
        input.style.width = '95%';
        input.addEventListener('keyup', e => filterTable(e.target, i));
        th.appendChild(input);
        filterRow.appendChild(th);
      }
    }

    createFooterBar(table);
  }

  global.initFiltroColumna = initFiltroColumna;
})(window);
