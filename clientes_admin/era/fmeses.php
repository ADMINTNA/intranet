<?php
$hoy = date('j-m-Y');
$fcontrato='07-05-2014';
$horafutura='00:00';
$meses_cont = 36;
$d1 = DateTime::createFromFormat('j-m-Y', $hoy);
$d2 = DateTime::createFromFormat('j-m-Y', $fcontrato);

$diff = $d1->diff($d2);
echo $meses = $meses_cont - (($diff->format('%y') *12) + $diff->format('%m'));

echo ' Hoy = '.$hoy.' <-> Fecha Contarto = '.$fcontrato. ' diferencia='.$diff->format('%y years %m months %d days');


	
?>