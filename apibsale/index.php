<?php
/**
 * Interfaz principal para el Maestro de Productos de BSale
 */
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maestro de Productos - BSale</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header class="header-section">
            <div>
                <h1>📦 Maestro de Productos BSale</h1>
                <p>Consulta y búsqueda en tiempo real de productos</p>
            </div>
            <div style="flex: 1; max-width: 300px; margin: 0 20px;">
                <select id="companySelect" class="company-selector" onchange="performSearch()">
                    <option value="tna_office" selected>TNA Office</option>
                    <option value="tna_solutions">TNA Solutions</option>
                </select>
            </div>
            <div class="theme-switch" onclick="toggleTheme()">
                <div class="icon active" id="theme-light">☀️</div>
                <div class="icon" id="theme-dark">🌙</div>
            </div>
        </header>

        <section class="card">
            <div class="tabs-container">
                <button class="tab-btn active" onclick="filterByCategory('all', this)">Todos <span id="count-all">0</span></button>
                <button class="tab-btn" onclick="filterByCategory(0, this)">Productos <span id="count-products">0</span></button>
                <button class="tab-btn" onclick="filterByCategory(1, this)">Servicios <span id="count-services">0</span></button>
                <button class="tab-btn" onclick="filterByCategory(2, this)">Pack <span id="count-packs">0</span></button>
            </div>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Buscar por nombre o SKU..." autocomplete="off" oninput="debouncedSearch()">
                <button onclick="clearSearch()" class="btn-clear">🧹 Limpiar</button>
            </div>
        </section>

        <section class="card">
            <div id="loading" class="loading-overlay hidden">
                <div class="spinner"></div>
                <p>Cargando productos...</p>
            </div>

            <div id="resultsTable" class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>SKU</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Inventario</th>
                            <th>Precio Neto</th>
                            <th>Precio Venta</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="productBody">
                        <!-- Se llena vía AJAX -->
                    </tbody>
                </table>
            </div>

            <div id="noResults" class="hidden" style="text-align: center; padding: 40px; color: var(--text-muted);">
                <p>No se encontraron productos para esta categoría o búsqueda.</p>
            </div>
        </section>
    </div>

    <script>
        let searchTimeout;
        let allProducts = [];
        let currentFilter = 'all';

        function debouncedSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch();
            }, 400); // 400ms debounce
        }

        async function performSearch() {
            const query = document.getElementById('searchInput').value;
            const company = document.getElementById('companySelect').value;
            const loading = document.getElementById('loading');
            const table = document.getElementById('resultsTable');
            const tbody = document.getElementById('productBody');
            const noResults = document.getElementById('noResults');

            loading.classList.remove('hidden');
            if (query === '') table.classList.add('hidden'); // Solo ocultar si es carga inicial/limpia
            noResults.classList.add('hidden');

            try {
                const response = await fetch(`buscar_productos.php?search=${encodeURIComponent(query)}&company=${company}`);
                const result = await response.json();

                loading.classList.add('hidden');

                if (result.success) {
                    allProducts = result.products;
                    updateCounts(allProducts);
                    renderProducts(currentFilter);
                } else {
                    table.classList.add('hidden');
                    noResults.classList.remove('hidden');
                }
            } catch (error) {
                loading.classList.add('hidden');
                alert('Error al conectar con la API de BSale');
                console.error(error);
            }
        }

        function updateCounts(products) {
            document.getElementById('count-all').textContent = products.length;
            document.getElementById('count-products').textContent = products.filter(p => p.classification === 0).length;
            document.getElementById('count-services').textContent = products.filter(p => p.classification === 1).length;
            document.getElementById('count-packs').textContent = products.filter(p => p.classification === 2).length;
        }

        function filterByCategory(category, btn) {
            currentFilter = category;
            
            // Actualizar UI de botones
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            renderProducts(category);
        }

        function renderProducts(category) {
            const tbody = document.getElementById('productBody');
            const table = document.getElementById('resultsTable');
            const noResults = document.getElementById('noResults');
            const currency = { symbol: '$', name: 'CLP' }; // Simplificado, idealmente viene de la última búsqueda exitosa

            tbody.innerHTML = '';
            
            const filtered = category === 'all' 
                ? allProducts 
                : allProducts.filter(p => p.classification === category);

            if (filtered.length > 0) {
                table.classList.remove('hidden');
                noResults.classList.add('hidden');
                
                filtered.forEach(p => {
                    const tr = document.createElement('tr');
                    const stockStyle = p.stock <= 0 ? 'color: #e74c3c; font-weight: bold;' : 'font-weight: 600;';
                    
                    tr.innerHTML = `
                        <td>${p.id}</td>
                        <td><code>${p.code}</code></td>
                        <td><strong>${p.name}</strong><br><small style="color: var(--text-muted)">${p.variant_description}</small></td>
                        <td><span class="type-label">${p.type_name}</span></td>
                        <td style="${stockStyle}">${p.stock}</td>
                        <td class="price">${formatPrice(p.price, { symbol: p.price > 1000 ? '$' : 'UF', name: p.price > 1000 ? 'CLP' : 'UF' })}</td>
                        <td class="price">${formatPrice(p.tax_included_price, { symbol: p.price > 1000 ? '$' : 'UF', name: p.price > 1000 ? 'CLP' : 'UF' })}</td>
                        <td><span class="badge ${p.state === 'Activo' ? 'badge-success' : 'badge-danger'}">${p.state}</span></td>
                        <td><a href="${p.link}" target="_blank" class="btn-link">Ver ↗</a></td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                table.classList.add('hidden');
                noResults.classList.remove('hidden');
            }
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            performSearch();
        }

        function formatPrice(value, currency) {
            const isUF = currency.name === 'UF' || currency.symbol.includes('UF');
            return new Intl.NumberFormat('es-CL', { 
                style: 'currency', 
                currency: isUF ? 'CLF' : 'CLP',
                minimumFractionDigits: isUF ? 2 : 0,
                maximumFractionDigits: isUF ? 2 : 0
            }).format(value).replace('CLF', 'UF');
        }

        function toggleTheme() {
            const current = document.documentElement.getAttribute('data-theme');
            const target = current === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', target);
            
            document.getElementById('theme-light').classList.toggle('active');
            document.getElementById('theme-dark').classList.toggle('active');
            
            localStorage.setItem('theme', target);
        }

        // Carga inicial
        window.onload = () => {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                toggleTheme();
            }
            performSearch(); // Carga inicial de productos
        };

        // Escuchar Enter en el input
        document.getElementById('searchInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') performSearch();
        });
    </script>
</body>
</html>
