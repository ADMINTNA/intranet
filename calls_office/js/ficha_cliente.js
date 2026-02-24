// ==========================================================
// FICHA DE CLIENTE - CARGA AJAX
// Autor: mAo + Antigravity
// Fecha: 2026-01-07
// ==========================================================

console.log('🚀 Ficha de Cliente AJAX cargado');

// Función para mostrar loader en una sección
function showLoader(sectionId) {
    const section = document.getElementById(sectionId);
    if (!section) return;

    const body = section.querySelector('.section-body');
    if (body) {
        body.innerHTML = `
            <div class="ajax-loader">
                <div class="spinner"></div>
                <p>Cargando datos...</p>
            </div>
        `;
    }
}

// Función para mostrar error en una sección
function showError(sectionId, message) {
    const section = document.getElementById(sectionId);
    if (!section) return;

    const body = section.querySelector('.section-body');
    if (body) {
        body.innerHTML = `
            <div class="ajax-error">
                <p>❌ Error: ${message}</p>
                <button onclick="retryLoad('${sectionId}')">Reintentar</button>
            </div>
        `;
    }
}

// Función para cargar sección de cliente
function loadClientInfo(empresa) {
    showLoader('section-cliente');

    fetch(`includes/ajax/load_cliente.php?empresa=${encodeURIComponent(empresa)}`)
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            return response.text();
        })
        .then(html => {
            const section = document.getElementById('section-cliente');
            const body = section.querySelector('.section-body');
            body.innerHTML = html;
            console.log('✅ Cliente cargado');
        })
        .catch(error => {
            console.error('Error cargando cliente:', error);
            showError('section-cliente', error.message);
        });
}

// Función para cargar portal de pago
function loadPaymentPortal(empresa) {
    showLoader('section-payment');

    fetch(`includes/ajax/load_payment.php?empresa=${encodeURIComponent(empresa)}`)
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            return response.text();
        })
        .then(html => {
            const section = document.getElementById('section-payment');
            const body = section.querySelector('.section-body');
            body.innerHTML = html;
            console.log('✅ Portal de pago cargado');
        })
        .catch(error => {
            console.error('Error cargando portal de pago:', error);
            showError('section-payment', error.message);
        });
}

// Función para cargar servicios activos
function loadActiveServices(empresa) {
    showLoader('section-services');

    fetch(`includes/ajax/load_services.php?empresa=${encodeURIComponent(empresa)}`)
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            return response.text();
        })
        .then(html => {
            const section = document.getElementById('section-services');
            const body = section.querySelector('.section-body');
            body.innerHTML = html;
            console.log('✅ Servicios activos cargados');
        })
        .catch(error => {
            console.error('Error cargando servicios:', error);
            showError('section-services', error.message);
        });
}

// Función para reintentar carga
function retryLoad(sectionId) {
    const empresa = new URLSearchParams(window.location.search).get('empresa');
    if (!empresa) return;

    switch (sectionId) {
        case 'section-cliente':
            loadClientInfo(empresa);
            break;
        case 'section-payment':
            loadPaymentPortal(empresa);
            break;
        case 'section-services':
            loadActiveServices(empresa);
            break;
    }
}

// Cargar todas las secciones al inicio
document.addEventListener('DOMContentLoaded', () => {
    const empresa = new URLSearchParams(window.location.search).get('empresa');

    if (empresa) {
        console.log('📊 Cargando ficha de cliente para:', empresa);

        // Cargar secciones en paralelo para mejor rendimiento
        loadClientInfo(empresa);
        loadPaymentPortal(empresa);
        loadActiveServices(empresa);
    } else {
        console.warn('⚠️ No se especificó empresa en la URL');
    }
});
