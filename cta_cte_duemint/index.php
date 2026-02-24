<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Cuenta Corriente - Duemint</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header class="header-section">
            <div>
                <h1>💳 Cuenta Corriente Duemint</h1>
                <p>Consulta el estado de cuenta por RUT</p>
            </div>
            <div class="theme-switch" onclick="toggleTheme()">
                <div class="icon active" id="theme-light">☀️</div>
                <div class="icon" id="theme-dark">🌙</div>
            </div>
        </header>

        <section class="card">
            <form id="searchForm" class="search-form">
                <div class="form-group" style="flex: 2;">
                    <label for="rut">RUT del Cliente</label>
                    <input 
                        type="text" 
                        name="rut" 
                        id="rut" 
                        placeholder="12.345.678-9"
                        autocomplete="off"
                        maxlength="12"
                        required
                    >
                    <span class="error-message" id="rut-error"></span>
                </div>
                <button type="submit" id="btnBuscar">🔍 Buscar</button>
            </form>
        </section>

        <!-- Loading State -->
        <div id="loading" class="loading hidden">
            <div class="spinner"></div>
            <p>Consultando Duemint...</p>
        </div>

        <!-- Results Section -->
        <div id="results" class="hidden">
            <div class="section-card">
                <div class="section-header">
                    💳 Estado de Cuenta - Portal de Pago
                </div>
                <div class="section-body">
                    <div class="payment-portal">
                        <div class="payment-card warning">
                            <div class="payment-label">Por Vencer</div>
                            <div class="payment-amount" id="amount-por-vencer">$0</div>
                        </div>
                        
                        <div class="payment-card danger">
                            <div class="payment-label">Vencido</div>
                            <div class="payment-amount" id="amount-vencida">$0</div>
                        </div>
                        
                        <div class="payment-card">
                            <div class="payment-label">Cuenta Corriente</div>
                            <a href="#" id="portal-link" target="_blank" class="payment-link">
                                Ver Detalle Completo →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Not Found Message -->
        <div id="not-found" class="card alert-warning hidden">
            <h3>⚠️ Cliente no encontrado en Duemint</h3>
            <p>El RUT consultado no tiene cuenta corriente registrada en el sistema.</p>
            <a href="https://www.duemint.com" target="_blank" class="btn btn-secondary">
                Ir a Duemint →
            </a>
        </div>
    </div>

    <script>
        // Auto-format RUT as user types
        document.getElementById('rut').addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9kK]/g, '');
            
            if (value.length > 1) {
                let rut = value.slice(0, -1);
                let dv = value.slice(-1);
                
                // Add dots
                rut = rut.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                
                e.target.value = rut + '-' + dv;
            } else {
                e.target.value = value;
            }
        });

        // Handle form submission
        document.getElementById('searchForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const rut = document.getElementById('rut').value;
            const errorEl = document.getElementById('rut-error');
            
            // Clear previous error
            errorEl.textContent = '';
            
            // Basic validation
            if (!rut || rut.length < 3) {
                errorEl.textContent = 'Por favor ingrese un RUT válido';
                return;
            }
            
            // Show loading
            document.getElementById('loading').classList.remove('hidden');
            document.getElementById('results').classList.add('hidden');
            document.getElementById('not-found').classList.add('hidden');
            
            try {
                const response = await fetch('buscar.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'rut=' + encodeURIComponent(rut)
                });
                
                const data = await response.json();
                
                // Hide loading
                document.getElementById('loading').classList.add('hidden');
                
                if (data.success && data.encontrado) {
                    // Show results
                    document.getElementById('amount-por-vencer').textContent = 
                        '$' + Number(data.datos.por_vencer).toLocaleString('es-CL');
                    document.getElementById('amount-vencida').textContent = 
                        '$' + Number(data.datos.vencida).toLocaleString('es-CL');
                    document.getElementById('portal-link').href = data.datos.portal_url;
                    
                    document.getElementById('results').classList.remove('hidden');
                } else {
                    // Show not found
                    document.getElementById('not-found').classList.remove('hidden');
                }
            } catch (error) {
                document.getElementById('loading').classList.add('hidden');
                errorEl.textContent = 'Error al consultar. Por favor intente nuevamente.';
                console.error('Error:', error);
            }
        });

        // Theme toggle
        function setTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            if(theme === 'dark') {
                document.getElementById('theme-light').classList.remove('active');
                document.getElementById('theme-dark').classList.add('active');
            } else {
                document.getElementById('theme-dark').classList.remove('active');
                document.getElementById('theme-light').classList.add('active');
            }
        }

        function toggleTheme() {
            const current = document.documentElement.getAttribute('data-theme');
            setTheme(current === 'light' ? 'dark' : 'light');
        }

        // Load saved theme
        const savedTheme = localStorage.getItem('theme') || 'light';
        setTheme(savedTheme);
    </script>
</body>
</html>
