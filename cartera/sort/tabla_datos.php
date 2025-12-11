<?PHP // include que muestra datos en tabla.php y fech_details.php
    $conn = DbConnect("tnasolut_sweet");

$result = mysqli_query($conn,$query);
$ptr = 0;
while($row = mysqli_fetch_array($result)){
	$ptr ++; 
	?><tr>
		<td><?php echo $ptr;     					?></td>
		<td><?php echo $row["cuenta"];      		?></td>
		<td><?php echo $row["cuenta_tipo"]; 		?></td>
		<td><?php echo $row{"cuenta_rut"};			?></td>
		<td><?php echo $row["cuenta_estado"]; 		?></td>
		<td><?php echo $row['cuenta_direccion']; 	?></td>
		<td><?php echo $row['cuenta_ciudad']; 		?></td>
		<td><?php echo $row['cuenta_telefono'];	 	?></td>
		<td><?php echo $row['vendedor']; 			?></td>
		<td><?php echo $row['contacto_nombre']; 	?></td>
		<td><?php echo $row['contacto_tipo']; 		?></td>
		<td><?php echo $row['contacto_celular']; 	?></td>
		<td><?php echo $row['contacto_fono']; 		?></td>
		<td><?php echo $row['contacto_email']; 		?></td>
	</tr> <?php 
} 
?>