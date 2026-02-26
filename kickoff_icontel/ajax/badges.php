<?php
// ==========================================================
// KickOff AJAX – Endpoint de badges del menú
// /kickoff_icontel/ajax/badges.php
// Devuelve JSON con contadores para todos los módulos.
// Caché en sesión: 3 minutos (180s).
// Autor: mAo
// ==========================================================
mb_internal_encoding("UTF-8");
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../ajax_bootstrap.php';

// ── Caché de sesión ───────────────────────────────────────────
$CACHE_TTL = 180; // 3 minutos
$cache_key = 'badges_cache_' . md5($sg_id);

if (
    !empty($_GET['force']) ||
    empty($_SESSION[$cache_key]) ||
    empty($_SESSION[$cache_key . '_ts']) ||
    (time() - $_SESSION[$cache_key . '_ts']) > $CACHE_TTL
) {
    // ── Calcular badges frescos ───────────────────────────────
    $ventas      = "/ Ventas / -..Ghislaine / -..MAO / -..Natalia / -..Monica / -..Raquel";
    $operaciones = "/ -..Bryan / Operaciones / -..Alex /";
    $sac         = "/ Servicio al Cliente / -..Maria José / -..DAM /";
    $admin       = "/ -..MAM / -..MAO ";
    $proveedores = "/ -..MAO";
    $mao_mam     = "/ -..MAO / -..MAM";

    $b = [
        'casos'          => 0,
        'sujeto_cobro'   => 0,
        'traslados'      => 0,
        'casos_baja'     => 0,
        'congelados'     => 0,
        'seguimiento'    => 0,
        'cobranza'       => 0,
        'potenciales'    => 0,
        'tareas'         => 0,
        'delegadas'      => 0,
        'notas'          => 0,
        'oportunidades'  => 0,
        'demo'           => 0,
        'archivadas'     => 0,
        'oc_pendientes'  => 0,
    ];

    if (function_exists('DbConnect')) {
        $conn = DbConnect("tnasolut_sweet");
        if ($conn) {
            $run = function($sql) use ($conn) {
                while ($conn->more_results()) $conn->next_result();
                $res = $conn->query($sql);
                if (!$res) return 0;
                $n = $res->num_rows;
                $res->free();
                return $n;
            };
            $runVal = function($sql) use ($conn) {
                while ($conn->more_results()) $conn->next_result();
                $res = $conn->query($sql);
                if (!$res) return 0;
                $row = $res->fetch_assoc();
                $res->free();
                return (int)($row['total'] ?? 0);
            };

            $sg_esc = $conn->real_escape_string($sg_id);

            $b['casos']    = $run("CALL Kick_Off_Operaciones_Abiertos('$sg_esc')");
            $b['tareas']   = $run("CALL Kick_Off_Operaciones_Tareas_Abiertas('$sg_esc')");
            $b['delegadas']= $run("CALL Kick_Off_Tareas_Abiertas_Creadas('$sg_esc')");
            $b['notas']    = $run("CALL cm_notas_abiertas('$sg_esc')");

            if (strpos($proveedores, $sg_name) !== false)
                $b['sujeto_cobro'] = $run("CALL Kick_Off_Operaciones_Abiertos_sujeto_a_cobro()");

            if (strpos($mao_mam, $sg_name) !== false)
                $b['traslados'] = $run("CALL CM_Cotizaciones_baja_traslado()");

            if (strpos($admin, $sg_name) !== false)
                $b['casos_baja'] = $run("CALL Kick_Off_Casos_Abiertos_de_baja()");

            if (strpos($sac, $sg_name) !== false) {
                $b['congelados']  = $run("CALL CM_Casos_Abiertos_Congelados('$sg_esc')");
                $b['seguimiento'] = $run("CALL CM_Casos_Abiertos_Seguimiento('$sg_esc')");
            }

            if (strpos($ventas, $sg_name) !== false || strpos($admin, $sg_name) !== false)
                $b['cobranza'] = $runVal("
                    SELECT COUNT(*) AS total FROM accounts ac
                    JOIN accounts_cstm acc ON acc.id_c = ac.id
                    WHERE acc.estatusfinanciero_c IN (
                        'cobranza_comercial','acuerdo_cobranza_comer','suspender',
                        'Suspendido','retencion_posible_baja'
                    )");

            if (strpos($ventas, $sg_name) !== false)
                $b['potenciales'] = $run("CALL Clientes_Potenciales_Pendientes()");

            if ($sg_name !== "Soporte tecnico")
                $b['oportunidades'] = $run("CALL Oportunidades_Pendientes('$sg_esc')");

            if (strpos($ventas . $operaciones, $sg_name) !== false)
                $b['demo'] = $run("CALL Oportunidades_en_Demo()");

            if (strpos($ventas, $sg_name) !== false && $sg_name !== "-..MAO")
                $b['archivadas'] = $run("CALL Oportunidades_en_Pausa()");

            if (strpos($admin, $sg_name) !== false)
                $b['oc_pendientes'] = $run("CALL CM_Ordenes_de_Compra_Pendientes()");

            $conn->close();
        }
    }

    // Guardar en caché de sesión
    session_start();
    $_SESSION[$cache_key]        = $b;
    $_SESSION[$cache_key . '_ts'] = time();
    session_write_close();

} else {
    // Devolver desde caché
    $b = $_SESSION[$cache_key];
}

echo json_encode(['success' => true, 'badges' => $b, 'cached' => !empty($_SESSION[$cache_key . '_ts'])]);