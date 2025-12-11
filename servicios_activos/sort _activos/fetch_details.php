<?php
include "config.php";
$columnName = $_POST['columnName'];
$sort = $_POST['sort'];
session_start();
$select_query =  $_SESSION["query"].$_SESSION["where"]." order by ".$columnName." ".$sort." ";
$conn = DbConnect("tnasolut_sweet"); 
$result = mysqli_query($conn,$select_query);
$ptr = 0;
while($row = mysqli_fetch_array($result)){
    $ptr ++;    
    $valor        = $row['valor'];
    $costo        = $row['costo'];
    $tot_valor    += $valor;
    $tot_costo    += $costo;
    ?>
    <tr>
        <td><?php echo $ptr; ?></td>
        <td><?php echo $row['cliente'] ?></td>
        <td><?php echo $row['producto']; ?></td>
        <td><?php echo $row['codigo'] ?></td>
		<td><a href="<?php echo $row["coti_url"];?>" target="_blank"> <?php echo $row["coti_numero"];?></a></td>
        <td><?php echo $row['proveedor'] ?></td>
        <td><?php echo $row['codigo_servicio']; ?></td>        
        <td><?php echo $row["detalle_instalacion"];?></td>        
        <td><?php echo $row['categoria'] ?></td>
        <td align="right"><?php echo number_format($valor, 2, ',', '.'); ?></td>
        <td align="right"><?php echo number_format($costo, 2, ',', '.'); ?></td>
        <td><?php echo $row['recurrencia'];?></td>
    </tr>
    <?php 
}
?>
<tr>
 	<td colspan="9"> </td>
    <th align="right"><?php echo number_format($tot_valor, 2, ',', '.'); ?></th>
    <th align="right"><?php echo number_format($tot_costo, 2, ',', '.'); ?></th>
    <td> </td>
</tr>
<?php



