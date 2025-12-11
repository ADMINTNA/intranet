<?php
session_start();
$ip_server = $_SERVER['SERVER_ADDR'];
$server = $_SERVER['SERVER_NAME'];
switch ($ip_server) {
	case "170.79.232.130":
		$server = "CP2";
         break;
	case  "170.79.233.6":
		$server = "Chicago";
         break;
    case "170.79.234.214":
         $server = "California";
         break;
}

if(!$_SESSION['loggedin']) {
	$_SESSION['url_origen'] = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
	header("location: /login/");
	echo "UPs, ha ocurrido un error. La página que busca no se encuentra.";
	exit();
}
?>