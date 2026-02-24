// ==========================================================
// FICHA DE CLIENTE - FILTROS Y BÚSQUEDA
// Autor: mAo + Antigravity
// Fecha: 2026-01-07
// ==========================================================

console.log('🔍 Filtros y búsqueda cargado');

// Estado de filtros
let filtrosActivos = {
    busqueda: '',
    estadoServicio: [],
    tipoContacto: []
};

// Función para aplicar filtros
function aplicarFiltros() {
    const tablaClientes = document.querySelector('.contenedor-scroll-empresa table tbody');
    const tablaServicios = document.querySelector('.contenedor-scroll table tbody');

    if (tablaClientes) {
        filtrarTablaClientes(tablaClientes);
    }

    if (tablaServicios) {
        filtrarTablaServicios(tablaServicios);
    }

    actualizarContadores();
}

// Filtrar tabla de clientes
function filtrarTablaClientes(tbody) {
    const filas = tbody.querySelectorAll('tr');
    let visibles = 0;

    filas.forEach(fila => {
        let mostrar = true;
        const texto = fila.textContent.toLowerCase();

        // Filtro de búsqueda
        if (filtrosActivos.busqueda && !texto.includes(filtrosActivos.busqueda.toLowerCase())) {
            mostrar = false;
        }

        // Filtro de tipo de contacto
        if (filtrosActivos.tipoContacto.length > 0) {
            const tipoCell = fila.cells[5]; // Columna "Tipo"
            if (tipoCell) {
                const tipo = tipoCell.textContent.trim().toLowerCase();
                if (!filtrosActivos.tipoContacto.some(t => tipo.includes(t.toLowerCase()))) {
                    mostrar = false;
                }
            }
        }

        fila.style.display = mostrar ? '' : 'none';
        if (mostrar) visibles++;
    });

    console.log(`👥 Contactos visibles: ${visibles}`);
}

// Filtrar tabla de servicios
function filtrarTablaServicios(tbody) {
    const filas = tbody.querySelectorAll('tr');
    let visibles = 0;

    filas.forEach(fila => {
        // Saltar el tfoot
        if (fila.parentElement.tagName === 'TFOOT') return;

        let mostrar = true;
        const texto = fila.textContent.toLowerCase();

        // Filtro de búsqueda
        if (filtrosActivos.busqueda && !texto.includes(filtrosActivos.busqueda.toLowerCase())) {
            mostrar = false;
        }

        // Filtro de estado de servicio
        if (filtrosActivos.estadoServicio.length > 0) {
            const estadoCell = fila.cells[2]; // Columna "Estado"
            if (estadoCell) {
                const estado = estadoCell.textContent.trim().toLowerCase();
                if (!filtrosActivos.estadoServicio.some(e => estado.includes(e.toLowerCase()))) {
                    mostrar = false;
                }
            }
        }

        fila.style.display = mostrar ? '' : 'none';
        if (mostrar) visibles++;
    });

    console.log(`🔧 Servicios visibles: ${visibles}`);
}

// Actualizar contadores
function actualizarContadores() {
    const btnFiltrar = document.getElementById('btn-filtrar');
    if (!btnFiltrar) return;

    let count = 0;
    if (filtrosActivos.busqueda) count++;
    count += filtrosActivos.estadoServicio.length;
    count += filtrosActivos.tipoContacto.length;

    if (count > 0) {
        btnFiltrar.textContent = `🔍 Filtros (${count})`;
        btnFiltrar.classList.add('activo');
    } else {
        btnFiltrar.textContent = '🔍 Filtrar';
        btnFiltrar.classList.remove('activo');
    }
}

// Toggle panel de filtros
function togglePanelFiltros() {
    const panel = document.getElementById('panel-filtros');
    const overlay = document.getElementById('overlay-filtros');

    if (panel.classList.contains('hidden')) {
        panel.classList.remove('hidden');
        overlay.classList.add('visible');
    } else {
        panel.classList.add('hidden');
        overlay.classList.remove('visible');
    }
}

// Cerrar panel
function cerrarPanelFiltros() {
    const panel = document.getElementById('panel-filtros');
    const overlay = document.getElementById('overlay-filtros');

    panel.classList.add('hidden');
    overlay.classList.remove('visible');
}

// Limpiar filtros
function limpiarFiltros() {
    filtrosActivos = {
        busqueda: '',
        estadoServicio: [],
        tipoContacto: []
    };

    // Limpiar inputs
    const searchInput = document.getElementById('search-input');
    if (searchInput) searchInput.value = '';

    document.querySelectorAll('.filter-checkbox').forEach(cb => cb.checked = false);

    aplicarFiltros();
    guardarFiltros();
}

// Guardar filtros en localStorage
function guardarFiltros() {
    localStorage.setItem('ficha_cliente_filtros', JSON.stringify(filtrosActivos));
}

// Restaurar filtros
function restaurarFiltros() {
    const saved = localStorage.getItem('ficha_cliente_filtros');
    if (!saved) return;

    try {
        filtrosActivos = JSON.parse(saved);

        // Restaurar búsqueda
        const searchInput = document.getElementById('search-input');
        if (searchInput && filtrosActivos.busqueda) {
            searchInput.value = filtrosActivos.busqueda;
        }

        // Restaurar checkboxes
        document.querySelectorAll('.filter-checkbox').forEach(cb => {
            const tipo = cb.dataset.tipo;
            const valor = cb.value;

            if (tipo === 'estado' && filtrosActivos.estadoServicio.includes(valor)) {
                cb.checked = true;
            } else if (tipo === 'contacto' && filtrosActivos.tipoContacto.includes(valor)) {
                cb.checked = true;
            }
        });

        aplicarFiltros();
    } catch (e) {
        console.error('Error restaurando filtros:', e);
    }
}

// Inicializar
document.addEventListener('DOMContentLoaded', () => {
    // Búsqueda en tiempo real
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            filtrosActivos.busqueda = e.target.value.trim();
            aplicarFiltros();
            guardarFiltros();
        });
    }

    // Checkboxes de filtros
    document.querySelectorAll('.filter-checkbox').forEach(cb => {
        cb.addEventListener('change', (e) => {
            const tipo = e.target.dataset.tipo;
            const valor = e.target.value;

            if (tipo === 'estado') {
                if (e.target.checked) {
                    filtrosActivos.estadoServicio.push(valor);
                } else {
                    filtrosActivos.estadoServicio = filtrosActivos.estadoServicio.filter(v => v !== valor);
                }
            } else if (tipo === 'contacto') {
                if (e.target.checked) {
                    filtrosActivos.tipoContacto.push(valor);
                } else {
                    filtrosActivos.tipoContacto = filtrosActivos.tipoContacto.filter(v => v !== valor);
                }
            }

            aplicarFiltros();
            guardarFiltros();
        });
    });

    // Restaurar filtros guardados
    restaurarFiltros();

    console.log('✅ Sistema de filtros inicializado');
});

// Exponer funciones globales
window.togglePanelFiltros = togglePanelFiltros;
window.cerrarPanelFiltros = cerrarPanelFiltros;
window.limpiarFiltros = limpiarFiltros;
