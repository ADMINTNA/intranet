/**
 * ============================================================
 * WHM Server Report - Frontend Application
 * Icontel Intranet
 * ============================================================
 * Archivo    : app.js
 * Path       : /home/icontel/public_html/intranet/whm-report/assets/js/app.js
 * Version    : 1.0.0
 * Fecha      : 2026-02-25 20:57:00
 * Proyecto   : WHM Server Report - Icontel Intranet
 * Autor      : Icontel Dev Team
 * ============================================================
 */

const App = {
    data: null,
    currentTab: 'all',
    sortState: {
        allAccounts: { key: 'disk_used', dir: 'desc' }
    },
    use24h: true, // Default to 24h as requested
    packages: [], // Guardar planes disponibles

    // ---- INIT ----
    async init() {
        this.showLoader(true);
        this.loadSettings(); // Cargar ajustes persistentes si existen
        this.bindEvents();
        await this.loadReport();
        this.showLoader(false);
    },

    loadSettings() {
        const saved = localStorage.getItem('whm_use24h');
        if (saved !== null) {
            this.use24h = saved === 'true';
        }
        const toggle = document.getElementById('toggle24h');
        if (toggle) toggle.checked = this.use24h;
    },

    saveSettings() {
        localStorage.setItem('whm_use24h', this.use24h);
    },

    // Formatear números con separador de miles
    formatNumber(num) {
        if (num === null || num === undefined) return '--';
        return Number(num).toLocaleString('es-CL');
    },

    // Formatear porcentajes con 2 decimales
    formatPercent(num) {
        if (num === null || num === undefined) return '--';
        return Number(num).toLocaleString('es-CL', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    },

    // Formatear fecha y hora según el estado de use24h
    formatDateTime(dateInput, includeTime = true) {
        if (!dateInput) return 'N/A';
        const date = dateInput instanceof Date ? dateInput : new Date(dateInput);
        if (isNaN(date.getTime())) return 'Fecha inválida';

        const dateOptions = { day: '2-digit', month: '2-digit', year: 'numeric' };
        const timeOptions = { hour: '2-digit', minute: '2-digit', hour12: !this.use24h };

        const dStr = date.toLocaleDateString('es-CL', dateOptions);
        if (!includeTime) return dStr;

        const tStr = date.toLocaleTimeString('es-CL', timeOptions);
        return `${dStr} ${tStr}`;
    },

    bindEvents() {
        document.getElementById('btnRefresh').addEventListener('click', () => this.refresh());

        const toggle24h = document.getElementById('toggle24h');
        if (toggle24h) {
            toggle24h.addEventListener('change', (e) => {
                this.use24h = e.target.checked;
                this.saveSettings();
                this.render(); // Re-render everything with new time format
            });
        }

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.switchTab(e.target.closest('.tab-btn').dataset.tab));
        });
    },

    async refresh() {
        const btn = document.getElementById('btnRefresh');
        btn.innerHTML = '* Cargando...';
        btn.disabled = true;
        this.showLoader(true);
        await this.loadReport();
        this.showLoader(false);
        btn.innerHTML = '* Actualizar';
        btn.disabled = false;
    },

    showLoader(show) {
        const loader = document.getElementById('loader');
        if (show) loader.classList.remove('hidden');
        else loader.classList.add('hidden');
    },

    // ---- API ----
    async loadReport() {
        try {
            const res = await fetch(`api/index.php?action=report&_t=${Date.now()}`);
            const data = await res.json();
            if (data.error) {
                this.showError(data.message);
                return;
            }
            this.data = data;
            // Iniciar con todos los dueños seleccionados si existe el filtro
            if (this.data.owners && !this.selectedOwners) {
                this.selectedOwners = this.data.owners.map(o => o.name);
            }
            this.renderOwnerFilter();

            // Cargar paquetes
            await this.loadPackages();

            this.render();
        } catch (err) {
            this.showError('No se pudo conectar con la API. Verifica la configuracion en config/config.php');
        }
    },

    async loadPackages() {
        try {
            const res = await fetch(`api/index.php?action=packages&_t=${Date.now()}`);
            const data = await res.json();
            if (data.packages) {
                this.packages = data.packages.map(p => p.name).sort((a, b) => a.localeCompare(b));
            }
        } catch (err) {
            console.error('Error cargando planes:', err);
        }
    },

    async loadAccountDetail(user) {
        try {
            const res = await fetch(`api/index.php?action=account_detail&user=${encodeURIComponent(user)}`);
            return await res.json();
        } catch (err) {
            return null;
        }
    },

    // ---- RENDER ----
    render() {
        if (!this.data) return;
        this.renderSummary();
        this.renderDiskBar();
        this.renderTopSpace();
        this.renderTopActivity();
        this.renderInactiveAccounts();
        this.renderAlerts();
        this.renderAllAccounts();
        this.renderHealth();
        this.updateTimestamp();
    },

    renderSummary() {
        const s = this.data.summary;
        document.getElementById('totalAccounts').textContent = this.formatNumber(s.total_accounts);
        document.getElementById('activeAccounts').textContent = this.formatNumber(s.active_accounts);
        document.getElementById('suspendedAccounts').textContent = this.formatNumber(s.suspended_accounts);
        const diskUsedEl = document.getElementById('diskUsed');
        const diskPercentEl = document.getElementById('diskPercent');

        diskUsedEl.textContent = s.total_disk_used_hr;
        diskPercentEl.textContent = `${s.total_disk_limit_hr} (${this.formatPercent(s.disk_percent)}%)`;

        // Dashboard semaphore colors for disk
        const diskCard = diskUsedEl.closest('.summary-card');
        diskCard.classList.remove('purple', 'green', 'yellow', 'orange', 'red', 'emergency');
        diskUsedEl.className = 'card-value';

        if (s.disk_percent >= 95) {
            diskCard.classList.add('emergency');
            diskUsedEl.classList.add('emergency');
        } else if (s.disk_percent >= 90) {
            diskCard.classList.add('red');
            diskUsedEl.classList.add('red');
        } else if (s.disk_percent >= 85) {
            diskCard.classList.add('orange');
            diskUsedEl.classList.add('orange');
        } else if (s.disk_percent >= 75) {
            diskCard.classList.add('yellow');
            diskUsedEl.classList.add('yellow');
        } else {
            diskCard.classList.add('green');
            diskUsedEl.classList.add('green');
        }

        // Contar inactivas (sin bandwidth / poco movimiento)
        const inactive = this.data.accounts.filter(a => a.bw_used < 1024 && !a.suspended).length;
        document.getElementById('inactiveAccounts').textContent = this.formatNumber(inactive);
    },

    renderDiskBar() {
        const s = this.data.summary;
        const bar = document.getElementById('diskBarFill');
        const stats = document.getElementById('diskBarStats');

        bar.style.width = Math.min(s.disk_percent, 100) + '%';
        bar.className = 'disk-bar-fill';
        if (s.disk_percent >= 95) bar.classList.add('emergency');
        else if (s.disk_percent >= 90) bar.classList.add('danger');
        else if (s.disk_percent >= 85) bar.classList.add('orange');
        else if (s.disk_percent >= 75) bar.classList.add('warning');

        stats.textContent = `${s.total_disk_used_hr} / ${s.total_disk_limit_hr} (${this.formatPercent(s.disk_percent)}%)`;
    },

    renderTopSpace() {
        const containers = ['topSpaceTable', 'topSpaceTableTab'].map(id => document.getElementById(id)).filter(el => el);
        if (containers.length === 0) return;

        const activeBtn = document.querySelector('#topSpaceToggleOverview .toggle-btn.active, #topSpaceToggle .toggle-btn.active');
        const mode = activeBtn?.dataset.mode || 'most';
        const totalUsed = this.data.summary.total_disk_used || 1;

        let sorted = [...this.data.accounts].sort((a, b) => {
            return mode === 'most' ? b.disk_used - a.disk_used : a.disk_used - b.disk_used;
        });

        if (mode === 'least') {
            sorted = sorted.filter(a => a.disk_used > 0);
        }

        const top10 = sorted.slice(0, 10);
        if (top10.length === 0) {
            containers.forEach(c => c.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:20px">Sin datos de espacio</td></tr>');
            return;
        }

        const html = top10.map((acct, i) => {
            const relPct = (acct.disk_used / totalUsed) * 100;
            const barClass = acct.severity?.class || 'green';

            return `
                <tr class="animate-in delay-${Math.min(i + 1, 6)}" onclick="App.showDetail('${acct.user}')" style="cursor:pointer">
                    <td><span class="rank ${i < 3 ? 'rank-' + (i + 1) : 'rank-n'}">${i + 1}</span></td>
                    <td><span class="td-user">${acct.user}</span></td>
                    <td class="td-domain">${acct.domain}</td>
                    <td class="td-mono">${acct.disk_used_hr}</td>
                    <td class="td-mono">${acct.disk_limit_hr}</td>
                    <td>
                        <span class="td-mono">${this.formatPercent(relPct)}%</span>
                        <span class="mini-bar"><span class="mini-bar-fill ${barClass}" style="width:${Math.min(relPct * 2, 100)}%"></span></span>
                    </td>
                    <td class="td-mono">${acct.plan}</td>
                </tr>`;
        }).join('');

        containers.forEach(c => c.innerHTML = html);
    },

    renderHealth() {
        const container = document.getElementById('tnaHealthWidget');
        if (!container || !this.data.server || !this.data.server.health) return;

        const h = this.data.server.health;
        const cores = h.load.cores || 1;
        const loadValue = h.load.one || 0;

        // CPU Logic (Política TNA)
        let cpuClass = 'green';
        let cpuIcon = '🟢';
        if (loadValue > cores * 2) { cpuClass = 'red'; cpuIcon = '🔴'; }
        else if (loadValue > cores * 1.5) { cpuClass = 'orange'; cpuIcon = '🟠'; }
        else if (loadValue > cores) { cpuClass = 'yellow'; cpuIcon = '🟡'; }

        // RAM Logic (Política TNA)
        const ramPct = h.memory.percent || 0;
        let ramClass = 'green';
        let ramIcon = '🟢';
        if (ramPct >= 90) { ramClass = 'red'; ramIcon = '🔴'; }
        else if (ramPct >= 85) { ramClass = 'orange'; ramIcon = '🟠'; }
        else if (ramPct >= 75) { ramClass = 'yellow'; ramIcon = '🟡'; }

        // Mail Queue Logic (Política TNA)
        const mq = h.mail_queue || 0;
        let mqClass = 'green';
        let mqIcon = '🟢';
        if (mq > 500) { mqClass = 'red'; mqIcon = '🔴'; }
        else if (mq > 300) { mqClass = 'orange'; mqIcon = '🟠'; }
        else if (mq > 100) { mqClass = 'yellow'; mqIcon = '🟡'; }

        // Backup Logic (Política TNA)
        const b = h.backups || {};
        const bStatus = b.status || 'unknown';
        let bClass = (bStatus === 'complete' || bStatus === 'active') ? 'green' : (bStatus === 'running' ? 'yellow' : 'red');
        let bIcon = bClass === 'green' ? '🟢' : (bClass === 'yellow' ? '🟡' : '🔴');

        // Inode Logic (Política TNA)
        const i = h.inodes || { percent: 0, status: { class: 'green', code: '🟢' } };
        const iClass = i.status.class || 'green';
        const iIcon = i.status.code || '🟢';

        container.innerHTML = `
            <div class="health-pill">
                <span class="label">CPU Load (1m) <small style="font-size:8px;opacity:0.4">(h.load)</small></span>
                <span class="value ${cpuClass}">${cpuIcon} ${loadValue.toFixed(2)} <span style="font-size:10px;color:var(--text-muted);font-weight:400;margin-left:4px">/ ${cores} cores</span></span>
            </div>
            <div class="health-pill">
                <span class="label">RAM Usage <small style="font-size:8px;opacity:0.4">(h.memory)</small></span>
                <span class="value ${ramClass}">${ramIcon} ${ramPct}% <span style="font-size:10px;color:var(--text-muted);font-weight:400;margin-left:4px">(${h.memory.used_hr || '0 B'})</span></span>
            </div>
            <div class="health-pill">
                <span class="label">Mail Queue <small style="font-size:8px;opacity:0.4">(h.mail_queue)</small></span>
                <span class="value ${mqClass}">${mqIcon} ${mq} <span style="font-size:10px;color:var(--text-muted);font-weight:400;margin-left:4px">waiting</span></span>
            </div>
            <div class="health-pill">
                <span class="label">Total Inodes <small style="font-size:8px;opacity:0.4">(h.inodes)</small></span>
                <span class="value ${iClass}">${iIcon} ${i.percent}% <span style="font-size:10px;color:var(--text-muted);font-weight:400;margin-left:4px">filesystem</span></span>
            </div>
            <div class="health-pill">
                <span class="label">Backups <small style="font-size:8px;opacity:0.4">(h.backups)</small></span>
                <span class="value ${bClass}">${bIcon} ${bStatus.toUpperCase()} <span style="font-size:10px;color:var(--text-muted);font-weight:400;margin-left:4px">${b.last_run || ''}</span></span>
            </div>
        `;
    },

    renderTopActivity() {
        const containers = ['topActivityTable', 'topActivityTableTab'].map(id => document.getElementById(id)).filter(el => el);
        if (containers.length === 0) return;

        const activeBtn = document.querySelector('#topActivityToggleOverview .toggle-btn.active, #topActivityToggle .toggle-btn.active');
        const mode = activeBtn?.dataset.mode || 'most';
        const totalBw = this.data.summary.total_bw_used || 1;

        let sorted = [...this.data.accounts].sort((a, b) => {
            return mode === 'most' ? (b.bw_used || 0) - (a.bw_used || 0) : (a.bw_used || 0) - (b.bw_used || 0);
        });

        if (mode === 'least') {
            sorted = sorted.filter(a => !a.suspended);
        }

        const top10 = sorted.slice(0, 10);
        if (top10.length === 0) {
            containers.forEach(c => c.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:20px">Sin datos de tráfico</td></tr>');
            return;
        }

        const html = top10.map((acct, i) => {
            const bw = acct.bw_used || 0;
            const relPct = (bw / totalBw) * 100;
            return `
                <tr class="animate-in delay-${Math.min(i + 1, 6)}" onclick="App.showDetail('${acct.user}')" style="cursor:pointer">
                    <td><span class="rank ${i < 3 ? 'rank-' + (i + 1) : 'rank-n'}">${i + 1}</span></td>
                    <td><span class="td-user">${acct.user}</span></td>
                    <td class="td-domain">${acct.domain}</td>
                    <td class="td-mono">${acct.bw_used_hr || '0 B'}</td>
                    <td class="td-mono">${acct.bw_limit_hr || 'unlimited'}</td>
                    <td>
                        <span class="td-mono" style="font-size:11px">${this.formatPercent(relPct)}%</span>
                        <span class="mini-bar" style="width:100px"><span class="mini-bar-fill green" style="width:${Math.min(relPct * 2, 100)}%"></span></span>
                    </td>
                    <td>${acct.suspended ? '<span class="badge badge-suspended">Suspendida</span>' : '<span class="badge badge-active">Activa</span>'}</td>
                </tr>`;
        }).join('');

        containers.forEach(c => c.innerHTML = html);
    },

    renderInactiveAccounts() {
        const container = document.getElementById('inactiveTable');
        const filterDays = parseInt(document.getElementById('inactiveDaysFilter')?.value || '0');

        // Cuentas con muy poco o nada de bandwidth = sin movimiento
        let inactive = this.data.accounts.filter(a => a.bw_used < 1024 && !a.suspended);

        // Ordenar por dias desde creacion (mas antiguas primero)
        inactive.sort((a, b) => b.days_since_creation - a.days_since_creation);

        if (filterDays > 0) {
            inactive = inactive.filter(a => a.days_since_creation >= filterDays);
        }

        document.getElementById('inactiveCount').textContent = `${this.formatNumber(inactive.length)} cuentas`;

        container.innerHTML = inactive.map((acct, i) => {
            const days = acct.days_since_creation;
            let dotClass = 'ok';
            let badgeClass = 'badge-active';
            let label = 'Reciente';

            if (days >= 180) { dotClass = 'critical'; badgeClass = 'badge-suspended'; label = 'Abandonada'; }
            else if (days >= 60) { dotClass = 'critical'; badgeClass = 'badge-warning'; label = 'Critica'; }
            else if (days >= 30) { dotClass = 'warning'; badgeClass = 'badge-info'; label = 'Preventiva'; }

            return `
                <tr onclick="App.showDetail('${acct.user}')" style="cursor:pointer">
                    <td>
                        <span class="inactive-dot ${dotClass}"></span>
                        <span class="td-user">${acct.user}</span>
                    </td>
                    <td class="td-domain">${acct.domain}</td>
                    <td class="td-mono">${acct.disk_used_hr} / ${acct.disk_limit_hr} ${acct.disk_limit > 0 ? `(${this.formatPercent(acct.disk_percent)}%)` : '(<span class="text-danger">Ilimitado</span>)'}</td>
                    <td class="td-mono">${this.formatNumber(days)} dias</td>
                    <td class="td-mono">${acct.start_date}</td>
                    <td><span class="badge ${badgeClass}">${label}</span></td>
                    <td class="td-mono">${acct.plan}</td>
                </tr>`;
        }).join('');

        if (inactive.length === 0) {
            container.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted)">No se encontraron cuentas inactivas con estos filtros</td></tr>';
        }
    },

    renderAlerts() {
        const container = document.getElementById('alertsTable');
        if (!container) return;

        // Criterios de Alerta: Uso Disco > 80% o Uso Disco por casilla > 15GB
        const CRITICAL_PERCENT = 80;
        const CRITICAL_MAILBOX_BYTES = 15 * 1024 * 1024 * 1024;

        let alerts = this.data.accounts.filter(acct => {
            // Cuentas con riesgo real o crítico según Política TNA (🟠, 🔴, 🚨)
            return !acct.suspended && ['high', 'critical', 'emergency'].includes(acct.severity?.level);
        });

        // Ordenar por espacio ocupado (descendente)
        alerts.sort((a, b) => b.disk_used - a.disk_used);

        const badge = document.getElementById('alertCount');
        if (badge) {
            badge.textContent = alerts.length + ' cuentas críticas';
            if (alerts.length > 0) {
                badge.style.background = 'var(--danger,#ef4444)';
                badge.classList.add('pulse'); // Optionally add an animation class
            } else {
                badge.style.background = 'var(--success,#22c55e)';
            }
        }

        if (alerts.length === 0) {
            container.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:40px;">🎉 Excelente. No hay cuentas con alertas críticas de migración actualmente.</td></tr>';
            return;
        }

        container.innerHTML = alerts.map(acct => {
            const barClass = acct.severity?.class || 'green';
            return `
                <tr class="table-severity-${acct.severity?.level || 'none'}">
                    <td onclick="App.showDetail('${acct.user}')" style="cursor:pointer"><span class="td-user">${acct.user}</span></td>
                    <td class="td-domain" onclick="App.showDetail('${acct.user}')" style="cursor:pointer">${acct.domain}</td>
                    <td class="td-mono text-danger fw-bold" onclick="App.showDetail('${acct.user}')" style="cursor:pointer">${acct.disk_used_hr}</td>
                    <td onclick="App.showDetail('${acct.user}')" style="cursor:pointer" style="min-width: 60px;">
                        <span class="mini-bar"><span class="mini-bar-fill ${barClass}" style="width:${acct.disk_limit > 0 ? Math.min(acct.disk_percent, 100) : (acct.disk_used > 1024 * 1024 * 1024 ? 100 : 0)}%"></span></span>
                        <div style="font-size:10px;text-align:right;margin-top:2px;">
                            ${acct.disk_limit > 0 ? this.formatPercent(acct.disk_percent) + '%' : '<span class="text-danger">Ilimitado</span>'} 
                            ${acct.severity?.code || ''}
                        </div>
                    </td>
                    <td class="td-mono" onclick="App.showDetail('${acct.user}')" style="cursor:pointer">${acct.disk_limit_hr}</td>
                    <td onclick="App.showDetail('${acct.user}')" style="cursor:pointer"><span class="badge badge-active">Activa</span></td>
                    <td style="text-align:center;">
                        <a href="client_report_template.php?user=${encodeURIComponent(acct.user)}" target="_blank" title="Generar Informe Cliente" style="text-decoration:none;font-size:18px;" onclick="event.stopPropagation()">📊</a>
                    </td>
                </tr>`;
        }).join('');
    },

    renderAllAccounts() {
        const container = document.getElementById('allAccountsTable');
        const search = (document.getElementById('searchInput')?.value || '').toLowerCase();
        const statusFilter = document.getElementById('statusFilter')?.value || 'all';

        let filtered = [...this.data.accounts];

        if (search) {
            filtered = filtered.filter(a =>
                a.user.toLowerCase().includes(search) ||
                a.domain.toLowerCase().includes(search) ||
                a.email.toLowerCase().includes(search) ||
                a.plan.toLowerCase().includes(search)
            );
        }

        if (statusFilter === 'active') filtered = filtered.filter(a => !a.suspended);
        else if (statusFilter === 'suspended') filtered = filtered.filter(a => a.suspended);

        // Filter by Owner
        if (this.selectedOwners) {
            filtered = filtered.filter(a => this.selectedOwners.includes(a.owner));
        }

        // Sorting
        const sortKey = this.sortState.allAccounts?.key;
        const sortDir = this.sortState.allAccounts?.dir || 'asc';
        if (sortKey) {
            filtered.sort((a, b) => {
                let va = a[sortKey], vb = b[sortKey];
                if (typeof va === 'string') va = va.toLowerCase();
                if (typeof vb === 'string') vb = vb.toLowerCase();
                if (va < vb) return sortDir === 'asc' ? -1 : 1;
                if (va > vb) return sortDir === 'asc' ? 1 : -1;
                return 0;
            });
        }

        document.getElementById('filteredCount').textContent = `${this.formatNumber(filtered.length)} cuentas`;

        // Opciones del select de planes
        const planOptionsHtml = (currentPlan) => {
            if (!this.packages || this.packages.length === 0) {
                return `<option value="${currentPlan}">${currentPlan}</option>`;
            }
            return this.packages.map(p =>
                `<option value="${p}" ${p === currentPlan ? 'selected' : ''}>${p}</option>`
            ).join('');
        };

        container.innerHTML = filtered.map(acct => {
            const sev = acct.severity || { class: 'green', code: '🟢', level: 'info' };
            const isAlert = ['high', 'critical', 'emergency'].includes(sev.level);
            const rowClass = isAlert ? `table-severity-${sev.level}` : '';
            const diskClass = isAlert ? 'text-danger fw-bold' : 'td-mono';

            return `
                <tr class="${rowClass}">
                    <td onclick="App.showDetail('${acct.user}')" style="cursor:pointer">
                        <span style="font-size:14px;margin-right:4px;">${sev.code}</span>
                        <span class="td-user">${acct.user}</span>
                    </td>
                    <td class="td-domain" onclick="App.showDetail('${acct.user}')" style="cursor:pointer">${acct.domain}</td>
                    <td class="${diskClass}" onclick="App.showDetail('${acct.user}')" style="cursor:pointer">
                        ${acct.disk_used_hr} / ${acct.disk_limit_hr} ${acct.disk_limit > 0 ? `(${this.formatPercent(acct.disk_percent)}%)` : ''}
                    </td>
                    <td onclick="App.showDetail('${acct.user}')" style="cursor:pointer" style="min-width: 100px;">
                        <span class="mini-bar" style="height:8px; border-radius:10px;"><span class="mini-bar-fill ${sev.class}" style="width:${acct.disk_limit > 0 ? Math.min(acct.disk_percent, 100) : (acct.disk_used > 1024 * 1024 * 1024 ? 100 : 0)}%"></span></span>
                        <div style="font-size:10px; text-align:right; margin-top:3px; font-weight:600;">
                            ${acct.disk_limit > 0 ? this.formatPercent(acct.disk_percent) + '%' : '<span class="text-danger">!!!ILIMITADO!!!</span>'}
                        </div>
                    </td>
                    <!-- AGENT_VERIFY_FLAG_v1 -->
                    <td class="td-mono" onclick="App.showDetail('${acct.user}')" style="cursor:pointer">${acct.bw_used_hr}</td>
                    <td onclick="App.showDetail('${acct.user}')" style="cursor:pointer">${acct.suspended ? '<span class="badge badge-suspended">Suspendida</span>' : '<span class="badge badge-active">Activa</span>'}</td>
                    <td class="td-mono" onclick="App.showDetail('${acct.user}')" style="cursor:pointer">${this.formatNumber(acct.email_count)} / ${acct.email_limit}</td>
                    <td class="td-mono" onclick="App.showDetail('${acct.user}')" style="cursor:pointer;text-align:center;">
                        ${acct.forwarder_count > 0 ? `<span class="badge badge-info" style="background:#6c5ce7;color:#fff;">${acct.forwarder_count}</span>` : '<span style="color:var(--text-muted)">-</span>'}
                    </td>
                    <td>
                        <select class="form-select form-select-sm td-mono" style="min-width: 120px;" onchange="App.changePlan('${acct.user}', this.value, '${acct.plan}')">
                            ${planOptionsHtml(acct.plan)}
                        </select>
                    </td>
                    <td class="td-mono" onclick="App.showDetail('${acct.user}')" style="cursor:pointer">${acct.owner}</td>
                    <td style="text-align:center;">
                        <a href="client_report_template.php?user=${encodeURIComponent(acct.user)}" target="_blank" title="Generar Informe Cliente" style="text-decoration:none;font-size:18px;" onclick="event.stopPropagation()">📊</a>
                    </td>
                </tr>`;
        }).join('');
    },

    async changePlan(user, newPlan, oldPlan) {
        if (!confirm(`¿Estás seguro de cambiar el plan del usuario "${user}" a "${newPlan}"?`)) {
            // Revertir selección si cancela
            this.renderAllAccounts();
            return;
        }

        try {
            // Show some loading state if possible
            const formData = new FormData();
            formData.append('user', user);
            formData.append('pkg', newPlan);

            const res = await fetch('api/index.php?action=change_package', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            if (data.error || !data.metadata?.result) {
                const msg = data.message || data.metadata?.reason || 'Error desconocido';
                throw new Error(msg);
            }

            // Actualizar el plan en los datos locales para que se refleje inmediatamente
            const acct = this.data.accounts.find(a => a.user === user);
            if (acct) {
                acct.plan = newPlan;
            }

            this.renderAllAccounts();
            alert(`Plan de "${user}" cambiado exitosamente a "${newPlan}".`);
        } catch (err) {
            console.error('Error cambiando plan:', err);
            alert(`Error al cambiar el plan: ${err.message}`);
            this.renderAllAccounts(); // Revertir visualmente en caso de error
        }
    },

    updateTimestamp() {
        const ts = this.data.summary?.generated_at;
        document.getElementById('timestamp').textContent = this.formatDateTime(ts);
    },

    // ---- TABS ----
    switchTab(tabId) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.querySelector(`.tab-btn[data-tab="${tabId}"]`).classList.add('active');
        document.getElementById(`tab-${tabId}`).classList.add('active');
        this.currentTab = tabId;

        if (tabId === 'firewall') {
            this.loadFirewall();
            this.loadWhitelisted();
        }
    },

    // ---- TOGGLES ----
    toggleTopSpace(mode, btn) {
        // Sincronizar todos los toggles de espacio (Overview y Tab)
        document.querySelectorAll('#topSpaceToggleOverview .toggle-btn, #topSpaceToggle .toggle-btn').forEach(b => {
            if (b.dataset.mode === mode) b.classList.add('active');
            else b.classList.remove('active');
        });
        this.renderTopSpace();
    },

    toggleTopActivity(mode, btn) {
        // Sincronizar todos los toggles de actividad (Overview y Tab)
        document.querySelectorAll('#topActivityToggleOverview .toggle-btn, #topActivityToggle .toggle-btn').forEach(b => {
            if (b.dataset.mode === mode) b.classList.add('active');
            else b.classList.remove('active');
        });
        this.renderTopActivity();
    },

    // ---- SORT ----
    sortTable(table, key) {
        if (!this.sortState[table]) this.sortState[table] = {};
        if (this.sortState[table].key === key) {
            this.sortState[table].dir = this.sortState[table].dir === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortState[table] = { key, dir: 'asc' };
        }

        // Update header classes
        document.querySelectorAll(`#${table}Header th`).forEach(th => {
            th.classList.remove('sorted-asc', 'sorted-desc');
        });
        const th = document.querySelector(`#${table}Header th[data-sort="${key}"]`);
        if (th) th.classList.add(this.sortState[table].dir === 'asc' ? 'sorted-asc' : 'sorted-desc');

        this.renderAllAccounts();
    },

    // ---- FILTERS ----
    filterAccounts() {
        this.renderAllAccounts();
    },

    toggleOwner(owner, checked) {
        if (owner === 'all') {
            if (checked) this.selectedOwners = this.data.owners.map(o => o.name);
            else this.selectedOwners = [];
        } else {
            if (checked) {
                if (!this.selectedOwners.includes(owner)) this.selectedOwners.push(owner);
            } else {
                this.selectedOwners = this.selectedOwners.filter(o => o !== owner);
            }
        }
        this.renderOwnerFilter();
        this.renderAllAccounts();
    },

    renderOwnerFilter() {
        const container = document.getElementById('ownerFilterContainer');
        if (!container || !this.data.owners) return;

        const allChecked = this.selectedOwners.length === this.data.owners.length;

        let html = `<div style="font-size:12px; font-weight:bold; color:var(--text-secondary); margin-right:8px;">Filtrar por Dueño:</div>`;

        // Checkbox Todas
        html += `
            <label style="display:flex; align-items:center; gap:5px; font-size:12px; cursor:pointer; background:var(--bg-input); padding:4px 8px; border-radius:4px; border:1px solid var(--border);">
                <input type="checkbox" ${allChecked ? 'checked' : ''} onchange="App.toggleOwner('all', this.checked)">
                <strong>Todas</strong>
            </label>
        `;

        this.data.owners.forEach(o => {
            const isChecked = this.selectedOwners.includes(o.name);
            html += `
                <label style="display:flex; align-items:center; gap:5px; font-size:12px; cursor:pointer; background:var(--bg-input); padding:4px 8px; border-radius:4px; border:1px solid var(--border);">
                    <input type="checkbox" ${isChecked ? 'checked' : ''} onchange="App.toggleOwner('${o.name}', this.checked)">
                    ${o.name} <span style="font-size:10px; color:var(--text-muted)">(${o.count})</span>
                </label>
            `;
        });

        container.innerHTML = html;
    },

    filterInactive() {
        this.renderInactiveAccounts();
    },

    // ---- DETAIL MODAL ----
    async showDetail(user) {
        const overlay = document.getElementById('modalOverlay');
        const content = document.getElementById('modalContent');

        overlay.classList.add('active');
        this.emailSortState = { key: '_diskused', dir: 'desc' };
        this.emailFilter = null;
        this.modalEmailSearch = '';
        this.emailPage = 0;
        this.modalUser = user;
        content.innerHTML = '<div style="text-align:center;padding:40px"><div class="loader-spinner" style="margin:0 auto"></div><p style="margin-top:16px;color:var(--text-secondary)">Cargando detalle...</p></div>';

        const detail = await this.loadAccountDetail(user);
        this.currentDetailData = detail;

        if (!detail || detail.error) {
            content.innerHTML = '<p style="color:var(--danger);text-align:center">Error cargando detalle de la cuenta</p>';
            return;
        }
        const acct = detail.account || {};
        const emails = detail.emails || [];
        const databases = detail.databases || [];
        const domains = detail.domains || {};
        const forwarders = detail.forwarders || [];
        const autoresponders = detail.autoresponders || [];
        const mailingLists = detail.mailing_lists || [];

        const allDomains = [];
        if (domains.main_domain) allDomains.push(domains.main_domain);
        if (domains.addon_domains) allDomains.push(...domains.addon_domains);
        if (domains.sub_domains) allDomains.push(...domains.sub_domains);
        if (domains.parked_domains) allDomains.push(...domains.parked_domains);

        // Email stats
        var totalEmailDisk = 0;
        var suspendedLoginCount = 0;
        var suspendedLoginDisk = 0;
        var suspendedIncomingCount = 0;
        var suspendedIncomingDisk = 0;
        var now = Math.floor(Date.now() / 1000);
        var inactiveEmailCount = 0; // >60 days
        var inactiveEmailDisk = 0;
        var oldestMtime = now;
        var newestMtime = 0;
        emails.forEach(function (e) {
            var diskBytes = parseFloat(e._diskused || 0);
            totalEmailDisk += diskBytes;
            if (parseInt(e.suspended_login || 0) === 1) {
                suspendedLoginCount++;
                suspendedLoginDisk += diskBytes;
            }
            if (parseInt(e.suspended_incoming || 0) === 1) {
                suspendedIncomingCount++;
                suspendedIncomingDisk += diskBytes;
            }
            var mt = parseInt(e.mtime || 0);
            if (mt > 0) {
                if (Math.floor((now - mt) / 86400) > 60) {
                    inactiveEmailCount++;
                    inactiveEmailDisk += diskBytes;
                }
                if (mt < oldestMtime) oldestMtime = mt;
                if (mt > newestMtime) newestMtime = mt;
            }
        });
        var emailDiskHr = this.formatBytesJS(totalEmailDisk);
        var suspendedLoginDiskHr = this.formatBytesJS(suspendedLoginDisk);
        var suspendedIncomingDiskHr = this.formatBytesJS(suspendedIncomingDisk);
        var inactiveEmailDiskHr = this.formatBytesJS(inactiveEmailDisk);


        // Forwarders HTML
        var forwardersHtml = '';
        if (forwarders.length > 0) {
            forwardersHtml = '<div class="table-container"><table>' +
                '<thead><tr><th>Origen</th><th>Destino</th></tr></thead><tbody>';
            forwarders.forEach(function (f) {
                // WHM Screenshot: 'dest' is Source, 'forward' is Destination
                var from = f.dest || f.html_dest || f.uri_dest || 'N/A';
                var dest = f.forward || f.html_forward || f.uri_forward || f.forwarder || String(f);
                forwardersHtml += '<tr>' +
                    '<td class="td-mono">' + from + '</td>' +
                    '<td class="td-mono">' + dest + '</td>' +
                    '</tr>';
            });
            forwardersHtml += '</tbody></table></div>';
        } else {
            forwardersHtml = '<p style="color:var(--text-muted);font-size:13px">Sin reenvíos configurados</p>';
        }

        // Autoresponders HTML
        var autorespondersHtml = '';
        if (autoresponders.length > 0) {
            autorespondersHtml = '<div class="table-container"><table>' +
                '<thead><tr><th>Email</th><th>Asunto</th><th>Desde</th><th>Hasta</th></tr></thead><tbody>';
            autoresponders.forEach((ar) => {
                var email = ar.email || 'N/A';
                var subject = ar.subject || 'N/A';
                var start = ar.start ? this.formatDateTime(ar.start * 1000, false) : 'N/A';
                var stop = ar.stop ? this.formatDateTime(ar.stop * 1000, false) : 'Indefinido';
                autorespondersHtml += '<tr>' +
                    '<td class="td-mono">' + email + '</td>' +
                    '<td class="td-mono">' + subject + '</td>' +
                    '<td class="td-mono">' + start + '</td>' +
                    '<td class="td-mono">' + stop + '</td>' +
                    '</tr>';
            });
            autorespondersHtml += '</tbody></table></div>';
        } else {
            autorespondersHtml = '<p style="color:var(--text-muted);font-size:13px">Sin respuestas automáticas</p>';
        }

        // Mailing lists HTML
        var mailingHtml = '';
        if (mailingLists.length > 0) {
            mailingHtml = '<div style="display:flex;flex-wrap:wrap;gap:6px">';
            mailingLists.forEach(function (ml) {
                var name = ml.list || ml.listname || ml;
                mailingHtml += '<span class="badge badge-purple">' + name + '</span>';
            });
            mailingHtml += '</div>';
        } else {
            mailingHtml = '<p style="color:var(--text-muted);font-size:13px">Sin listas de correo</p>';
        }

        content.innerHTML = `
                <div class="modal-grid">
                <div class="modal-item">
                    <div class="modal-item-label">Usuario <span style="font-size:9px;opacity:0.4">(acct.user)</span></div>
                    <div class="modal-item-value" style="color:var(--accent)">${acct.user || user}</div>
                </div>
                <div class="modal-item">
                    <div class="modal-item-label">Dominio Principal <span style="font-size:9px;opacity:0.4">(acct.domain)</span></div>
                    <div class="modal-item-value">${acct.domain || 'N/A'}</div>
                </div>
                <div class="modal-item">
                    <div class="modal-item-label">Email Contacto <span style="font-size:9px;opacity:0.4">(acct.email)</span></div>
                    <div class="modal-item-value">${acct.email || 'N/A'}</div>
                </div>
                <div class="modal-item">
                    <div class="modal-item-label">Plan <span style="font-size:9px;opacity:0.4">(acct.plan)</span></div>
                    <div class="modal-item-value">${acct.plan || 'N/A'}</div>
                </div>
                <div class="modal-item">
                    <div class="modal-item-label">Disco Usado <span style="font-size:9px;opacity:0.4">(acct.diskused)</span></div>
                    <div class="modal-item-value">${acct.diskused || 'N/A'} / ${acct.disklimit === 'unlimited' || !acct.disklimit ? '<span class="text-danger">Ilimitado</span>' : acct.disklimit}</div>
                </div>
                <div class="modal-item">
                    <div class="modal-item-label">IP <span style="font-size:9px;opacity:0.4">(acct.ip)</span></div>
                    <div class="modal-item-value">${acct.ip || 'N/A'}</div>
                </div>
                <div class="modal-item" style="grid-column: span 2">
                    <div class="modal-item-label">Inodes (Cantidad de Archivos) <span style="font-size:9px;opacity:0.4">(acct.inodesused)</span></div>
                    <div class="modal-item-value">
                        ${(() => {
                const iUsed = parseInt(acct.inodesused || 0);
                const isManagerial = acct.is_managerial || false;
                const limits = isManagerial ? { y: 100000, o: 150000, r: 200000 } : { y: 50000, o: 80000, r: 120000 };
                const typeLabel = isManagerial ? 'Gerencial' : 'Estándar';
                let color = 'var(--text-primary)';
                let icon = '';
                let msg = '';
                let explanation = '';
                if (iUsed >= limits.r) {
                    color = 'var(--danger)'; icon = '🚨 '; msg = 'Excedido Crítico';
                    explanation = `La cuenta tiene <strong>${this.formatNumber(iUsed)}</strong> archivos, más de <strong>${this.formatNumber(limits.r)}</strong> (límite crítico para cuenta ${typeLabel}). Esto ralentiza los respaldos del servidor, puede causar demoras en la entrega de correos y pone en riesgo la estabilidad del servicio. Se recomienda limpiar correos antiguos o migrar a una plataforma profesional.`;
                } else if (iUsed >= limits.o) {
                    color = 'var(--orange)'; icon = '🟠 '; msg = 'Máximo Recomendado';
                    explanation = `La cuenta tiene <strong>${this.formatNumber(iUsed)}</strong> archivos, superando el umbral naranja de <strong>${this.formatNumber(limits.o)}</strong> para una cuenta ${typeLabel}. El límite máximo tolerable antes de impacto es <strong>${this.formatNumber(limits.r)}</strong>. Se recomienda eliminar correos antiguos o archivos innecesarios.`;
                } else if (iUsed >= limits.y) {
                    color = 'var(--warning)'; icon = '🟡 '; msg = 'Umbral Preventivo';
                    explanation = `La cuenta tiene <strong>${this.formatNumber(iUsed)}</strong> archivos, acercándose al umbral de alerta de <strong>${this.formatNumber(limits.y)}</strong> para una cuenta ${typeLabel}. Por ahora no hay impacto, pero se recomienda monitorear el crecimiento.`;
                } else {
                    explanation = `La cuenta tiene <strong>${this.formatNumber(iUsed)}</strong> archivos. El umbral de alerta para una cuenta ${typeLabel} es <strong>${this.formatNumber(limits.y)}</strong>. Estado normal ✔`;
                }
                return `<span style="color:${color}; font-weight:bold; font-size:15px;">${icon}${this.formatNumber(iUsed)}</span>
                    <span style="font-size:11px;color:var(--text-muted);margin-left:6px">${msg}</span>
                    <span style="color:var(--text-muted)"> / ${acct.inodeslimit === 'unlimited' || !acct.inodeslimit ? '<span class="text-danger">Ilimitado</span>' : this.formatNumber(acct.inodeslimit)}</span>
                    <div style="margin-top:7px;font-size:12px;color:var(--text-secondary);padding:8px 10px;background:var(--bg-primary);border-radius:6px;line-height:1.5">${explanation}</div>`;
            })()}
                    </div>
                </div>
                <div class="modal-item">
                    <div class="modal-item-label">Fecha Creación <span style="font-size:9px;opacity:0.4">(acct.startdate)</span></div>
                    <div class="modal-item-value">${this.formatDateTime(acct.startdate, false)}</div>
                </div>
                <div class="modal-item">
                    <div class="modal-item-label">Email Max/Hora <span style="font-size:9px;opacity:0.4">(acct.max_email_per_hour)</span></div>
                    <div class="modal-item-value">${this.formatNumber(acct.max_email_per_hour) || 'N/A'}</div>
                </div>
                <div class="modal-item">
                    <div class="modal-item-label">Cuentas Email <span style="font-size:9px;opacity:0.4">(acct.email_count)</span></div>
                    <div class="modal-item-value">${this.formatNumber(acct.email_count || '0')} / ${acct.email_limit === 'unlimited' || !acct.email_limit ? '<span class="text-danger">Ilimitado</span>' : this.formatNumber(acct.email_limit)}</div>
                </div>
                <div class="modal-item">
                    <div class="modal-item-label">Reseller (Owner) <span style="font-size:9px;opacity:0.4">(acct.owner)</span></div>
                    <div class="modal-item-value">
                        ${(() => {
                const owner = acct.owner || 'root';
                const resellers = this._resellers && this._resellers.length > 0
                    ? this._resellers.map(r => r.name)
                    : (this.data.owners ? this.data.owners.map(o => o.name).filter(n => n !== 'root') : []);

                if (resellers.length === 0) {
                    return `<span style="color:var(--text-muted)">root</span> <a href="#" onclick="App.switchTab('resellers'); App.closeModal(); return false;" style="color:var(--accent); font-size:11px; margin-left:8px; text-decoration:underline;">+ Crear Reseller</a>`;
                }

                let options = `<option value="root" ${owner === 'root' ? 'selected' : ''}>root (Servidor)</option>`;
                resellers.forEach(r => {
                    options += `<option value="${r}" ${owner === r ? 'selected' : ''}>${r}</option>`;
                });

                return `<select onchange="App.reassignAccount('${user}', this.value)" style="width:100%; padding:4px 8px; background:var(--bg-input); border:1px solid var(--border); border-radius:6px; color:var(--text-primary); font-size:13px">
                    ${options}
                </select>`;
            })()}
                    </div>
                </div>
            </div>
            
            <div class="modal-section-title">📧 Cuentas de Email (${this.formatNumber(emails.length)}) <span style="font-size:9px;opacity:0.4">(renderEmailTable)</span></div>
            <div class="modal-grid" style="margin-bottom:16px">
                <div class="modal-item">
                    <div class="modal-item-label">Disco Total Emails <span style="font-size:9px;opacity:0.4">(totalEmailDisk)</span></div>
                    <div class="modal-item-value" style="color:var(--info)">${emailDiskHr}</div>
                </div>
                <div class="modal-item" onclick="App.filterEmailTable(function(e){ return parseInt(e.suspended_login||0)===1; }, 'Login Suspendido')" style="cursor:pointer" title="Haz clic para filtrar">
                    <div class="modal-item-label">🔒 Login Suspendido <span style="font-size:9px;opacity:.6">(clic para filtrar)</span></div>
                    <div class="modal-item-value" style="color:${suspendedLoginCount > 0 ? 'var(--danger)' : 'var(--accent)'}">${this.formatNumber(suspendedLoginCount)} <span style="font-size:14px;font-weight:400;color:var(--text-secondary)">(${suspendedLoginDiskHr})</span></div>
                </div>
                <div class="modal-item" onclick="App.filterEmailTable(function(e){ return parseInt(e.suspended_incoming||0)===1; }, 'Entrante Suspendido')" style="cursor:pointer" title="Haz clic para filtrar">
                    <div class="modal-item-label">📥 Entrante Suspendido <span style="font-size:9px;opacity:.6">(clic para filtrar)</span></div>
                    <div class="modal-item-value" style="color:${suspendedIncomingCount > 0 ? 'var(--warning)' : 'var(--accent)'}">${this.formatNumber(suspendedIncomingCount)} <span style="font-size:14px;font-weight:400;color:var(--text-secondary)">(${suspendedIncomingDiskHr})</span></div>
                </div>
                <div class="modal-item" onclick="App.filterEmailTable(function(e){ var mt=parseInt(e.mtime||0); return mt>0 && Math.floor((Math.floor(Date.now()/1000)-mt)/86400)>60; }, 'Inactivas +60d')" style="cursor:pointer" title="Haz clic para filtrar">
                    <div class="modal-item-label">⏸️ Inactivas (+60d) <span style="font-size:9px;opacity:.6">(clic para filtrar)</span></div>
                    <div class="modal-item-value" style="color:${inactiveEmailCount > 0 ? 'var(--warning)' : 'var(--accent)'}">${this.formatNumber(inactiveEmailCount)} <span style="font-size:14px;font-weight:400;color:var(--text-secondary)">(${inactiveEmailDiskHr})</span></div>
                </div>
            </div>
            <div id="emailTableContainer" style="display:grid;grid-template-columns: 1fr;">
                <div id="emailTableToolbar">${emails.length > 0 ? this.renderEmailToolbar(emails) : ''}</div>
                <div id="emailTableData">
                    ${emails.length > 0 ? this.renderEmailTable(emails) : '<p style="color:var(--text-muted);font-size:13px">Sin cuentas de email</p>'}
                </div>
            </div>
            
            <div class="modal-section-title">↗️ Reenvíos / Forwarders (${this.formatNumber(forwarders.length)}) <span style="font-size:9px;opacity:0.4">(forwardersHtml)</span></div>
            ${forwardersHtml}

<div class="modal-section-title">🤖 Respuestas Automáticas (${this.formatNumber(autoresponders.length)}) <span style="font-size:9px;opacity:0.4">(autorespondersHtml)</span></div>
            ${autorespondersHtml}

<div class="modal-section-title">📋 Listas de Correo (${this.formatNumber(mailingLists.length)}) <span style="font-size:9px;opacity:0.4">(mailingHtml)</span></div>
            ${mailingHtml}

<div class="modal-section-title">🗄️ Bases de Datos (${this.formatNumber(databases.length)} creadas / ${acct.maxsql} permitidas) <span style="font-size:9px;opacity:0.4">(databases)</span></div>
            ${databases.length > 0 ? `
                <div class="table-container">
                    <table>
                        <thead><tr>
                            <th>Nombre</th>
                            <th>Tamaño</th>
                        </tr></thead>
                        <tbody>
                            ${databases.map(db => `
                                <tr>
                                    <td class="td-mono">${db.db || db.database || 'N/A'}</td>
                                    <td class="td-mono">${db.sizemb ? db.sizemb + ' MB' : (db.disk_usage || 'N/A')}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            ` : '<p style="color:var(--text-muted);font-size:13px">Sin bases de datos</p>'
            }

<div class="modal-section-title">🌐 Dominios (${allDomains.length}) <span style="font-size:9px;opacity:0.4">(allDomains)</span></div>
            ${allDomains.length > 0 ? `
                <div style="display:flex;flex-wrap:wrap;gap:6px">
                    ${allDomains.map(d => `<span class="badge badge-info">${d}</span>`).join('')}
                </div>
            ` : '<p style="color:var(--text-muted);font-size:13px">Solo dominio principal</p>'
            }
`;

        document.getElementById('modalTitle').textContent = `Detalle: ${acct.user || user} `;
    },

    closeModal() {
        document.getElementById('modalOverlay').classList.remove('active');
    },

    // ---- EMAIL TABLE ----
    modalEmails: [],
    emailSortState: { key: '_diskused', dir: 'desc' },
    emailFilter: null,
    modalEmailSearch: '',
    emailPage: 0,
    emailPageSize: 25,

    filterModalEmails(term) {
        this.modalEmailSearch = term.toLowerCase();
        this.emailPage = 0;
        var container = document.getElementById('emailTableData');
        if (container) container.innerHTML = this.renderEmailTable(this.modalEmails);
    },

    filterEmailTable(fn, label) {
        // Toggle: si ya está activo el mismo filtro, limpiar
        if (this.emailFilter && this.emailFilter.label === label) {
            this.emailFilter = null;
        } else {
            this.emailFilter = { fn: fn, label: label };
        }
        var container = document.getElementById('emailTableData');
        if (container) container.innerHTML = this.renderEmailTable(this.modalEmails);
    },

    clearEmailFilters() {
        this.emailFilter = null;
        this.modalEmailSearch = '';
        var searchInput = document.getElementById('modalEmailSearchInput');
        if (searchInput) searchInput.value = '';
        this.emailPage = 0;
        var container = document.getElementById('emailTableData');
        if (container) container.innerHTML = this.renderEmailTable(this.modalEmails);
    },

    sortEmailTable(key) {
        if (this.emailSortState.key === key) {
            this.emailSortState.dir = this.emailSortState.dir === 'asc' ? 'desc' : 'asc';
        } else {
            this.emailSortState = { key: key, dir: 'desc' };
        }
        this.emailPage = 0; // reset to first page on sort
        var container = document.getElementById('emailTableData');
        if (container) {
            container.innerHTML = this.renderEmailTable(this.modalEmails);
        }
    },

    setEmailPage(page) {
        this.emailPage = page;
        var container = document.getElementById('emailTableData');
        if (container) container.innerHTML = this.renderEmailTable(this.modalEmails);
    },

    setEmailPageSize(size) {
        this.emailPageSize = parseInt(size) || 25;
        this.emailPage = 0;
        var container = document.getElementById('emailTableData');
        if (container) container.innerHTML = this.renderEmailTable(this.modalEmails);
    },

    renderEmailToolbar(emails) {
        // --- Toolbar "Nueva casilla" ---
        var domains = [];
        emails.forEach(function (e) {
            var d = (e.email || e.login || '').split('@')[1];
            if (d && domains.indexOf(d) === -1) domains.push(d);
        });
        var domOpts = domains.map(function (d) { return '<option value="' + d + '">' + d + '</option>'; }).join('');
        var toolbar = '<div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">' +
            '<button onclick="App.toggleCreateEmailForm()" id="btnNuevaCasilla" ' +
            'style="padding:5px 14px;background:var(--accent);border:none;border-radius:7px;color:#fff;cursor:pointer;font-size:12px;font-weight:600">➕ Nueva casilla</button>' +
            '<input type="text" id="modalEmailSearchInput" placeholder="🔍 Buscar casilla..." oninput="App.filterModalEmails(this.value)" value="' + (this.modalEmailSearch || '') + '" ' +
            'style="padding:5px 10px;border-radius:7px;border:1px solid var(--border);background:var(--bg-input);color:var(--text-primary);font-size:12px;width:200px">' +
            '</div>' +
            '<div id="createEmailForm" style="display:none;margin-bottom:12px;padding:14px;background:var(--bg-input);border-radius:10px;border:1px solid var(--border)">' +
            '<div style="font-size:12px;font-weight:700;margin-bottom:10px;color:var(--text-primary)">Nueva cuenta de email</div>' +
            '<div style="display:flex;flex-wrap:wrap;gap:8px;align-items:flex-end">' +
            '<div><label style="font-size:11px;color:var(--text-muted)">Nombre</label><br>' +
            '<input id="newEmailLocal" placeholder="nombre" style="padding:5px 8px;border-radius:6px;border:1px solid var(--border);background:var(--bg-card);color:var(--text-primary);font-size:12px;width:140px"></div>' +
            '<div style="padding-bottom:5px;color:var(--text-muted);font-size:14px">@</div>' +
            '<div><label style="font-size:11px;color:var(--text-muted)">Dominio</label><br>' +
            '<select id="newEmailDomain" style="padding:5px 8px;border-radius:6px;border:1px solid var(--border);background:var(--bg-card);color:var(--text-primary);font-size:12px">' + domOpts + '</select></div>' +
            '<div><label style="font-size:11px;color:var(--text-muted)">Contraseña</label><br>' +
            '<input id="newEmailPass" type="password" placeholder="contraseña" style="padding:5px 8px;border-radius:6px;border:1px solid var(--border);background:var(--bg-card);color:var(--text-primary);font-size:12px;width:160px"></div>' +
            '<div><label style="font-size:11px;color:var(--text-muted)">Quota MB (0=∞)</label><br>' +
            '<input id="newEmailQuota" type="number" value="500" min="0" style="padding:5px 8px;border-radius:6px;border:1px solid var(--border);background:var(--bg-card);color:var(--text-primary);font-size:12px;width:90px"></div>' +
            '<button onclick="App.createEmail()" style="padding:6px 16px;background:var(--accent);border:none;border-radius:7px;color:#fff;cursor:pointer;font-size:12px;font-weight:600">✔ Crear</button>' +
            '<button onclick="App.toggleCreateEmailForm()" style="padding:6px 12px;background:transparent;border:1px solid var(--border);border-radius:7px;color:var(--text-muted);cursor:pointer;font-size:12px">✕ Cancelar</button>' +
            '</div>' +
            '<div id="createEmailMsg" style="margin-top:8px;font-size:11px"></div>' +
            '</div>';
        return toolbar;
    },

    renderEmailTable(emails) {
        this.modalEmails = emails;
        var sortKey = this.emailSortState.key;
        var sortDir = this.emailSortState.dir;

        // Sortear emails
        var searchTerm = this.modalEmailSearch;
        var filteredBySearch = searchTerm ? emails.filter(function (e) {
            return (e.email || e.login || '').toLowerCase().indexOf(searchTerm) !== -1;
        }) : emails;

        var sorted = (this.emailFilter ? filteredBySearch.filter(this.emailFilter.fn) : filteredBySearch).slice().sort(function (a, b) {
            var va, vb;
            if (sortKey === '_diskused') {
                va = parseFloat(a._diskused || a.diskused || 0);
                vb = parseFloat(b._diskused || b.diskused || 0);
            } else if (sortKey === 'diskquota') {
                va = parseFloat(a._diskquota || a.diskquota || 0) || 0;
                vb = parseFloat(b._diskquota || b.diskquota || 0) || 0;
            } else if (sortKey === 'diskusedpercent') {
                va = parseFloat(a.diskusedpercent_float || a.diskusedpercent || 0);
                vb = parseFloat(b.diskusedpercent_float || b.diskusedpercent || 0);
            } else if (sortKey === 'mtime') {
                va = parseInt(a.mtime || 0);
                vb = parseInt(b.mtime || 0);
            } else if (sortKey === 'email') {
                va = (a.email || a.login || '').toLowerCase();
                vb = (b.email || b.login || '').toLowerCase();
                if (va < vb) return sortDir === 'asc' ? -1 : 1;
                if (va > vb) return sortDir === 'asc' ? 1 : -1;
                return 0;
            } else if (sortKey === 'suspended_login') {
                va = parseInt(a.suspended_login || 0);
                vb = parseInt(b.suspended_login || 0);
            } else if (sortKey === 'suspended_incoming') {
                va = parseInt(a.suspended_incoming || 0);
                vb = parseInt(b.suspended_incoming || 0);
            } else if (sortKey === 'forwarder_count') {
                va = parseInt(a.forwarder_count || 0);
                vb = parseInt(b.forwarder_count || 0);
            }
            return sortDir === 'asc' ? va - vb : vb - va;
        });

        // Flechas de sort
        var arrow = function (col) {
            if (sortKey !== col) return '';
            return sortDir === 'asc' ? ' ▲' : ' ▼';
        };

        var now = Math.floor(Date.now() / 1000);
        var rows = '';

        // Paginación
        var pageSize = this.emailPageSize === 0 ? sorted.length : this.emailPageSize; // 0 = Todos
        var totalPages = Math.max(1, Math.ceil(sorted.length / pageSize));
        if (this.emailPage >= totalPages) this.emailPage = totalPages - 1;
        var start = this.emailPage * pageSize;
        var end = Math.min(start + pageSize, sorted.length);
        var paginated = sorted.slice(start, end);
        var limit = paginated.length;
        for (var i = 0; i < limit; i++) {
            var e = paginated[i];
            var rowIdx = start + i;
            var used = parseFloat(e._diskused || e.diskused || 0);
            var quota = parseFloat(e.diskquota || 0);
            var pctFloat = parseFloat(e.diskusedpercent_float || e.diskusedpercent || 0);
            var pct = Math.round(pctFloat);

            // Política de colores:
            // 0-69%   🟢 Normal    (green)
            // 70-84%  🟡 Preventivo(yellow)
            // 85-94%  🟠 Alto      (orange)
            // 95-99%  🔴 Crítico   (red)
            // 100%    ⛔ Bloqueo   (darkred)
            var barColor = 'green';
            if (pct >= 100) barColor = 'darkred';
            else if (pct >= 95) barColor = 'red';
            else if (pct >= 85) barColor = 'orange'; // var(--orange) needs to be mapped to a color or CSS class
            else if (pct >= 70) barColor = 'yellow';

            var barHtml = (quota > 0) ? '<span class="mini-bar"><span class="mini-bar-fill ' + barColor + '" style="width:' + Math.min(pctFloat, 100) + '%"></span></span>' : '';

            // Construir celda de porcentaje con GB usados
            var pctCellText = '';
            if (displayQuota !== '<span class="text-danger">Ilimitado</span>' && displayQuota !== 'Ilimitado') {
                pctCellText = '<strong style="color:var(--text-primary)">' + (e.humandiskused || '0') + ' de ' + displayQuota + '</strong> <span style="color:var(--text-muted);font-size:11px">(' + pct + '%)</span>';
            } else {
                pctCellText = '<strong style="color:var(--text-primary)">' + (e.humandiskused || '0') + '</strong> <span style="color:var(--text-muted);font-size:11px">(' + pct + '%)</span>';
            }

            // Último acceso (mtime)
            var lastDateStr = 'Sin registro';
            var dateBadge = 'badge-info';
            var mtime = parseInt(e.mtime || 0);

            if (mtime > 0) {
                lastDateStr = this.formatDateTime(mtime * 1000);

                var daysAgo = Math.floor((now - mtime) / 86400);

                if (daysAgo > 365) {
                    dateBadge = 'badge-suspended';
                    lastDateStr += ' (' + this.formatNumber(Math.floor(daysAgo / 365)) + ' año' + (Math.floor(daysAgo / 365) > 1 ? 's' : '') + ')';
                } else if (daysAgo > 60) {
                    dateBadge = 'badge-warning';
                    lastDateStr += ' (' + this.formatNumber(daysAgo) + 'd)';
                } else if (daysAgo > 30) {
                    dateBadge = 'badge-info';
                    lastDateStr += ' (' + this.formatNumber(daysAgo) + 'd)';
                } else {
                    dateBadge = 'badge-active';
                    lastDateStr += ' (' + this.formatNumber(daysAgo) + 'd)';
                }
            }

            // Status badges
            var statusHtml = '';
            if (parseInt(e.suspended_login || 0) === 1) statusHtml += '<span class="badge badge-suspended" style="margin-left:4px" title="Login suspendido">🔒</span>';
            if (parseInt(e.suspended_incoming || 0) === 1) statusHtml += '<span class="badge badge-warning" style="margin-left:4px" title="Correo entrante suspendido">📥</span>';

            // Toggle buttons login / incoming
            var isLoginSuspended = parseInt(e.suspended_login || 0) === 1;
            var isIncomingSuspended = parseInt(e.suspended_incoming || 0) === 1;
            var emailAddr = e.email || e.login || '';
            var userAttr = JSON.stringify(App.modalUser || '');
            var loginBtn = '<button title="' + (isLoginSuspended ? 'Activar login' : 'Suspender login') + '" ' +
                'onclick="App.toggleEmailLogin(\'' + emailAddr + '\', ' + (isLoginSuspended ? 'false' : 'true') + ')" ' +
                'style="padding:3px 7px;border:none;border-radius:5px;cursor:pointer;font-size:11px;margin-right:3px;' +
                'background:' + (isLoginSuspended ? 'var(--accent)' : 'var(--danger)') + ';color:#fff">' +
                (isLoginSuspended ? '🔓 Login' : '🔒 Login') + '</button>';
            var incomingBtn = '<button title="' + (isIncomingSuspended ? 'Activar entrada' : 'Suspender entrada') + '" ' +
                'onclick="App.toggleEmailIncoming(\'' + emailAddr + '\', ' + (isIncomingSuspended ? 'false' : 'true') + ')" ' +
                'style="padding:3px 7px;border:none;border-radius:5px;cursor:pointer;font-size:11px;' +
                'background:' + (isIncomingSuspended ? 'var(--accent)' : 'var(--warning)') + ';color:' + (isIncomingSuspended ? '#fff' : '#000') + '">' +
                (isIncomingSuspended ? '📥 Activar' : '📥 Parar') + '</button>';

            // Estado Login column
            var estadoLogin = '';
            if (isLoginSuspended) {
                estadoLogin += '<span class="badge badge-suspended" style="margin-right:3px">🔒 Login</span>';
            } else {
                estadoLogin += '<span class="badge badge-active" style="margin-right:3px">🔓 Login</span>';
            }
            if (isIncomingSuspended) {
                estadoLogin += '<span class="badge badge-warning">📥 Pausado</span>';
            } else {
                estadoLogin += '<span class="badge badge-active">📥 Activo</span>';
            }

            var logBtn = '<button title="Ver log de actividad" ' +
                'onclick="App.showEmailLog(\'' + emailAddr + '\')" ' +
                'style="padding:3px 7px;border:none;border-radius:5px;cursor:pointer;font-size:11px;margin-left:3px;' +
                'background:var(--info-dim);color:var(--info)">📋 Log</button>';

            var pwdBtn = '<button title="Cambiar contraseña" ' +
                'onclick="App.changeEmailPassword(\'' + emailAddr + '\')" ' +
                'style="padding:3px 7px;border:none;border-radius:5px;cursor:pointer;font-size:11px;margin-left:3px;' +
                'background:var(--bg-hover);color:var(--text-secondary)">🔑</button>';

            var currentQuota = parseFloat(e._diskquota || e.diskquota || 0);
            var quotaBtn = '<button title="Cambiar quota" ' +
                'onclick="App.editEmailQuota(\'' + emailAddr + '\',' + currentQuota + ')" ' +
                'style="padding:3px 7px;border:none;border-radius:5px;cursor:pointer;font-size:11px;margin-left:3px;' +
                'background:var(--bg-hover);color:var(--text-secondary)">📊</button>';

            var deleteBtn = '<button title="Eliminar casilla" ' +
                'onclick="App.deleteEmail(\'' + emailAddr + '\')" ' +
                'style="padding:3px 7px;border:none;border-radius:5px;cursor:pointer;font-size:11px;margin-left:3px;' +
                'background:rgba(220,53,69,0.12);color:var(--danger)">🗑️</button>';

            var fwdBtn = '<button title="Forwardear email" ' +
                'onclick="App.showForwarders(\'' + emailAddr + '\')" ' +
                'style="padding:3px 7px;border:none;border-radius:5px;cursor:pointer;font-size:11px;margin-left:3px;' +
                'background:rgba(108,92,231,0.12);color:#a29bfe">➡️ Fwd</button>';
            var displayQuota = (e.humandiskquota || e.diskquota || '<span class="text-danger">Ilimitado</span>');
            if (displayQuota === 'None' || displayQuota === 'unlimited') displayQuota = '<span class="text-danger">Ilimitado</span>';

            var fwdIndicator = (e.forwarder_count > 0) ? '<span title="' + e.forwarder_count + ' reenvío(s)" style="cursor:help;margin-left:4px">↗️</span>' : '';
            var fwdCell = '<td class="td-mono" style="text-align:center">' +
                (e.forwarder_count > 0 ? '<span class="badge badge-info" style="cursor:pointer" onclick="App.showForwarders(\'' + emailAddr + '\')">' + e.forwarder_count + '</span>' : '<span style="color:var(--text-muted)">0</span>') +
                '</td>';

            rows +=
                '<tr id="emailrow-' + rowIdx + '">' +
                '<td style="color:var(--text-muted);font-size:11px;font-family:var(--font-mono);text-align:center;width:36px">' + this.formatNumber(start + i + 1) + '</td>' +
                '<td class="td-mono">' + (e.email || e.login || 'N/A') + statusHtml + fwdIndicator + '</td>' +
                '<td class="td-mono">' + pctCellText + '<div style="margin-top:4px; max-width: 150px;">' + barHtml + '</div></td>' +
                fwdCell +
                '<td style="white-space:nowrap">' +
                (isLoginSuspended ? '<span class="badge badge-suspended">🔒 Suspendido</span>' : '<span class="badge badge-active">🔓 Activo</span>') +
                '</td>' +
                '<td style="white-space:nowrap">' +
                (isIncomingSuspended ? '<span class="badge badge-warning">📥 Pausado</span>' : '<span class="badge badge-active">📥 Activo</span>') +
                '</td>' +
                '<td><span class="badge ' + dateBadge + '">' + lastDateStr + '</span></td>' +
                '<td style="white-space:nowrap">' + loginBtn + incomingBtn + logBtn + pwdBtn + quotaBtn + fwdBtn + deleteBtn + '</td>' +
                '</tr>' +
                '<tr id="emaillog-' + rowIdx + '" style="display:none"><td colspan="10" style="padding:0"></td></tr>';
        }

        var displayEmails = this.emailFilter ? emails.filter(this.emailFilter.fn) : emails;
        var filterBanner = this.emailFilter ?
            '<div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;padding:6px 12px;background:var(--warning-dim);border-radius:8px;font-size:12px">' +
            '🔍 Filtrando: <strong>' + this.emailFilter.label + '</strong> (' + displayEmails.length + ' de ' + emails.length + ')' +
            ' <button onclick="App.filterEmailTable(null,\'' + (this.emailFilter.label) + '\')" style="margin-left:auto;background:transparent;border:none;color:var(--warning);cursor:pointer;font-weight:700">✕ Quitar filtro</button>' +
            '</div>' : '';

        // --- Paginación bar ---
        var btnStyle = 'padding:4px 10px;background:var(--bg-input);border:1px solid var(--border);border-radius:6px;color:var(--text-primary);cursor:pointer;font-size:12px';
        var btnDisabled = 'padding:4px 10px;background:transparent;border:1px solid var(--border);border-radius:6px;color:var(--text-muted);cursor:default;font-size:12px';
        var pageSizes = [10, 25, 50, 100, 0]; // 0 = Todos
        var sizeOptions = pageSizes.map(function (s) {
            var lbl = s === 0 ? 'Todos' : s;
            var sel = (App.emailPageSize === s) ? ' selected' : '';
            return '<option value="' + s + '"' + sel + '>' + lbl + '</option>';
        }).join('');
        var paginationBar = '<div style="display:flex;align-items:center;gap:8px;margin-top:10px;flex-wrap:wrap">' +
            '<span style="font-size:12px;color:var(--text-muted)">Página <strong>' + this.formatNumber(this.emailPage + 1) + '</strong> de <strong>' + this.formatNumber(totalPages) + '</strong> &nbsp;|&nbsp; ' + this.formatNumber(sorted.length) + ' casillas</span>' +
            '<div style="margin-left:auto;display:flex;align-items:center;gap:6px">' +
            '<button ' + (this.emailPage > 0 ? 'onclick="App.setEmailPage(' + (this.emailPage - 1) + ')" style="' + btnStyle + '"' : 'style="' + btnDisabled + '"') + '>← Ant</button>' +
            '<button ' + (this.emailPage < totalPages - 1 ? 'onclick="App.setEmailPage(' + (this.emailPage + 1) + ')" style="' + btnStyle + '"' : 'style="' + btnDisabled + '"') + '>Sig →</button>' +
            '<select onchange="App.setEmailPageSize(this.value)" style="padding:4px 8px;background:var(--bg-input);border:1px solid var(--border);border-radius:6px;color:var(--text-primary);font-size:12px;cursor:pointer">' +
            sizeOptions + '</select>' +
            '</div></div>';

        return filterBanner + '<div class="table-container"><table>' +
            '<thead><tr>' +
            '<th style="width:36px;text-align:center">#</th>' +
            '<th style="cursor:pointer" onclick="App.sortEmailTable(\'email\')">Email' + arrow('email') + '</th>' +
            '<th style="cursor:pointer" onclick="App.sortEmailTable(\'_diskused\')">Disco Usado' + arrow('_diskused') + '</th>' +
            '<th style="cursor:pointer" onclick="App.sortEmailTable(\'diskquota\')">Quota' + arrow('diskquota') + '</th>' +
            '<th style="cursor:pointer" onclick="App.sortEmailTable(\'diskusedpercent\')">% Uso' + arrow('diskusedpercent') + '</th>' +
            '<th style="cursor:pointer" onclick="App.sortEmailTable(\'forwarder_count\')" title="Ordenar por Reenvíos">Reenvíos' + arrow('forwarder_count') + '</th>' +
            '<th style="cursor:pointer" onclick="App.sortEmailTable(\'suspended_login\')" title="Ordenar por Login">Login' + arrow('suspended_login') + '</th>' +
            '<th style="cursor:pointer" onclick="App.sortEmailTable(\'suspended_incoming\')" title="Ordenar por Entrada">Entrada' + arrow('suspended_incoming') + '</th>' +
            '<th style="cursor:pointer" onclick="App.sortEmailTable(\'mtime\')">Última Actividad' + arrow('mtime') + '</th>' +
            '<th>Acciones</th>' +
            '</tr></thead>' +
            '<tbody>' + (rows || '<tr><td colspan="10" style="text-align:center;padding:40px;color:var(--text-muted)">No se encontraron casillas con este filtro</td></tr>') + '</tbody>' +
            '</table></div>' + paginationBar;
    },

    // ---- EMAIL CREATE / PASSWORD ----

    toggleCreateEmailForm() {
        var form = document.getElementById('createEmailForm');
        if (form) form.style.display = form.style.display === 'none' ? '' : 'none';
        var msg = document.getElementById('createEmailMsg');
        if (msg) msg.innerHTML = '';
    },

    async createEmail() {
        var local = (document.getElementById('newEmailLocal') || {}).value || '';
        var domain = (document.getElementById('newEmailDomain') || {}).value || '';
        var pass = (document.getElementById('newEmailPass') || {}).value || '';
        var quota = (document.getElementById('newEmailQuota') || {}).value || 0;
        var msg = document.getElementById('createEmailMsg');

        if (!local || !domain || !pass) {
            if (msg) msg.innerHTML = '<span style="color:var(--danger)">⚠️ Completa todos los campos obligatorios.</span>';
            return;
        }
        if (pass.length < 8) {
            if (msg) msg.innerHTML = '<span style="color:var(--danger)">⚠️ La contraseña debe tener al menos 8 caracteres.</span>';
            return;
        }
        if (msg) msg.innerHTML = '<span style="color:var(--text-muted)">Creando cuenta...</span>';

        try {
            var fd = new FormData();
            fd.append('action', 'create_email');
            fd.append('user', this.modalUser);
            fd.append('localpart', local);
            fd.append('domain', domain);
            fd.append('password', pass);
            fd.append('quota', quota);
            var res = await fetch('api/index.php', { method: 'POST', body: fd });
            var data = await res.json();
            if (data.success) {
                if (msg) msg.innerHTML = '<span style="color:var(--accent)">✅ ' + local + '@' + domain + ' creada correctamente.</span>';
                // Limpiar campos
                document.getElementById('newEmailLocal').value = '';
                document.getElementById('newEmailPass').value = '';
                // Recargar la lista de emails después de 1.5s
                var self = this;
                setTimeout(function () {
                    var container = document.getElementById('emailTableContainer');
                    if (container) container.innerHTML = self.renderEmailTable(self.modalEmails);
                }, 1500);
            } else {
                if (msg) msg.innerHTML = '<span style="color:var(--danger)">❌ ' + (data.message || 'Error al crear cuenta') + '</span>';
            }
        } catch (e) {
            if (msg) msg.innerHTML = '<span style="color:var(--danger)">❌ Error: ' + e.message + '</span>';
        }
    },

    editEmailQuota(email, currentQuotaMB) {
        // Reusar la fila emaillog para mostrar el panel de quota
        var targetRow = null;
        var emailRows = document.querySelectorAll('[id^="emailrow-"]');
        emailRows.forEach(function (row) {
            var emailCell = row.querySelector('td.td-mono');
            if (emailCell && emailCell.textContent.trim().slice(0, email.length) === email) {
                var idx = row.id.replace('emailrow-', '');
                targetRow = document.getElementById('emaillog-' + idx);
            }
        });
        if (!targetRow) return;

        // Toggle
        var td = targetRow.querySelector('td');
        if (targetRow.style.display !== 'none' && targetRow.style.display !== '' && td.getAttribute('data-mode') === 'quota') {
            targetRow.style.display = 'none';
            return;
        }

        var presets = [
            { label: '100 MB', mb: 100 },
            { label: '250 MB', mb: 250 },
            { label: '500 MB', mb: 500 },
            { label: '1 GB', mb: 1024 },
            { label: '2 GB', mb: 2048 },
            { label: '5 GB', mb: 5120 },
            { label: '10 GB', mb: 10240 },
            { label: '∞ Ilimitado', mb: 0 },
        ];

        var closeBtn = '<button onclick="document.getElementById(\'' + targetRow.id + '\').style.display=\'none\'" ' +
            'style="padding:3px 10px;border:1px solid var(--border);border-radius:6px;background:transparent;color:var(--text-muted);cursor:pointer;font-size:11px">✕ Cancelar</button>';

        var presetBtns = presets.map(function (p) {
            var isCurrent = currentQuotaMB === p.mb;
            var bg = isCurrent ? 'var(--accent)' : 'var(--bg-card)';
            var color = isCurrent ? '#fff' : 'var(--text-primary)';
            var border = isCurrent ? 'var(--accent)' : 'var(--border)';
            return '<button onclick="App._applyQuota(\'' + email + '\',' + p.mb + ',\'' + targetRow.id + '\')" ' +
                'style="padding:5px 12px;border:1px solid ' + border + ';border-radius:7px;background:' + bg + ';color:' + color + ';cursor:pointer;font-size:12px;font-weight:' + (isCurrent ? '700' : '400') + '">' +
                p.label + (isCurrent ? ' ✓' : '') + '</button>';
        }).join('');

        td.setAttribute('data-mode', 'quota');
        td.innerHTML = '<div style="padding:12px;background:var(--bg-input)">' +
            '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">' +
            '<span style="font-size:11px;color:var(--text-muted)">📊 Cambiar quota de <strong style="color:var(--info)">' + email + '</strong></span>' +
            closeBtn + '</div>' +
            '<div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px">' + presetBtns + '</div>' +
            '<div style="display:flex;align-items:center;gap:8px">' +
            '<span style="font-size:11px;color:var(--text-muted)">Personalizada (MB):</span>' +
            '<input id="customQuotaInput" type="number" min="0" placeholder="ej: 750" value="' + (currentQuotaMB || '') + '" ' +
            'style="width:100px;padding:4px 8px;border-radius:6px;border:1px solid var(--border);background:var(--bg-card);color:var(--text-primary);font-size:12px">' +
            '<button onclick="App._applyQuota(\'' + email + '\',parseInt(document.getElementById(\'customQuotaInput\').value||0),\'' + targetRow.id + '\')" ' +
            'style="padding:4px 12px;background:var(--accent);border:none;border-radius:6px;color:#fff;cursor:pointer;font-size:12px">Aplicar</button>' +
            '</div>' +
            '<div id="quotaMsg-' + targetRow.id + '" style="margin-top:6px;font-size:11px"></div>' +
            '</div>';
        targetRow.style.display = '';
    },

    async _applyQuota(email, quota, rowId) {
        var msgEl = document.getElementById('quotaMsg-' + rowId);
        if (msgEl) msgEl.innerHTML = '<span style="color:var(--text-muted)">Aplicando...</span>';

        var parts = email.split('@');
        if (parts.length !== 2) return;

        try {
            var fd = new FormData();
            fd.append('action', 'edit_email_quota');
            fd.append('user', this.modalUser);
            fd.append('localpart', parts[0]);
            fd.append('domain', parts[1]);
            fd.append('quota', quota);
            var res = await fetch('api/index.php', { method: 'POST', body: fd });
            var data = await res.json();
            if (data.success) {
                if (msgEl) msgEl.innerHTML = '<span style="color:var(--accent)">✅ Quota actualizada a ' + (quota === 0 ? 'ilimitado' : quota + ' MB') + '</span>';
                // Actualizar estado local
                this.modalEmails.forEach(function (e) {
                    if ((e.email || e.login) === email) {
                        e.diskquota = quota;
                        e._diskquota = quota;
                        e.humandiskquota = quota === 0 ? 'Ilimitado' : quota + ' MB';
                    }
                });
                var self = this;
                setTimeout(function () {
                    var container = document.getElementById('emailTableContainer');
                    if (container) container.innerHTML = self.renderEmailTable(self.modalEmails);
                }, 1200);
            } else {
                if (msgEl) msgEl.innerHTML = '<span style="color:var(--danger)">❌ ' + (data.message || 'Error') + '</span>';
            }
        } catch (e) {
            if (msgEl) msgEl.innerHTML = '<span style="color:var(--danger)">❌ ' + e.message + '</span>';
        }
    },

    async showForwarders(email, forceOpen = false) {
        // Usar la fila emaillog para mostrar el panel de forwarders
        var targetRow = null;
        var emailRows = document.querySelectorAll('[id^="emailrow-"]');
        emailRows.forEach(function (row) {
            var emailCell = row.querySelector('td.td-mono');
            if (emailCell && emailCell.textContent.trim().slice(0, email.length) === email) {
                var idx = row.id.replace('emailrow-', '');
                targetRow = document.getElementById('emaillog-' + idx);
            }
        });
        if (!targetRow) return;

        var td = targetRow.querySelector('td');
        if (!forceOpen && targetRow.style.display !== 'none' && targetRow.style.display !== '' && td.getAttribute('data-mode') === 'fwd') {
            targetRow.style.display = 'none';
            return;
        }

        var closeBtn = '<button onclick="document.getElementById(\'' + targetRow.id + '\').style.display=\'none\'" ' +
            'style="padding:3px 10px;border:1px solid var(--border);border-radius:6px;background:transparent;color:var(--text-muted);cursor:pointer;font-size:11px">✕ Cerrar</button>';

        td.setAttribute('data-mode', 'fwd');
        td.innerHTML = '<div style="padding:12px;background:var(--bg-input)">' +
            '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">' +
            '<span style="font-size:11px;color:var(--text-muted)">➡️ Forwarders de <strong style="color:var(--info)">' + email + '</strong></span>' +
            closeBtn + '</div>' +
            '<div id="fwdList-' + targetRow.id + '" style="margin-bottom:10px;font-size:11px;color:var(--text-muted)">Cargando...</div>' +
            '<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">' +
            '<input id="fwdDest-' + targetRow.id + '" type="email" placeholder="destino@ejemplo.com" ' +
            'style="padding:5px 10px;border-radius:6px;border:1px solid var(--border);background:var(--bg-card);color:var(--text-primary);font-size:12px;width:240px">' +
            '<button onclick="App._addFwd(\'' + email + '\',\'' + targetRow.id + '\')" ' +
            'style="padding:5px 14px;background:var(--accent);border:none;border-radius:6px;color:#fff;cursor:pointer;font-size:12px">+ Agregar</button>' +
            '</div>' +
            '<div id="fwdMsg-' + targetRow.id + '" style="margin-top:6px;font-size:11px"></div>' +
            '</div>';
        targetRow.style.display = '';

        // Cargar forwarders actuales
        this._loadFwd(email, targetRow.id);
    },

    async _loadFwd(email, rowId) {
        var listEl = document.getElementById('fwdList-' + rowId);
        if (!listEl) return;
        try {
            var res = await fetch('api/index.php?action=list_forwarders&user=' + encodeURIComponent(this.modalUser) + '&email=' + encodeURIComponent(email));
            var data = await res.json();
            var fwds = (data.forwarders || []).filter(function (f) {
                // Filtrar donde 'dest' (Source) sea el email de la cuenta
                var src = f.dest || f.html_dest || f.uri_dest || '';
                return src === email || src === email.split('@')[0];
            });
            if (fwds.length === 0) {
                listEl.innerHTML = '<em style="color:var(--text-muted)">Sin forwarders configurados.</em>';
                return;
            }
            var html = '<div style="display:flex;flex-wrap:wrap;gap:6px">';
            fwds.forEach(function (f) {
                // Mostrar 'forward' (Destination) en los tags
                var destStr = f.forward || f.html_forward || f.dest || String(f);
                html += '<span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;background:rgba(108,92,231,0.15);border-radius:20px;font-size:11px;color:#a29bfe">' +
                    '➡️ ' + destStr +
                    '<button onclick="App._deleteFwd(\'' + email + '\',\'' + destStr + '\',\'' + rowId + '\')" ' +
                    'style="background:transparent;border:none;color:var(--danger);cursor:pointer;font-size:12px;padding:0;margin-left:2px" title="Eliminar">✕</button>' +
                    '</span>';
            });
            html += '</div>';
            listEl.innerHTML = html;
            this._checkFwdPolicy(email, fwds, rowId);

            // Política TNA de Forwarders
            this._checkFwdPolicy(email, fwds, rowId);
        } catch (e) {
            if (listEl) listEl.innerHTML = '<span style="color:var(--danger)">Error: ' + e.message + '</span>';
        }
    },

    async _addFwd(email, rowId) {
        var input = document.getElementById('fwdDest-' + rowId);
        var msgEl = document.getElementById('fwdMsg-' + rowId);
        var dest = input ? input.value.trim() : '';
        if (!dest || !dest.includes('@')) {
            if (msgEl) msgEl.innerHTML = '<span style="color:var(--danger)">⚠️ Ingresa un email de destino válido.</span>';
            return;
        }
        var parts = email.split('@');
        if (parts.length !== 2) return;
        if (msgEl) msgEl.innerHTML = '<span style="color:var(--text-muted)">Agregando...</span>';
        try {
            var fd = new FormData();
            fd.append('action', 'add_forwarder');
            fd.append('user', this.modalUser);
            fd.append('localpart', parts[0]);
            fd.append('domain', parts[1]);
            fd.append('fwdest', dest);
            var res = await fetch('api/index.php', { method: 'POST', body: fd });
            var data = await res.json();
            if (data.success) {
                if (msgEl) msgEl.innerHTML = '<span style="color:var(--accent)">✅ Forwarder agregado.</span>';
                if (input) input.value = '';

                // Actualizar contador local
                if (this.modalEmails) {
                    var me = this.modalEmails.find(function (m) { return (m.email || m.login) === email; });
                    if (me) {
                        me.forwarder_count = (parseInt(me.forwarder_count || 0)) + 1;
                        // Forzar re-render de la tabla para ver el nuevo contador
                        var dataContainer = document.getElementById('emailTableData');
                        if (dataContainer) {
                            dataContainer.innerHTML = this.renderEmailTable(this.modalEmails);
                            this.showForwarders(email, true);
                        }
                    }
                }

                this._loadFwd(email, rowId);
            } else {
                if (msgEl) msgEl.innerHTML = '<span style="color:var(--danger)">❌ ' + (data.message || 'Error') + '</span>';
            }
        } catch (e) {
            if (msgEl) msgEl.innerHTML = '<span style="color:var(--danger)">❌ ' + e.message + '</span>';
        }
    },

    async _deleteFwd(email, dest, rowId) {
        if (!confirm('¿Eliminar el forwarder de ' + email + ' a ' + dest + '?')) return;
        var msgEl = document.getElementById('fwdMsg-' + rowId);
        if (msgEl) msgEl.innerHTML = '<span style="color:var(--text-muted)">Eliminando...</span>';
        try {
            var fd = new FormData();
            fd.append('action', 'delete_forwarder');
            fd.append('user', this.modalUser);
            fd.append('email', email);
            fd.append('fwdest', dest);
            var res = await fetch('api/index.php', { method: 'POST', body: fd });
            var data = await res.json();
            if (data.success) {
                if (msgEl) msgEl.innerHTML = '<span style="color:var(--accent)">✅ Eliminado.</span>';

                // Actualizar contador local
                if (this.modalEmails) {
                    var me = this.modalEmails.find(function (m) { return (m.email || m.login) === email; });
                    if (me) {
                        me.forwarder_count = Math.max(0, (parseInt(me.forwarder_count || 0)) - 1);
                        // Forzar re-render de la tabla para ver el nuevo contador
                        var dataContainer = document.getElementById('emailTableData');
                        if (dataContainer) {
                            dataContainer.innerHTML = this.renderEmailTable(this.modalEmails);
                            this.showForwarders(email, true);
                        }
                    }
                }

                this._loadFwd(email, rowId);
            } else {
                if (msgEl) msgEl.innerHTML = '<span style="color:var(--danger)">❌ ' + (data.message || 'Error') + '</span>';
            }
        } catch (e) {
            if (msgEl) msgEl.innerHTML = '<span style="color:var(--danger)">❌ ' + e.message + '</span>';
        }
    },

    async deleteEmail(email) {
        if (!confirm('⚠️ ¿Está seguro que desea ELIMINAR la siguiente casilla de email?\n\n→ ' + email + '\n\nEsta acción es IRREVERSIBLE. Se eliminarán todos los correos almacenados.')) return;

        var confirmText = prompt('🚨 SEGUNDA CONFIRMACIÓN REQUERIDA\n\nPara eliminar permanentemente la cuenta ' + email + ', por favor escriba "Eliminar Cuenta" (sin comillas) a continuación:');

        if (confirmText !== 'Eliminar Cuenta') {
            alert('❌ Eliminación cancelada. El texto ingresado no coincide.');
            return;
        }

        var parts = email.split('@');
        if (parts.length !== 2) { alert('Email inválido'); return; }

        try {
            var fd = new FormData();
            fd.append('action', 'delete_email');
            fd.append('user', this.modalUser);
            fd.append('localpart', parts[0]);
            fd.append('domain', parts[1]);
            var res = await fetch('api/index.php', { method: 'POST', body: fd });
            var data = await res.json();
            if (data.success) {
                alert('✅ ' + email + ' eliminada correctamente.');
                // Quitar de la lista local y re-renderizar
                this.modalEmails = this.modalEmails.filter(function (e) {
                    return (e.email || e.login) !== email;
                });
                var container = document.getElementById('emailTableContainer');
                if (container) container.innerHTML = this.renderEmailTable(this.modalEmails);
            } else {
                alert('❌ Error: ' + (data.message || 'No se pudo eliminar la cuenta'));
            }
        } catch (e) { alert('❌ Error: ' + e.message); }
    },

    async changeEmailPassword(email) {
        var newPass = prompt('🔑 Nueva contraseña para:\n' + email + '\n\n(min. 8 caracteres)');
        if (!newPass) return;
        if (newPass.length < 8) { alert('⚠️ La contraseña debe tener al menos 8 caracteres.'); return; }

        var parts = email.split('@');
        if (parts.length !== 2) { alert('Email inválido'); return; }

        try {
            var fd = new FormData();
            fd.append('action', 'change_email_password');
            fd.append('user', this.modalUser);
            fd.append('localpart', parts[0]);
            fd.append('domain', parts[1]);
            fd.append('password', newPass);
            var res = await fetch('api/index.php', { method: 'POST', body: fd });
            var data = await res.json();
            if (data.success) {
                alert('✅ Contraseña de ' + email + ' actualizada correctamente.');
            } else {
                alert('❌ Error: ' + (data.message || 'No se pudo cambiar la contraseña'));
            }
        } catch (e) {
            alert('❌ Error: ' + e.message);
        }
    },

    // ---- EMAIL TOGGLE ACTIONS ----
    modalUser: '',

    async toggleEmailLogin(email, suspend) {
        var action = suspend ? 'suspender' : 'reactivar';
        if (!confirm((suspend ? '🔒 Suspender' : '🔓 Activar') + ' el login de:\n' + email)) return;
        try {
            var act = suspend ? 'suspend' : 'unsuspend';
            var res = await fetch('api/index.php?action=toggle_email_login&user=' + encodeURIComponent(this.modalUser) + '&email=' + encodeURIComponent(email) + '&act=' + act);
            var data = await res.json();
            if (data.success) {
                // Actualizar estado local y re-renderizar
                this.modalEmails.forEach(function (e) {
                    if ((e.email || e.login) === email) e.suspended_login = suspend ? 1 : 0;
                });
                var container = document.getElementById('emailTableContainer');
                if (container) container.innerHTML = this.renderEmailTable(this.modalEmails);
            } else {
                alert('Error: ' + (data.message || 'No se pudo completar la acción'));
            }
        } catch (e) { alert('Error: ' + e.message); }
    },

    async toggleEmailIncoming(email, suspend) {
        if (!confirm((suspend ? '📥 Suspender' : '📥 Activar') + ' correo entrante de:\n' + email)) return;
        try {
            var act = suspend ? 'suspend' : 'unsuspend';
            var res = await fetch('api/index.php?action=toggle_email_incoming&user=' + encodeURIComponent(this.modalUser) + '&email=' + encodeURIComponent(email) + '&act=' + act);
            var data = await res.json();
            if (data.success) {
                this.modalEmails.forEach(function (e) {
                    if ((e.email || e.login) === email) e.suspended_incoming = suspend ? 1 : 0;
                });
                var container = document.getElementById('emailTableContainer');
                if (container) container.innerHTML = this.renderEmailTable(this.modalEmails);
            } else {
                alert('Error: ' + (data.message || 'No se pudo completar la acción'));
            }
        } catch (e) { alert('Error: ' + e.message); }
    },

    // ---- ERROR ----
    showError(message) {
        document.getElementById('appContent').innerHTML = `
    < div class="error-state" >
                <div class="icon">⚠️</div>
                <h2>Error de Conexión</h2>
                <p>${message}</p>
                <br>
                <button class="btn-refresh" onclick="App.refresh()">⟳ Reintentar</button>
            </div>`;
    },

    // ---- HELPERS ----
    formatBytesJS(bytes) {
        if (!bytes || bytes === 0) return '0 B';
        var sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        var i = Math.floor(Math.log(bytes) / Math.log(1024));
        if (i >= sizes.length) i = sizes.length - 1;
        return parseFloat((bytes / Math.pow(1024, i)).toFixed(2)) + ' ' + sizes[i];
    },

    // ---- EXCEL (XLS) EXPORT ----
    exportTNAMatrix() {
        if (!this.data || !this.data.accounts || this.data.accounts.length === 0) {
            alert('No hay datos cargados para exportar.');
            return;
        }
        var headers = [
            'TNA NIVEL', 'ESTADO TNA', 'Usuario', 'Dominio', 'Email Contacto', 'Plan',
            'Disco Usado', 'Disco Límite', 'Disco %',
            'Casilla más pesada', 'Inodes %',
            'Bandwidth Usado', 'Bandwidth Límite',
            'Estado WHM', 'Owner',
            'Bases de Datos', 'Días Creación'
        ];

        var html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        html += '<head><meta charset="UTF-8"></head><body><table border="1">';
        html += '<thead><tr><th>' + headers.join('</th><th>') + '</th></tr></thead><tbody>';

        var self = this;
        this.data.accounts.forEach(function (a) {
            var sev = a.severity || { level: 'info', label: 'Sano', code: '🟢' };
            var row = [
                sev.code + ' ' + sev.level.toUpperCase(),
                sev.label,
                a.user || '',
                a.domain || '',
                a.email || '',
                a.plan || '',
                a.disk_used_hr || '',
                a.disk_limit_hr || '',
                (a.disk_percent || 0) + '%',
                self.formatBytesJS(a.max_mailbox_usage || 0),
                (a.inodes_percent || 0) + '%',
                a.bw_used_hr || '',
                a.bw_limit_hr || '',
                a.suspended ? 'Suspendida' : 'Activa',
                a.owner || '',
                a.db_count || '0',
                a.days_since_creation || ''
            ];
            html += '<tr><td>' + row.join('</td><td>') + '</td></tr>';
        });

        html += '</tbody></table></body></html>';

        var blob = new Blob([html], { type: 'application/vnd.ms-excel' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'tna-matrix-' + new Date().toISOString().slice(0, 10) + '.xls';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    },

    exportXLS() {
        if (!this.data || !this.data.accounts || this.data.accounts.length === 0) {
            alert('No hay datos cargados para exportar.');
            return;
        }
        var headers = [
            'Usuario', 'Dominio', 'Plan', 'Disco Usado', 'Disco Límite', 'Disco %', 'Bandwidth Usado', 'Estado'
        ];

        var html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        html += '<head><meta charset="UTF-8"></head><body><table border="1">';
        html += '<thead><tr><th>' + headers.join('</th><th>') + '</th></tr></thead><tbody>';

        this.data.accounts.forEach(function (a) {
            var row = [
                a.user || '',
                a.domain || '',
                a.plan || '',
                a.disk_used_hr || '',
                a.disk_limit_hr || '',
                (a.disk_percent || 0) + '%',
                a.bw_used_hr || '',
                a.suspended ? 'Suspendida' : 'Activa'
            ];
            html += '<tr><td>' + row.join('</td><td>') + '</td></tr>';
        });

        html += '</tbody></table></body></html>';

        var blob = new Blob([html], { type: 'application/vnd.ms-excel' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'whm-report-' + new Date().toISOString().slice(0, 10) + '.xls';
        document.body.appendChild(a);
        a.click();
        URL.revokeObjectURL(url);
    },

    // ---- EXCEL (XLS) DETAIL EXPORT ----
    exportDetailXLS() {
        if (!this.currentDetailData || !this.modalUser) {
            alert('No hay detalles cargados para exportar.');
            return;
        }
        var d = this.currentDetailData;
        var html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        html += '<head><meta charset="UTF-8"></head><body>';

        html += '<h2>Detalle de Cuenta: ' + this.modalUser + '</h2><br>';

        // Emails
        if (d.emails && d.emails.length > 0) {
            html += '<h3>Cuentas de Email</h3><table border="1">';
            html += '<thead><tr><th>Email</th><th>Disco Usado</th><th>Disco Límite</th><th>% Uso</th><th>Reenvíos</th><th>Último Acceso</th><th>Login Suspendido</th><th>Entrante Suspendido</th></tr></thead><tbody>';
            d.emails.forEach(e => {
                var loginSusp = parseInt(e.suspended_login || 0) ? 'Sí' : 'No';
                var incSusp = parseInt(e.suspended_incoming || 0) ? 'Sí' : 'No';
                var mt = parseInt(e.mtime || 0);
                var lastAccess = mt > 0 ? this.formatDateTime(mt * 1000) : 'Sin acceso registrado';
                html += '<tr><td>' + (e.email || '') + '</td><td>' + (e._diskused || '0') + ' MB</td><td>' + (e._diskquota || '0') + ' MB</td><td>' + (e._diskused_percent || '0') + '%</td><td>' + (e.forwarder_count || 0) + '</td><td>' + lastAccess + '</td><td>' + loginSusp + '</td><td>' + incSusp + '</td></tr>';
            });
            html += '</tbody></table><br>';
        }

        // Forwarders
        if (d.forwarders && d.forwarders.length > 0) {
            html += '<h3>Reenvíos / Forwarders</h3><table border="1">';
            html += '<thead><tr><th>Origen</th><th>Destino</th></tr></thead><tbody>';
            d.forwarders.forEach(f => {
                html += '<tr><td>' + (f.html || f.uri || f.email || 'N/A') + '</td><td>' + (f.forward || f.dest || 'N/A') + '</td></tr>';
            });
            html += '</tbody></table><br>';
        }

        // Respuestas Automáticas
        if (d.autoresponders && d.autoresponders.length > 0) {
            html += '<h3>Respuestas Automáticas</h3><table border="1">';
            html += '<thead><tr><th>Email</th><th>Asunto</th><th>Desde</th><th>Hasta</th></tr></thead><tbody>';
            d.autoresponders.forEach(ar => {
                var start = ar.start ? this.formatDateTime(ar.start * 1000, false) : 'N/A';
                var stop = ar.stop ? this.formatDateTime(ar.stop * 1000, false) : 'Indefinido';
                html += '<tr><td>' + (ar.email || 'N/A') + '</td><td>' + (ar.subject || 'N/A') + '</td><td>' + start + '</td><td>' + stop + '</td></tr>';
            });
            html += '</tbody></table><br>';
        }

        // DBs
        if (d.databases && d.databases.length > 0) {
            html += '<h3>Bases de Datos</h3><table border="1">';
            html += '<thead><tr><th>Nombre</th><th>Tamaño</th></tr></thead><tbody>';
            d.databases.forEach(db => {
                html += '<tr><td>' + (db.db || db.database || 'N/A') + '</td><td>' + (db.sizemb ? db.sizemb + ' MB' : (db.disk_usage || 'N/A')) + '</td></tr>';
            });
            html += '</tbody></table><br>';
        }

        html += '</body></html>';

        var blob = new Blob([html], { type: 'application/vnd.ms-excel' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'detalle-' + this.modalUser + '-' + new Date().toISOString().slice(0, 10) + '.xls';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    },

    // ---- FIREWALL ----
    // ---- FIREWALL ----
    firewallIPs: [],
    whitelistIPs: [],

    async loadFirewall() {
        var container = document.getElementById('firewallTableContainer');
        if (!container) return;
        container.innerHTML = '<div style="text-align:center;padding:20px"><div class="loader-spinner" style="margin:0 auto;width:30px;height:30px"></div></div>';
        try {
            var res = await fetch('api/index.php?action=blocked_ips');
            var data = await res.json();
            this.firewallIPs = data.blocked_ips || [];
            this.renderFirewallTable(this.firewallIPs);
        } catch (e) {
            container.innerHTML = '<p style="color:var(--danger)">Error: ' + e.message + '</p>';
        }
    },

    renderFirewallTable(ips) {
        var container = document.getElementById('firewallTableContainer');
        if (!container) return;
        if (ips.length === 0) {
            container.innerHTML = '<p style="color:var(--text-muted);font-size:13px;padding:10px">No hay IPs bloqueadas actualmente.</p>';
            return;
        }
        var rows = '';
        var self = this;
        ips.forEach(function (entry) {
            var isBrute = entry.line && (entry.line.includes('Bruta') || entry.line.includes('Reintentos'));
            var sourceBadge = entry.source === 'csf'
                ? '<span class="badge badge-warning" style="font-size:10px">CSF</span>'
                : (isBrute ? '<span class="badge badge-danger" style="font-size:10px">BRUTE</span>' : '<span class="badge badge-purple" style="font-size:10px">cPHulk</span>');

            rows += '<tr>' +
                '<td class="td-mono" style="font-size:12px">' + entry.ip + ' ' + sourceBadge + '</td>' +
                '<td style="font-size:11px;color:var(--text-muted)">' + (entry.line || '') + '</td>' +
                '<td style="text-align:right;white-space:nowrap">' +
                '<button onclick="App.unblockIP(\'' + entry.ip + '\')" class="btn-action" title="Desbloquear" style="background:var(--warning-dim);color:var(--warning);margin-right:4px">🔓</button>' +
                '<button onclick="App.whitelistIP(\'' + entry.ip + '\', \'Desde Bloqueados\')" class="btn-action" title="Pasar a Whitelist" style="background:var(--accent-dim);color:var(--accent)">✅</button>' +
                '</td>' +
                '</tr>';
        });
        container.innerHTML = '<div class="table-container" style="max-height:400px;overflow-y:auto"><table>' +
            '<thead><tr><th>IP <span style="font-size:9px;opacity:0.4">(entry.ip)</span></th><th>Motivo / Fuente <span style="font-size:9px;opacity:0.4">(entry.line / entry.source)</span></th><th style="text-align:right">Acciones</th></tr></thead>' +
            '<tbody>' + rows + '</tbody></table></div>' +
            '<p style="color:var(--text-muted);font-size:11px;margin-top:8px">' + self.formatNumber(ips.length) + ' bloqueos activos</p>';
    },

    filterFirewall(query) {
        if (!this.firewallIPs) return;
        var q = query.toLowerCase().trim();
        var filtered = q ? this.firewallIPs.filter(function (e) {
            return e.ip.includes(q) || (e.line && e.line.toLowerCase().includes(q));
        }) : this.firewallIPs;
        this.renderFirewallTable(filtered);
    },

    async unblockIP(ip) {
        if (!confirm('¿Desbloquear la IP ' + ip + '?')) return;
        try {
            var res = await fetch('api/index.php?action=unblock_ip&ip=' + encodeURIComponent(ip));
            var data = await res.json();
            if (data.error) alert('Error: ' + data.message);
            else {
                this.loadFirewall();
                this.loadWhitelisted(); // Por si acaso estaba en ambos (raro)
            }
        } catch (e) { alert('Error: ' + e.message); }
    },

    // ---- WHITELIST ----
    async loadWhitelisted() {
        var container = document.getElementById('whitelistTableContainer');
        if (!container) return;
        container.innerHTML = '<div style="text-align:center;padding:20px"><div class="loader-spinner" style="margin:0 auto;width:30px;height:30px"></div></div>';
        try {
            var res = await fetch('api/index.php?action=whitelisted_ips');
            var data = await res.json();
            this.whitelistIPs = data.whitelisted_ips || [];
            this.renderWhitelistTable(this.whitelistIPs);
        } catch (e) {
            container.innerHTML = '<p style="color:var(--danger)">Error: ' + e.message + '</p>';
        }
    },

    renderWhitelistTable(ips) {
        var container = document.getElementById('whitelistTableContainer');
        if (!container) return;
        if (ips.length === 0) {
            container.innerHTML = '<p style="color:var(--text-muted);font-size:13px;padding:10px">La lista blanca está vacía.</p>';
            return;
        }
        var rows = '';
        ips.forEach(function (entry) {
            rows += '<tr>' +
                '<td class="td-mono" style="font-size:12px">' + entry.ip + '</td>' +
                '<td style="font-size:11px;color:var(--text-muted)">' + (entry.comment || '') + '</td>' +
                '<td style="text-align:right">' +
                '<button onclick="App.removeWhitelistIP(\'' + entry.ip + '\')" class="btn-action" title="Eliminar" style="background:var(--danger-dim);color:var(--danger)">✕</button>' +
                '</td>' +
                '</tr>';
        });
        container.innerHTML = '<div class="table-container" style="max-height:400px;overflow-y:auto"><table>' +
            '<thead><tr><th>IP <span style="font-size:9px;opacity:0.4">(entry.ip)</span></th><th>Comentario <span style="font-size:9px;opacity:0.4">(entry.comment)</span></th><th style="text-align:right"></th></tr></thead>' +
            '<tbody>' + rows + '</tbody></table></div>';
    },

    async whitelistIP(ip, comment = '') {
        if (!confirm('¿Añadir ' + ip + ' a la Lista Blanca?')) return;
        try {
            var res = await fetch('api/index.php?action=whitelist_ip&ip=' + encodeURIComponent(ip) + '&comment=' + encodeURIComponent(comment));
            var data = await res.json();
            if (data.error) alert('Error: ' + data.message);
            else {
                this.loadWhitelisted();
                this.loadFirewall(); // Remover de bloqueos si estaba ahí
            }
        } catch (e) { alert('Error: ' + e.message); }
    },

    async removeWhitelistIP(ip) {
        if (!confirm('¿Eliminar ' + ip + ' de la Lista Blanca?')) return;
        try {
            var res = await fetch('api/index.php?action=remove_whitelist_ip&ip=' + encodeURIComponent(ip));
            var data = await res.json();
            if (data.error) alert('Error: ' + data.message);
            else this.loadWhitelisted();
        } catch (e) { alert('Error: ' + e.message); }
    },

    manualWhitelist() {
        var input = document.getElementById('manualWhitelistIp');
        var ip = input.value.trim();
        if (!ip) return;
        this.whitelistIP(ip, 'Manual');
        input.value = '';
    },

    async showEmailLog(email) {
        // Buscar fila de log
        var targetRow = null;
        var emailRows = document.querySelectorAll('[id^="emailrow-"]');
        emailRows.forEach(function (row) {
            var emailCell = row.querySelector('td.td-mono');
            if (emailCell && emailCell.textContent.trim().slice(0, email.length) === email) {
                var idx = row.id.replace('emailrow-', '');
                targetRow = document.getElementById('emaillog-' + idx);
            }
        });
        if (!targetRow) return;

        // Toggle
        if (targetRow.style.display !== 'none' && targetRow.style.display !== '') {
            targetRow.style.display = 'none';
            return;
        }

        var td = targetRow.querySelector('td');

        // --- 1. MOSTRAR DATOS CONOCIDOS INMEDIATAMENTE ---
        var knownEmail = null;
        this.modalEmails.forEach(function (e) {
            if ((e.email || e.login) === email) knownEmail = e;
        });

        var entryHtml = function (line, src, highlight) {
            var color = highlight ? 'var(--warning)' : 'var(--text-secondary)';
            var bg = highlight ? 'rgba(255,176,32,0.06)' : 'transparent';
            var badge = '<span style="background:var(--border);color:var(--text-muted);padding:1px 5px;border-radius:3px;font-size:10px;margin-right:6px">' + src + '</span>';
            return '<div style="font-family:var(--font-mono);font-size:10px;color:' + color + ';background:' + bg + ';padding:3px 4px;border-radius:3px;margin-bottom:2px;word-break:break-all">' + badge + line + '</div>';
        };

        var knownHtml = '';
        if (knownEmail) {
            var now = Math.floor(Date.now() / 1000);
            var isLoginSusp = parseInt(knownEmail.suspended_login || 0) === 1;
            var isIncomingSusp = parseInt(knownEmail.suspended_incoming || 0) === 1;
            var mtime = parseInt(knownEmail.mtime || 0);
            var lastAccess = mtime > 0 ? this.formatDateTime(mtime * 1000) : 'Sin registro';
            var daysAgo = mtime > 0 ? Math.floor((now - mtime) / 86400) : null;

            knownHtml += '<div style="margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid var(--border)">';
            knownHtml += '<div style="font-size:10px;color:var(--text-muted);margin-bottom:5px;text-transform:uppercase;letter-spacing:1px">📊 Estado de la cuenta</div>';
            knownHtml += entryHtml('Login suspendido: ' + (isLoginSusp ? 'SÍ' : 'NO'), 'estado', isLoginSusp);
            knownHtml += entryHtml('Entrante suspendido: ' + (isIncomingSusp ? 'SÍ' : 'NO'), 'estado', isIncomingSusp);
            var displayQuota = knownEmail.humandiskquota || knownEmail.diskquota || '?';
            if (displayQuota === 'None' || displayQuota === 'unlimited') displayQuota = '<span class="text-danger">Ilimitado</span>';

            knownHtml += entryHtml('Disco: ' + (knownEmail.humandiskused || '?') + ' de ' + displayQuota, 'estado');
            knownHtml += entryHtml('Último acceso: ' + lastAccess + (daysAgo !== null ? ' (hace ' + this.formatNumber(daysAgo) + ' días)' : ''), 'estado', daysAgo > 60);
            knownHtml += '</div>';
        }

        var closeBtn = '<button onclick="document.getElementById(\'' + targetRow.id + '\').style.display=\'none\'" style="background:transparent;border:none;color:var(--text-muted);cursor:pointer;font-size:12px">✕ Cerrar</button>';

        td.innerHTML = '<div style="padding:12px;background:var(--bg-input);max-height:360px;overflow-y:auto">' +
            '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">' +
            '<span style="font-size:11px;color:var(--info);font-family:var(--font-mono)">🔍 ' + email + '</span>' +
            closeBtn + '</div>' +
            knownHtml +
            '<div id="emaillog-api-' + targetRow.id + '" style="color:var(--text-muted);font-size:10px">Consultando servidor...<div class="loader-spinner" style="width:16px;height:16px;border-width:2px;margin:6px 0"></div></div>' +
            '</div>';
        targetRow.style.display = '';

        // --- 2. CARGAR DATOS DEL API Y AGREGAR ---
        try {
            var res = await fetch('api/index.php?action=email_log&email=' + encodeURIComponent(email) + '&user=' + encodeURIComponent(this.modalUser));
            var data = await res.json();
            var apiDiv = document.getElementById('emaillog-api-' + targetRow.id);
            if (!apiDiv) return;

            if (data.error || !data.entries || data.entries.length === 0) {
                apiDiv.innerHTML = '<div style="color:var(--text-muted);font-size:10px">' + (data.message || 'Sin registros adicionales del servidor.') + '</div>';
                return;
            }

            var html = '<div style="font-size:10px;color:var(--text-muted);margin:8px 0 5px;text-transform:uppercase;letter-spacing:1px">📋 Diagnóstico del servidor (' + data.entries.length + ' registros)</div>';
            data.entries.forEach(function (entry) {
                html += entryHtml(entry.line, entry.source, entry.highlight);
            });
            apiDiv.innerHTML = html;
        } catch (e) {
            var apiDiv2 = document.getElementById('emaillog-api-' + targetRow.id);
            if (apiDiv2) apiDiv2.innerHTML = '<div style="color:var(--danger);font-size:10px">Error API: ' + e.message + '</div>';
        }
    },

    async manualUnblock() {
        var input = document.getElementById('manualUnblockIp');
        var ip = input ? input.value.trim() : '';
        if (!ip) { alert('Ingresa una IP válida.'); return; }
        await this.unblockIP(ip);
        if (input) input.value = '';
    },

    // =========================================================
    // RESELLER MANAGEMENT
    // =========================================================

    _resellers: [],

    async loadResellers() {
        var cards = document.getElementById('resellerCards');
        var table = document.getElementById('resellerTable');
        var badge = document.getElementById('resellerCount');
        if (cards) cards.innerHTML = '<p style="color:var(--text-muted);font-size:13px">⏳ Cargando resellers...</p>';

        try {
            var res = await fetch('api/index.php?action=list_resellers');
            var data = await res.json();
            this._resellers = data.resellers || [];
        } catch (e) {
            if (cards) cards.innerHTML = '<p style="color:var(--danger);font-size:13px">❌ Error al cargar resellers: ' + e.message + '</p>';
            return;
        }

        if (badge) badge.textContent = this._resellers.length + ' resellers';
        this.renderResellerCards(this._resellers);
        this.renderResellerTable(this._resellers);
    },

    formatMB(mb) {
        if (!mb || mb == 0) return 'Ilimitado';
        if (mb >= 1024 * 1024) return (mb / 1024 / 1024).toFixed(1) + ' TB';
        if (mb >= 1024) return (mb / 1024).toFixed(1) + ' GB';
        return mb + ' MB';
    },

    renderResellerCards(resellers) {
        var cards = document.getElementById('resellerCards');
        if (!cards) return;
        if (!resellers.length) {
            cards.innerHTML = '<p style="color:var(--text-muted);font-size:13px">No hay resellers configurados en el servidor.</p>';
            return;
        }
        cards.innerHTML = resellers.map(function (r) {
            var diskPct = 0;
            if (r.disklimit && r.disklimit !== 'unlimited' && r.disklimit > 0) {
                diskPct = Math.min(100, Math.round((r.diskused / r.disklimit) * 100));
            }
            var fillClass = diskPct >= 85 ? 'danger' : diskPct >= 70 ? 'warning' : '';
            var suspClass = r.suspended ? ' suspended' : '';
            var suspIcon = r.suspended ? '🔴' : '🟢';
            return `<div class="reseller-card${suspClass}" onclick="App.showResellerDetail('${r.name}')">
                <div class="reseller-card-name">${suspIcon} 👤 ${r.name}</div>
                <div class="reseller-card-stats">
                    <div class="reseller-stat">
                        <div class="reseller-stat-label">Cuentas <span style="font-size:9px;opacity:0.4">(acctcount)</span></div>
                        <div class="reseller-stat-value">${r.acctcount}</div>
                    </div>
                    <div class="reseller-stat">
                        <div class="reseller-stat-label">Disco Usado <span style="font-size:9px;opacity:0.4">(diskused)</span></div>
                        <div class="reseller-stat-value">${App.formatMB(r.diskused)}</div>
                    </div>
                    <div class="reseller-stat">
                        <div class="reseller-stat-label">Límite Disco <span style="font-size:9px;opacity:0.4">(disklimit)</span></div>
                        <div class="reseller-stat-value">${r.disklimit === 'unlimited' || !r.disklimit ? '<span class="text-danger">Ilimitado</span>' : App.formatMB(r.disklimit)}</div>
                    </div>
                    <div class="reseller-stat">
                        <div class="reseller-stat-label">BW Usado <span style="font-size:9px;opacity:0.4">(bwused)</span></div>
                        <div class="reseller-stat-value">${App.formatMB(r.bwused)}</div>
                    </div>
                </div>
                ${diskPct > 0 ? `<div class="reseller-usage-bar"><div class="reseller-usage-fill ${fillClass}" style="width:${diskPct}%"></div></div>` : ''}
            </div>`;
        }).join('');
    },

    renderResellerTable(resellers) {
        var tbody = document.getElementById('resellerTable');
        if (!tbody) return;
        if (!resellers.length) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:var(--text-muted)">Sin resellers</td></tr>';
            return;
        }
        tbody.innerHTML = resellers.map(function (r, idx) {
            var suspBadge = r.suspended
                ? '<span class="badge badge-suspended">🔴 Suspendido</span>'
                : '<span class="badge badge-active">🟢 Activo</span>';
            var suspBtn = r.suspended
                ? `<button onclick="event.stopPropagation();App.unsuspendReseller('${r.name}')" style="padding:3px 8px;border:none;border-radius:5px;cursor:pointer;font-size:11px;background:var(--accent);color:#000;margin-right:4px">▶ Reactivar</button>`
                : `<button onclick="event.stopPropagation();App.suspendReseller('${r.name}')" style="padding:3px 8px;border:none;border-radius:5px;cursor:pointer;font-size:11px;background:var(--warning);color:#000;margin-right:4px">⏸ Suspender</button>`;
            return `<tr style="cursor:pointer" onclick="App.showResellerDetail('${r.name}')">
                <td style="color:var(--text-muted);font-size:11px;font-family:var(--font-mono);text-align:center">${idx + 1}</td>
                <td class="td-user" style="color:var(--purple)">${r.name}</td>
                <td class="td-mono">${r.acctcount}</td>
                <td class="td-mono">${App.formatMB(r.diskused)}</td>
                <td class="td-mono">${r.disklimit === 'unlimited' || !r.disklimit ? '<span class="text-danger">Ilimitado</span>' : App.formatMB(r.disklimit)}</td>
                <td class="td-mono">${App.formatMB(r.bwused)}</td>
                <td>${suspBadge}</td>
                <td style="text-align:center;white-space:nowrap" onclick="event.stopPropagation()">
                    ${suspBtn}
                    <button onclick="App.showResellerDetail('${r.name}')" style="padding:3px 8px;border:none;border-radius:5px;cursor:pointer;font-size:11px;background:var(--purple-dim);color:var(--purple);margin-right:4px">📋 Detalle</button>
                    <button onclick="event.stopPropagation();App.removeReseller('${r.name}')" style="padding:3px 8px;border:none;border-radius:5px;cursor:pointer;font-size:11px;background:var(--danger-dim);color:var(--danger)">🗑 Quitar Rol</button>
                </td>
            </tr>`;
        }).join('');
    },

    async showResellerDetail(name) {
        var r = this._resellers.find(function (x) { return x.name === name; });
        if (!r) return;

        var title = document.getElementById('modalTitle');
        var content = document.getElementById('modalContent');
        var overlay = document.getElementById('modalOverlay');
        if (!overlay) return;
        title.textContent = '👤 Reseller: ' + name;
        content.innerHTML = '<p style="color:var(--text-muted);font-size:13px">⏳ Cargando detalle...</p>';
        overlay.classList.add('active');

        // Build accounts list
        var accts = r.accts || [];
        var allResellers = this._resellers.map(function (x) { return x.name; });

        var accountRows = accts.map(function (a, idx) {
            var user = a.user || a;
            var domain = a.domain || '';
            var diskHr = a.diskhuman || '';
            var status = parseInt(a.suspended || 0) ? '<span class="badge badge-suspended">Suspendida</span>' : '<span class="badge badge-active">Activa</span>';
            var reassignOptions = allResellers.filter(function (rn) { return rn !== name; }).map(function (rn) {
                return `<option value="${rn}">${rn}</option>`;
            }).join('');
            return `<tr>
                <td style="color:var(--text-muted);font-size:11px;text-align:center">${idx + 1}</td>
                <td class="td-user" style="cursor:pointer" onclick="App.showDetail('${user}')">${user}</td>
                <td class="td-domain">${domain}</td>
                <td class="td-mono">${diskHr}</td>
                <td>${status}</td>
                <td>
                    <select onchange="App.reassignAccount('${user}', this.value)" style="padding:3px 6px;background:var(--bg-input);border:1px solid var(--border);border-radius:5px;color:var(--text-primary);font-size:11px">
                        <option value="">Reasignar a...</option>
                        <option value="root">root (servidor)</option>
                        ${reassignOptions}
                    </select>
                </td>
            </tr>`;
        }).join('');

        var diskPct = (r.disklimit && r.disklimit !== 'unlimited' && r.disklimit > 0)
            ? Math.min(100, Math.round((r.diskused / r.disklimit) * 100)) : 0;

        content.innerHTML = `
        <div class="modal-grid" style="margin-bottom:16px">
            <div class="modal-item">
                <div class="modal-item-label">Estado</div>
                <div class="modal-item-value">${r.suspended ? '<span class="badge badge-suspended">🔴 Suspendido</span>' : '<span class="badge badge-active">🟢 Activo</span>'}</div>
            </div>
            <div class="modal-item">
                <div class="modal-item-label">Cuentas Asignadas</div>
                <div class="modal-item-value" style="color:var(--purple);font-weight:700;font-size:20px">${r.acctcount}</div>
            </div>
            <div class="modal-item">
                <div class="modal-item-label">Disco Usado <span style="font-size:9px;opacity:0.4">(r.diskused)</span></div>
                <div class="modal-item-value">${this.formatMB(r.diskused)} ${diskPct > 0 ? '<span style="font-size:11px;color:var(--text-muted)">(' + diskPct + '%)</span>' : ''}</div>
            </div>
            <div class="modal-item">
                <div class="modal-item-label">Límite Disco <span style="font-size:9px;opacity:0.4">(r.disklimit)</span></div>
                <div class="modal-item-value">${(!r.disklimit || r.disklimit === 'unlimited') ? '<span class="text-danger">Ilimitado</span>' : this.formatMB(r.disklimit)}</div>
            </div>
            <div class="modal-item">
                <div class="modal-item-label">BW Usado <span style="font-size:9px;opacity:0.4">(r.bwused)</span></div>
                <div class="modal-item-value">${this.formatMB(r.bwused)}</div>
            </div>
            <div class="modal-item">
                <div class="modal-item-label">Límite BW <span style="font-size:9px;opacity:0.4">(r.bwlimit)</span></div>
                <div class="modal-item-value">${(!r.bwlimit || r.bwlimit === 'unlimited') ? '<span class="text-danger">Ilimitado</span>' : this.formatMB(r.bwlimit)}</div>
            </div>
        </div>

        <div class="modal-section-title">⚙️ Límites de Recursos</div>
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:10px;padding:16px;margin-bottom:16px">
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:12px">
                <div>
                    <label style="font-size:11px;color:var(--text-muted);display:block;margin-bottom:4px">Disco (MB, 0=ilimitado)</label>
                    <input type="number" id="rl_disk_${name}" value="${r.disklimit && r.disklimit !== 'unlimited' ? r.disklimit : 0}" min="0"
                        style="width:100%;padding:6px 10px;background:var(--bg-input);border:1px solid var(--border);border-radius:6px;color:var(--text-primary);font-size:13px">
                </div>
                <div>
                    <label style="font-size:11px;color:var(--text-muted);display:block;margin-bottom:4px">BW (MB, 0=ilimitado)</label>
                    <input type="number" id="rl_bw_${name}" value="${r.bwlimit && r.bwlimit !== 'unlimited' ? r.bwlimit : 0}" min="0"
                        style="width:100%;padding:6px 10px;background:var(--bg-input);border:1px solid var(--border);border-radius:6px;color:var(--text-primary);font-size:13px">
                </div>
                <div>
                    <label style="font-size:11px;color:var(--text-muted);display:block;margin-bottom:4px">Máx. Cuentas (0=ilimitado)</label>
                    <input type="number" id="rl_acct_${name}" value="0" min="0"
                        style="width:100%;padding:6px 10px;background:var(--bg-input);border:1px solid var(--border);border-radius:6px;color:var(--text-primary);font-size:13px">
                </div>
            </div>
            <button onclick="App.saveResellerLimits('${name}')" style="padding:7px 18px;background:var(--accent);color:#000;border:none;border-radius:7px;cursor:pointer;font-size:13px;font-weight:600">💾 Guardar Límites</button>
        </div>

        <div class="modal-section-title">🚦 Acciones</div>
        <div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap">
            ${r.suspended
                ? `<button onclick="App.unsuspendReseller('${name}')" style="padding:7px 16px;background:var(--accent);color:#000;border:none;border-radius:7px;cursor:pointer;font-size:13px;font-weight:600">▶ Reactivar Reseller</button>`
                : `<button onclick="App.suspendReseller('${name}')" style="padding:7px 16px;background:var(--warning);color:#000;border:none;border-radius:7px;cursor:pointer;font-size:13px;font-weight:600">⏸ Suspender Reseller</button>`
            }
            <button onclick="App.removeReseller('${name}')" style="padding:7px 16px;background:var(--orange-dim);color:var(--orange);border:1px solid var(--orange);border-radius:7px;cursor:pointer;font-size:13px">🛡️ Quitar Rol Reseller</button>
            <button onclick="App.terminateReseller('${name}')" style="padding:7px 16px;background:var(--danger-dim);color:var(--danger);border:1px solid var(--danger);border-radius:7px;cursor:pointer;font-size:13px;font-weight:700">⚠️ TERMINAR Reseller + Cuentas</button>
        </div>

        <div class="modal-section-title">📋 Cuentas Asignadas (${accts.length})</div>
        <div class="table-container">
            <div class="table-scroll">
                <table>
                    <thead><tr>
                        <th style="width:36px;text-align:center">#</th>
                        <th>Usuario</th>
                        <th>Dominio</th>
                        <th>Disco</th>
                        <th>Estado</th>
                        <th>Reasignar</th>
                    </tr></thead>
                    <tbody>${accountRows || '<tr><td colspan="6" style="text-align:center;color:var(--text-muted)">Sin cuentas asignadas</td></tr>'}</tbody>
                </table>
            </div>
        </div>`;
    },

    async saveResellerLimits(name) {
        var disk = document.getElementById('rl_disk_' + name);
        var bw = document.getElementById('rl_bw_' + name);
        var acct = document.getElementById('rl_acct_' + name);
        var fd = new FormData();
        fd.append('action', 'set_reseller_limits');
        fd.append('reseller', name);
        fd.append('diskquota', disk ? disk.value : 0);
        fd.append('bwlimit', bw ? bw.value : 0);
        fd.append('maxacct', acct ? acct.value : 0);
        try {
            var res = await fetch('api/index.php', { method: 'POST', body: fd });
            var data = await res.json();
            alert(data.error ? '❌ Error: ' + data.message : '✅ Límites actualizados correctamente para ' + name);
        } catch (e) { alert('❌ Error: ' + e.message); }
    },

    async suspendReseller(name) {
        var reason = prompt('Razón de suspensión para "' + name + '" (opcional):') ?? '';
        var fd = new FormData();
        fd.append('action', 'suspend_reseller');
        fd.append('reseller', name);
        if (reason) fd.append('reason', reason);
        try {
            var res = await fetch('api/index.php', { method: 'POST', body: fd });
            var data = await res.json();
            alert(data.error ? '❌ Error: ' + data.message : '✅ Reseller "' + name + '" suspendido.');
            this.closeModal();
            this.loadResellers();
        } catch (e) { alert('❌ Error: ' + e.message); }
    },

    async unsuspendReseller(name) {
        var fd = new FormData();
        fd.append('action', 'unsuspend_reseller');
        fd.append('reseller', name);
        try {
            var res = await fetch('api/index.php', { method: 'POST', body: fd });
            var data = await res.json();
            alert(data.error ? '❌ Error: ' + data.message : '✅ Reseller "' + name + '" reactivado.');
            this.closeModal();
            this.loadResellers();
        } catch (e) { alert('❌ Error: ' + e.message); }
    },

    async removeReseller(name) {
        if (!confirm('⚠️ ¿Quitar el rol de Reseller a "' + name + '"?\n\nSus cuentas permanecerán activas pero pasarán a estar bajo "root".')) return;
        var fd = new FormData();
        fd.append('action', 'remove_reseller');
        fd.append('reseller', name);
        try {
            var res = await fetch('api/index.php', { method: 'POST', body: fd });
            var data = await res.json();
            alert(data.error ? '❌ Error: ' + data.message : '✅ Rol de reseller removido de "' + name + '".');
            this.closeModal();
            this.loadResellers();
        } catch (e) { alert('❌ Error: ' + e.message); }
    },

    async terminateReseller(name) {
        if (!confirm('🚨 ADVERTENCIA: Esto eliminará el reseller "' + name + '" Y TODAS SUS CUENTAS.\n\nEsta acción es IRREVERSIBLE. ¿Está seguro?')) return;
        var confirm2 = prompt('🚨 SEGUNDA CONFIRMACIÓN\n\nEscriba exactamente "Eliminar ' + name + '" para confirmar:');
        if (confirm2 !== 'Eliminar ' + name) { alert('❌ Texto incorrecto. Operación cancelada.'); return; }
        var fd = new FormData();
        fd.append('action', 'terminate_reseller');
        fd.append('reseller', name);
        try {
            var res = await fetch('api/index.php', { method: 'POST', body: fd });
            var data = await res.json();
            alert(data.error ? '❌ Error: ' + data.message : '✅ Reseller "' + name + '" y sus cuentas han sido eliminados.');
            this.closeModal();
            this.loadResellers();
        } catch (e) { alert('❌ Error: ' + e.message); }
    },

    async reassignAccount(user, newOwner) {
        if (!newOwner) return;
        if (!confirm('¿Reasignar la cuenta "' + user + '" al reseller "' + newOwner + '"?')) return;
        var fd = new FormData();
        fd.append('action', 'reassign_account');
        fd.append('user', user);
        fd.append('new_owner', newOwner);
        try {
            var res = await fetch('api/index.php', { method: 'POST', body: fd });
            var data = await res.json();
            alert(data.error ? '❌ Error: ' + data.message : '✅ Cuenta "' + user + '" reasignada a "' + newOwner + '".');
            this.loadResellers();
        } catch (e) { alert('❌ Error: ' + e.message); }
    },

    async showCreateResellerModal() {
        // List current non-reseller accounts
        var existingResellers = this._resellers.map(function (r) { return r.name; });
        var allAccts = this.data ? (this.data.accounts || []) : [];
        var nonResellers = allAccts.filter(function (a) { return !existingResellers.includes(a.user); });

        var options = nonResellers.map(function (a) {
            return `<option value="${a.user}">${a.user} (${a.domain})</option>`;
        }).join('');

        var title = document.getElementById('modalTitle');
        var content = document.getElementById('modalContent');
        var overlay = document.getElementById('modalOverlay');
        title.textContent = '➕ Crear Nuevo Reseller';
        content.innerHTML = `
        <div style="padding:8px 0">
            <p style="color:var(--text-muted);font-size:13px;margin-bottom:16px">Selecciona una cuenta cPanel existente para convertirla en Reseller. Esto le dará acceso a gestionar sub-cuentas bajo su nombre.</p>
            <div style="margin-bottom:16px">
                <label style="font-size:12px;color:var(--text-muted);display:block;margin-bottom:6px">Cuenta a convertir en Reseller:</label>
                <select id="newResellerUser" style="width:100%;padding:8px 12px;background:var(--bg-input);border:1px solid var(--border);border-radius:8px;color:var(--text-primary);font-size:14px">
                    ${options || '<option value="">No hay cuentas disponibles</option>'}
                </select>
            </div>
            <button onclick="App.createReseller()" style="padding:8px 20px;background:var(--accent);color:#000;border:none;border-radius:8px;cursor:pointer;font-size:14px;font-weight:700">✅ Crear Reseller</button>
        </div>`;
        overlay.classList.add('active');
    },

    async createReseller() {
        var sel = document.getElementById('newResellerUser');
        var user = sel ? sel.value : '';
        if (!user) { alert('Selecciona una cuenta.'); return; }
        var fd = new FormData();
        fd.append('action', 'create_reseller');
        fd.append('user', user);
        try {
            var res = await fetch('api/index.php', { method: 'POST', body: fd });
            var data = await res.json();
            alert(data.error ? '❌ Error: ' + data.message : '✅ "' + user + '" ahora es un Reseller.');
            this.closeModal();
            this.loadResellers();
        } catch (e) { alert('❌ Error: ' + e.message); }
    },

    _checkFwdPolicy(email, fwds, rowId) {
        var msgEl = document.getElementById('fwdMsg-' + rowId);
        if (!msgEl) return;

        var alerts = [];
        var externalCount = 0;
        var myDomain = email.split('@')[1];

        // Obtener data de la cuenta para análisis holístico
        var accountInfo = this.modalEmails ? this.modalEmails.find(m => (m.email || m.login) === email) : null;
        var isHighUsage = accountInfo && (parseFloat(accountInfo._diskused) / (1024 * 1024 * 1024)) > 10; // > 10GB
        var isSuspended = accountInfo && (accountInfo.suspended_login == 1 || accountInfo.suspended_incoming == 1);

        fwds.forEach(f => {
            var dest = f.forward || f.html_forward || f.dest || String(f);
            if (dest === email) {
                alerts.push('<span style="color:var(--danger)">🚨 <b>Bucle Detectado:</b> El correo se reenvía a sí mismo. Genera carga innecesaria en el servidor. Recomendamos eliminarlo.</span>');
            }
            var destDomain = dest.split('@')[1];
            if (destDomain && destDomain !== myDomain) externalCount++;
        });

        if (externalCount > 0) {
            if (isHighUsage) {
                alerts.push('<span style="color:var(--info)">🚀 <b>Migración Sugerida:</b> Esta cuenta usa ' + externalCount + ' reenvíos externos y tiene un alto consumo de disco (' + formatBytesJS(accountInfo._diskused) + '). Recomendamos migrar a <b>Google Workspace</b> o <b>M365</b> para usar apps oficiales y liberar recursos del servidor.</span>');
            } else {
                alerts.push('<span style="color:var(--info)">ℹ️ <b>Reenvío Externo:</b> Recomendamos usar apps oficiales (Gmail/Outlook) vía IMAP en lugar de reenvíos externos para evitar bloqueos de Spam y asegurar la entrega.</span>');
            }
        }

        if (isSuspended) {
            alerts.push('<span style="color:var(--danger)">⚠️ <b>Casilla Restringida:</b> Esta cuenta tiene el acceso o la entrada suspendida. Los reenvíos podrían no funcionar correctamente hasta que se reactive.</span>');
        }

        if (alerts.length > 0) {
            msgEl.innerHTML = '<div style="margin-top:10px;padding:10px;background:rgba(255,255,255,0.05);border-radius:8px;border-left:4px solid var(--accent);display:flex;flex-direction:column;gap:8px;font-size:12px">' + alerts.join('') + '</div>';
        } else {
            msgEl.innerHTML = '';
        }
    }
};

// Init on DOM ready
document.addEventListener('DOMContentLoaded', () => App.init());
