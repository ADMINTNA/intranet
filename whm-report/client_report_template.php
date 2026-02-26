<?php
/**
 * Plantilla de Reporte para Cliente (Perfil No IT)
 * Genera el detalle de la cuenta con formateo avanzado (GB/MB, separadores).
 */

require_once __DIR__ . '/includes/WhmApi.php';

$userParam = $_GET['user'] ?? '';
if (empty($userParam)) {
    die("Error: No se recibió el nombre de usuario.");
}

$whm = new WhmApi();
// Obtenemos el reporte completo para extraer de ahí la data consolidada de la cuenta
$report = $whm->getFullReport();

$accountData = null;
if (isset($report['accounts']) && is_array($report['accounts'])) {
    foreach ($report['accounts'] as $acc) {
        if ($acc['user'] === $userParam) {
            $accountData = $acc;
            break;
        }
    }
}

if (!$accountData) {
    die("Error: No se encontró información para el usuario '{$userParam}'.");
}

function formatBytesCustom($bytes) {
    if ($bytes === 'unlimited' || $bytes === null || $bytes === 'Ilimitado') return 'Ilimitado';
    if (!is_numeric($bytes) || $bytes <= 0) return "0,00 MB";
    
    $mb = $bytes / (1024 * 1024);
    
    if ($mb >= 1024 * 1024) {
        $val = $mb / (1024 * 1024);
        $unit = "TB";
    } elseif ($mb >= 1024) {
        $val = $mb / 1024;
        $unit = "GB";
    } else {
        $val = $mb;
        $unit = "MB";
    }
    
    return number_format($val, 2, ',', '.') . ' ' . $unit;
}

$clientDomain = $accountData['domain'];
$clientPlan = $accountData['plan'];

$diskUsedBytes = $accountData['disk_used'];
$diskLimitBytes = $accountData['disk_limit'];
$diskPercent = $accountData['disk_percent'] ?? 0;

$emailCount = $accountData['email_count'];
$emailLimit = $accountData['email_limit'];

$inodesUsed = $accountData['inodes_used'] ?? 0;
// Tiered absolute limits (TNA Policy)
$isManagerial = ($diskLimitBytes > 4 * 1024 * 1024 * 1024) || ($diskLimitBytes == 0);
$inodeYellow = $isManagerial ? 100000 : 50000;
$inodeOrange = $isManagerial ? 150000 : 80000;
$inodeRed    = $isManagerial ? 200000 : 120000;

$inodeStatusClass = 'status-healthy';
if ($inodesUsed >= $inodeRed) $inodeStatusClass = 'status-critical';
elseif ($inodesUsed >= $inodeOrange) $inodeStatusClass = 'status-warning';
elseif ($inodesUsed >= $inodeYellow) $inodeStatusClass = 'status-warning';

$emailPercent = 0;
if ($emailLimit > 0 && is_numeric($emailLimit)) {
    $emailPercent = min(100, round(($emailCount / $emailLimit) * 100, 2));
} elseif (strtolower($emailLimit) == 'unlimited') {
    $emailLimit = 'Ilimitadas';
}

$bwUsedBytes = $accountData['bw_used'];
$bwLimitBytes = $accountData['bw_limit'];
$bwPercent = 0;
if ($bwLimitBytes > 0) {
    $bwPercent = min(100, round(($bwUsedBytes / $bwLimitBytes) * 100, 2));
}

$isSuspended = $accountData['suspended'] ?? false;
$suspendReason = $accountData['suspend_reason'] ?? '';

// Obtener detalles de casillas de correo
$emails = $whm->getEmailAccounts($userParam);
if (!is_array($emails)) {
    $emails = [];
}

$totalEmailDiskBytes = 0;
foreach ($emails as $email) {
    $totalEmailDiskBytes += floatval($email['_diskused'] ?? 0);
}

// Ordenar emails de mayor a menor uso (capacidad)
usort($emails, function($a, $b) {
    return floatval($b['_diskused'] ?? 0) <=> floatval($a['_diskused'] ?? 0);
});

