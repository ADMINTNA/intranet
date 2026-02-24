<?php
/**
 * =============================================================================
 * ARCHIVO: /intranet/includes/sweet_auth.php
 * =============================================================================
 * 
 * Módulo de Autenticación SuiteCRM para Kickoff
 * 
 * Verifica si el usuario está autenticado en SuiteCRM y extrae sus datos
 * incluyendo security groups.
 * 
 * @author Mauricio Araneda (mAo)
 * @date 2025-12-08
 */

class SweetAuth {
    
    private static $suitecrm_path = '/home/icontel/public_html/intranet/sweet';
    private static $current_user = null;
    private static $security_groups = null;
    
    /**
     * Verifica si el usuario está autenticado en SuiteCRM
     * 
     * @return bool True si está autenticado, false en caso contrario
     */
    public static function isAuthenticated() {
        // Guardar directorio actual
        $original_dir = getcwd();
        
        try {
            // Cambiar al directorio de SuiteCRM
            if (!chdir(self::$suitecrm_path)) {
                error_log("SweetAuth: No se pudo cambiar al directorio de SuiteCRM");
                return false;
            }
            
            // Workaround para Composer platform check
            // Crear un archivo dummy que no haga nada
            $platform_check = 'vendor/composer/platform_check.php';
            $platform_check_bak = 'vendor/composer/platform_check.php.original';
            $created_dummy = false;
            
            if (file_exists($platform_check)) {
                // Respaldar el original
                copy($platform_check, $platform_check_bak);
                // Crear archivo dummy que no hace nada
                file_put_contents($platform_check, "<?php\n// Dummy file for Kickoff compatibility\n");
                $created_dummy = true;
            }
            
            // Definir que somos un entry point válido
            if (!defined('sugarEntry')) {
                define('sugarEntry', true);
            }
            
            // Incluir el entryPoint de SuiteCRM
            if (!file_exists('include/entryPoint.php')) {
                error_log("SweetAuth: No se encuentra include/entryPoint.php");
                // Restaurar archivo original si fue modificado
                if ($created_dummy && file_exists($platform_check_bak)) {
                    copy($platform_check_bak, $platform_check);
                    unlink($platform_check_bak);
                }
                chdir($original_dir);
                return false;
            }
            
            require_once('include/entryPoint.php');
            
            // Restaurar archivo original
            if ($created_dummy && file_exists($platform_check_bak)) {
                copy($platform_check_bak, $platform_check);
                unlink($platform_check_bak);
            }
            
            // Verificar si hay usuario autenticado
            $authenticated = isset($_SESSION['authenticated_user_id']) && !empty($_SESSION['authenticated_user_id']);
            
            // Restaurar directorio original
            chdir($original_dir);
            
            return $authenticated;
            
        } catch (Exception $e) {
            error_log("SweetAuth: Error al verificar autenticación - " . $e->getMessage());
            // Restaurar archivo original si fue modificado
            if (isset($created_dummy) && $created_dummy && file_exists($platform_check_bak)) {
                copy($platform_check_bak, $platform_check);
                unlink($platform_check_bak);
            }
            chdir($original_dir);
            return false;
        }
    }
    
    /**
     * Obtiene los datos del usuario autenticado en SuiteCRM
     * 
     * @return array|null Array con datos del usuario o null si no está autenticado
     */
    public static function getUserData() {
        if (self::$current_user !== null) {
            return self::$current_user;
        }
        
        // Guardar directorio actual
        $original_dir = getcwd();
        
        try {
            // Cambiar al directorio de SuiteCRM
            chdir(self::$suitecrm_path);
            
            // Deshabilitar verificación de plataforma de Composer
            putenv('COMPOSER_PLATFORM_CHECK=0');
            $_ENV['COMPOSER_PLATFORM_CHECK'] = '0';
            
            // Definir que somos un entry point válido
            if (!defined('sugarEntry')) {
                define('sugarEntry', true);
            }
            
            // Incluir el entryPoint de SuiteCRM
            require_once('include/entryPoint.php');
            
            // Obtener el objeto $current_user global
            global $current_user;
            
            if (!isset($current_user) || empty($current_user->id)) {
                chdir($original_dir);
                return null;
            }
            
            // Extraer datos del usuario
            $user_data = [
                'id' => $current_user->id,
                'user_name' => $current_user->user_name,
                'first_name' => $current_user->first_name ?? '',
                'last_name' => $current_user->last_name ?? '',
                'full_name' => trim(($current_user->first_name ?? '') . ' ' . ($current_user->last_name ?? '')),
                'email' => $current_user->email1 ?? '',
                'is_admin' => $current_user->is_admin ?? false,
                'status' => $current_user->status ?? 'Active',
            ];
            
            // Guardar en cache
            self::$current_user = $user_data;
            
            // Restaurar directorio original
            chdir($original_dir);
            
            return $user_data;
            
        } catch (Exception $e) {
            error_log("SweetAuth: Error al obtener datos del usuario - " . $e->getMessage());
            chdir($original_dir);
            return null;
        }
    }
    
