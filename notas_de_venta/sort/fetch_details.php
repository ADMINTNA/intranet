<?php
include "config.php";
session_start();

$conn = DbConnect("tnasolut_sweet");
$columnName = $_POST['columnName'];
$sort = $_POST['sort'];

// query base desde la sesión
$query = $_SESSION["query"] . " ORDER BY " . $columnName . " " . $sort;
$result = mysqli_query($conn, $query);

$ptr = 0;
while($row = mysqli_fetch_array($result)){
    $ptr++;
    $fac_url     = $row["fac_url"];
    $cot_url     = $row["cot_url"];
    $cli_url     = $row["cli_url"];
    $nv_num      = $row["nv_num"];
    $fac_num     = $row['fac_num'];
    $fac_fecha = date("d-m-Y", strtotime($row['fac_fecha']));
    $cot_num     = $row['cot_num'];
    $cot_estado  = $row['cot_estado'];
    $descripcion = $row['descripcion'];
    $cliente     = $row['cliente'];
    $fac_tipo    = $row["fac_tipo"];
    $moneda      = $row["moneda"];
    $monto       = $row["monto"];
    $usuario     = $row["usuario"];
    ?>
    <tr>
        <td><?php echo $ptr; ?></td>
        <td><?php echo $nv_num; ?></td>
        <td><a target="_blank" href="<?php echo $fac_url; ?>"><?php echo $fac_num; ?></a></td>
        <td align="center"><?php echo $fac_tipo; ?></td>
        <td align="center"><?php echo $fac_fecha; ?></td>
        <td><a target="_blank" href="<?php echo $cot_url; ?>"><?php echo $cot_num; ?></a></td>
        <td><?php echo $cot_estado; ?></td>
        <td><?php echo $descripcion; ?></td>
        <td><a target="_blank" href="<?php echo $cli_url; ?>"><?php echo $cliente; ?></a></td>
        <td align="center"><?php echo $moneda; ?></td>
        <td align="right"><?php echo number_format($monto,2); ?></td>
        <td><?php echo $usuario; ?></td>
    </tr>
    <?php
}
?>