$isHealthy = !$isSuspended && $diskPercent < 80 && $emailPercent < 80 && $bwPercent < 80;

$maxMailboxBytes = 0;
foreach ($emails as $email) {
    $ebytes = floatval($email['_diskused'] ?? 0);
    if ($ebytes > $maxMailboxBytes) $maxMailboxBytes = $ebytes;
}

$isCriticalMigration = !$isSuspended && ($diskPercent >= 80 || $maxMailboxBytes >= 15 * 1024 * 1024 * 1024 || $inodesUsed >= $inodeRed);

// Alertas de Mantenimiento
$suspendedEmails = [];
$inactiveEmails = [];
$now = time();
foreach ($emails as $email) {
    if (isset($email['suspended_login']) && $email['suspended_login'] == 1) $suspendedEmails[] = $email['email'];
    if (isset($email['suspended_incoming']) && $email['suspended_incoming'] == 1) $suspendedEmails[] = $email['email'];
    
    $mtime = intval($email['mtime'] ?? 0);
    if ($mtime > 0 && ($now - $mtime) > (90 * 86400)) {
        $inactiveEmails[] = $email['email'];
    }
}
$suspendedEmailsCount = count(array_unique($suspendedEmails));
$inactiveEmailsCount = count($inactiveEmails);
$noTraffic = ($bwUsedBytes <= 0);

