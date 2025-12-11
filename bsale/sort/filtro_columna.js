// ===== filtro_columna.js =====
// Filtros de columnas con barra inferior translÃºcida (ðŸ§¹ Borrar, ðŸ§® Calcular, â“ Ayuda)

(function(global){
  // --- ConversiÃ³n de fecha ---
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
  function filterTable(input, colIndex, numericCols, dateCols){
    const filter = (input.value || '').trim();
    const table  = input.closest('table');
    const rows   = table.querySelectorAll('tbody tr');

    for(let i=0; i<rows.length; i++){
      const cell = rows[i].querySelectorAll('td')[colIndex];
      if(!cell) continue;
      const txt = (cell.textContent || '').trim();
      let show = true;

      // === NumÃ©ricos ===
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

      // === Fechas ===
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

      // === Texto ===
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
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(r => r.style.display = '');
  }

  // --- Ventana de ayuda ---
  function showHelpOverlay(){
    if (document.querySelector('.help-overlay')) return;

    const overlay = document.createElement('div');
    overlay.className = 'help-overlay';
    overlay.innerHTML = `
      <div class="help-content">
        <h3>ðŸ§­ GuÃ­a rÃ¡pida de uso</h3>
        <p><b>ðŸ”¹ Ordenar columnas:</b><br>
        Clic en el nombre de la columna â†’ â–² ascendente, â–¼ descendente.</p>
        <p><b>ðŸ”¹ Filtros:</b><br>
        Escribe en los campos bajo el tÃ­tulo.<br>
        Ejemplos:<br>
        <code>mayor 100</code> &nbsp; <code>menor 500</code><br>
        <code>entre 100 y 2000</code><br>
        <code>entre 01-01-2024 y 31-03-2024</code></p>
        <p><b>ðŸ”¹ SelecciÃ³n y suma:</b><br>
        - Arrastra verticalmente para seleccionar una columna.<br>
        - âŒ˜ (Mac) / Ctrl (Windows) + clic = celdas no contiguas.<br>
        - BotÃ³n <b>â€œCalcular selecciÃ³nâ€</b> â†’ sumar o contar.</p>
        <button class="close-help">Cerrar</button>
      </div>
    `;
    document.body.appendChild(overlay);
    document.querySelector('.close-help').addEventListener('click', () => overlay.remove());
  }

      // --- Barra inferior translÃºcida ---
      function createFooterBar(table){
        // Evita duplicados
        const existing = document.querySelector('.page-footer-bar');
        if(existing) existing.remove();

        const bar = document.createElement('div');
        bar.className = 'page-footer-bar';
        bar.style.cssText = `
          position: fixed;
          bottom: calc(0.0em + 0px); /* una línea por encima del footer, adaptable */
          left: 0;
          width: 99%;
          display: flex;
          justify-content: center;
          align-items: center;
          gap: 20px;
          padding: 10px 20px;
          background: #fff;
          color: #fff;
          border-top: 1px solid rgba(255,255,255,0.2);
          box-shadow: 0 -2px 10px rgba(0,0,0,0.3);
          font-size: 14px;
          z-index: 9999;
          backdrop-filter: blur(5px);
        `;

        // 🧹 Borrar filtros
        const btnClear = document.createElement('button');
        btnClear.textContent = '🧹 Borrar filtros';
        btnClear.style.cssText = `
          background: #c0392b;
          color: #fff;
          border: none;
          border-radius: 6px;
          padding: 6px 14px;
          cursor: pointer;
          font-weight: bold;
        `;
        btnClear.addEventListener('click', () => clearAllFilters(table));


        // ❓ Ayuda
        const helpBtn = document.createElement('button');
        helpBtn.textContent = '❓ Ayuda';
        helpBtn.style.cssText = `
          background: #3498db;
          color: #fff;
          border: none;
          border-radius: 6px;
          padding: 6px 14px;
          cursor: pointer;
          font-weight: bold;
        `;
        helpBtn.addEventListener('click', showHelpOverlay);

        bar.append(btnClear, helpBtn);
        document.body.appendChild(bar);
      }

  // --- Inicialización ---
  function initFiltroColumna(selector, options = {}){
    const table = document.querySelector(selector);
    if(!table){ console.warn("[filtro_columna] No se encontrÃ³ la tabla:", selector); return; }

    const numericCols  = options.numericCols  || [];
    const dateCols     = options.dateCols     || [];
    const excludedCols = options.excludedCols || [];

    let header = table.querySelector('thead');
    if(!header){
      const firstRow = table.querySelector('tr');
      if(firstRow){
        header = document.createElement('thead');
        header.appendChild(firstRow);
        table.insertBefore(header, table.firstChild);
      } else return;
    }

    let filterRow = header.querySelector('.filter-row');
    if(!filterRow){
      const firstRow = header.rows[0];
      filterRow = header.insertRow(1);
      filterRow.classList.add('filter-row');

      for(let i=0; i<firstRow.cells.length; i++){
        const th = document.createElement('th');
        if(excludedCols.includes(i)){
          th.innerHTML = '&nbsp;';
          filterRow.appendChild(th);
          continue;
        }
        const input = document.createElement('input');
        input.type = 'text';
        input.placeholder = 'Filtrar...';
        input.addEventListener('keyup', e => filterTable(e.target, i, numericCols, dateCols));
        th.appendChild(input);
        filterRow.appendChild(th);
      }
    }

    createFooterBar(table);
  }

  global.initFiltroColumna = initFiltroColumna;
})(window);