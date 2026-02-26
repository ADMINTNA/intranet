/**
 * WHM Server Report - Frontend Application
 * Icontel Intranet
 */

const App = {
    data: null,
    currentTab: 'overview',
    sortState: {},

    // ---- INIT ----
    async init() {
        this.showLoader(true);
        this.bindEvents();
        await this.loadReport();
        this.showLoader(false);
    },

    bindEvents() {
        document.getElementById('btnRefresh').addEventListener('click', () => this.refresh());
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.switchTab(e.target.closest('.tab-btn').dataset.tab));
        });
    },

    async refresh() {
        const btn = document.getElementById('btnRefresh');
        btn.innerHTML = '⟳ Cargando...';
        btn.disabled = true;
        this.showLoader(true);
        await this.loadReport();
        this.showLoader(false);
        btn.innerHTML = '⟳ Actualizar';
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
            const res = await fetch('api/index.php?action=report');
            const data = await res.json();
            if (data.error) {
                this.showError(data.message);
                return;
            }
            this.data = data;
            this.render();
        } catch (err) {
            this.showError('No se pudo conectar con la API. Verifica la configuración en config/config.php');
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
        this.renderAllAccounts();
        this.updateTimestamp();
    },

    renderSummary() {
        const s = this.data.summary;
        document.getElementById('totalAccounts').textContent = s.total_accounts;
        document.getElementById('activeAccounts').textContent = s.active_accounts;
        document.getElementById('suspendedAccounts').textContent = s.suspended_accounts;
        document.getElementById('diskUsed').textContent = s.total_disk_used_hr;
        document.getElementById('diskPercent').textContent = s.disk_percent + '%';

        // Contar inactivas (sin bandwidth / poco movimiento)
        const inactive = this.data.accounts.filter(a => a.bw_used < 1024 && !a.suspended).length;
        document.getElementById('inactiveAccounts').textContent = inactive;
    },

    renderDiskBar() {
        const s = this.data.summary;
        const bar = document.getElementById('diskBarFill');
        const stats = document.getElementById('diskBarStats');
        
        bar.style.width = Math.min(s.disk_percent, 100) + '%';
        bar.className = 'disk-bar-fill';
        if (s.disk_percent > 85) bar.classList.add('danger');
        else if (s.disk_percent > 70) bar.classList.add('warning');
        
        stats.textContent = `${s.total_disk_used_hr} / ${s.total_disk_limit_hr} (${s.disk_percent}%)`;
    },

    renderTopSpace() {
        const container = document.getElementById('topSpaceTable');
        const mode = document.querySelector('#topSpaceToggle .toggle-btn.active')?.dataset.mode || 'most';
        
        let sorted = [...this.data.accounts].sort((a, b) => {
            return mode === 'most' ? b.disk_used - a.disk_used : a.disk_used - b.disk_used;
        });
        
        // Filtrar cuentas sin espacio si buscamos las menores
        if (mode === 'least') {
            sorted = sorted.filter(a => a.disk_used > 0);
        }
        
        const top10 = sorted.slice(0, 10);
        const maxDisk = top10.length > 0 ? Math.max(...top10.map(a => a.disk_used)) : 1;
        
        container.innerHTML = top10.map((acct, i) => {
            const pct = maxDisk > 0 ? (acct.disk_used / maxDisk * 100) : 0;
            const barColor = acct.disk_percent > 85 ? 'red' : acct.disk_percent > 70 ? 'yellow' : 'green';
            return `
                <tr class="animate-in delay-${Math.min(i+1, 6)}" onclick="App.showDetail('${acct.user}')" style="cursor:pointer">
                    <td><span class="rank ${i < 3 ? 'rank-'+(i+1) : 'rank-n'}">${i+1}</span></td>
                    <td><span class="td-user">${acct.user}</span></td>
                    <td class="td-domain">${acct.domain}</td>
                    <td class="td-mono">${acct.disk_used_hr}</td>
                    <td class="td-mono">${acct.disk_limit_hr}</td>
                    <td>
                        <span class="td-mono">${acct.disk_percent}%</span>
                        <span class="mini-bar"><span class="mini-bar-fill ${barColor}" style="width:${Math.min(acct.disk_percent, 100)}%"></span></span>
                    </td>
                    <td class="td-mono">${acct.plan}</td>
                </tr>`;
        }).join('');
    },

    renderTopActivity() {
        const container = document.getElementById('topActivityTable');
        const mode = document.querySelector('#topActivityToggle .toggle-btn.active')?.dataset.mode || 'most';
        
        let sorted = [...this.data.accounts].sort((a, b) => {
            return mode === 'most' ? b.bw_used - a.bw_used : a.bw_used - b.bw_used;
        });
        
        if (mode === 'least') {
            sorted = sorted.filter(a => !a.suspended);
        }
        
        const top10 = sorted.slice(0, 10);
        const maxBw = top10.length > 0 ? Math.max(...top10.map(a => a.bw_used), 1) : 1;
        
        container.innerHTML = top10.map((acct, i) => {
            const pct = maxBw > 0 ? (acct.bw_used / maxBw * 100) : 0;
            return `
                <tr class="animate-in delay-${Math.min(i+1, 6)}" onclick="App.showDetail('${acct.user}')" style="cursor:pointer">
                    <td><span class="rank ${i < 3 ? 'rank-'+(i+1) : 'rank-n'}">${i+1}</span></td>
                    <td><span class="td-user">${acct.user}</span></td>
                    <td class="td-domain">${acct.domain}</td>
                    <td class="td-mono">${acct.bw_used_hr}</td>
                    <td class="td-mono">${acct.bw_limit_hr}</td>
                    <td>
                        <span class="mini-bar" style="width:100px"><span class="mini-bar-fill green" style="width:${Math.min(pct, 100)}%"></span></span>
                    </td>
                    <td>${acct.suspended ? '<span class="badge badge-suspended">Suspendida</span>' : '<span class="badge badge-active">Activa</span>'}</td>
                </tr>`;
        }).join('');
    },

    renderInactiveAccounts() {
        const container = document.getElementById('inactiveTable');
        const filterDays = parseInt(document.getElementById('inactiveDaysFilter')?.value || '0');
        
        // Cuentas con muy poco o nada de bandwidth = sin movimiento
        let inactive = this.data.accounts.filter(a => a.bw_used < 1024 && !a.suspended);
        
        // Ordenar por días desde creación (más antiguas primero)
        inactive.sort((a, b) => b.days_since_creation - a.days_since_creation);
        
        if (filterDays > 0) {
            inactive = inactive.filter(a => a.days_since_creation >= filterDays);
        }
        
        document.getElementById('inactiveCount').textContent = `${inactive.length} cuentas`;
        
        container.innerHTML = inactive.map((acct, i) => {
            const days = acct.days_since_creation;
            let dotClass = 'ok';
            let badgeClass = 'badge-active';
            let label = 'Reciente';
            
            if (days >= 180) { dotClass = 'critical'; badgeClass = 'badge-suspended'; label = 'Abandonada'; }
            else if (days >= 90) { dotClass = 'critical'; badgeClass = 'badge-warning'; label = 'Crítica'; }
            else if (days >= 30) { dotClass = 'warning'; badgeClass = 'badge-info'; label = 'Inactiva'; }
            
            return `
                <tr onclick="App.showDetail('${acct.user}')" style="cursor:pointer">
                    <td>
                        <span class="inactive-dot ${dotClass}"></span>
                        <span class="td-user">${acct.user}</span>
                    </td>
                    <td class="td-domain">${acct.domain}</td>
                    <td class="td-mono">${acct.disk_used_hr}</td>
                    <td class="td-mono">${days} días</td>
                    <td class="td-mono">${acct.start_date}</td>
                    <td><span class="badge ${badgeClass}">${label}</span></td>
                    <td class="td-mono">${acct.plan}</td>
                </tr>`;
        }).join('');
        
        if (inactive.length === 0) {
            container.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted)">No se encontraron cuentas inactivas con estos filtros</td></tr>';
        }
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
        
        document.getElementById('filteredCount').textContent = `${filtered.length} cuentas`;
        
        container.innerHTML = filtered.map(acct => {
            const barColor = acct.disk_percent > 85 ? 'red' : acct.disk_percent > 70 ? 'yellow' : 'green';
            return `
                <tr onclick="App.showDetail('${acct.user}')" style="cursor:pointer">
                    <td><span class="td-user">${acct.user}</span></td>
                    <td class="td-domain">${acct.domain}</td>
                    <td class="td-mono">${acct.disk_used_hr}</td>
                    <td>
                        <span class="td-mono">${acct.disk_percent}%</span>
                        <span class="mini-bar"><span class="mini-bar-fill ${barColor}" style="width:${Math.min(acct.disk_percent, 100)}%"></span></span>
                    </td>
                    <td class="td-mono">${acct.bw_used_hr}</td>
                    <td>${acct.suspended ? '<span class="badge badge-suspended">Suspendida</span>' : '<span class="badge badge-active">Activa</span>'}</td>
                    <td class="td-mono">${acct.plan}</td>
                    <td class="td-mono">${acct.owner}</td>
                </tr>`;
        }).join('');
    },

    updateTimestamp() {
        const ts = this.data.summary?.generated_at || new Date().toLocaleString();
        document.getElementById('timestamp').textContent = ts;
    },

    // ---- TABS ----
    switchTab(tabId) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.querySelector(`.tab-btn[data-tab="${tabId}"]`).classList.add('active');
        document.getElementById(`tab-${tabId}`).classList.add('active');
        this.currentTab = tabId;
    },

    // ---- TOGGLES ----
    toggleTopSpace(mode, btn) {
        document.querySelectorAll('#topSpaceToggle .toggle-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        this.renderTopSpace();
    },

    toggleTopActivity(mode, btn) {
        document.querySelectorAll('#topActivityToggle .toggle-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
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

    filterInactive() {
        this.renderInactiveAccounts();
    },

    // ---- DETAIL MODAL ----
    async showDetail(user) {
        const overlay = document.getElementById('modalOverlay');
        const content = document.getElementById('modalContent');
        
        overlay.classList.add('active');
        content.innerHTML = '<div style="text-align:center;padding:40px"><div class="loader-spinner" style="margin:0 auto"></div><p style="margin-top:16px;color:var(--text-secondary)">Cargando detalle...</p></div>';
        
        const detail = await this.loadAccountDetail(user);
        
        if (!detail || detail.error) {
            content.innerHTML = '<p style="color:var(--danger);text-align:center">Error cargando detalle de la cuenta</p>';
            return;
        }
        
        const acct = detail.account || {};
        const emails = detail.emails || [];
        const databases = detail.databases || [];
        const domains = detail.domains || {};
        
        const allDomains = [];
        if (domains.main_domain) allDomains.push(domains.main_domain);
        if (domains.addon_domains) allDomains.push(...domains.addon_domains);
        if (domains.sub_domains) allDomains.push(...domains.sub_domains);
        if (domains.parked_domains) allDomains.push(...domains.parked_domains);
        
        content.innerHTML = `
            <div class="modal-grid">
                <div class="modal-item">
                    <div class="modal-item-label">Usuario</div>
                    <div class="modal-item-value" style="color:var(--accent)">${acct.user || user}</div>
                </div>
                <div class="modal-item">
                    <div class="modal-item-label">Dominio Principal</div>
                    <div class="modal-item-value">${acct.domain || 'N/A'}</div>
                </div>
                <div class="modal-item">
                    <div class="modal-item-label">Email Contacto</div>
                    <div class="modal-item-value">${acct.email || 'N/A'}</div>
                </div>
                <div class="modal-item">
                    <div class="modal-item-label">Plan</div>
                    <div class="modal-item-value">${acct.plan || 'N/A'}</div>
                </div>
                <div class="modal-item">
                    <div class="modal-item-label">Disco Usado</div>
                    <div class="modal-item-value">${acct.diskused || 'N/A'} / ${acct.disklimit || 'unlimited'}</div>
                </div>
                <div class="modal-item">
                    <div class="modal-item-label">IP</div>
                    <div class="modal-item-value">${acct.ip || 'N/A'}</div>
                </div>
                <div class="modal-item">
                    <div class="modal-item-label">Inodes</div>
                    <div class="modal-item-value">${acct.inodesused || '0'} / ${acct.inodeslimit || 'unlimited'}</div>
                </div>
                <div class="modal-item">
                    <div class="modal-item-label">Fecha Creación</div>
                    <div class="modal-item-value">${acct.startdate || 'N/A'}</div>
                </div>
                <div class="modal-item">
                    <div class="modal-item-label">Email Max/Hora</div>
                    <div class="modal-item-value">${acct.max_email_per_hour || 'N/A'}</div>
                </div>
                <div class="modal-item">
                    <div class="modal-item-label">Owner</div>
                    <div class="modal-item-value">${acct.owner || 'N/A'}</div>
                </div>
            </div>
            
            <div class="modal-section-title">📧 Cuentas de Email (${emails.length})</div>
            ${emails.length > 0 ? `
                <div class="table-container">
                    <table>
                        <thead><tr>
                            <th>Email</th>
                            <th>Disco Usado</th>
                            <th>Quota</th>
                        </tr></thead>
                        <tbody>
                            ${emails.slice(0, 20).map(e => `
                                <tr>
                                    <td class="td-mono">${e.email || e.login || 'N/A'}</td>
                                    <td class="td-mono">${e.humandiskused || e._diskused || 'N/A'}</td>
                                    <td class="td-mono">${e.humandiskquota || e.diskquota || 'unlimited'}</td>
                                </tr>
                            `).join('')}
                            ${emails.length > 20 ? `<tr><td colspan="3" style="text-align:center;color:var(--text-muted)">... y ${emails.length - 20} más</td></tr>` : ''}
                        </tbody>
                    </table>
                </div>
            ` : '<p style="color:var(--text-muted);font-size:13px">Sin cuentas de email</p>'}
            
            <div class="modal-section-title">🗄️ Bases de Datos (${databases.length})</div>
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
            ` : '<p style="color:var(--text-muted);font-size:13px">Sin bases de datos</p>'}
            
            <div class="modal-section-title">🌐 Dominios (${allDomains.length})</div>
            ${allDomains.length > 0 ? `
                <div style="display:flex;flex-wrap:wrap;gap:6px">
                    ${allDomains.map(d => `<span class="badge badge-info">${d}</span>`).join('')}
                </div>
            ` : '<p style="color:var(--text-muted);font-size:13px">Solo dominio principal</p>'}
        `;
        
        document.getElementById('modalTitle').textContent = `Detalle: ${acct.user || user}`;
    },

    closeModal() {
        document.getElementById('modalOverlay').classList.remove('active');
    },

    // ---- ERROR ----
    showError(message) {
        document.getElementById('appContent').innerHTML = `
            <div class="error-state">
                <div class="icon">⚠️</div>
                <h2>Error de Conexión</h2>
                <p>${message}</p>
                <br>
                <button class="btn-refresh" onclick="App.refresh()">⟳ Reintentar</button>
            </div>`;
    }
};

// Init on DOM ready
document.addEventListener('DOMContentLoaded', () => App.init());
