<?php
header("Content-Type: text/plain");

foreach ($_GET['select2'] as $selectedOption)
    echo $selectedOption."\n";


var_dump($_GET);


echo "fin";

?>