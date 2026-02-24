// ==========================================================
// FICHA DE CLIENTE - ORDENAMIENTO DE COLUMNAS
// Autor: mAo + Antigravity
// Fecha: 2026-01-07
// ==========================================================

console.log('🔄 Sort columns cargado');

// Función para ordenar tabla
function sortTable(table, columnIndex, isAscending) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));

    // Ordenar filas
    rows.sort((a, b) => {
        const aCell = a.cells[columnIndex];
        const bCell = b.cells[columnIndex];

        if (!aCell || !bCell) return 0;

        let aValue = aCell.textContent.trim();
        let bValue = bCell.textContent.trim();

        // Intentar convertir a número si es posible
        const aNum = parseFloat(aValue.replace(/[^0-9.-]/g, ''));
        const bNum = parseFloat(bValue.replace(/[^0-9.-]/g, ''));

        if (!isNaN(aNum) && !isNaN(bNum)) {
            return isAscending ? aNum - bNum : bNum - aNum;
        }

        // Ordenar como texto
        return isAscending
            ? aValue.localeCompare(bValue, 'es')
            : bValue.localeCompare(aValue, 'es');
    });

    // Reordenar DOM
    rows.forEach(row => tbody.appendChild(row));
}

// Función para agregar indicadores de ordenamiento
function updateSortIndicators(th, isAscending) {
    // Remover todos los indicadores existentes
    const allHeaders = th.closest('thead').querySelectorAll('th');
    allHeaders.forEach(header => {
        header.classList.remove('sort-asc', 'sort-desc');
        const indicator = header.querySelector('.sort-indicator');
        if (indicator) indicator.remove();
    });

    // Agregar indicador al header actual
    th.classList.add(isAscending ? 'sort-asc' : 'sort-desc');
    const indicator = document.createElement('span');
    indicator.className = 'sort-indicator';
    indicator.textContent = isAscending ? ' ▲' : ' ▼';
    th.appendChild(indicator);
}

// Función para hacer headers clickeables
function makeSortable(table) {
    const headers = table.querySelectorAll('thead th');

    headers.forEach((th, index) => {
        // Saltar headers vacíos o de acciones
        if (th.textContent.trim() === '' || th.textContent.trim() === ' ') {
            return;
        }

        // Agregar cursor pointer
        th.style.cursor = 'pointer';
        th.style.userSelect = 'none';

        // Estado de ordenamiento
        let isAscending = true;

        th.addEventListener('click', () => {
            sortTable(table, index, isAscending);
            updateSortIndicators(th, isAscending);
            isAscending = !isAscending;
        });

        // Hover effect
        th.addEventListener('mouseenter', () => {
            if (!th.classList.contains('sort-asc') && !th.classList.contains('sort-desc')) {
                th.style.opacity = '0.8';
            }
        });

        th.addEventListener('mouseleave', () => {
            th.style.opacity = '1';
        });
    });
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    // Hacer todas las tablas ordenables
    const tables = document.querySelectorAll('.contenedor-scroll-empresa table, .contenedor-scroll table');

    tables.forEach(table => {
        makeSortable(table);
    });

    console.log(`✅ ${tables.length} tablas ahora son ordenables`);
});
