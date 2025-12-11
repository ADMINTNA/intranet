<?PHP // include que muestra datos en tabla.php y fech_details.php
$conn = DbConnect("tnasolut_sweet");
$result = mysqli_query($conn,$_SESSION['query_contactos']);
$ptr = 0;
while($row = mysqli_fetch_array($result)){
	$ptr ++; 
	?><tr>
		<td><?php echo $ptr;     					?></td>
		<td><?php echo $row["empresa"];      		?></td>
		<td><?php echo $row["office_tel"]; 			?></td>
		<td><?php echo $row["contacto"];			?></td>
		<td><?php echo $row["cargo"]; 				?></td>
		<td><?php echo $row['tipo_contacto']; 		?></td>
        <td><?php echo $row["celular"]; 			?></td>
        <td><?php echo $row["telefono"]; 			?></td>
		<td><?php echo $row['email']; 				?></td>
	</tr> <?php 
}
?>

