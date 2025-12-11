<?php
// ==========================================================
// /intranet/kickoff/index.php
// KickOff con lengüetas SOLO para Admin
// Redirección fuera del iframe en caso de sesión expirada
// Autor: mAo
// Fecha: 2025-11-22
// Codificación: UTF-8 sin BOM
// ==========================================================

// Forzar UTF-8
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding("UTF-8");

// Leer sesión creada en intranet/index.php
session_name('icontel_intranet_sess');
session_start();

// ----------------------------------------------------------
// SI SESIÓN NO EXISTE → REDIRIGIR AL MARCO PRINCIPAL
// ----------------------------------------------------------
if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo "<script>
        window.top.location.href = 'https://intranet.icontel.cl/index.php?error=session';
    </script>";
    exit;
}

$ROL = $_SESSION['rol'] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>KickOff – Panel</title>

<!-- Script global para salir siempre al marco principal -->
<script>
function salirIntranet() {
    window.top.location.href = "https://intranet.icontel.cl/index.php";
}
</script>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        background: #f7f7f7;
    }

    /* -------------------------------------------------- */
    /* ESTILOS SOLO PARA ADMIN (con lengüetas)            */
    /* -------------------------------------------------- */
    <?php if ($ROL === "Admin"): ?>
    #tabs {
        display: flex;
        background: #1F1D3E;
        color: white;
        height: 28px;
        align-items: center;
        padding-left: 10px;
    }

    .tab {
        padding: 5px 20px;
        margin-right: 4px;
        cursor: pointer;
        background: #2A285C;
        border-top-left-radius: 6px;
        border-top-right-radius: 6px;
        font-size: 12px;
        user-select: none;
        transition: 0.2s;
    }

    .tab:hover {
        background: #3B3980;
    }

    .tab.active {
        background: #ffffff;
        color: #1F1D3E;
        font-weight: bold;
        border-bottom: 2px solid white;
    }

    .capa {
        display: none;
        height: calc(100vh - 28px);
        overflow: auto;
        background: #fff;
        border-top: 2px solid #ddd;
    }

    .capa.active {
        display: block;
    }

    iframe {
        width: 100%;
        height: 100%;
        border: none;
    }
    <?php endif; ?>
</style>

<?php if ($ROL === "Admin"): ?>
<script>
// ----------------------------------------------------------
// Cambiar capa activa sin recargar página
// ----------------------------------------------------------
function abrirCapa(tabId, capaId) {

    // Desactivar tabs
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));

    // Activar tab clickeado
    document.getElementById(tabId).classList.add('active');

    // Ocultar capas
    document.querySelectorAll('.capa').forEach(c => c.classList.remove('active'));

    // Mostrar capa seleccionada
    document.getElementById(capaId).classList.add('active');
}
</script>
<?php endif; ?>

</head>
<body>

<?php if ($ROL === "Admin"): ?>

    <!-- ======================================
         LENGÜETAS SOLO PARA ADMIN
    ======================================= -->
    <div id="tabs">

        <div class="tab active"
             id="tab_icontel"
             onclick="abrirCapa('tab_icontel','capa_icontel');">
            iContel
        </div>

        <div class="tab"
             id="tab_tna"
             onclick="abrirCapa('tab_tna','capa_tna');">
            TNA Office
        </div>

    </div>

    <!-- ======================================
         CAPAS SOLO PARA ADMIN
    ======================================= -->
    <div class="capa active" id="capa_icontel">
        <iframe src="icontel.php"></iframe>
    </div>

    <div class="capa" id="capa_tna">
        <iframe src="../kickoff_office/office.php"></iframe>
    </div>

<?php else: ?>

    <!-- ======================================
         USUARIO NORMAL (sin lengüetas)
         carga una sola página completa
    ======================================= -->
    <iframe src="icontel.php"
            style="width:100%; height:100vh; border:none;">
    </iframe>

<?php endif; ?>

</body>
</html>
