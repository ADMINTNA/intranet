<?php
//=========================================================
// /intranet/index.php
// Controlador central de Login + Redirección según Rol
// Autor: Mauricio Araneda (mAo)
// Fecha: 2025-11
// Codificación: UTF-8 sin BOM
//=========================================================

ob_start();

// -----------------------------------------------------
// 🔐 CONFIGURAR SESIÓN
// -----------------------------------------------------
session_name('icontel_intranet_sess');
session_set_cookie_params(
    0,              // lifetime
    '/',            // path
    '.icontel.cl',  // domain
    false,          // secure (para compatibilidad si no todo es HTTPS, aunque sea recomendable true)
    true            // httponly
);
session_start();

// -----------------------------------------------------
// GET → BORRAR SESIÓN Y MOSTRAR LOGIN
// -----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Solo borrar si NO estamos logueados ya (para permitir refrescar o volver sin perder la sesión)
    if (empty($_SESSION['loggedin'])) {
        session_unset();
        session_destroy();
        setcookie('icontel_intranet_sess','',time()-3600,'/','.icontel.cl');
        session_start();
    }

    $DEBUG = isset($_GET['debug']) && $_GET['debug']=="1";
    if ($DEBUG) $_SESSION['debug']=true;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Intranet – Acceso</title>
<style>
body { font-family: Arial; background:#1F1D3E; color:white; margin:0; padding:0; }
.form-box {
    width:320px; margin:140px auto; padding:25px;
    background:#27304A; border-radius:8px;
    box-shadow:0 0 10px rgba(0,0,0,0.4);
}
input {
    width:100%; padding:10px; margin-bottom:12px;
    border-radius:5px; border:1px solid #C39BD3;
    background:#fff; color:#000;
    box-sizing: border-box;
}
button {
    width:100%; padding:10px; background:#512554;
    color:white; border:none; border-radius:5px;
    cursor:pointer; font-size:15px;
}
button:hover { background:#7D3C98; }
.password-wrapper {
    position: relative;
    margin-bottom: 12px;
    width: 100%;
}
.password-wrapper input {
    margin-bottom: 0;
    padding-right: 40px;
    width: 100%;
}
.toggle-password {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    user-select: none;
    font-size: 18px;
    color: #666;
    transition: color 0.2s;
}
.toggle-password:hover {
    color: #512554;
}
</style>
</head>

<body>
<div class="form-box">
    <form action="" method="post">
        <h3>Acceso a Intranet</h3>

        <label>Usuario</label>
        <input type="text" name="username" required>

        <label>Contraseña</label>
        <div class="password-wrapper">
            <input type="password" name="password" id="password" required>
            <span class="toggle-password" onclick="togglePassword()">👁️</span>
        </div>

        <?php if (!empty($_SESSION['debug'])): ?>
            <input type="hidden" name="debug" value="1">
        <?php endif; ?>

        <?php if (isset($_GET['dev']) && $_GET['dev'] == '1'): ?>
            <input type="hidden" name="dev" value="1">
        <?php endif; ?>

        <button type="submit">Ingresar</button>
    </form>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.querySelector('.toggle-password');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.textContent = '🙈';
    } else {
        passwordInput.type = 'password';
        toggleIcon.textContent = '👁️';
    }
}
</script>
</body>
</html>
<?php
    exit;
}

// -----------------------------------------------------
// POST → VALIDAR LOGIN
// -----------------------------------------------------
$username = trim($_POST['username']);
$password = trim($_POST['password']);

if (isset($_POST['debug']) && $_POST['debug']=="1") {
    $_SESSION['debug'] = true;
}

$username = mb_strtolower(str_replace(['.', ' ', '-'], '', $username));

$con = new mysqli("localhost", "tnasolut_app", "1Ngr3s0.,", "tnasolut_app");
if ($con->connect_errno) {
    die("❌ Error MySQL: " . $con->connect_error);
}

$stmt = $con->prepare("
    SELECT id, username, password, razon_social, rut, sec_id, rol, sec_id_office
    FROM clientes
    WHERE LOWER(REPLACE(REPLACE(REPLACE(username,'.',''),' ',''),'-','')) = ?
");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) { // no encontró al usuario}
    
    // 🔥 BORRAR TODO: sesión + cookie
    session_unset();
    session_destroy();
    setcookie('icontel_intranet_sess','', time() - 3600, '/', '.icontel.cl');

    // 🔄 Redirigir SIEMPRE al login limpio
    header("Location: https://intranet.icontel.cl");
    exit;
}


$stmt->bind_result($id, $db_user, $db_pass, $rs, $rut, $sec_id, $rol, $sec_id_office);
$stmt->fetch();

if ($password !== $db_pass) {
    echo "<h3 style='color:red;text-align:center;'>❌ Contraseña incorrecta</h3>";
    exit;
}

// session_regenerate_id(true); // ⚠️ Comentado temporalmente para evitar race conditions con iframe/cookies

$_SESSION['loggedin'] = true;
$_SESSION['id']             = $id;
$_SESSION['name']           = $db_user;
$_SESSION['cliente']        = $rs;
$_SESSION['rut']            = $rut;
$_SESSION['rol']            = $rol;
$_SESSION['sg_id']          = $sec_id;

// Persistir grupo en Cookie para KickOff (30 días)
setcookie('icontel_last_sg_id', $sec_id, time() + (86400 * 30), '/', '.icontel.cl', false, true);

// -----------------------------------------------------
// 🎯 NUEVA LÓGICA DE DESTINO SEGÚN ROL
// -----------------------------------------------------
$ROL = $_SESSION['rol'];

// Verificar si se solicitó versión de desarrollo
$use_dev = (isset($_GET['dev']) && $_GET['dev'] == '1') || (isset($_POST['dev']) && $_POST['dev'] == '1');

if ($ROL === "iContel") {
    if ($use_dev) {
        $iframe_url = "https://intranet.icontel.cl/kickoff_icontel_dev/";
    } else {
        $iframe_url = "https://intranet.icontel.cl/kickoff_icontel/icontel.php";
    }
}
elseif ($ROL === "Office") {
    $iframe_url = "https://intranet.icontel.cl/kickoff_office/index.php";
}
elseif ($ROL === "Admin") {
    $iframe_url = "https://intranet.icontel.cl/kickoff_icontel/index.php"; // con pestañas
}
else {
    // Rol desconocido → iContel por defecto
    $iframe_url = "https://intranet.icontel.cl";
}

// -----------------------------------------------------
// SI NO ES DEBUG → MOSTRAR IFRAME PRINCIPAL
// -----------------------------------------------------
if (empty($_SESSION['debug'])) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Intranet</title>
<style>
body { margin:0; padding:0; overflow:hidden; }
iframe { width:100%; height:100vh; border:none; }
</style>
</head>
<body>

<!-- 🎯 TODO EL SISTEMA QUEDA DENTRO DE ESTE FRAME -->
<iframe src="<?php echo $iframe_url; ?>"></iframe>

</body>
</html>
<?php
    exit;
}

// -----------------------------------------------------
// DEBUG
// -----------------------------------------------------
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Sesión iniciada (Debug)</title>
</head>
<body style="font-family:Arial; background:#F0F0F0; padding:40px;">

<h2>✅ Login correcto — Sesión inicializada</h2>

<pre style="background:#FFF;padding:20px;border:1px solid #CCC;"><?php var_dump($_SESSION); ?></pre>

<form action="<?php echo $iframe_url; ?>">
    <button style="padding:10px 20px;background:#27304A;color:white;border:none;">
        Continuar
    </button>
</form>

</body>
</html>
<?php
exit;
?>


