<?php
// ==========================================================
// SweetAuth - Cliente de autenticación SuiteCRM para Kickoff
// Ruta: /kickoff_icontel/includes/sweet_auth.php
// Descripción: Biblioteca cliente para verificar autenticación
//              de SuiteCRM mediante API HTTP (compatible PHP 7.0)
// Autor: GPT Sweet
// Fecha: 2025-12-08
// Codificación: UTF-8 sin BOM
// ==========================================================

class SweetAuth
{
    // URL del API de Sweet
    const API_URL = 'https://sweet.icontel.cl/custom/tools/sweet_session_api.php';
    
    // URL de login de Sweet
    const LOGIN_URL = 'https://sweet.icontel.cl/index.php';
    
    // ==========================================================
    // MÉTODO: isAuthenticated()
    // Verifica si el usuario está autenticado en SuiteCRM
    // ==========================================================
    public static function isAuthenticated()
    {
        // Si ya tenemos datos en sesión, no hacer otra llamada al API
        if (isset($_SESSION['sweet_auth_data']) && 
            isset($_SESSION['sweet_auth_data']['authenticated']) &&
            $_SESSION['sweet_auth_data']['authenticated'] === true) {
            return true;
        }
        
        // Hacer llamada al API de Sweet
        $response = self::callSweetAPI();
        
        if ($response === false) {
            return false;
        }
        
        // Guardar respuesta en sesión
        $_SESSION['sweet_auth_data'] = $response;
        
        return (isset($response['authenticated']) && $response['authenticated'] === true);
    }
    
    // ==========================================================
    // MÉTODO: getLoginUrl($return_url)
    // Devuelve URL de login de Sweet con parámetro de retorno
    // ==========================================================
    public static function getLoginUrl($return_url = '')
    {
        if (empty($return_url)) {
            return self::LOGIN_URL;
        }
        
        return self::LOGIN_URL . '?return_url=' . urlencode($return_url);
    }
    
    // ==========================================================
    // MÉTODO: createKickoffSession()
    // Crea variables de sesión de Kickoff desde datos de Sweet
    // ==========================================================
    public static function createKickoffSession()
    {
        // Verificar que tenemos datos de Sweet
        if (!isset($_SESSION['sweet_auth_data']) || 
            !isset($_SESSION['sweet_auth_data']['authenticated']) ||
            $_SESSION['sweet_auth_data']['authenticated'] !== true) {
            return false;
        }
        
        $data = $_SESSION['sweet_auth_data'];
        
        // Crear variables de sesión de Kickoff
        $_SESSION['loggedin'] = true;
        $_SESSION['usuario'] = $data['user_name'];
        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['user_full_name'] = $data['full_name'];
        $_SESSION['user_email'] = $data['email'];
        $_SESSION['is_admin'] = $data['is_admin'];
        
        // Guardar Security Groups
        if (isset($data['security_groups']) && is_array($data['security_groups'])) {
            $_SESSION['sweet_security_groups'] = $data['security_groups'];
            
            // Extraer solo los IDs para compatibilidad
            $group_ids = array();
            foreach ($data['security_groups'] as $group) {
                if (isset($group['id'])) {
                    $group_ids[] = $group['id'];
                }
            }
            $_SESSION['sweet_security_group_ids'] = $group_ids;
        }
        
        return true;
    }
    
    // ==========================================================
    // MÉTODO: getUserData()
    // Devuelve datos del usuario autenticado
    // ==========================================================
    public static function getUserData()
    {
        if (isset($_SESSION['sweet_auth_data'])) {
            return $_SESSION['sweet_auth_data'];
        }
        
        return null;
    }
    
    // ==========================================================
    // MÉTODO PRIVADO: callSweetAPI()
    // Hace llamada cURL al API de Sweet
    // ==========================================================
    private static function callSweetAPI()
    {
        // Inicializar cURL
        $ch = curl_init(self::API_URL);
        
        if ($ch === false) {
            return false;
        }
        
        // Configurar opciones de cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        // IMPORTANTE: Enviar cookies del navegador
        // Esto permite que Sweet vea la sesión del usuario
        if (isset($_COOKIE)) {
            $cookie_string = '';
            foreach ($_COOKIE as $name => $value) {
                $cookie_string .= $name . '=' . $value . '; ';
            }
            curl_setopt($ch, CURLOPT_COOKIE, rtrim($cookie_string, '; '));
        }
        
        // Ejecutar petición
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        // Verificar respuesta
        if ($response === false || $http_code !== 200) {
            return false;
        }
        
        // Decodificar JSON
        $data = json_decode($response, true);
        
        if (!is_array($data)) {
            return false;
        }
        
        return $data;
    }
    
    // ==========================================================
    // MÉTODO: clearSession()
    // Limpia datos de sesión de Sweet
    // ==========================================================
    public static function clearSession()
    {
        unset($_SESSION['sweet_auth_data']);
        unset($_SESSION['sweet_security_groups']);
        unset($_SESSION['sweet_security_group_ids']);
    }
}
