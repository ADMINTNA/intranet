<?php
/**
 * WHM API Configuration
 * Intranet Icontel - WHM Server Report
 * 
 * INSTRUCCIONES:
 * 1. Ingresa a WHM → Development → Manage API Tokens
 * 2. Genera un token con los permisos necesarios
 * 3. Copia el token aquí abajo
 */

// ============================================
// CONFIGURACIÓN WHM API
// ============================================
define('WHM_HOST', 'cleveland.icontel.cl');  // Servidor WHM Icontel
define('WHM_PORT', 2087);                    // Puerto WHM (2087 = HTTPS)
define('WHM_USERNAME', 'root');              // Usuario WHM
define('WHM_API_TOKEN', '4RPASEKM68R74H3G7CX66S7ZEAB6X8BM');  // API Token

// ============================================
// CONFIGURACIÓN GENERAL
// ============================================
define('APP_NAME', 'WHM Server Report');
define('APP_VERSION', '1.0.0');
define('TIMEZONE', 'America/Santiago');

// Días sin actividad para considerar cuenta "inactiva"
define('INACTIVE_DAYS_WARNING', 30);
define('INACTIVE_DAYS_CRITICAL', 90);
define('INACTIVE_DAYS_ABANDONED', 180);

date_default_timezone_set(TIMEZONE);

// ============================================
// NO EDITAR DEBAJO DE ESTA LÍNEA
// ============================================
define('WHM_API_URL', 'https://' . WHM_HOST . ':' . WHM_PORT);
