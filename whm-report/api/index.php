<?php
/**
 * ============================================================
 * API Endpoint - WHM Report Data
 * Retorna datos JSON para el frontend
 * ============================================================
 * Archivo    : index.php
 * Path       : /home/icontel/public_html/intranet/whm-report/api/index.php
 * Versión    : 1.0.0
 * Fecha      : 2026-02-25 20:57:00
 * Proyecto   : WHM Server Report - Icontel Intranet
 * Autor      : Icontel Dev Team
 * ============================================================
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../includes/WhmApi.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'report';
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
            foreach ($accounts as &$acct) {
                $acct['email_count'] = $whm->countEmailAccounts($acct['user']);
                $acct['email_limit'] = $acct['maxpop'] ?? 'unlimited';
            }
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
            $lastLogins = $whm->getEmailLastLogins($user);
            $databases = $whm->getDatabases($user);
            $domains = $whm->getAddonDomains($user);
            $forwarders = $whm->getForwarders($user);
            $autoresponders = $whm->getAutoResponders($user);
            $mailingLists = $whm->getMailingLists($user);
            
            echo json_encode([
                'account' => array_merge($summary, [
                    'email_count' => is_array($emails) ? count($emails) : 0,
                    'email_limit' => $summary['maxpop'] ?? 'unlimited',
                    'is_managerial' => (($whm->parseSize($summary['disklimit'] ?? '0')) > 4 * 1024 * 1024 * 1024) || (($whm->parseSize($summary['disklimit'] ?? '0')) == 0)
                ]),
                'emails' => $emails,
                'last_logins' => $lastLogins,
                'databases' => $databases,
                'domains' => $domains,
                'forwarders' => $forwarders,
                'autoresponders' => $autoresponders,
                'mailing_lists' => $mailingLists,
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
            
        case 'packages':
            $packages = $whm->listPackages();
            echo json_encode(['packages' => $packages]);
            break;
            
        case 'change_package':
            $user = $_GET['user'] ?? $_POST['user'] ?? '';
            $pkg = $_GET['pkg'] ?? $_POST['pkg'] ?? '';
            if (empty($user) || empty($pkg)) {
                echo json_encode(['error' => true, 'message' => 'Usuario y paquete requeridos']);
                break;
            }
            $result = $whm->changePackage($user, $pkg);
            echo json_encode($result);
            break;
            
        case 'bandwidth':
            $month = $_GET['month'] ?? null;
            $year = $_GET['year'] ?? null;
            $bw = $whm->getBandwidth($month, $year);
            echo json_encode(['bandwidth' => $bw]);
            break;
            
        case 'blocked_ips':
            $ips = $whm->getBlockedIPs();
            echo json_encode(['blocked_ips' => $ips]);
            break;
            
        case 'unblock_ip':
            $ip = $_GET['ip'] ?? $_POST['ip'] ?? '';
            if (empty($ip)) {
                echo json_encode(['error' => true, 'message' => 'IP requerida']);
                break;
            }
            $result = $whm->unblockIP($ip);
            echo json_encode($result);
            break;

        case 'whitelisted_ips':
            $ips = $whm->getWhitelistedIPs();
            echo json_encode(['whitelisted_ips' => $ips]);
            break;

        case 'whitelist_ip':
            $ip = $_GET['ip'] ?? $_POST['ip'] ?? '';
            $comment = $_GET['comment'] ?? $_POST['comment'] ?? 'Añadido desde WHM Report';
            if (empty($ip)) {
                echo json_encode(['error' => true, 'message' => 'IP requerida']);
                break;
            }
            $result = $whm->whitelistIP($ip, $comment);
            echo json_encode($result);
            break;

        case 'remove_whitelist_ip':
            $ip = $_GET['ip'] ?? $_POST['ip'] ?? '';
            if (empty($ip)) {
                echo json_encode(['error' => true, 'message' => 'IP requerida']);
                break;
            }
            $result = $whm->removeWhitelistIP($ip);
            echo json_encode($result);
            break;

        case 'toggle_email_login':
            $user  = $_GET['user'] ?? '';
            $email = $_GET['email'] ?? '';
            $act   = $_GET['act'] ?? 'suspend'; // suspend | unsuspend
            if (empty($user) || empty($email)) {
                echo json_encode(['error' => true, 'message' => 'Parámetros requeridos']);
                break;
            }
            $result = ($act === 'unsuspend')
                ? $whm->unsuspendEmailLogin($user, $email)
                : $whm->suspendEmailLogin($user, $email);
            echo json_encode($result);
            break;

        case 'toggle_email_incoming':
            $user  = $_GET['user'] ?? '';
            $email = $_GET['email'] ?? '';
            $act   = $_GET['act'] ?? 'suspend';
            if (empty($user) || empty($email)) {
                echo json_encode(['error' => true, 'message' => 'Parámetros requeridos']);
                break;
            }
            $result = ($act === 'unsuspend')
                ? $whm->unsuspendEmailIncoming($user, $email)
                : $whm->suspendEmailIncoming($user, $email);
            echo json_encode($result);
            break;
            
        case 'email_log':
            $email = $_GET['email'] ?? '';
            $user  = $_GET['user'] ?? '';
            if (empty($email)) {
                echo json_encode(['error' => true, 'message' => 'Email requerido']);
                break;
            }
            $result = $whm->getEmailSuspendLog($email, $user);
            echo json_encode($result);
            break;

        case 'create_email':
            $user      = $_POST['user']      ?? '';
            $localpart = $_POST['localpart'] ?? '';
            $domain    = $_POST['domain']    ?? '';
            $password  = $_POST['password']  ?? '';
            $quota     = $_POST['quota']     ?? 0;
            if (empty($user) || empty($localpart) || empty($domain) || empty($password)) {
                echo json_encode(['error' => true, 'message' => 'Faltan parámetros requeridos']);
                break;
            }
            $result = $whm->createEmailAccount($user, $localpart, $domain, $password, $quota);
            echo json_encode($result);
            break;

        case 'change_email_password':
            $user      = $_POST['user']      ?? '';
            $localpart = $_POST['localpart'] ?? '';
            $domain    = $_POST['domain']    ?? '';
            $password  = $_POST['password']  ?? '';
            if (empty($user) || empty($localpart) || empty($domain) || empty($password)) {
                echo json_encode(['error' => true, 'message' => 'Faltan parámetros requeridos']);
                break;
            }
            $result = $whm->changeEmailPassword($user, $localpart, $domain, $password);
            echo json_encode($result);
            break;

        case 'edit_email_quota':
            $user      = $_POST['user']      ?? '';
            $localpart = $_POST['localpart'] ?? '';
            $domain    = $_POST['domain']    ?? '';
            $quota     = $_POST['quota']     ?? 0;
            if (empty($user) || empty($localpart) || empty($domain)) {
                echo json_encode(['error' => true, 'message' => 'Faltan parámetros']);
                break;
            }
            $result = $whm->editEmailQuota($user, $localpart, $domain, $quota);
            echo json_encode($result);
            break;

        case 'delete_email':
            $user      = $_POST['user']      ?? '';
            $localpart = $_POST['localpart'] ?? '';
            $domain    = $_POST['domain']    ?? '';
            if (empty($user) || empty($localpart) || empty($domain)) {
                echo json_encode(['error' => true, 'message' => 'Faltan parámetros']);
                break;
            }
            $result = $whm->deleteEmailAccount($user, $localpart, $domain);
            echo json_encode($result);
            break;

        case 'list_forwarders':
            $user  = $_GET['user']  ?? '';
            $email = $_GET['email'] ?? '';
            if (empty($user) || empty($email)) { echo json_encode(['error' => true, 'message' => 'Faltan parámetros']); break; }
            $result = $whm->listForwarders($user, $email);
            echo json_encode(['forwarders' => $result]);
            break;

        case 'add_forwarder':
            $user      = $_POST['user']      ?? '';
            $localpart = $_POST['localpart'] ?? '';
            $domain    = $_POST['domain']    ?? '';
            $fwdest    = $_POST['fwdest']    ?? '';
            if (empty($user) || empty($localpart) || empty($domain) || empty($fwdest)) {
                echo json_encode(['error' => true, 'message' => 'Faltan parámetros']); break;
            }
            $result = $whm->addForwarder($user, $localpart, $domain, $fwdest);
            echo json_encode($result);
            break;

        case 'delete_forwarder':
            $user    = $_POST['user']    ?? '';
            $email   = $_POST['email']   ?? '';
            $fwdest  = $_POST['fwdest']  ?? '';
            if (empty($user) || empty($email) || empty($fwdest)) {
                echo json_encode(['error' => true, 'message' => 'Faltan parámetros']); break;
            }
            $result = $whm->deleteForwarder($user, $email, $fwdest);
            echo json_encode($result);
            break;

        // =====================================================
        // RESELLER MANAGEMENT
        // =====================================================

        case 'list_resellers':
            echo json_encode(['resellers' => $whm->getResellerStats()]);
            break;

        case 'create_reseller':
            $user = $_POST['user'] ?? '';
            if (empty($user)) { echo json_encode(['error' => true, 'message' => 'Usuario requerido']); break; }
            echo json_encode($whm->createReseller($user, true));
            break;

        case 'remove_reseller':
            $reseller = $_POST['reseller'] ?? '';
            if (empty($reseller)) { echo json_encode(['error' => true, 'message' => 'Reseller requerido']); break; }
            echo json_encode($whm->removeReseller($reseller));
            break;

        case 'suspend_reseller':
            $reseller = $_POST['reseller'] ?? '';
            $reason   = $_POST['reason']   ?? '';
            if (empty($reseller)) { echo json_encode(['error' => true, 'message' => 'Reseller requerido']); break; }
            echo json_encode($whm->suspendReseller($reseller, $reason));
            break;

        case 'unsuspend_reseller':
            $reseller = $_POST['reseller'] ?? '';
            if (empty($reseller)) { echo json_encode(['error' => true, 'message' => 'Reseller requerido']); break; }
            echo json_encode($whm->unsuspendReseller($reseller));
            break;

        case 'terminate_reseller':
            $reseller = $_POST['reseller'] ?? '';
            if (empty($reseller)) { echo json_encode(['error' => true, 'message' => 'Reseller requerido']); break; }
            echo json_encode($whm->terminateReseller($reseller, true));
            break;

        case 'set_reseller_limits':
            $reseller = $_POST['reseller'] ?? '';
            if (empty($reseller)) { echo json_encode(['error' => true, 'message' => 'Reseller requerido']); break; }
            $limits = [
                'diskquota' => intval($_POST['diskquota'] ?? 0),
                'bwlimit'   => intval($_POST['bwlimit']   ?? 0),
                'maxacct'   => intval($_POST['maxacct']   ?? 0),
            ];
            echo json_encode($whm->setResellerLimits($reseller, $limits));
            break;

        case 'list_acls':
            echo json_encode(['acls' => $whm->listACLs()]);
            break;

        case 'get_reseller_acls':
            $reseller = $_GET['reseller'] ?? '';
            if (empty($reseller)) { echo json_encode(['error' => true, 'message' => 'Reseller requerido']); break; }
            echo json_encode(['acls' => $whm->getResellerACLs($reseller)]);
            break;

        case 'set_reseller_acls':
            $reseller = $_POST['reseller'] ?? '';
            if (empty($reseller)) { echo json_encode(['error' => true, 'message' => 'Reseller requerido']); break; }
            $aclsRaw = $_POST['acls'] ?? '{}';
            $acls = json_decode($aclsRaw, true) ?? [];
            echo json_encode($whm->setResellerACLs($reseller, $acls));
            break;

        case 'reassign_account':
            $user     = $_POST['user']     ?? '';
            $newOwner = $_POST['new_owner'] ?? '';
            if (empty($user) || empty($newOwner)) { echo json_encode(['error' => true, 'message' => 'Usuario y nuevo owner requeridos']); break; }
            echo json_encode($whm->reassignAccount($user, $newOwner));
            break;

        case 'get_reseller_ips':
            $reseller = $_GET['reseller'] ?? '';
            if (empty($reseller)) { echo json_encode(['error' => true, 'message' => 'Reseller requerido']); break; }
            echo json_encode(['ips' => $whm->getResellerIPs($reseller)]);
            break;

        default:
            echo json_encode(['error' => true, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
