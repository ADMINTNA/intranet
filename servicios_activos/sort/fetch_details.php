<?php
include "config.php";
$columnName = $_POST['columnName'];
$sort = $_POST['sort'];
session_start();
$select_query =  $_SESSION["query"]." order by ".$columnName." ".$sort." ";
$conn = DbConnect("tnasolut_sweet"); 
$result = mysqli_query($conn,$select_query);
$ptr = 0;
while($row = mysqli_fetch_array($result)){
    $ptr ++;    
    $cliente      = $row['cliente'];
    $producto     = $row['producto'];
    $codigo       = $row['codigo'];
    $proveedor    = $row['proveedor'];
    $codigo_servicio = $row['codigo_servicio'];    
    $categoria    = $row['categoria'];
    $coti_numero  = $row['coti_numero'];
    $valor        = $row['valor'];
    $costo        = $row['costo'];
    $recurrencia  = $row['recurrencia'];
    $tot_valor    += $valor;
    $tot_costo    += $costo;
    ?>
    <tr>
        <td><?php echo $ptr; ?></td>
        <td><?php echo $cliente; ?></td>
        <td><?php echo $producto; ?></td>
        <td><?php echo $codigo; ?></td>
		<td><a href="<?php echo $row["coti_url"];?>" target="_blank"> <?php echo $row["coti_numero"];?></a></td>
        <td><?php echo $proveedor; ?></td>
        <td><?php echo $codigo_servicio; ?></td>        
        <td><?php echo $categoria; ?></td>
        <td align="right"><?php echo number_format($valor, 2, ',', '.'); ?></td>
        <td align="right"><?php echo number_format($costo, 2, ',', '.'); ?></td>
        <td><?php echo $recurrencia; ?></td>
    </tr>
    <?php 
}
?>
<tr>
 	<td colspan="8"> </td>
    <th align="right"><?php echo number_format($tot_valor, 2, ',', '.'); ?></th>
    <th align="right"><?php echo number_format($tot_costo, 2, ',', '.'); ?></th>
    <td> </td>
</tr>
<?php



