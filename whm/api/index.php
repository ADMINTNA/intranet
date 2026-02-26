<?php
/**
 * API Endpoint - WHM Report Data
 * Retorna datos JSON para el frontend
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../includes/WhmApi.php';

$action = $_GET['action'] ?? 'report';
$whm = new WhmApi();

try {
    switch ($action) {
        
        case 'test':
            $result = $whm->testConnection();
            echo json_encode($result);
            break;
            
        case 'report':
            $report = $whm->getFullReport();
            echo json_encode($report);
            break;
            
        case 'accounts':
            $accounts = $whm->listAccounts();
            echo json_encode(['accounts' => $accounts]);
            break;
            
        case 'account_detail':
            $user = $_GET['user'] ?? '';
            if (empty($user)) {
                echo json_encode(['error' => true, 'message' => 'User parameter required']);
                break;
            }
            $summary = $whm->accountSummary($user);
            $emails = $whm->getEmailAccounts($user);
            $databases = $whm->getDatabases($user);
            $domains = $whm->getAddonDomains($user);
            
            echo json_encode([
                'account' => $summary,
                'emails' => $emails,
                'databases' => $databases,
                'domains' => $domains,
            ]);
            break;
            
        case 'server_info':
            $info = $whm->getServerInfo();
            echo json_encode($info);
            break;
            
        case 'suspended':
            $suspended = $whm->getSuspendedAccounts();
            echo json_encode(['suspended' => $suspended]);
            break;
            
        case 'bandwidth':
            $month = $_GET['month'] ?? null;
            $year = $_GET['year'] ?? null;
            $bw = $whm->getBandwidth($month, $year);
            echo json_encode(['bandwidth' => $bw]);
            break;
            
        default:
            echo json_encode(['error' => true, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
