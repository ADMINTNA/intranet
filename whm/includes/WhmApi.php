<?php
/**
 * WHM API Client
 * Maneja todas las comunicaciones con la API de WHM
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
     * Realiza una llamada a la API de WHM
     */
    private function call($function, $params = []) {
        $params['api.version'] = 1;
        $url = $this->baseUrl . '/json-api/' . $function . '?' . http_build_query($params);
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTPHEADER => [
                'Authorization: WHM ' . $this->username . ':' . $this->token
            ]
        ]);
        
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
     */
    private function callUapi($user, $module, $function, $params = []) {
        $url = $this->baseUrl . '/json-api/cpanel';
        $params['cpanel_jsonapi_user'] = $user;
        $params['cpanel_jsonapi_apiversion'] = '3';
        $params['cpanel_jsonapi_module'] = $module;
        $params['cpanel_jsonapi_func'] = $function;
        
        $url .= '?' . http_build_query($params);
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Authorization: WHM ' . $this->username . ':' . $this->token
            ]
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
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
     * Obtener estadísticas del servidor
     */
    public function getServerInfo() {
        $hostname = $this->call('gethostname');
        $version = $this->call('version');
        $loadavg = $this->call('systemloadavg', ['api.version' => 1]);
        
        return [
            'hostname' => $hostname['data']['hostname'] ?? 'N/A',
            'version' => $version['data']['version'] ?? 'N/A',
            'loadavg' => $loadavg['data'] ?? []
        ];
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
        $totalAccounts = count($accounts);
        $totalSuspended = count($suspendedUsers);
        $totalActive = $totalAccounts - $totalSuspended;
        
        $accountsData = [];
        
        foreach ($accounts as $acct) {
            $user = $acct['user'];
            $diskUsedRaw = $this->parseSize($acct['diskused'] ?? '0M');
            $diskLimitRaw = $this->parseSize($acct['disklimit'] ?? 'unlimited');
            
            $totalDiskUsed += $diskUsedRaw;
            if ($diskLimitRaw > 0) $totalDisk += $diskLimitRaw;
            
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
            
            $isSuspended = isset($suspendedUsers[$user]);
            
            $accountsData[] = [
                'user' => $user,
                'domain' => $acct['domain'] ?? '',
                'email' => $acct['email'] ?? '',
                'plan' => $acct['plan'] ?? '',
                'ip' => $acct['ip'] ?? '',
                'disk_used' => $diskUsedRaw,
                'disk_used_hr' => $acct['diskused'] ?? '0M',
                'disk_limit' => $diskLimitRaw,
                'disk_limit_hr' => $acct['disklimit'] ?? 'unlimited',
                'disk_percent' => ($diskLimitRaw > 0) ? round(($diskUsedRaw / $diskLimitRaw) * 100, 1) : 0,
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
                'inodes_used' => $acct['inodesused'] ?? 0,
            ];
        }
        
        return [
            'summary' => [
                'total_accounts' => $totalAccounts,
                'active_accounts' => $totalActive,
                'suspended_accounts' => $totalSuspended,
                'total_disk_used' => $totalDiskUsed,
                'total_disk_used_hr' => $this->formatBytes($totalDiskUsed),
                'total_disk_limit' => $totalDisk,
                'total_disk_limit_hr' => $this->formatBytes($totalDisk),
                'disk_percent' => ($totalDisk > 0) ? round(($totalDiskUsed / $totalDisk) * 100, 1) : 0,
                'generated_at' => date('Y-m-d H:i:s'),
            ],
            'accounts' => $accountsData,
        ];
    }
    
    /**
     * Parsea tamaños como "500M", "1G", "unlimited" a bytes
     */
    private function parseSize($size) {
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
        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }
}