$hasMaintenanceAlerts = $suspendedEmailsCount > 0 || $inactiveEmailsCount > 0 || $noTraffic;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Estado: Servicio de Correos - <?php echo htmlspecialchars($clientDomain); ?></title>
    <!-- Usando Tailwind CSS para un diseño limpio y moderno -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .card { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); border: 1px solid #e2e8f0; }
        .progress-bar-bg { background-color: #e2e8f0; border-radius: 9999px; overflow: hidden; height: 12px; }
        .progress-bar-fill { height: 100%; border-radius: 9999px; transition: width 0.5s ease; }
        .status-healthy { background-color: #10b981; } /* Verde */
        .status-warning { background-color: #f59e0b; } /* Amarillo */
        .status-critical { background-color: #ef4444; } /* Rojo */
        
        /* Reglas para la tabla de emails */
        .table-emails th, .table-emails td { padding: 8px 10px; font-size: 13px; line-height: 1.25; }
        .break-email { word-break: break-all; min-width: 150px; }
        
        @media (max-width: 640px) {
            .table-emails th, .table-emails td { font-size: 11px; padding: 6px 4px; }
            .break-email { min-width: 100px; }
        }
        
        <?php
            function getStatusColor($percent) {
                if ($percent >= 90) return 'status-critical';
                if ($percent >= 75) return 'status-warning';
                return 'status-healthy';
            }
        ?>
    </style>
</head>
<body class="text-slate-800 p-4 md:p-8">

    <div class="max-w-4xl mx-auto">
        <!-- Encabezado -->
        <header class="text-center mb-8 mt-2">
            <img src="assets/img/header.jpeg" alt="Header IConTel" class="mx-auto block w-full max-w-4xl h-auto mb-4" />
            <h1 class="text-2xl font-extrabold text-slate-800 uppercase tracking-tight">Reporte de Estado: Servicio de Correos</h1>
            <p class="text-sm text-slate-400 mt-1 font-medium">Generado el: <?php echo date('d/m/Y H:i'); ?></p>
        </header>

        <!-- Información del Cliente -->
        <div class="card p-6 mb-6 flex flex-col md:flex-row justify-between items-center border-l-4 <?php echo $isSuspended ? 'border-l-red-600' : 'border-l-blue-600'; ?>">
            <div>
                <p class="text-sm text-slate-500 uppercase tracking-wider font-semibold">Dominio Principal</p>
                <p class="text-2xl font-bold text-slate-800"><?php echo htmlspecialchars($clientDomain); ?></p>
            </div>
            <div class="mt-4 md:mt-0 text-center md:text-right">
                <p class="text-sm text-slate-500 uppercase tracking-wider font-semibold">Plan Contratado</p>
                <p class="text-xl font-medium text-slate-700"><?php echo htmlspecialchars($clientPlan); ?></p>
            </div>
            <div class="mt-4 md:mt-0 text-center md:text-right">
                <p class="text-sm text-slate-500 uppercase tracking-wider font-semibold">Estado de Cuenta</p>
                <?php if ($isSuspended): ?>
                    <p class="text-xl font-bold text-red-600">Suspendida</p>
                    <p class="text-xs text-red-500 mt-1"><?php echo htmlspecialchars($suspendReason); ?></p>
                <?php else: ?>
                    <p class="text-xl font-bold text-green-600">Activa</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Mensaje de Estado General -->
        <?php if ($isSuspended): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-5 mb-8 flex items-start">
            <svg class="w-6 h-6 text-red-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>
                <h3 class="font-bold text-lg">Cuenta Suspendida</h3>
                <p class="text-red-700 mt-1">Tu cuenta de alojamiento se encuentra actualmente suspendida. Por favor contáctanos lo antes posible.</p>
            </div>
        </div>
        <?php elseif ($isCriticalMigration): ?>
        <div class="bg-orange-50 border border-orange-200 text-orange-800 rounded-lg p-5 mb-8 flex items-start">
            <svg class="w-6 h-6 text-orange-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>
                <h3 class="font-bold text-lg">Alerta de Migración Sugerida</h3>
                    <div style="margin-top: 10px;">
                        <p class="text-orange-800 font-medium">
                            <strong>Motivo:</strong> Su cuenta ha superado el 80% de su capacidad total, el uso de disco por casilla es mayor a 15 GB, o el volumen de archivos (inodos) ha alcanzado un nivel crítico para el servidor.
                        </p>
                        <p style="margin-top:8px; font-weight:normal; font-style:italic; color: #9a3412;">
                            Esta medida preventiva busca garantizar la integridad de sus datos y evitar bloqueos en el flujo de correos por falta de espacio.
                        </p>
                        <p class="text-orange-800 mt-4 leading-relaxed">
                            "Hemos detectado que el éxito de su negocio requiere una infraestructura de correo a nivel empresarial, ya que actualmente están consumiendo casi la totalidad de su espacio de alojamiento y un alto volumen de archivos (inodes). Les recomendamos mantener su sitio web en su plan actual (mucho más económico y liviano) y migrar la plataforma de correo a una plataforma de correos profesional tipo Google Workspace o Microsoft 365 para garantizar su continuidad operativa y correos seguros."
                        </p>
                    </div>
            </div>
        </div>
        <?php elseif ($isHealthy): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-5 mb-8 flex items-start">
            <svg class="w-6 h-6 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <h3 class="font-bold text-lg">¡Tu cuenta está en excelente estado!</h3>
                <p class="text-green-700 mt-1">Actualmente la cuenta está activa y tienes recursos suficientes para que tu sitio web y correos funcionen sin problemas.</p>
            </div>
        </div>
        <?php else: ?>
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg p-5 mb-8 flex items-start">
            <svg class="w-6 h-6 text-yellow-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>
                <h3 class="font-bold text-lg">Atención: Algunos recursos están altos</h3>
                <p class="text-yellow-700 mt-1">Te recomendamos revisar los indicadores marcados en naranja o rojo. Si están muy llenos, podrías dejar de recibir correos o tu página web podría detenerse.</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Alertas de Mantenimiento / Optimización -->
        <?php if ($hasMaintenanceAlerts && !$isSuspended): ?>
        <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-5 mb-8">
            <div class="flex items-start mb-2">
                <svg class="w-6 h-6 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="font-bold text-lg">Sugerencias de Optimización</h3>
            </div>
            <ul class="ml-9 list-disc text-blue-700 space-y-1 text-sm">
                <?php if ($suspendedEmailsCount > 0): ?>
                    <li><strong>Casillas Suspendidas:</strong> Se detectaron <?php echo $suspendedEmailsCount; ?> cuenta(s) con restricciones de acceso o entrada.</li>
                <?php endif; ?>
                <?php if ($inactiveEmailsCount > 0): ?>
                    <li><strong>Cuentas sin Uso (+90 días):</strong> Hay <?php echo $inactiveEmailsCount; ?> casilla(s) que no registran actividad reciente. Podría eliminarlas para ahorrar espacio.</li>
                <?php endif; ?>
                <?php if ($noTraffic): ?>
                    <li><strong>Sin Tráfico Web:</strong> No se registra consumo de ancho de banda este mes. Verifique que su sitio web sea visible o si requiere mayor difusión.</li>
                <?php endif; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            
            <!-- Tarjeta 1: Espacio en Disco -->
            <div class="card p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 p-3 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Espacio en Disco</h2>
                        <p class="text-sm text-slate-500">Archivos, fotos y correos almacenados.</p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <div class="flex justify-between text-sm font-medium mb-1.5">
                        <span class="text-slate-700"><?php echo formatBytesCustom($diskUsedBytes); ?> usados</span>
                        <span class="text-slate-500">De <?php echo formatBytesCustom($diskLimitBytes); ?></span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill <?php echo getStatusColor($diskPercent); ?>" style="width: <?php echo $diskPercent; ?>%"></div>
                    </div>
                    <p class="text-right text-xs font-bold mt-1 text-slate-400"><?php echo $diskPercent; ?>% Lleno</p>
                </div>

                <?php if ($diskPercent >= 80): ?>
                    <p class="mt-3 text-sm text-red-600 font-medium bg-red-50 p-2 rounded border border-red-100">
                        ⚠️ Te estás quedando sin espacio. Considera borrar correos antiguos con archivos pesados o contáctanos para ampliar tu plan.
                    </p>
                <?php else: ?>
                    <p class="mt-3 text-sm text-slate-500 bg-slate-50 p-2 rounded">
                        💡 Tienes espacio suficiente. Aquí se guarda toda la información de tu web y bandeja de entrada.
                    </p>
                <?php endif; ?>
            </div>

            <!-- Tarjeta 2: Cuentas de Correo -->
            <div class="card p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-purple-100 p-3 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Cuentas de Correo</h2>
                        <p class="text-sm text-slate-500">Direcciones @tu-empresa.cl creadas.</p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <div class="flex justify-between text-sm font-medium mb-1.5">
                        <span class="text-slate-700"><?php echo $emailCount; ?> Cuentas en uso</span>
                        <span class="text-slate-500">Límite: <?php echo $emailLimit; ?></span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill <?php echo getStatusColor($emailPercent); ?>" style="width: <?php echo $emailPercent; ?>%"></div>
                    </div>
                    <p class="text-right text-xs font-bold mt-1 text-slate-400"><?php echo $emailPercent; ?>% Utilizado</p>
                </div>

                <?php if ($emailCount <= 50 && ($emailLimit - $emailCount) > 0): ?>
                <p class="mt-3 text-sm text-slate-500 bg-slate-50 p-2 rounded">
                    💡 Aún puedes crear <?php echo ($emailLimit - $emailCount); ?> cuentas de correo adicionales para tu equipo de trabajo.
                </p>
                <?php endif; ?>
            </div>

            <!-- Tarjeta 3: Uso de Archivos (Inodos) -->
            <div class="card p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-orange-100 p-3 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Uso de Archivos</h2>
                        <p class="text-sm text-slate-500">Cantidad de elementos en su cuenta.</p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <div class="flex justify-between text-sm font-medium mb-1.5">
                        <span class="text-slate-700"><?php echo number_format($inodesUsed, 0, ',', '.'); ?> archivos</span>
                        <span class="text-slate-500">Límite Sugerido: <?php echo number_format($inodeOrange, 0, ',', '.'); ?></span>
                    </div>
                    <?php 
                        $inodePct = min(100, round(($inodesUsed / $inodeRed) * 100, 2));
                    ?>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill <?php echo $inodeStatusClass; ?>" style="width: <?php echo $inodePct; ?>%"></div>
                    </div>
                    <p class="text-right text-xs font-bold mt-1 text-slate-400"><?php echo $isManagerial ? 'Cuenta Gerencial' : 'Cuenta Estándar'; ?></p>
                </div>

                <?php if ($inodesUsed >= $inodeRed): ?>
                    <p class="mt-3 text-sm text-red-700 font-bold bg-red-100 p-3 rounded border border-red-200">
                        🚨 ALERTA CRÍTICA: Su cuenta tiene un volumen de archivos (<?php echo number_format($inodesUsed, 0, ',', '.'); ?>) que excede drásticamente los límites recomendados. 
                        <br><br>
                        Esto suele ocurrir por tener decenas de miles de correos de hace muchos años o archivos de sistema innecesarios. Un alto número de inodos ralentiza los respaldos y puede causar pérdida de correos. **Es urgente realizar una limpieza o migrar a una plataforma profesional.**
                    </p>
                <?php elseif ($inodesUsed >= $inodeOrange): ?>
                    <p class="mt-3 text-sm text-red-600 font-medium bg-red-50 p-2 rounded border border-red-100">
                        ⚠️ Su cuenta tiene demasiados archivos (inodos). Esto ocurre por acumulación de correos muy antiguos o caché. Le recomendamos una limpieza para garantizar la integridad y velocidad de su servicio.
                    </p>
                <?php else: ?>
                    <p class="mt-3 text-sm text-slate-500 bg-slate-50 p-2 rounded">
                        💡 Todo bien. Un bajo número de archivos garantiza que sus correos lleguen y se abran de forma instantánea.
                    </p>
                <?php endif; ?>
            </div>

            <!-- Tarjeta 4: Tráfico (Ancho de Banda) -->
            <div class="card p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-indigo-100 p-3 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Visitas y Tráfico Web (Ancho de Banda)</h2>
                        <p class="text-sm text-slate-500">Datos consumidos cada vez que alguien entra a tu web o te envían un correo.</p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <div class="flex justify-between text-sm font-medium mb-1.5">
                        <span class="text-slate-700"><?php echo formatBytesCustom($bwUsedBytes); ?> transferidos este mes</span>
                        <span class="text-slate-500">Plan: <?php echo formatBytesCustom($bwLimitBytes); ?></span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill <?php echo getStatusColor($bwPercent); ?>" style="width: <?php echo $bwPercent; ?>%"></div>
                    </div>
                    <p class="text-right text-xs font-bold mt-1 text-slate-400"><?php echo $bwPercent; ?>% Consumido</p>
                </div>

                 <p class="mt-3 text-sm text-slate-500 bg-slate-50 p-2 rounded">
                    💡 Este valor se reinicia a cero al inicio de cada mes. Si llega al 100%, tu página podría mostrar un error de "Bandwidth Limit Exceeded".
                </p>
            </div>
        </div>

        <!-- Detalle de Cuentas de Correo -->
        <div class="card py-6 mb-8 overflow-hidden">
            <div class="flex items-center mb-6 border-b pb-4 px-6">
                <div class="bg-indigo-100 p-3 rounded-lg mr-4">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Desglose de Casillas de Correo</h2>
                    <p class="text-sm text-slate-500">Listado de direcciones de email, cuánto espacio consumen y su nivel de uso y actividad.</p>
                </div>
            </div>

            <?php if (count($emails) > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-emails">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                            <th class="p-3 border-b text-center">#</th>
                            <th class="p-3 border-b">Casilla (Email)</th>
                            <th class="p-3 border-b">Uso Disco</th>
                            <th class="p-3 border-b">% Uso</th>
                            <th class="p-3 border-b">Último Acceso</th>
                            <th class="p-3 border-b text-center">Login</th>
                            <th class="p-3 border-b text-center">Entrada</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php 
                            $idx = 1;
                            foreach ($emails as $em): 
                            $emUsed = floatval($em['_diskused'] ?? 0);
                            $pctTotal = $totalEmailDiskBytes > 0 ? min(100, round(($emUsed / $totalEmailDiskBytes) * 100, 2)) : 0;
                            
                            $mtime = intval($em['mtime'] ?? 0);
                            $daysAgo = $mtime > 0 ? floor((time() - $mtime) / (24 * 3600)) : null;
                            $lastLoginStr = $mtime > 0 ? date('d/m/Y H:i', $mtime) : 'Sin Registro';
                            $loginColorClass = ($daysAgo !== null && $daysAgo >= 60) ? 'text-red-600 font-bold' : 'text-slate-500';

                            $loginSusp = intval($em['suspended_login'] ?? 0) === 1;
                            $incSusp = intval($em['suspended_incoming'] ?? 0) === 1;
                        ?>
                        <tr class="border-b last:border-0 hover:bg-slate-50">
                            <td class="p-3 text-center text-slate-400 font-mono text-xs"><?php echo $idx++; ?></td>
                            <td class="p-3 font-medium text-slate-800 break-email">
                                <?php 
                                    $emailName = $em['email'] ?? '';
                                    if (strpos($emailName, '@') === false) {
                                        $emailName .= '@' . ($em['domain'] ?? '');
                                    }
                                    echo htmlspecialchars($emailName); 
                                ?>
                            </td>
                            <td class="p-3 text-slate-600"><?php echo formatBytesCustom($emUsed); ?></td>
                            <td class="p-3 text-slate-600">
                                <div class="flex items-center">
                                    <span class="mr-2 w-10 text-right"><?php echo number_format($pctTotal, 2, ',', '.'); ?>%</span>
                                    <div class="w-16 bg-slate-200 rounded-full h-1.5 hidden md:block">
                                        <div class="bg-blue-500 rounded-full h-1.5" style="width: <?php echo $pctTotal; ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3 <?php echo $loginColorClass; ?>"><?php echo $lastLoginStr; ?></td>
                            <td class="p-3 text-center">
                                <?php if ($loginSusp): ?>
                                    <span class="px-1.5 py-0.5 bg-red-100 text-red-700 rounded text-[10px] font-bold uppercase">OFF</span>
                                <?php else: ?>
                                    <span class="px-1.5 py-0.5 bg-green-100 text-green-700 rounded text-[10px] font-bold uppercase">ON</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3 text-center">
                                <?php if ($incSusp): ?>
                                    <span class="px-1.5 py-0.5 bg-red-100 text-red-700 rounded text-[10px] font-bold uppercase">OFF</span>
                                <?php else: ?>
                                    <span class="px-1.5 py-0.5 bg-green-100 text-green-700 rounded text-[10px] font-bold uppercase">ON</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="p-4 text-center text-slate-500 bg-slate-50 rounded">
                No hay casillas de correo creadas en esta cuenta.
            </div>
            <?php endif; ?>
        </div>

        <!-- Glosario Simplificado -->
        <div class="card p-6 mt-4 opacity-90">
            <h3 class="text-lg font-bold text-slate-800 mb-3 border-b pb-2">Glosario Rápido</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-slate-600">
                <div>
                    <strong class="text-slate-800 block mb-1">Disco (Almacenamiento)</strong>
                    Es como el "disco duro" de tu cuenta. Aquí vivien los archivos de tu página, las fotos que subes y todos los correos electrónicos que recibes y no borras.
                </div>
                <div>
                    <strong class="text-slate-800 block mb-1">Tráfico (Ancho de Banda)</strong>
                    Es la carretera de tu sitio. Cada vez que alguien entra a tu página, "descarga" los datos (imágenes, textos) a su celular o PC. Eso consume tu tráfico.
                </div>
                <div>
                    <strong class="text-slate-800 block mb-1">Límites</strong>
                    Si alcanzas el tope de "Disco", no entrarán más correos nuevos. Si alcanzas el tope de "Tráfico", la página web dejará de verse hasta el próximo mes.
                </div>
            </div>
        </div>

        <footer class="text-center mt-10 mb-2">
            <img src="assets/img/footer.jpg" alt="Footer IConTel" class="mx-auto block w-full max-w-3xl h-auto" />
        </footer>
    </div>

</body>
</html>
