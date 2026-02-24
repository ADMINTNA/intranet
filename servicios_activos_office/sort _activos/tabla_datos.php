<?PHP // include que muestra datos en tabla.php y fech_details.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$query = $_SESSION['query'].$_SESSION['where'].$_SESSION['orden'];

$conn = DbConnect("tnaoffice_suitecrm");
$result = mysqli_query($conn, $query);
$ptr = 0;
$tot_valor = 0;
$tot_costo = 0;
while($row = mysqli_fetch_array($result)){
	$ptr ++; 
	?><tr>
		<td><?php echo $ptr;     					?></td>
		<td><?php echo $row["cliente"];      		?></td>
		<td><?php echo $row["producto"]; 			?></td>
		<td><?php echo $row["codigo"]; 			    ?></td>
		<td><a href="<?php echo $row["coti_url"];?>" target="_blank"> <?php echo $row["coti_numero"];?></a></td>
		<td><?php echo $row["proveedor"];			?></td>
		<td><?php echo $row["codigo_servicio"]; 	?></td>
		<td><?php echo $row["detalle_instalacion"]; ?></td>
		<td><?php echo $row['categoria']; 			?></td>
        <td align="right"><?php echo number_format($row["valor"], 2, ',', '.'); ?></td>
        <td align="right"><?php echo number_format($row["costo"], 2, ',', '.'); ?></td>
		<td><?php echo $row['recurrencia']; 		?></td>
	</tr> <?php 
	$tot_valor    += $row['valor'];
	$tot_costo    += $row['costo'];
} ?>
	<tr>
		<td colspan="9"> </td>
		<th align="right"><?php echo number_format($tot_valor, 2, ',', '.'); ?></th>
		<th align="right"><?php echo number_format($tot_costo, 2, ',', '.'); ?></th>
		<td> </td>
	</tr>

