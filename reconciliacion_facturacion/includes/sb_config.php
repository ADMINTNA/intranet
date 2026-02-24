<?php
// ==========================================================
// Sweet ↔ BSale Invoice Reconciliation - Configuration
// /reconciliacion_facturacion/includes/sb_config.php
// Author: Mauricio Araneda (mAo)
// Date: 2025-12-18
// Encoding: UTF-8 without BOM
// ==========================================================

// Force UTF-8 header
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding("UTF-8");

// Error reporting (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Timezone
date_default_timezone_set("America/Santiago");
setlocale(LC_ALL, 'es_CL');

// ==========================================================
// DATABASE CONFIGURATION
// ==========================================================
define('DB_HOST', 'localhost');
define('DB_USER', 'data_studio');
define('DB_PASS', '1Ngr3s0.,');
define('DB_SWEET', 'tnasolut_sweet');
define('DB_CLIENTES', 'icontel_clientes');
define('DB_MONEDAS', 'icontel_monedas');

// ==========================================================
// BSALE API CONFIGURATION
// ==========================================================
define('BSALE_TOKEN', '9c54f0b10d15274ec67b08363e6964a9e3474543');
define('BSALE_API_URL', 'https://api.bsale.io/v1');
define('BSALE_API_TIMEOUT', 30);

// ==========================================================
// SUITECRM CONFIGURATION
// ==========================================================
define('SWEET_URL', 'https://sweet.icontel.cl');
define('SWEET_API_URL', SWEET_URL . '/custom/tools');

// ==========================================================
// APPLICATION SETTINGS
// ==========================================================
define('REQUIRE_CONFIRMATION', true);  // Require user confirmation before updates
define('ENABLE_AUDIT_LOG', true);      // Log all changes
define('CURRENCY_TOLERANCE', 0.01);    // Tolerance for currency comparison (1%)

// ==========================================================
// DATABASE CONNECTION FUNCTION
// ==========================================================
function DbConnect($dbname) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    mysqli_set_charset($conn, "utf8mb4");
    
    $bd_seleccionada = mysqli_select_db($conn, $dbname);
    if (!$bd_seleccionada) {
        die("Cannot use database $dbname: " . mysqli_error($conn));
    }
    
    return $conn;
}

// ==========================================================
// CURRENCY CONVERSION FUNCTIONS
// ==========================================================
function getUFValue($date = null) {
    $conn = DbConnect(DB_SWEET);
    
    if ($date === null) {
        $date = date('Y-m-d');
    }
    
    $sql = "CALL tnasolut_sweet.moneda_ultimo_valor(6)"; // UF = 6
    $result = $conn->query($sql);
    
    $uf_value = 0;
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $uf_value = floatval($row['valor']);
    }
    
    $conn->close();
    return $uf_value;
}

function getUSDValue($date = null) {
    $conn = DbConnect(DB_SWEET);
    
    if ($date === null) {
        $date = date('Y-m-d');
    }
    
    $sql = "CALL tnasolut_sweet.moneda_ultimo_valor(2)"; // USD = 2
    $result = $conn->query($sql);
    
    $usd_value = 0;
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $usd_value = floatval($row['valor']);
    }
    
    $conn->close();
    return $usd_value;
}

function convertToCLP($amount, $currency, $uf_value = null, $usd_value = null) {
    $currency = strtoupper(trim($currency));
    
    switch ($currency) {
        case 'CLP':
        case '$':
            return floatval($amount);
            
        case 'UF':
        case 'CLF':  // BSale uses CLF for UF
            if ($uf_value === null) {
                $uf_value = getUFValue();
            }
            return floatval($amount) * $uf_value;
            
        case 'USD':
        case 'US$':
            if ($usd_value === null) {
                $usd_value = getUSDValue();
            }
            return floatval($amount) * $usd_value;
            
        default:
            return floatval($amount);
    }
}

// ==========================================================
// UTILITY FUNCTIONS
// ==========================================================
function formatCurrency($amount, $currency = 'CLP') {
    $currency = strtoupper(trim($currency));
    
    switch ($currency) {
        case 'UF':
            return 'UF ' . number_format($amount, 2, ',', '.');
        case 'USD':
            return 'US$ ' . number_format($amount, 2, ',', '.');
        default:
            return '$ ' . number_format($amount, 0, ',', '.');
    }
}

function logAuditChange($action, $record_type, $record_id, $old_value, $new_value, $user_id = null) {
    if (!ENABLE_AUDIT_LOG) {
        return;
    }
    
    $conn = DbConnect(DB_CLIENTES);
    
    // Check if table exists, if not skip logging
    $check = $conn->query("SHOW TABLES LIKE 'audit_log_sweet_bsale'");
    if ($check->num_rows == 0) {
        $conn->close();
        return;
    }
    
    $sql = "INSERT INTO audit_log_sweet_bsale 
            (action, record_type, record_id, old_value, new_value, user_id, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('ssssss', $action, $record_type, $record_id, $old_value, $new_value, $user_id);
        $stmt->execute();
        $stmt->close();
    }
    
    $conn->close();
}

// ==========================================================
// SESSION VALIDATION
// ==========================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function validateSession() {
    // TEMPORARY: Session validation disabled for testing
    return;
    
    // Check if user is logged in (reusing KickOff session)
    if (!isset($_SESSION['sg_id'])) {
        header('Location: ../index.php?error=session');
        exit;
    }
    
    // Store user ID for audit logging
    if (!isset($_SESSION['user_id']) && isset($_SESSION['sg_id'])) {
        $_SESSION['user_id'] = $_SESSION['sg_id'];
    }
}

?>
