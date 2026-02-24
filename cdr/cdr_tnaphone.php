<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();

/* ================= FUNCIONES FECHA ================= */

function ultimo_dia_mes() {
    return date('Y-m-t');
}
function primer_dia_mes() {
    return date('Y-m-01');
}
function ultimo_dia_mes_anterior() {
    return date('Y-m-t', strtotime('first day of last month'));
}
function primer_dia_mes_anterior() {
    return date('Y-m-01', strtotime('first day of last month'));
}

/* ================= SESIONES ================= */

if (isset($_POST['desde'])) $_SESSION['desde'] = $_POST['desde'];
if (isset($_POST['hasta'])) $_SESSION['hasta'] = $_POST['hasta'];
if (!isset($_SESSION['desde'])) $_SESSION['desde'] = primer_dia_mes();
if (!isset($_SESSION['hasta'])) $_SESSION['hasta'] = ultimo_dia_mes();
if (isset($_POST['cliente'])) $_SESSION['cliente'] = $_POST['cliente'];

$estemes_desde       = primer_dia_mes();
$estemes_hasta       = ultimo_dia_mes();
$mesanterior_desde   = primer_dia_mes_anterior();
$mesanterior_hasta   = ultimo_dia_mes_anterior();

/* ================= CONEXIÓN BD ================= */

$db_host = "cdr.tnasolutions.cl";
$db_user = "cdr";
$db_pwd  = "Pq63_10ad";
$db_name = "tnasolutions";

$con = new mysqli($db_host, $db_user, $db_pwd, $db_name);
if ($con->connect_error) {
    die("Error conexión: " . $con->connect_error);
}
$con->set_charset("utf8");

/* ================= CLIENTES ================= */

$sql = "SELECT clid FROM tnaphone.clientes ORDER BY nombre ASC";
$query = $con->query($sql);

$ids = [];
while ($row = $query->fetch_row()) {
    $ids[] = $row[0];
}
$query->free();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<?php include_once("../meta_data/meta_data.html"); ?>
<title>CDR Recargas iConTel</title>
<link href="../css/cdr.css" rel="stylesheet">
<script src="../js/IP_generalLib.js"></script>
<script>
function toggle_visible(id){
    let e = document.getElementById(id);
    e.style.display = (e.style.display === 'block') ? 'none' : 'block';
}
</script>
</head>

<body>

<form method="post" id="formulario" class="no-print">
<table>
<tr>
<td>Fecha Desde:</td>
<td><input type="text" name="desde" id="desde" value="<?= $_SESSION['desde'] ?>"></td>
<td>Fecha Hasta:</td>
<td><input type="text" name="hasta" id="hasta" value="<?= $_SESSION['hasta'] ?>"></td>
<td><input type="submit" value="Buscar"></td>
<td><input type="button" value="Este Mes" onclick="estemes()"></td>
<td><input type="button" value="Mes Anterior" onclick="mesanterior()"></td>
</tr>
</table>
</form>

<div id="progress" style="display:none;border:1px solid;background:orange"></div>

<script>
function estemes(){
    desde.value = "<?= $estemes_desde ?>";
    hasta.value = "<?= $estemes_hasta ?>";
    formulario.submit();
}
function mesanterior(){
    desde.value = "<?= $mesanterior_desde ?>";
    hasta.value = "<?= $mesanterior_hasta ?>";
    formulario.submit();
}
</script>

<table class="fixed_header">
<thead>
<tr>
<th>#</th>
<th>Razón Social</th>
<th>Llamadas</th>
<th>Duración</th>
<th>Valor $</th>
</tr>
</thead>
<tbody>

<?php
if (isset($_POST['desde'], $_POST['hasta'])) {

    echo "<script>toggle_visible('progress')</script>";

    $contador = 0;
    $totalids = count($ids);

    foreach ($ids as $id) {

        $contador++;
        $percent = intval($contador / $totalids * 100) . "%";
        echo "<script>
        document.getElementById('progress').innerHTML =
        '<div style=\"width:$percent;background:#ddd\">$contador clientes procesados</div>';
        </script>";

        /* ===== CLIENTE ===== */
        $sql = "SELECT nombre FROM tnaphone.clientes WHERE clid = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($cliente);
        $stmt->fetch();
        $stmt->close();

        /* ===== CDR ===== */
        $sql = "
        SELECT billsec/60 AS minutos,
               (billsec/60) * costo AS valor
        FROM tnaphone.cdr
        WHERE clid = ?
        AND calldate BETWEEN ? AND ?
        ";

        $stmt = $con->prepare($sql);
        $stmt->bind_param("iss", $id, $_POST['desde'], $_POST['hasta']);
        $stmt->execute();
        $result = $stmt->get_result();

        $llamadas = $result->num_rows;
        $minutos = 0;
        $valor   = 0;

        while ($row = $result->fetch_assoc()) {
            $minutos += $row['minutos'];
            $valor   += $row['valor'];
        }

        echo "<tr>
            <td>$contador</td>
            <td>$cliente</td>
            <td align='right'>$llamadas</td>
            <td align='right'>".number_format($minutos,0)."</td>
            <td align='right'>$ ".number_format($valor,0)."</td>
        </tr>";

        $stmt->close();
    }

    echo "<script>toggle_visible('progress')</script>";
}
?>

</tbody>
</table>

<?php $con->close(); ?>
</body>
</html>
