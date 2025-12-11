<?php

include "config.php";
//date_default_timezone_set("America/Santiago");
      // activo mostrar errores
      error_reporting(E_ALL);
      ini_set('display_errors', '1');

$columnName = $_POST['columnName'];
$sort = $_POST['sort'];
session_start();
$select_query = $_SESSION["query_cotizacion"]." order by ".$columnName." ".$sort." ";
$conn = DbConnect("tnasolut_sweet"); 
$result = mysqli_query($conn,$select_query);
$ptr = 0;
                    while($row = mysqli_fetch_array($result)){
                        $ptr ++; 
						$url_coti 		= $row["url_coti"];
						$url_op 		= $row["url_op"];
                        $url_caso       = $row["url_caso"];
                        $numero         = $row["cot_numero"];
                        $caso_num       = $row["caso_numero"];
                        $titulo         = $row['titulo'];
                        $producto       = $row['producto'];
                        $cliente        = $row['cliente'];
                        $dte       		= $row['dte'];
                        $op_num       	= $row['op_numero'];
                        $etapa_venta    = $row['etapa_venta'];
                        $moneda       	= $row['moneda'];
						$neto			= $row['neto'];
						$asignado_a		= $row['asignado_a'];
                        $etapa 			= $row['etapa_cot']; 
                        $fecha          = fechacl($row['fecha_creacion']);           
                        ?>
                        <tr>
                            <td><?php echo $ptr; ?></td>
                            <td><a target="_blank" href="<?php echo $url_coti; ?>"><?php echo $row['cot_numero']; ?></a></td>
                            <td><?php echo $titulo; ?></td>
                            <td><?php echo $cliente; ?></td>
                            <td><?php echo $producto; ?></td>
                            <td><?php echo $dte; ?></td>
                            <td><a target="_blank" href="<?php echo $url_op; ?>"><?php echo $op_num; ?></a></td>
                            <td><?php echo $etapa_venta; ?></td>
                            <td><a target="_blank" href="<?php echo $url_caso; ?>"><?php echo $caso_num; ?></a></td>
                            <td align="center"><?php echo $moneda; ?></td>
                            <td align="right"><?php echo number_format($neto, 2); ?></td>
                            <td ><?php echo $asignado_a; ?></td>
                            <td><?php echo $etapa; ?></td>
                            <td align="center"><?php echo $fecha; ?></td>
                        </tr>
                <?php
                }