    /**
     * Obtiene los security groups del usuario autenticado
     * 
     * @return array Array de security groups con id, name y description
     */
    public static function getSecurityGroups() {
        if (self::$security_groups !== null) {
            return self::$security_groups;
        }
        
        $user_data = self::getUserData();
        
        if (!$user_data) {
            return [];
        }
        
        // Guardar directorio actual
        $original_dir = getcwd();
        
        try {
            // Cambiar al directorio de SuiteCRM
            chdir(self::$suitecrm_path);
            
            // Deshabilitar verificación de plataforma de Composer
            putenv('COMPOSER_PLATFORM_CHECK=0');
            $_ENV['COMPOSER_PLATFORM_CHECK'] = '0';
            
            // Definir que somos un entry point válido
            if (!defined('sugarEntry')) {
                define('sugarEntry', true);
            }
            
            // Incluir el entryPoint de SuiteCRM
            require_once('include/entryPoint.php');
            
            // Obtener conexión a BD de SuiteCRM
            global $db;
            
            $query = "
                SELECT sg.id, sg.name, sg.description
                FROM securitygroups sg
                JOIN securitygroups_users sgu ON sg.id = sgu.securitygroup_id
                WHERE sgu.user_id = '" . $db->quote($user_data['id']) . "'
                  AND sgu.deleted = 0 
                  AND sg.deleted = 0
                ORDER BY sg.name
            ";
            
            $result = $db->query($query);
            
            $groups = [];
            while ($row = $db->fetchByAssoc($result)) {
                $groups[] = $row;
            }
            
            // Guardar en cache
            self::$security_groups = $groups;
            
            // Restaurar directorio original
            chdir($original_dir);
            
            return $groups;
            
        } catch (Exception $e) {
            error_log("SweetAuth: Error al obtener security groups - " . $e->getMessage());
            chdir($original_dir);
            return [];
        }
    }
    
    /**
     * Crea la sesión de Kickoff con datos de SuiteCRM
     * 
     * @return bool True si se creó exitosamente, false en caso contrario
     */
    public static function createKickoffSession() {
        $user_data = self::getUserData();
        $groups = self::getSecurityGroups();
        
        if (!$user_data) {
            return false;
        }
        
        // Crear variables de sesión de Kickoff
        $_SESSION['loggedin'] = true;
        $_SESSION['sweet_user_id'] = $user_data['id'];
        $_SESSION['name'] = $user_data['user_name'];
        $_SESSION['cliente'] = $user_data['full_name'];
        $_SESSION['email'] = $user_data['email'];
        $_SESSION['is_admin'] = $user_data['is_admin'];
        
        // Asignar security group
        if (!empty($groups)) {
            $_SESSION['sg_id'] = $groups[0]['id'];
            $_SESSION['sg_name'] = $groups[0]['name'];
        } else {
            // Usuario sin grupos - asignar grupo por defecto
            $_SESSION['sg_id'] = 'a03a40e8-bda8-0f1b-b447-58dcfb6f5c19';
            $_SESSION['sg_name'] = 'Sin Grupo Asignado';
        }
        
        return true;
    }
    
    /**
     * Obtiene la URL de redirección a SuiteCRM para login
     * 
     * @param string $return_url URL a la que volver después del login
     * @return string URL de redirección
     */
    public static function getLoginUrl($return_url = '') {
        if (empty($return_url)) {
            $return_url = 'https://intranet.icontel.cl/kickoff_icontel/icontel.php';
        }
        
        return 'https://sweet.icontel.cl/index.php?return_url=' . urlencode($return_url);
    }
}
?>
