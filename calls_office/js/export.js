// ==========================================================
// FICHA DE CLIENTE - EXPORTACIÓN DE DATOS
// Autor: mAo + Antigravity
// Fecha: 2026-01-07
// ==========================================================

console.log('📤 Exportación de datos cargado');

// Función para exportar tabla a CSV
function exportarCSV(tabla, nombreArchivo) {
    const rows = [];

    // Headers
    const headers = [];
    tabla.querySelectorAll('thead th').forEach(th => {
        headers.push(th.textContent.trim());
    });
    rows.push(headers.join(','));

    // Datos (solo filas visibles)
    tabla.querySelectorAll('tbody tr').forEach(tr => {
        if (tr.style.display !== 'none') {
            const cells = [];
            tr.querySelectorAll('td').forEach(td => {
                // Limpiar y escapar el texto
                let text = td.textContent.trim();
                text = text.replace(/"/g, '""'); // Escapar comillas
                cells.push(`"${text}"`);
            });
            rows.push(cells.join(','));
        }
    });

    // Crear blob y descargar
    const csv = rows.join('\n');
    const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);

    link.setAttribute('href', url);
    link.setAttribute('download', nombreArchivo);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    console.log(`✅ Exportado: ${nombreArchivo}`);
}

// Función para exportar tabla a Excel (HTML)
function exportarExcel(tabla, nombreArchivo) {
    const rows = [];

    // Headers
    rows.push('<tr>');
    tabla.querySelectorAll('thead th').forEach(th => {
        rows.push(`<th style="background:#512554;color:white;font-weight:bold;padding:8px;">${th.textContent.trim()}</th>`);
    });
    rows.push('</tr>');

    // Datos (solo filas visibles)
    tabla.querySelectorAll('tbody tr').forEach(tr => {
        if (tr.style.display !== 'none') {
            rows.push('<tr>');
            tr.querySelectorAll('td').forEach(td => {
                rows.push(`<td style="padding:8px;border:1px solid #ddd;">${td.innerHTML}</td>`);
            });
            rows.push('</tr>');
        }
    });

    const html = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
        <head>
            <meta charset="utf-8">
            <style>
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #512554; color: white; font-weight: bold; }
            </style>
        </head>
        <body>
            <table>${rows.join('')}</table>
        </body>
        </html>
    `;

    const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);

    link.setAttribute('href', url);
    link.setAttribute('download', nombreArchivo);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    console.log(`✅ Exportado: ${nombreArchivo}`);
}

// Función para copiar tabla al portapapeles
function copiarTabla(tabla) {
    const rows = [];

    // Headers
    const headers = [];
    tabla.querySelectorAll('thead th').forEach(th => {
        headers.push(th.textContent.trim());
    });
    rows.push(headers.join('\t'));

    // Datos (solo filas visibles)
    tabla.querySelectorAll('tbody tr').forEach(tr => {
        if (tr.style.display !== 'none') {
            const cells = [];
            tr.querySelectorAll('td').forEach(td => {
                cells.push(td.textContent.trim());
            });
            rows.push(cells.join('\t'));
        }
    });

    const text = rows.join('\n');

    navigator.clipboard.writeText(text).then(() => {
        mostrarNotificacion('✅ Tabla copiada al portapapeles');
        console.log('✅ Tabla copiada');
    }).catch(err => {
        console.error('Error copiando:', err);
        mostrarNotificacion('❌ Error al copiar');
    });
}

// Mostrar notificación temporal
function mostrarNotificacion(mensaje) {
    const notif = document.createElement('div');
    notif.className = 'notificacion';
    notif.textContent = mensaje;
    document.body.appendChild(notif);

    setTimeout(() => {
        notif.classList.add('visible');
    }, 10);

    setTimeout(() => {
        notif.classList.remove('visible');
        setTimeout(() => {
            document.body.removeChild(notif);
        }, 300);
    }, 3000);
}

// Mostrar menú de exportación
function mostrarMenuExportar() {
    const menu = document.getElementById('menu-exportar');
    menu.classList.toggle('visible');
}

// Cerrar menú al hacer click fuera
document.addEventListener('click', (e) => {
    const menu = document.getElementById('menu-exportar');
    const btnExportar = document.getElementById('btn-exportar');

    if (menu && !menu.contains(e.target) && e.target !== btnExportar) {
        menu.classList.remove('visible');
    }
});

// Exportar sección específica
function exportarSeccion(seccion, formato) {
    const fecha = new Date().toISOString().split('T')[0];
    let tabla, nombre;

    if (seccion === 'clientes') {
        tabla = document.querySelector('.contenedor-scroll-empresa table');
        nombre = `clientes_${fecha}`;
    } else if (seccion === 'servicios') {
        tabla = document.querySelector('.contenedor-scroll table');
        nombre = `servicios_${fecha}`;
    }

    if (!tabla) {
        mostrarNotificacion('❌ No se encontró la tabla');
        return;
    }

    if (formato === 'csv') {
        exportarCSV(tabla, `${nombre}.csv`);
    } else if (formato === 'excel') {
        exportarExcel(tabla, `${nombre}.xls`);
    } else if (formato === 'copiar') {
        copiarTabla(tabla);
    }

    document.getElementById('menu-exportar').classList.remove('visible');
}

// Exponer funciones globales
window.mostrarMenuExportar = mostrarMenuExportar;
window.exportarSeccion = exportarSeccion;
