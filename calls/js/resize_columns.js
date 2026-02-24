// ==========================================================
// FICHA DE CLIENTE - REDIMENSIONAMIENTO DE COLUMNAS
// Autor: mAo + Antigravity
// Fecha: 2026-01-07
// Basado en cm_resizable_columns_universal.js
// ==========================================================

console.log('📏 Resize columns cargado');

class ColumnResizer {
    constructor(table) {
        this.table = table;
        this.isResizing = false;
        this.currentTh = null;
        this.startX = 0;
        this.startWidth = 0;

        this.init();
    }

    init() {
        const headers = this.table.querySelectorAll('thead th');

        headers.forEach((th, index) => {
            // Saltar el último header
            if (index === headers.length - 1) return;

            // Crear handle de resize
            const resizeHandle = document.createElement('div');
            resizeHandle.className = 'resize-handle';
            th.appendChild(resizeHandle);

            // Eventos del handle
            resizeHandle.addEventListener('mousedown', (e) => this.onMouseDown(e, th));
        });

        // Eventos globales
        document.addEventListener('mousemove', (e) => this.onMouseMove(e));
        document.addEventListener('mouseup', () => this.onMouseUp());
    }

    onMouseDown(e, th) {
        e.preventDefault();
        e.stopPropagation(); // Evitar que se active el sort

        this.isResizing = true;
        this.currentTh = th;
        this.startX = e.pageX;
        this.startWidth = th.offsetWidth;

        // Agregar clase para cursor
        document.body.style.cursor = 'col-resize';
        this.table.classList.add('resizing');
    }

    onMouseMove(e) {
        if (!this.isResizing) return;

        const diff = e.pageX - this.startX;
        const newWidth = Math.max(50, this.startWidth + diff); // Mínimo 50px

        this.currentTh.style.width = newWidth + 'px';
        this.currentTh.style.minWidth = newWidth + 'px';
        this.currentTh.style.maxWidth = newWidth + 'px';
    }

    onMouseUp() {
        if (!this.isResizing) return;

        this.isResizing = false;
        this.currentTh = null;

        document.body.style.cursor = '';
        this.table.classList.remove('resizing');
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    // Hacer todas las tablas redimensionables
    const tables = document.querySelectorAll('.contenedor-scroll-empresa table, .contenedor-scroll table');

    tables.forEach(table => {
        new ColumnResizer(table);
    });

    console.log(`✅ ${tables.length} tablas ahora tienen columnas redimensionables`);
});
