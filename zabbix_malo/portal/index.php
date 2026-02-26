<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zabbix Central Hub — TNA Solutions</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-base:     #0a0e1a;
            --border:      rgba(255,255,255,0.08);
            --header-bg:   rgba(10,14,26,0.95);
            --accent:      #5c6bc0;
            --text-main:   #e8eaf6;
            --text-muted:  #7986cb;
            --glass:       rgba(255, 255, 255, 0.03);
            --glass-h:     rgba(255, 255, 255, 0.08);
        }

        body.light-theme {
            --bg-base:     #f0f2f8;
            --border:      rgba(0,0,0,0.10);
            --header-bg:   rgba(255,255,255,0.95);
            --accent:      #4c5fc4;
            --text-main:   #1a1f36;
            --text-muted:  #4a5568;
            --glass:       rgba(0, 0, 0, 0.02);
            --glass-h:     rgba(0, 0, 0, 0.05);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        html, body { 
            height: 100%; 
            overflow: hidden; 
            font-family: 'Inter', sans-serif;
            background: var(--bg-base);
            color: var(--text-main);
            transition: background 0.3s;
        }

        /* Portal Header */
        .portal-header {
            height: 64px;
            background: var(--header-bg);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            backdrop-filter: blur(20px);
            z-index: 1000;
            position: relative;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 16px;
            letter-spacing: -0.5px;
        }

        .brand-logo {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--accent), #26a69a);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: white;
        }

        /* Tab Navigation */
        .nav-tabs {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            background: var(--glass);
            padding: 4px;
            border-radius: 12px;
            border: 1px solid var(--border);
            gap: 4px;
        }

        .nav-btn {
            padding: 8px 20px;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-btn:hover { background: var(--glass-h); color: var(--text-main); }
        .nav-btn.active {
            background: var(--accent);
            color: white;
            box-shadow: 0 4px 12px rgba(92, 107, 192, 0.3);
        }

        /* Controls */
        .controls {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .theme-toggle {
            background: var(--glass);
            border: 1px solid var(--border);
            color: var(--text-muted);
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .theme-toggle:hover { background: var(--glass-h); transform: scale(1.05); }

        /* Main Viewport */
        .viewport {
            height: calc(100% - 64px);
            position: relative;
        }

        iframe {
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            border: none;
            display: none;
        }

        iframe.active {
            display: block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .brand span { display: none; }
            .nav-tabs { position: static; transform: none; margin: 0 auto; }
            .portal-header { padding: 0 12px; }
        }
    </style>
</head>
<body>

    <header class="portal-header">
        <div class="brand">
            <div class="brand-logo" onclick="window.top.location.href='https://intranet.icontel.cl/'" style="cursor:pointer">🏠</div>
            <span>PORTAL ZABBIX</span>
        </div>

        <nav class="nav-tabs">
            <button class="nav-btn active" onclick="switchTab('monitoreo')">
                <span>📊</span> Monitoreo
            </button>
            <button class="nav-btn" onclick="switchTab('bandwidth')">
                <span>🔌</span> Ancho de Banda
            </button>
        </nav>

        <div class="controls">
            <button class="theme-toggle" onclick="toggleTheme()" title="Cambiar tema">☀️</button>
        </div>
    </header>

    <main class="viewport">
        <iframe src="https://intranet.icontel.cl/zabbix/index.php" id="frame-monitoreo" class="active"></iframe>
        <iframe src="https://intranet.icontel.cl/zabbix/consumo/index.php" id="frame-bandwidth"></iframe>
    </main>

    <script>
        function switchTab(tab) {
            // Update buttons
            document.querySelectorAll('.nav-btn').forEach(btn => {
                const text = btn.textContent.toLowerCase();
                const isActive = (tab === 'monitoreo' && text.includes('monitoreo')) || 
                                 (tab === 'bandwidth' && text.includes('ancho'));
                btn.classList.toggle('active', isActive);
            });

            // Update iframes
            document.querySelectorAll('iframe').forEach(frame => {
                frame.classList.toggle('active', frame.id === `frame-${tab}`);
            });

            // Optional: Store preference
            localStorage.setItem('zabbix-portal-tab', tab);
        }

        function initTheme() {
            const savedTheme = localStorage.getItem('zabbix-theme') || 'dark';
            if (savedTheme === 'light') {
                document.body.classList.add('light-theme');
                document.querySelector('.theme-toggle').textContent = '🌙';
            }
        }

        function toggleTheme() {
            document.body.classList.toggle('light-theme');
            const isLight = document.body.classList.contains('light-theme');
            document.querySelector('.theme-toggle').textContent = isLight ? '🌙' : '☀️';
            localStorage.setItem('zabbix-theme', isLight ? 'light' : 'dark');
            
            // Proactively notify the iframes if they support direct theme updates 
            // though they will also pick it up from localStorage on reload
        }

        // Restore tab
        const lastTab = localStorage.getItem('zabbix-portal-tab');
        if (lastTab) switchTab(lastTab);

        initTheme();
    </script>
</body>
</html>