<?php
/**
 * ============================================================
 * WHM API Client
 * Maneja todas las comunicaciones con la API de WHM
 * ============================================================
 * Archivo    : WhmApi.php
 * Path       : /home/icontel/public_html/intranet/whm-report/includes/WhmApi.php
 * Versión    : 1.0.1
 * Fecha      : 2026-02-25 20:57:00
 * Proyecto   : WHM Server Report - Icontel Intranet
 * Autor      : Icontel Dev Team
 * Changelog  :
 *   1.0.0 - 2026-02-25 - Versión inicial
 *   1.0.1 - 2026-02-25 - Fix SSL: CURLOPT_SSL_VERIFYHOST=0,
 *                         CURLOPT_SSLVERSION, CURLOPT_FOLLOWLOCATION
 * ============================================================
 */

require_once __DIR__ . '/../config/config.php';

class WhmApi {
    
    private $host;
    private $port;
    private $username;
    private $token;
    private $baseUrl;
    
    public function __construct() {
        $this->host = WHM_HOST;
        $this->port = WHM_PORT;
        $this->username = WHM_USERNAME;
        $this->token = WHM_API_TOKEN;
        $this->baseUrl = WHM_API_URL;
    }
    
    /**
     * Configura las opciones de cURL para conexión SSL
     * Usa cacert.pem local para validar certificados (requerido por PHP 7.0 en california)
     */
    private function setupCurl($ch, $url) {
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: WHM ' . $this->username . ':' . $this->token,
            'Accept: application/json'
        ]);
        
        // Usar cacert.pem con ruta absoluta (necesario en california/cleveland)
        $cacert = '/home/icontel/public_html/intranet/whm-report/cacert.pem';
        if (file_exists($cacert)) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
            curl_setopt($ch, CURLOPT_CAINFO, $cacert);
        } else {
            // Fallback si no existe el archivo (intenta ignorar si es posible)
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }
    }
    
    /**
     * Realiza una llamada a la API de WHM
     */
    public function call($function, $params = []) {
        $params['api.version'] = 1;
        $url = $this->baseUrl . '/json-api/' . $function . '?' . http_build_query($params);
        
        $ch = curl_init();
        $this->setupCurl($ch, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return ['error' => true, 'message' => 'cURL Error: ' . $error];
        }
        
        if ($httpCode !== 200) {
            return ['error' => true, 'message' => 'HTTP Error: ' . $httpCode];
        }
        
        $data = json_decode($response, true);
        
        if (isset($data['metadata']['result']) && $data['metadata']['result'] == 0) {
            return ['error' => true, 'message' => $data['metadata']['reason'] ?? 'Unknown API error'];
        }
        
        return $data;
    }
    
    /**
     * Llamada UAPI (para funciones de cPanel de usuario)
     * Usa el endpoint /json-api/cpanel (WHM API 1)
     */
    public function callUapi($user, $module, $function, $params = []) {
        $allParams = array_merge([
            'cpanel_jsonapi_user' => $user,
            'cpanel_jsonapi_apiversion' => '3',
            'cpanel_jsonapi_module' => $module,
            'cpanel_jsonapi_func' => $function
        ], $params);
        
        $url = $this->baseUrl . '/json-api/cpanel?' . http_build_query($allParams);
        $ch = curl_init();
        $this->setupCurl($ch, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $data = json_decode($response, true);
        if ($data === null && !empty($response)) {
            return ['error' => true, 'message' => 'JSON Decode Error', 'raw' => $response, 'http_code' => $http];
        }

        // Normalizar errores de cPanel/WHM
        if (isset($data['data']['result']) && $data['data']['result'] == 0) {
            $data['success'] = false;
            $data['message'] = $data['data']['reason'] ?? ($data['error'] ?? 'API Error');
        }
        
        return $data;
    }
    
    /**
     * Test de conexión
     */
    public function testConnection() {
        $result = $this->call('version');
        if (isset($result['error'])) {
            return ['success' => false, 'message' => $result['message']];
        }
        return ['success' => true, 'version' => $result['data']['version'] ?? 'Unknown'];
    }
    
    /**
     * Listar todas las cuentas con detalle
     */
    public function listAccounts() {
        $result = $this->call('listaccts');
        if (isset($result['error'])) return $result;
        return $result['data']['acct'] ?? [];
    }
    
    /**
     * Resumen de una cuenta específica
     */
    public function accountSummary($user) {
        $result = $this->call('accountsummary', ['user' => $user]);
        if (isset($result['error'])) return $result;
        return $result['data']['acct'][0] ?? [];
    }
    
    /**
     * Listar paquetes (planes) disponibles
     */
    public function listPackages() {
        $result = $this->call('listpkgs');
        if (isset($result['error'])) return $result;
        return $result['data']['pkg'] ?? [];
    }
    
    /**
     * Cambiar el paquete (plan) de una cuenta
     */
    public function changePackage($user, $pkg) {
        return $this->call('changepackage', [
            'user' => $user,
            'pkg' => $pkg
        ]);
    }
    
    /**
     * Obtener uso de ancho de banda
     */
    public function getBandwidth($month = null, $year = null) {
        $params = [];
        if ($month) $params['month'] = $month;
        if ($year) $params['year'] = $year;
        $result = $this->call('showbw', $params);
        if (isset($result['error'])) return $result;
        return $result['data']['acct'] ?? [];
    }
    
    /**
     * Listar cuentas de email de un usuario
     */
    public function getEmailAccounts($user) {
        $result = $this->callUapi($user, 'Email', 'list_pops_with_disk', []);
        if (isset($result['result']['data'])) {
            return $result['result']['data'];
        }
        return [];
    }
    
    /**
     * Contar cuentas de email de un usuario
     */
    public function countEmailAccounts($user) {
        $accounts = $this->getEmailAccounts($user);
        return is_array($accounts) ? count($accounts) : 0;
    }
    
    /**
     * Obtener último login de cuentas de email de un usuario
     * Usa la función Lastlogin::get_last_or_current_logged_in_ip
     */
    public function getEmailLastLogins($user) {
        $result = $this->callUapi($user, 'LastLogin', 'get_last_or_current_logged_in_ip', []);
        if (isset($result['result']['data'])) {
            return $result['result']['data'];
        }
        // Fallback: intentar con Email::list_pops_with_disk que a veces trae login info
        return [];
    }
    
    /**
     * Obtener estadísticas de login de webmail/email
     */
    public function getMailboxStats($user, $email) {
        $parts = explode('@', $email);
        if (count($parts) !== 2) return null;
        $result = $this->callUapi($user, 'Email', 'get_pop_quota', [
            'email' => $parts[0],
            'domain' => $parts[1],
        ]);
        if (isset($result['result']['data'])) {
            return $result['result']['data'];
        }
        return null;
    }
    
    /**
     * Crear nueva cuenta de email
     */
    public function createEmailAccount($user, $localpart, $domain, $password, $quota = 0) {
        $result = $this->callUapi($user, 'Email', 'add_pop', [
            'email'    => $localpart,
            'domain'   => $domain,
            'password' => $password,
            'quota'    => intval($quota),
        ], []); // USAR GET (Default)
        $status = $result['result']['status'] ?? 0;
        $errors = $result['result']['errors'] ?? [];
        return [
            'success' => $status == 1,
            'message' => $status == 1 ? 'Cuenta creada correctamente' : ($errors[0] ?? 'Error desconocido'),
        ];
    }

    /**
     * Cambiar contraseña de cuenta de email
     */
    public function changeEmailPassword($user, $localpart, $domain, $password) {
        $result = $this->callUapi($user, 'Email', 'passwd_pop', [
            'email'    => $localpart,
            'domain'   => $domain,
            'password' => $password,
        ], []); // USAR GET (Default)
        $status = $result['result']['status'] ?? 0;
        $errors = $result['result']['errors'] ?? [];
        return [
            'success' => $status == 1,
            'message' => $status == 1 ? 'Contraseña actualizada' : ($errors[0] ?? 'Error desconocido'),
        ];
    }

    /**
     * Modificar quota de cuenta de email
     */
    public function editEmailQuota($user, $localpart, $domain, $quota) {
        $result = $this->callUapi($user, 'Email', 'edit_pop_quota', [
            'email' => $localpart,
            'domain' => $domain,
            'quota'  => intval($quota),
        ], []); // USAR GET (Default)
        $status = $result['result']['status'] ?? 0;
        $errors = $result['result']['errors'] ?? ($result['message'] ? [$result['message']] : []);
        return [
            'success' => $status == 1,
            'message' => $status == 1 ? 'Quota actualizada' : ($errors[0] ?? 'Error desconocido'),
            'debug'   => $result
        ];
    }

    /**
     * Eliminar cuenta de email
     */
    public function deleteEmailAccount($user, $localpart, $domain) {
        $result = $this->callUapi($user, 'Email', 'delete_pop', [
            'email'  => $localpart,
            'domain' => $domain,
        ], []); // USAR GET (Default)
        $status = $result['result']['status'] ?? 0;
        $errors = $result['result']['errors'] ?? [];
        return [
            'success' => $status == 1,
            'message' => $status == 1 ? 'Cuenta eliminada' : ($errors[0] ?? 'Error desconocido'),
        ];
    }

    /**
     * Listar forwarders de un email
     */
    public function listForwarders($user, $email) {
        $result = $this->callUapi($user, 'Email', 'list_forwarders', []);
        $data = $result['result']['data'] ?? [];
        if (!is_array($data)) return [];
        
        $filtered = [];
        foreach ($data as $f) {
            // WHM Screenshot confirmation: 'dest' is Source, 'forward' is Destination
            $src = $f['dest'] ?? $f['forwarder'] ?? $f['uri'] ?? $f['email'] ?? '';
            if ($src === $email || strpos($src, $email . '@') === 0 || $src === explode('@', $email)[0]) {
                $filtered[] = $f;
            }
        }
        return $filtered;
    }

    /**
     * Agregar forwarder de email
     */
    public function addForwarder($user, $localpart, $domain, $fwdest) {
        $result = $this->callUapi($user, 'Email', 'add_forwarder', [
            'email'   => $localpart . '@' . $domain,
            'fwdest'  => $fwdest,
        ], true); // USAR POST
        $status = $result['result']['status'] ?? 0;
        $errors = $result['result']['errors'] ?? [];
        return [
            'success' => $status == 1,
            'message' => $status == 1 ? 'Forwarder agregado' : ($errors[0] ?? 'Error desconocido'),
        ];
    }

    /**
     * Eliminar forwarder de email
     */
    public function deleteForwarder($user, $email, $fwdest) {
        $result = $this->callUapi($user, 'Email', 'delete_forwarder', [
            'address' => $email,
            'forwarder' => $fwdest,
        ], true); // USAR POST
        $status = $result['result']['status'] ?? 0;
        $errors = $result['result']['errors'] ?? [];
        return [
            'success' => $status == 1,
            'message' => $status == 1 ? 'Forwarder eliminado' : ($errors[0] ?? 'Error desconocido'),
        ];
    }

    /**
     * Diagnóstico de cuenta de email: por qué puede estar suspendida
     * Usa la API WHM root (ya autenticada) y datos accesibles sin privilegios extra
     */
    public function getEmailSuspendLog($email, $user = '') {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['error' => true, 'message' => 'Email inválido'];
        }
        
        $parts = explode('@', $email);
        if (count($parts) !== 2) return ['error' => true, 'message' => 'Email inválido'];
        $localpart = $parts[0];
        $domain    = $parts[1];
        
        $entries = [];
        $keywords = ['suspend', 'reject', 'spam', 'block', 'fail', 'deny', 'banned', 'quota', 'limit', 'lock'];
        
        $addEntry = function($line, $source, $force_highlight = false) use (&$entries, $keywords) {
            $lower = strtolower($line);
            $highlight = $force_highlight;
            if (!$highlight) {
                foreach ($keywords as $kw) {
                    if (strpos($lower, $kw) !== false) { $highlight = true; break; }
                }
            }
            $entries[] = ['line' => $line, 'source' => $source, 'highlight' => $highlight];
        };

        // 1. UAPI: estado actual de la cuenta (requiere usuario cPanel)
        if (!empty($user)) {
        $quota = $this->callUapi($user, 'Email', 'get_pop_quota', [
            'email' => $localpart, 'domain' => $domain
        ]);
        if (isset($quota['result']['data'])) {
            $d = $quota['result']['data'];
            $suspLogin     = isset($d['suspended_login'])    ? ($d['suspended_login']    ? 'SÍ' : 'NO') : 'N/D';
            $suspIncoming  = isset($d['suspended_incoming']) ? ($d['suspended_incoming'] ? 'SÍ' : 'NO') : 'N/D';
            $diskUsed      = $d['_diskused'] ?? $d['diskused'] ?? '?';
            $diskQuota     = $d['diskquota'] ?? '?';
            $mtime         = isset($d['mtime']) && $d['mtime'] > 0
                ? date('d/m/Y H:i', $d['mtime'])
                : 'Sin registro';
            
            $addEntry("Estado actual | Login suspendido: {$suspLogin} | Entrante suspendido: {$suspIncoming}", 'uapi', $suspLogin === 'SÍ');
            $addEntry("Disco: {$diskUsed} MB de {$diskQuota} MB usados", 'uapi');
            $addEntry("Último acceso: {$mtime}", 'uapi');
        }
        } // end if(!empty($user))

        // 2. WHM API: estado del dominio (si la cuenta cPanel está suspendida)
        $domInfo = $this->call('getdomaininfo', ['domain' => $domain]);
        if (isset($domInfo['data']['acct'])) {
            $acct = $domInfo['data']['acct'][0] ?? $domInfo['data']['acct'] ?? [];
            $suspended = ($acct['suspended'] ?? 0) ? 'SÍ' : 'NO';
            $reason    = $acct['suspendreason'] ?? 'N/D';
            $addEntry("Cuenta cPanel suspendida: {$suspended}" . ($suspended === 'SÍ' ? " | Razón: {$reason}" : ''), 'whm', $suspended === 'SÍ');
            $disk = $acct['diskused'] ?? '?';
            $quota2 = $acct['diskquota'] ?? '?';
            $addEntry("Disco cPanel: {$disk} MB de {$quota2} MB", 'whm');
        }
        
        // 3. WHM API: quota de emails del dominio
        $emailQuota = $this->call('geteximstats');
        if (isset($emailQuota['data'])) {
            $data = $emailQuota['data'];
            $deferQueue = $data['defer_queue_count'] ?? '?';
            $failQueue  = $data['fail_queue_count']  ?? '?';
            $addEntry("Cola Exim global — En pausa: {$deferQueue} | Fallidos: {$failQueue}", 'exim');
        }
        
        // 4. WHM API: IPs bloqueadas en CSF (puede incluir la IP desde donde se conecta el usuario)
        $blocked = $this->getBlockedIPs();
        if (!empty($blocked) && count($blocked) > 0) {
            $addEntry("IPs bloqueadas en CSF actualmente: " . count($blocked) . " (pueden bloquear el acceso al mail)", 'csf');
        }
        
        // 5. Intentar leer logs si son accesibles (algunos servidores permiten lectura de grupo)
        $logPaths = [
            '/var/log/exim_mainlog'   => 'mainlog',
            '/var/log/exim_rejectlog' => 'rejectlog',
        ];
        foreach ($logPaths as $logPath => $src) {
            if (is_readable($logPath)) {
                // Solo si el archivo es legible, buscamos con grep
                $safe = escapeshellarg($email);
                $out = @shell_exec("grep -i {$safe} {$logPath} 2>/dev/null | tail -30");
                if ($out) {
                    $lines = array_filter(array_map('trim', explode("\n", $out)));
                    foreach ($lines as $line) {
                        if (!empty($line)) $addEntry($line, $src);
                    }
                }
            } else {
                $addEntry("Log {$src} ({$logPath}): sin acceso desde PHP (normal — es de root)", $src);
            }
        }
        
        if (empty($entries)) {
            return ['entries' => [], 'message' => 'No se pudo obtener información diagnóstica de esta cuenta.'];
        }
        
        return ['entries' => $entries, 'count' => count($entries)];
    }
    
    /**
     * Suspender login de cuenta de email
     */
    public function suspendEmailLogin($user, $email) {
        $parts = explode('@', $email);
        if (count($parts) !== 2) return ['error' => true, 'message' => 'Email inválido'];
        $result = $this->callUapi($user, 'Email', 'suspend_login', [
            'email' => $parts[0], 'domain' => $parts[1],
        ]);
        $status = $result['result']['status'] ?? 0;
        $reason = $result['result']['errors'][0] ?? 'OK';
        return ['success' => $status == 1, 'message' => $reason];
    }
    
    /**
     * Reactivar login de cuenta de email
     */
    public function unsuspendEmailLogin($user, $email) {
        $parts = explode('@', $email);
        if (count($parts) !== 2) return ['error' => true, 'message' => 'Email inválido'];
        $result = $this->callUapi($user, 'Email', 'unsuspend_login', [
            'email' => $parts[0], 'domain' => $parts[1],
        ]);
        $status = $result['result']['status'] ?? 0;
        $reason = $result['result']['errors'][0] ?? 'OK';
        return ['success' => $status == 1, 'message' => $reason];
    }
    
    /**
     * Suspender correo entrante
     */
    public function suspendEmailIncoming($user, $email) {
        $parts = explode('@', $email);
        if (count($parts) !== 2) return ['error' => true, 'message' => 'Email inválido'];
        $result = $this->callUapi($user, 'Email', 'suspend_incoming', [
            'email' => $parts[0], 'domain' => $parts[1],
        ]);
        $status = $result['result']['status'] ?? 0;
        $reason = $result['result']['errors'][0] ?? 'OK';
        return ['success' => $status == 1, 'message' => $reason];
    }
    
    /**
     * Reactivar correo entrante
     */
    public function unsuspendEmailIncoming($user, $email) {
        $parts = explode('@', $email);
        if (count($parts) !== 2) return ['error' => true, 'message' => 'Email inválido'];
        $result = $this->callUapi($user, 'Email', 'unsuspend_incoming', [
            'email' => $parts[0], 'domain' => $parts[1],
        ]);
        $status = $result['result']['status'] ?? 0;
        $reason = $result['result']['errors'][0] ?? 'OK';
        return ['success' => $status == 1, 'message' => $reason];
    }
    
    /**
     * Listar forwarders (reenvíos) de email de un usuario
     */
    public function getForwarders($user) {
        $result = $this->callUapi($user, 'Email', 'list_forwarders', []);
        if (isset($result['result']['data'])) {
            return $result['result']['data'];
        }
        return [];
    }
    
    /**
     * Listar auto-respondedores de un usuario
     */
    public function getAutoResponders($user) {
        $result = $this->callUapi($user, 'Email', 'list_auto_responders', []);
        if (isset($result['result']['data'])) {
            return $result['result']['data'];
        }
        return [];
    }
    
    /**
     * Listar listas de correo de un usuario
     */
    public function getMailingLists($user) {
        $result = $this->callUapi($user, 'Email', 'list_lists', []);
        if (isset($result['result']['data'])) {
            return $result['result']['data'];
        }
        return [];
    }
    
    /**
     * Listar bases de datos de un usuario
     */
    public function getDatabases($user) {
        $result = $this->callUapi($user, 'Mysql', 'list_databases', []);
        if (isset($result['result']['data'])) {
            return $result['result']['data'];
        }
        return [];
    }
    
    /**
     * Contar bases de datos de un usuario
     */
    public function countDatabases($user) {
        $databases = $this->getDatabases($user);
        return is_array($databases) ? count($databases) : 0;
    }
    
    /**
     * Listar dominios addon de un usuario
     */
    public function getAddonDomains($user) {
        $result = $this->callUapi($user, 'DomainInfo', 'list_domains', []);
        if (isset($result['result']['data'])) {
            return $result['result']['data'];
        }
        return [];
    }
    
    /**
     * Obtener estadísticas detalladas del servidor (TNA Health)
     */
    public function getSystemHealth() {
        $load = $this->call('systemloadavg', ['api.version' => 1]);
        
        // Intentar obtener info de CPUs para el Load Average (Regla TNA)
        $cpuInfo = $this->call('get_cpu_usage', ['api.version' => 1]);
        $cpuCores = 1;
        if (isset($cpuInfo['data']['cpu'])) {
            $cpuCores = count($cpuInfo['data']['cpu']);
        }

        // WHM no da RAM fácil por API1 directa sin privilegios altos, 
        // pero podemos intentar via 'get_server_information' o fallback
        $serverInfo = $this->call('get_server_information', ['api.version' => 1]);
        
        $memTotal = 0;
        $memUsed = 0;
        
        if (isset($serverInfo['data']['serverinfo']['memory'])) {
            $mem = $serverInfo['data']['serverinfo']['memory'];
            $memTotal = $this->parseSize($mem['total'] ?? '0');
            $memUsed = $this->parseSize($mem['used'] ?? '0');
        }

        return [
            'load' => [
                'one' => floatval($load['data']['one'] ?? 0),
                'five' => floatval($load['data']['five'] ?? 0),
                'fifteen' => floatval($load['data']['fifteen'] ?? 0),
                'cores' => $cpuCores
            ],
            'memory' => [
                'total' => $memTotal,
                'used' => $memUsed,
                'percent' => $memTotal > 0 ? round(($memUsed / $memTotal) * 100, 2) : 0,
                'total_hr' => $this->formatBytes($memTotal),
                'used_hr' => $this->formatBytes($memUsed)
            ],
            'mail_queue' => $this->getMailQueueCount(),
            'inodes' => $this->getSystemInodesUsage(), // Nueva métrica
            'backups' => $this->getBackupStatus()
        ];
    }

    /**
     * Obtener conteo de correos en cola
     */
    public function getMailQueueCount() {
        $result = $this->call('emailtrack_stats', ['api.version' => 1]);
        return intval($result['data']['waiting'] ?? 0);
    }

    /**
     * Obtener estado de los backups
     */
    public function getBackupStatus() {
        $result = $this->call('backup_status', ['api.version' => 1]);
        $data = $result['data'] ?? [];
        
        return [
            'status' => $data['status'] ?? 'unknown',
            'last_run' => $data['last_run'] ?? 'N/A',
            'is_running' => ($data['status'] ?? '') === 'running'
        ];
    }
    
    /**
     * Obtener estadísticas del servidor
     */
    public function getServerInfo() {
        $hostname = $this->call('gethostname');
        $version = $this->call('version');
        $health = $this->getSystemHealth();
        
        return [
            'hostname' => $hostname['data']['hostname'] ?? 'N/A',
            'version' => $version['data']['version'] ?? 'N/A',
            'health' => $health
        ];
    }
    
    /**
     * Obtener uso real del disco del servidor (Hardware/Partición root)
     */
    public function getSystemDiskUsage() {
        $result = $this->call('getdiskusage', []);
        if (isset($result['error'])) return null;
        
        $partitions = $result['data']['partition'] ?? [];
        foreach ($partitions as $p) {
            // Buscamos la partición principal (/)
            if ($p['mount'] === '/') {
                // Los valores vienen en KB
                $usedKB = (float)$p['used'];
                $totalKB = (float)$p['total'];
                $percent = (float)$p['percentage'];
                
                return [
                    'used_bytes' => $usedKB * 1024,
                    'total_bytes' => $totalKB * 1024,
                    'percent' => $percent
                ];
            }
        }
        return null;
    }

    /**
     * Obtener uso de inodos del servidor (Política TNA)
     */
    public function getSystemInodesUsage() {
        // Obtenemos inodos de la partición principal (/)
        $output = @shell_exec("df -i / 2>&1");
        if (!$output) return ['percent' => 0, 'limit' => 0, 'used' => 0];

        $lines = explode("\n", $output);
        if (count($lines) < 2) return ['percent' => 0, 'limit' => 0, 'used' => 0];

        // Parseamos la línea de datos (segunda línea)
        $dataStr = preg_replace('/\s+/', ' ', $lines[1]);
        $parts = explode(' ', $dataStr);
        
        // Formato df -i habitual: Filesystem Inodes IUsed IFree IUse% Mounted on
        if (count($parts) >= 5) {
            $total = intval($parts[1]);
            $used = intval($parts[2]);
            $percent = intval(str_replace('%', '', $parts[4]));
            
            return [
                'total' => $total,
                'used' => $used,
                'percent' => $percent,
                'status' => $this->calculateInodesSeverity($percent)
            ];
        }

        return ['percent' => 0, 'limit' => 0, 'used' => 0];
    }

    private function calculateInodesSeverity($percent) {
        if ($percent >= 90) return ['level' => 'critical', 'class' => 'red', 'code' => '🔴'];
        if ($percent >= 80) return ['level' => 'high', 'class' => 'orange', 'code' => '🟠'];
        if ($percent >= 70) return ['level' => 'preventive', 'class' => 'yellow', 'code' => '🟡'];
        return ['level' => 'info', 'class' => 'green', 'code' => '🟢'];
    }
    
    /**
     * Obtener cuentas suspendidas
     */
    public function getSuspendedAccounts() {
        $result = $this->call('listsuspended');
        if (isset($result['error'])) return $result;
        return $result['data']['account'] ?? [];
    }
    
    /**
     * Obtener último login de cPanel de un usuario
     */
    public function getLastLogin($user) {
        $result = $this->call('get_last_login', ['user' => $user, 'app' => 'cpanel']);
        if (isset($result['error'])) return null;
        return $result['data'] ?? null;
    }
    
    /**
     * Reporte completo consolidado
     */
    public function getFullReport() {
        $accounts = $this->listAccounts();
        if (isset($accounts['error'])) return $accounts;
        
        $bandwidth = $this->getBandwidth();
        $suspended = $this->getSuspendedAccounts();
        
        $suspendedUsers = [];
        if (is_array($suspended)) {
            foreach ($suspended as $s) {
                $suspendedUsers[$s['user']] = $s;
            }
        }
        
        $bwByUser = [];
        if (is_array($bandwidth)) {
            foreach ($bandwidth as $bw) {
                $user = $bw['user'] ?? '';
                $bwByUser[$user] = $bw;
            }
        }
        
        $totalDisk = 0;
        $totalDiskUsed = 0;
        $totalBwUsed = 0;
        $totalAccounts = count($accounts);
        $totalSuspended = count($suspendedUsers);
        $totalActive = $totalAccounts - $totalSuspended;
        
        $hasUnlimitedDisk = false;
        $accountsData = [];
        $ownerCounts = [];
        
        foreach ($accounts as $acct) {
            $owner = $acct['owner'] ?? 'root';
            if (!isset($ownerCounts[$owner])) {
                $ownerCounts[$owner] = 0;
            }
            $ownerCounts[$owner]++;
            
            $user = $acct['user'];
            $diskUsedRaw = $this->parseSize($acct['diskused'] ?? '0M');
            $diskLimitRaw = $this->parseSize($acct['disklimit'] ?? 'unlimited');
            
            $totalDiskUsed += $diskUsedRaw;
            
            if ($diskLimitRaw > 0) {
                $totalDisk += $diskLimitRaw;
            } else {
                // If an account has unlimited disk space, the total capacity is unlimited.
                $hasUnlimitedDisk = true;
            }
            
            // Obtener detalle de emails para contar y buscar el máximo uso por casilla
            $emailsData = $this->getEmailAccounts($user);
            $emailCount = is_array($emailsData) ? count($emailsData) : 0;
            $maxMailboxUsage = 0;
            if (is_array($emailsData)) {
                foreach ($emailsData as $em) {
                    $emUsage = floatval($em['_diskused'] ?? 0);
                    if ($emUsage > $maxMailboxUsage) $maxMailboxUsage = $emUsage;
                }
            }
            $emailLimit = $acct['maxpop'] ?? 'unlimited';

            // Contar forwarders totales de la cuenta
            $fwdsData = $this->getForwarders($user);
            $fwdCount = is_array($fwdsData) ? count($fwdsData) : 0;
            
            // Calcular días desde creación
            $startDate = $acct['unix_startdate'] ?? $acct['startdate'] ?? null;
            $daysSinceCreation = 0;
            if ($startDate) {
                if (is_numeric($startDate)) {
                    $daysSinceCreation = floor((time() - $startDate) / 86400);
                } else {
                    $ts = strtotime($startDate);
                    if ($ts) $daysSinceCreation = floor((time() - $ts) / 86400);
                }
            }
            
            // Bandwidth
            $bwUsed = 0;
            $bwLimit = 0;
            if (isset($bwByUser[$user])) {
                $bwUsed = $bwByUser[$user]['totalbytes'] ?? 0;
                $bwLimit = $this->parseSize($acct['bandwidthlimit'] ?? 'unlimited');
            }
            $totalBwUsed += $bwUsed;
            
            $isSuspended = isset($suspendedUsers[$user]);
            
            $accountsData[] = [
                'user' => $user,
                'domain' => $acct['domain'] ?? '',
                'email' => $acct['email'] ?? '',
                'plan' => $acct['plan'] ?? '',
                'ip' => $acct['ip'] ?? '',
                'disk_used' => $diskUsedRaw,
                'disk_used_hr' => $this->formatBytes($diskUsedRaw),
                'disk_limit' => $diskLimitRaw,
                'disk_limit_hr' => ($diskLimitRaw > 0) ? $this->formatBytes($diskLimitRaw) : 'Ilimitado',
                'disk_percent' => ($diskLimitRaw > 0) ? round(($diskUsedRaw / $diskLimitRaw) * 100, 2) : 0,
                'bw_used' => $bwUsed,
                'bw_used_hr' => $this->formatBytes($bwUsed),
                'bw_limit' => $bwLimit,
                'bw_limit_hr' => ($bwLimit > 0) ? $this->formatBytes($bwLimit) : 'unlimited',
                'start_date' => $acct['startdate'] ?? 'N/A',
                'days_since_creation' => $daysSinceCreation,
                'suspended' => $isSuspended,
                'suspend_reason' => $isSuspended ? ($suspendedUsers[$user]['reason'] ?? '') : '',
                'maxaddons' => $acct['maxaddons'] ?? 0,
                'maxsql' => $acct['maxsql'] ?? 0,
                'maxpop' => $acct['maxpop'] ?? 0,
                'maxftp' => $acct['maxftp'] ?? 0,
                'owner' => $acct['owner'] ?? '',
                'shell' => $acct['shell'] ?? '',
                'theme' => $acct['theme'] ?? '',
                'max_email_per_hour' => $acct['max_email_per_hour'] ?? 0,
                'inodes_used' => intval($acct['inodesused'] ?? 0),
                'inodes_limit' => intval(str_replace('unlimited', '0', $acct['inodeslimit'] ?? '0')),
                'inodes_percent' => (intval(str_replace('unlimited', '0', $acct['inodeslimit'] ?? '0')) > 0) ? round((intval($acct['inodesused'] ?? 0) / intval(str_replace('unlimited', '0', $acct['inodeslimit']))) * 100, 2) : 0,
                'email_count' => $emailCount,
                'email_limit' => $emailLimit,
                'forwarder_count' => $fwdCount,
                'max_mailbox_usage' => $maxMailboxUsage,
                'db_count' => $this->countDatabases($user),
                'is_managerial' => ($diskLimitRaw > 4 * 1024 * 1024 * 1024) || ($diskLimitRaw == 0),
                'severity' => $this->calculateAccountSeverity([
                    'disk_percent' => ($diskLimitRaw > 0) ? ($diskUsedRaw / $diskLimitRaw) * 100 : 0,
                    'disk_limit' => $diskLimitRaw,
                    'inodes_used' => intval($acct['inodesused'] ?? 0),
                    'max_mailbox_usage' => $maxMailboxUsage,
                    'suspended' => $isSuspended
                ])
            ];
        }
        
        
        // Obtener uso real del servidor y salud
        $sysDisk = $this->getSystemDiskUsage();
        $serverInfo = $this->getServerInfo();
        
        // Preparar lista de dueños para el filtro
        $owners = [];
        foreach ($ownerCounts as $name => $count) {
            $owners[] = [
                'name' => $name,
                'count' => $count
            ];
        }
        usort($owners, function($a, $b) {
            if ($a['name'] === 'root') return -1;
            if ($b['name'] === 'root') return 1;
            return strcmp($a['name'], $b['name']);
        });

        return [
            'summary' => [
                'total_accounts' => $totalAccounts,
                'active_accounts' => $totalActive,
                'suspended_accounts' => $totalSuspended,
                'total_cpanel_disk_used_hr' => $this->formatBytes($totalDiskUsed),
                // Reemplazamos los valores globales con los del servidor físico si están disponibles
                'total_disk_used' => $sysDisk ? $sysDisk['used_bytes'] : $totalDiskUsed,
                'total_disk_used_hr' => $sysDisk ? $this->formatBytes($sysDisk['used_bytes']) : $this->formatBytes($totalDiskUsed),
                'total_disk_limit' => $sysDisk ? $sysDisk['total_bytes'] : ($hasUnlimitedDisk ? 0 : $totalDisk),
                'total_disk_limit_hr' => $sysDisk ? $this->formatBytes($sysDisk['total_bytes']) : ($hasUnlimitedDisk ? 'Ilimitado' : $this->formatBytes($totalDisk)),
                'disk_percent' => $sysDisk ? round(($sysDisk['used_bytes'] / $sysDisk['total_bytes']) * 100, 2) : ($hasUnlimitedDisk ? 0 : round(($totalDiskUsed / $totalDisk) * 100, 2)),
                'total_bw_used' => $totalBwUsed,
                'total_bw_used_hr' => $this->formatBytes($totalBwUsed),
                'generated_at' => date('Y-m-d H:i:s'),
            ],
            'accounts' => $accountsData,
            'owners' => $owners,
            'server' => $serverInfo
        ];
    }
    
    /**
     * Parsea tamaños como "500M", "1G", "unlimited" a bytes
     */
    public function parseSize($size) {
        if (!$size || strtolower($size) === 'unlimited' || $size === '0') return 0;
        
        $size = trim($size);
        $unit = strtoupper(substr($size, -1));
        $value = floatval($size);
        
        switch ($unit) {
            case 'K': return $value * 1024;
            case 'M': return $value * 1024 * 1024;
            case 'G': return $value * 1024 * 1024 * 1024;
            case 'T': return $value * 1024 * 1024 * 1024 * 1024;
            default: return $value * 1024 * 1024; // assume MB
        }
    }
    
    /**
     * Formatea bytes a formato legible
     */
    public function formatBytes($bytes, $precision = 2) {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = floor(log($bytes) / log(1024));
        $pow = min($pow, count($units) - 1);
        return number_format($bytes / pow(1024, $pow), $precision, ',', '.') . ' ' . $units[$pow];
    }

