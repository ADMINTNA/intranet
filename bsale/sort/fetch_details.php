<?php
require_once __DIR__ . '/session_config.php';
// ... tu código de la página ...
include "config.php";

$conn = DbConnect("tnasolut_sweet");
$columnName = $_POST['columnName'];
$sort = $_POST['sort'];

// query base desde la sesión
$query = $_SESSION["query"] . " ORDER BY " . $columnName . " " . $sort;
$result = mysqli_query($conn, $query);

$ptr = 0;
while($row = mysqli_fetch_array($result)){
    $ptr++;
     ?>
                    <tr>
                        <td><?php echo $ptr; ?></td>
                        <td><?php echo $row["tipo_doc"]; ?></td>
                        <td align="right"><?php echo $row["num_doc"]; ?></td>
                        <td align="center"><?php echo date("d-m-Y", strtotime($row['fecha_emision'])); ?></td>
                        <td><?php echo $row['razon_social']; ?></td>
                        <td align="right"><?php echo $row['rut_cliente']; ?></td>
                        <td align="center"><?php echo $row['id_moneda']; ?></td>
                        <td align="right"><?php echo number_format((float)$row['netAmount'], 2, ',', '.'); ?></td>
                        <td><a target="_blank" href="<?php echo $row['urlPdf']; ?>">PHP</a></td>
                        <td><a target="_blank" href="<?php echo $row['urlPublicView']; ?>">Ver</a></td>
                     </tr>
    <?php
}
?>
