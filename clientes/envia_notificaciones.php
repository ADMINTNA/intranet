<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><META NAME="author" CONTENT="TNA Solutions">
<META NAME="subject" CONTENT="PIT Chile, Trafico Internet, BGP, ASN, Peering, IXP, IPV4, IPV6, Ancho de Banda">
<META NAME="Description" CONTENT="Punto Neutral de Intercambio de Tráfico Internet en Chile (IXP Internet eXchange Point en inglés), Peering, Ingenieria de Trafico">
<META NAME="Classification" CONTENT="PIT Chile, internet, Punto de intercambio de trafico, Neutral, BGP, ASN, peering, Internet eXchange Point, latencia, ancho de banda, estabilidad, ipv4 ipv6, capa 2 leyer 2">
<META NAME="Keywords" CONTENT="PIT Chile, internet, Punto de intercambio de trafico, Neutral, BGP, ASN, peering, Internet eXchange Point, latencia, ancho de banda, estabilidad, ipv4 ipv6, capa 2 leyer 2">
<META NAME="Geography" CONTENT="Chile">
<META NAME="Language" CONTENT="Spanish">
<META HTTP-EQUIV="Expires" CONTENT="never">
<META NAME="Copyright" CONTENT="TNA Solutions">
<META NAME="Designer" CONTENT="TNA Solutions">
<META NAME="Publisher" CONTENT="TNA Solutions">
<META NAME="Revisit-After" CONTENT="7 days">
<META NAME="distribution" CONTENT="Global">
<META NAME="Robots" CONTENT="INDEX,FOLLOW">
<META NAME="city" CONTENT="Santiago">
<META NAME="country" CONTENT="Chile">
<title>Enlaces PIT Chile SpA.</title></head>
<body>
<?php
if (isset($_GET["audit"])) {
	$audit=TRUE;
} else $audit=FALSE;
	
$servername = "localhost";
$username = "tnasolut_enlaces";
$password = "P3rf3ct0.,";
$dbname = "tnasolut_enlaces";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM `emails` WHERE `enviado` = FALSE";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
    //    echo "id: " . $row["id"]. " - to: " . $row["to"]. " - message: " . $row["message"]. " -> enviado <br>";
		$headers = 'From: TNA Solutions Enlaces<info@tnasolutions.cl>' . "\r\n" .
		'Reply-To: info@tnasolutions.cl' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		mail($row["to"], $row["subject"], $row["message"], $headers);
		if(audit){
			echo "Mensaje Enviado: <br>
			Para:    ".$row["to"]."<br>
			Asunto:  ".$row["subjet"]."<br>
			Mensaje: ".$row["message"]."<br><br>";
		}
    }
	$sql = "UPDATE `emails` SET `enviado` = TRUE  WHERE `enviado` = FALSE";
	$result = $conn->query($sql);

} else {	
		if(audit){
			//echo "Nada que enviar<br><br>";
		}
	
}
$conn->close();

?>
</body>
</html>