/**
 * Calcula la severidad de una cuenta basada en la Política TNA v1.0
 */
private function calculateAccountSeverity($data) {
    if ($data['suspended']) {
        return ['level' => 'critical', 'class' => 'red', 'label' => 'Suspendida', 'code' => '🔴'];
    }

    $disk = $data['disk_percent'] ?? 0;
    $diskLimitBytes = $data['disk_limit'] ?? 0;
    $inodesUsed = $data['inodes_used'] ?? 0;
    $mailbox = $data['max_mailbox_usage'] ?? 0;
    $MAILBOX_15GB = 15 * 1024 * 1024 * 1024;

    // Determinar si es cuenta Gerencial (Limit > 4GB) o Estándar
    $isManagerial = ($diskLimitBytes > 4 * 1024 * 1024 * 1024) || ($diskLimitBytes == 0); // 0 = Ilimitado (treat as Managerial for safety)
    
    // Umbrales de Inodos TNA
    $inodeThresholds = $isManagerial ? [
        'yellow' => 100000,
        'orange' => 150000,
        'red'    => 200000
    ] : [
        'yellow' => 50000,
        'orange' => 80000,
        'red'    => 120000
    ];

    // Nivel Emergencia (🚨)
    if ($disk >= 95) {
        return ['level' => 'emergency', 'class' => 'emergency', 'label' => 'Emergencia', 'code' => '🚨'];
    }

    // Nivel Critico (🔴)
    if ($disk >= 90 || $inodesUsed >= $inodeThresholds['red'] || $mailbox >= $MAILBOX_15GB) {
        return ['level' => 'critical', 'class' => 'red', 'label' => 'Crítico', 'code' => '🔴'];
    }

    // Nivel Alto (🟠)
    if ($disk >= 85 || $inodesUsed >= $inodeThresholds['orange']) {
        return ['level' => 'high', 'class' => 'orange', 'label' => 'Riesgo Alto', 'code' => '🟠'];
    }

    // Nivel Preventivo (🟡)
    if ($disk >= 75 || $inodesUsed >= $inodeThresholds['yellow'] || $mailbox >= (0.8 * $MAILBOX_15GB)) {
        return ['level' => 'preventive', 'class' => 'yellow', 'label' => 'Preventivo', 'code' => '🟡'];
    }

    // Nivel Informativo (🟢)
    return ['level' => 'info', 'class' => 'green', 'label' => 'Sano', 'code' => '🟢'];
}
    
    /**
     * Obtener IPs bloqueadas (CSF + cPHulk)
     */
    public function getBlockedIPs() {
        $blocked = [];
        
        // 1. Intentar con CSF (Shell - Solo si tiene permisos)
        $csfOutput = @shell_exec('csf -l 2>&1') ?: @shell_exec('/usr/sbin/csf -l 2>&1');
        if ($csfOutput && stripos($csfOutput, 'Permission denied') === false) {
            $lines = explode("\n", $csfOutput);
            foreach ($lines as $line) {
                $line = trim($line);
                if (preg_match('/(\d{1,3}(?:\.\d{1,3}){3})/', $line, $matches)) {
                    $ip = $matches[1];
                    if (!isset($blocked[$ip]) && !in_array($ip, ['0.0.0.0', '255.255.255.255'])) {
                        $blocked[$ip] = [
                            'ip' => $ip,
                            'line' => "CSF: " . $line,
                            'source' => 'csf'
                        ];
                    }
                }
            }
        }
        
        // 2. Obtener de cPHulk (WHM API - Siempre disponible con Token root)
        $hulk = $this->getCPHulkBlockedIPs();
        foreach ($hulk as $item) {
            $ip = $item['ip'];
            if (!isset($blocked[$ip])) {
                $blocked[$ip] = [
                    'ip' => $ip,
                    'line' => "cPHulk: " . ($item['reason'] ?? 'Bloqueo por seguridad'),
                    'source' => 'cphulk'
                ];
            } else {
                // Si ya está en CSF, solo anotar que también está en cPHulk
                $blocked[$ip]['line'] .= " | cPHulk: " . ($item['reason'] ?? 'Bloqueo');
            }
        }
        
        return array_values($blocked);
    }
    
    /**
     * Obtener IPs bloqueadas asociadas a una cuenta específica
     */
    public function getAccountBlockedIPs($user) {
        $allBlocked = $this->getBlockedIPs();
        if (empty($allBlocked)) return [];

        $accountBlocked = [];
        
        // 1. Obtener IPs de últimos logins exitosos del usuario
        $lastLogins = $this->getEmailLastLogins($user);
        $userIps = [];
        foreach ($lastLogins as $login) {
            if (isset($login['ip'])) $userIps[$login['ip']] = true;
        }

        // 2. Obtener intentos fallidos de cPHulk para este usuario
        $failedLogins = $this->call('get_cphulk_failed_logins', ['user' => $user]);
        if (isset($failedLogins['data']['failed_logins'])) {
            foreach ($failedLogins['data']['failed_logins'] as $failed) {
                if (isset($failed['ip'])) $userIps[$failed['ip']] = true;
            }
        }

        // 3. Cruzar con la lista total de bloqueos
        foreach ($allBlocked as $item) {
            if (isset($userIps[$item['ip']])) {
                $accountBlocked[] = $item;
            }
        }

        return $accountBlocked;
    }
    
    /**
     * Obtener IPs bloqueadas de cPHulk via WHM API
     */
    public function getCPHulkBlockedIPs() {
        $ips = [];
        
        // 1. Blacklist permanente
        $resBlack = $this->call('read_cphulk_records', ['list_name' => 'black']);
        if (isset($resBlack['data']['ips_in_list'])) {
            foreach ($resBlack['data']['ips_in_list'] as $entry) {
                $ip = is_array($entry) ? ($entry['ip'] ?? '') : $entry;
                if ($ip) $ips[] = ['ip' => $ip, 'reason' => 'Lista Negra Permanente'];
            }
        }
        
        // 2. Brutes (Bloqueos temporales)
        $resBrutes = $this->call('get_cphulk_brutes');
        if (isset($resBrutes['data']['brutes'])) {
            foreach ($resBrutes['data']['brutes'] as $brute) {
                if (isset($brute['ip'])) {
                    $ips[] = ['ip' => $brute['ip'], 'reason' => 'Bloqueo Temporal (Fuerza Bruta)'];
                }
            }
        }

        // 3. Excessive Brutes (Bloqueos por múltiples reintentos)
        $resExcessive = $this->call('get_cphulk_excessive_brutes');
        if (isset($resExcessive['data']['excessive_brutes'])) {
            foreach ($resExcessive['data']['excessive_brutes'] as $brute) {
                if (isset($brute['ip'])) {
                    $ips[] = ['ip' => $brute['ip'], 'reason' => 'Bloqueo por Reintentos Excesivos (' . ($brute['notes'] ?? '') . ')'];
                }
            }
        }
        
        return $ips;
    }
    
    /**
     * Desbloquear IP (Intenta CSF y cPHulk)
     */
    public function unblockIP($ip) {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return ['error' => true, 'message' => 'IP inválida'];
        }
        
        $results = [];
        
        // 1. Intentar CSF
        $safeIp = escapeshellarg($ip);
        $csfOut = @shell_exec("csf --dr {$safeIp} 2>&1") ?: @shell_exec("/usr/sbin/csf --dr {$safeIp} 2>&1");
        if ($csfOut) $results['csf'] = trim($csfOut);
        
        // 2. Intentar cPHulk (Blacklist)
        $hulkRes = $this->call('delete_cphulk_record', [
            'list_name' => 'black',
            'ip' => $ip
        ]);
        $results['cphulk_black'] = $hulkRes['metadata']['reason'] ?? 'OK';
        
        // 3. Intentar cPHulk (Flush temporary blocks)
        $hulkFlush = $this->call('flush_cphulk_logs', ['ip' => $ip]);
        $results['cphulk_flush'] = $hulkFlush['metadata']['reason'] ?? 'OK';
        
        return [
            'success' => true,
            'ip' => $ip,
            'details' => $results
        ];
    }

    /**
     * Obtener IPs en la Lista Blanca (cPHulk)
     */
    public function getWhitelistedIPs() {
        $ips = [];
        $res = $this->call('read_cphulk_records', ['list_name' => 'white']);
        if (isset($res['data']['ips_in_list'])) {
            foreach ($res['data']['ips_in_list'] as $entry) {
                $ip = is_array($entry) ? ($entry['ip'] ?? '') : $entry;
                if ($ip) {
                    $ips[] = [
                        'ip' => $ip,
                        'comment' => is_array($entry) ? ($entry['comment'] ?? '') : ''
                    ];
                }
            }
        }
        return $ips;
    }

    /**
     * Añadir IP a la Lista Blanca (cPHulk)
     */
    public function whitelistIP($ip, $comment = '') {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return ['error' => true, 'message' => 'IP inválida'];
        }
        
        $params = [
            'list_name' => 'white',
            'ip' => $ip
        ];
        if ($comment) $params['comment'] = $comment;

        $res = $this->call('create_cphulk_record', $params);
        
        // Al añadir a whitelist, también deberíamos sacarlo de brutes si está
        $this->call('flush_cphulk_logs', ['ip' => $ip]);

        return $res;
    }

    /**
     * Remover IP de la Lista Blanca (cPHulk)
     */
    public function removeWhitelistIP($ip) {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return ['error' => true, 'message' => 'IP inválida'];
        }

        return $this->call('delete_cphulk_record', [
            'list_name' => 'white',
            'ip' => $ip
        ]);
    }

    // =========================================================
    // RESELLER MANAGEMENT
    // =========================================================

    /**
     * Listar todos los resellers del servidor
     */
    public function listResellers() {
        $result = $this->call('listresellers');
        if (isset($result['error'])) return [];
        // API returns a single 'reseller' key or a list
        $raw = $result['data']['reseller'] ?? [];
        if (!is_array($raw)) $raw = [$raw];
        return array_filter($raw); // remove empty entries
    }

    /**
     * Obtener estadísticas de recursos de todos los resellers
     */
    public function getResellerStats() {
        $resellers = $this->listResellers();
        if (empty($resellers)) return [];

        $stats = [];
        foreach ($resellers as $reseller) {
            $name = is_array($reseller) ? ($reseller['user'] ?? $reseller) : $reseller;
            if (empty($name)) continue;

            $res = $this->call('resellerstats', ['reseller' => $name]);
            $data = $res['data'] ?? [];

            // Also get accounts owned by this reseller
            $accts = $this->call('listaccts', ['search' => $name, 'searchtype' => 'owner']);
            $ownedAccounts = $accts['data']['acct'] ?? [];
            if (!is_array($ownedAccounts)) $ownedAccounts = [];

            $stats[] = [
                'name'           => $name,
                'diskused'       => $data['diskused'] ?? 0,
                'disklimit'      => $data['disklimit'] ?? 'unlimited',
                'bwused'         => $data['bandwidthused'] ?? 0,
                'bwlimit'        => $data['bandwidthlimit'] ?? 'unlimited',
                'acctcount'      => count($ownedAccounts),
                'accts'          => $ownedAccounts,
                'suspended'      => intval($data['suspended'] ?? 0) === 1,
            ];
        }
        return $stats;
    }

    /**
     * Convertir una cuenta cPanel en Reseller
     */
    public function createReseller($user, $makeOwner = true) {
        return $this->call('setupreseller', [
            'user'      => $user,
            'makeowner' => $makeOwner ? 1 : 0,
        ]);
    }

    /**
     * Quitar el rol de Reseller a una cuenta
     */
    public function removeReseller($reseller) {
        return $this->call('unsetupreseller', ['reseller' => $reseller]);
    }

    /**
     * Suspender un Reseller y todas sus cuentas
     */
    public function suspendReseller($reseller, $reason = '') {
        $params = ['reseller' => $reseller];
        if ($reason) $params['reason'] = $reason;
        return $this->call('suspendreseller', $params);
    }

    /**
     * Reactivar un Reseller y sus cuentas
     */
    public function unsuspendReseller($reseller) {
        return $this->call('unsuspendreseller', ['reseller' => $reseller]);
    }

    /**
     * Terminar/Eliminar un Reseller y TODAS sus cuentas
     * DESTRUCTIVO - usar con máxima precaución
     */
    public function terminateReseller($reseller, $terminateAccounts = true) {
        return $this->call('terminatereseller', [
            'reseller'   => $reseller,
            'terminateResellers' => $terminateAccounts ? 1 : 0,
        ]);
    }

    /**
     * Establecer límites de recursos del Reseller
     * $limits = ['diskquota' => 5000, 'bwlimit' => 10000, 'maxacct' => 30]
     * Pasar 0 para ilimitado
     */
    public function setResellerLimits($reseller, array $limits) {
        $params = array_merge(['reseller' => $reseller], $limits);
        return $this->call('setresellerlimits', $params);
    }

    /**
     * Obtener ACLs disponibles en el servidor
     */
    public function listACLs() {
        $result = $this->call('listacls');
        return $result['data'] ?? [];
    }

    /**
     * Obtener las ACLs asignadas a un reseller
     */
    public function getResellerACLs($reseller) {
        $result = $this->call('listresellerpkgs', ['reseller' => $reseller]);
        // Fallback: use myprivs-style check
        if (isset($result['error'])) {
            return [];
        }
        return $result['data'] ?? [];
    }

    /**
     * Establecer ACLs para un Reseller
     * $acls = ['create-acct' => 1, 'suspend-acct' => 1, 'kill-acct' => 0, ...]
     */
    public function setResellerACLs($reseller, array $acls) {
        $params = ['reseller' => $reseller];
        foreach ($acls as $aclKey => $value) {
            $params["acl-$aclKey"] = $value ? 'yes' : 'no';
        }
        return $this->call('setacls', $params);
    }

    /**
     * Reasignar una cuenta de cPanel a un Reseller diferente
     */
    public function reassignAccount($user, $newOwner) {
        return $this->call('modifyacct', [
            'user'  => $user,
            'owner' => $newOwner,
        ]);
    }

    /**
     * Obtener las IPs asignadas a un Reseller
     */
    public function getResellerIPs($reseller) {
        $result = $this->call('getresellerips', ['reseller' => $reseller]);
        return $result['data']['ip'] ?? [];
    }
}
