// ==========================================================
// COLUMNAS REDIMENSIONABLES - Tareas Pendientes
// Autor: Mauricio Araneda (mAo)
// Fecha: 2026-01-06
// ==========================================================

(function () {
    'use strict';

    const STORAGE_KEY = 'kickoff_tareas_column_widths';
    let isResizing = false;
    let currentTh = null;
    let startX = 0;
    let startWidth = 0;

    // ==========================================================
    // INICIALIZAR COLUMNAS REDIMENSIONABLES
    // ==========================================================
    function initResizableColumns() {
        const tables = document.querySelectorAll('#tareas, #tareas_delegadas');

        tables.forEach(table => {
            const tableId = table.id;
            const headers = table.querySelectorAll('tr.subtit th');

            // Cargar anchos guardados
            loadColumnWidths(tableId, headers);

            headers.forEach((th, index) => {
                // Agregar cursor de resize al pasar sobre el borde derecho
                th.style.position = 'relative';
                th.style.cursor = 'default';

                // Detectar hover en borde derecho
                th.addEventListener('mousemove', (e) => {
                    const rect = th.getBoundingClientRect();
                    const isRightEdge = e.clientX > rect.right - 10;

                    if (isRightEdge) {
                        th.style.cursor = 'col-resize';
                    } else {
                        th.style.cursor = 'default';
                    }
                });

                // Iniciar resize
                th.addEventListener('mousedown', (e) => {
                    const rect = th.getBoundingClientRect();
                    const isRightEdge = e.clientX > rect.right - 10;

                    if (isRightEdge) {
                        e.preventDefault();
                        isResizing = true;
                        currentTh = th;
                        startX = e.clientX;
                        startWidth = th.offsetWidth;

                        document.body.style.cursor = 'col-resize';
                        document.body.style.userSelect = 'none';
                    }
                });

                // Doble-click para auto-ajustar
                th.addEventListener('dblclick', (e) => {
                    const rect = th.getBoundingClientRect();
                    const isRightEdge = e.clientX > rect.right - 10;

                    if (isRightEdge) {
                        autoFitColumn(table, index);
                        saveColumnWidths(tableId, table.querySelectorAll('tr.subtit th'));
                    }
                });
            });
        });

        // Eventos globales para resize
        document.addEventListener('mousemove', handleMouseMove);
        document.addEventListener('mouseup', handleMouseUp);
    }

    // ==========================================================
    // MANEJAR MOVIMIENTO DEL MOUSE
    // ==========================================================
    function handleMouseMove(e) {
        if (!isResizing || !currentTh) return;

        const diff = e.clientX - startX;
        const newWidth = Math.max(50, startWidth + diff); // Mínimo 50px

        currentTh.style.width = newWidth + 'px';
        currentTh.style.minWidth = newWidth + 'px';
        currentTh.style.maxWidth = newWidth + 'px';
    }

    // ==========================================================
    // FINALIZAR RESIZE
    // ==========================================================
    function handleMouseUp(e) {
        if (!isResizing) return;

        isResizing = false;
        document.body.style.cursor = '';
        document.body.style.userSelect = '';

        if (currentTh) {
            const table = currentTh.closest('table');
            const tableId = table.id;
            const headers = table.querySelectorAll('tr.subtit th');

            // Guardar anchos
            saveColumnWidths(tableId, headers);

            currentTh = null;
        }
    }

    // ==========================================================
    // AUTO-AJUSTAR COLUMNA AL CONTENIDO
    // ==========================================================
    function autoFitColumn(table, columnIndex) {
        const cells = table.querySelectorAll(`tr td:nth-child(${columnIndex + 1})`);
        let maxWidth = 0;

        cells.forEach(cell => {
            const width = cell.scrollWidth;
            if (width > maxWidth) maxWidth = width;
        });

        // Agregar padding
        maxWidth += 20;

        const th = table.querySelector(`tr.subtit th:nth-child(${columnIndex + 1})`);
        if (th) {
            th.style.width = maxWidth + 'px';
            th.style.minWidth = maxWidth + 'px';
            th.style.maxWidth = maxWidth + 'px';
        }
    }

    // ==========================================================
    // GUARDAR ANCHOS EN LOCALSTORAGE
    // ==========================================================
    function saveColumnWidths(tableId, headers) {
        const widths = {};

        headers.forEach((th, index) => {
            if (th.style.width) {
                widths[index] = th.style.width;
            }
        });

        const allWidths = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
        allWidths[tableId] = widths;
        localStorage.setItem(STORAGE_KEY, JSON.stringify(allWidths));
    }

    // ==========================================================
    // CARGAR ANCHOS DESDE LOCALSTORAGE
    // ==========================================================
    function loadColumnWidths(tableId, headers) {
        const allWidths = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
        const widths = allWidths[tableId];

        if (widths) {
            headers.forEach((th, index) => {
                if (widths[index]) {
                    th.style.width = widths[index];
                    th.style.minWidth = widths[index];
                    th.style.maxWidth = widths[index];
                }
            });
        }
    }

    // ==========================================================
    // RESETEAR ANCHOS (función pública)
    // ==========================================================
    window.resetColumnWidths = function (tableId) {
        const allWidths = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
        delete allWidths[tableId];
        localStorage.setItem(STORAGE_KEY, JSON.stringify(allWidths));

        // Recargar página para aplicar anchos por defecto
        location.reload();
    };

    // ==========================================================
    // INICIALIZAR AL CARGAR
    // ==========================================================
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initResizableColumns);
    } else {
        initResizableColumns();
    }

})();
