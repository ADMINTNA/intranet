// ===== suma_selected_col_cel.js =====
// Permite selección por arrastre vertical (rango continuo)
// y selección mútiple no contigua (⌥/Ctrl + click).
// Calcula suma o conteo solo de celdas visibles.

(function(global){
  // --- Funciones auxiliares ---
  function summarizeValues(values){
    const nums=[], texts=[];
    values.forEach(val=>{
      const v=String(val??"").trim();
      if(!v)return;
      const n=parseFloat(v.replace(/,/g,''));
      if(!Number.isNaN(n)&&Number.isFinite(n))nums.push(n);
      else texts.push(v);
    });
    if(nums.length&&!texts.length)return{type:"numeric",count:nums.length,sum:nums.reduce((a,b)=>a+b,0)};
    if(texts.length&&!nums.length)return{type:"text",count:texts.length};
    return{type:"mixed",numericCount:nums.length,textCount:texts.length,sum:nums.reduce((a,b)=>a+b,0)};
  }

  function showToast(msg){
    let el=document.querySelector(".ssc-toast");
    if(!el){
      el=document.createElement("div");
      el.className="ssc-toast";
      document.body.appendChild(el);
    }
    el.textContent=msg;
    el.style.display="block";
    clearTimeout(el._hideTimer);
    el._hideTimer=setTimeout(()=>{el.style.display="none";},4000);
  }

function addCalcButton(calcFn){
  if(document.querySelector(".ssc-btn")) return;

  const btn=document.createElement("button");
  btn.className="ssc-btn";
  btn.type="button";
  btn.textContent="🧮 Calcular selección";
  btn.style.cssText=`
    background:#f39c12;
    color:#fff;
    border:none;
    border-radius:6px;
    padding:2px 14px;
    cursor:pointer;
    font-weight:bold;
    margin-right:50px;
  `;
  btn.addEventListener("click",calcFn);

  // 🧩 Esperar a que la barra exista antes de insertarlo
  const tryInsert = () => {
    const bar = document.querySelector('.page-footer-bar');
    if (bar) bar.appendChild(btn);
    else setTimeout(tryInsert, 300);
  };
  tryInsert();
}
 
  function getVisibleSelectedValues(table){
    const vals=[];
    table.querySelectorAll("td.selected-col").forEach(td=>{
      const tr=td.closest("tr");
      const hidden=tr&&(tr.style.display==="none"||getComputedStyle(tr).display==="none");
      if(!hidden)vals.push(td.innerText.trim());
    });
    return vals;
  }

  // --- Selección principal ---
  function initSumaSelectedColCel(selector){
    const table=document.querySelector(selector||"#empTable");
    if(!table){console.warn("[suma_selected_col_cel] Tabla no encontrada:",selector);return;}

    let selecting=false;
    let startCol=null;
    let startRow=null;

    // === ARRRASTRE CONTINUO ===
    table.addEventListener("mousedown",e=>{
      const cell=e.target.closest("td");
      if(!cell)return;

      // No interferir con selecciÃ³n mÃºltiple (âŒ˜/Ctrl)
      if(e.metaKey||e.ctrlKey)return;

      startCol=cell.cellIndex;
      startRow=cell.parentNode.rowIndex;
      selecting=true;

      // limpiar selecciÃ³n previa
      table.querySelectorAll("td.selected-col").forEach(td=>td.classList.remove("selected-col"));
      e.preventDefault();
    });

    table.addEventListener("mousemove",e=>{
      if(!selecting||startCol===null)return;
      const cell=e.target.closest("td");
      if(!cell||cell.cellIndex!==startCol)return;

      const curRow=cell.parentNode.rowIndex;
      highlightRangeVisible(table,startCol,startRow,curRow);
    });

    document.addEventListener("mouseup",()=>{selecting=false;});

    // === SELECCIÃ“N NO CONTIGUA (âŒ˜/Ctrl + click) ===
    table.addEventListener("click",e=>{
      const cell=e.target.closest("td");
      if(!cell)return;

      // Si estÃ¡ en modo mÃºltiple, agregar o quitar sin borrar otras
      if(e.metaKey||e.ctrlKey){
        cell.classList.toggle("selected-col");
      }
    });

    // === BOTON CALCULAR ===
    addCalcButton(()=>calculateSelectedCells(table));

    // === ENTER + âŒ˜/CTRL ===
    document.addEventListener("keydown",e=>{
      const isEnter=e.key==="Enter"||e.keyCode===13;
      if(isEnter&&(e.metaKey||e.ctrlKey)){
        e.preventDefault();
        calculateSelectedCells(table);
      }
    });
  }

  function highlightRangeVisible(table,col,startRow,endRow){
    table.querySelectorAll("td.selected-col").forEach(td=>td.classList.remove("selected-col"));
    const min=Math.min(startRow,endRow),max=Math.max(startRow,endRow);
    table.querySelectorAll("tbody tr").forEach(tr=>{
      const hidden=tr&&(tr.style.display==="none"||getComputedStyle(tr).display==="none");
      if(hidden)return;
      const r=tr.rowIndex;
      if(r>=min&&r<=max){
        const td=tr.cells[col];
        if(td)td.classList.add("selected-col");
      }
    });
  }

  function calculateSelectedCells(table){
    const vals=getVisibleSelectedValues(table);
    if(!vals.length){showToast("No hay celdas seleccionadas visibles.");return;}
    const res=summarizeValues(vals);
    if(res.type==="numeric")showToast(`Suma: ${res.sum.toFixed(2)} (${res.count} valores visibles)`);
    else if(res.type==="text")showToast(`Conteo: ${res.count} elementos visibles`);
    else showToast(`Mixto: ${res.numericCount} nÃºmeros, ${res.textCount} textos Â· Suma ${res.sum}`);
  }

  global.initSumaSelectedColCel=initSumaSelectedColCel;
})(window);
