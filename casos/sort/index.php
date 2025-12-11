<?PHP
//=====================================================
// casos/sort/index.php
// busca casos
// Autor: Mauricio Araneda
// Actualizado: 08-11-2025
//=====================================================

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php include_once("../meta_data/meta_data.html"); ?>   
<title>Casos iContel Telecomunicaciones</title>
<style type="text/css">
html, body, div {
    margin: 0;
    padding: 0;
    height: 100%;
}
table {
    border: none;
    color: white;
    font-size: 15px;
    border-collapse: collapse;
    background-color: #19173C;
}
iframe {
    border: none;
    padding: 0;
    margin: 0;
    display: block;
    width: 100%;
    height: 80%;
}
footer {
    background-color: white;
    position: absolute;
    bottom: 0;
    width: 100%;
    height: 25px;
    color: gray;
    font-size: 12px;
    text-align: center;
}
/* Enlaces */
a:link, a:visited {
    color: gray;
    text-decoration: none;
}
a:hover {
    color: darkgrey;
    font-size: 16px;
    font-weight: bold;
}
a:active {
    color: blue;
}
</style>
</head>

<body>
    <table align="center" border="0" width="100%">
        <tr align="center" style="background-color: #1F1D3E;">
            <th width="200" height="130" valign="top" align="left">
                <img src="../images/logo_icontel_azul.jpg" height="115" alt="iConTel Logo"/>
            </th>
            <td>
                <table width="100%" height="100%">
                    <tr height="90">
                        <th align="center" style="font-size: 20px;">Buscador de Casos en Sweet</th>
                    </tr>
                    <tr>
                        <td align="center" style="font-size: 12px;">(Click sobre los títulos para ordenar)</td>
                    </tr>
                </table>
            </td>    
        </tr>
    </table>

    <iframe src="tabla.php"></iframe>

    <footer>
        © Copyright <span id="Year"></span>
        <b>iConTel</b> – 
        <a href="tel:+56228409988">☎ +56 2 2840 9988</a> – 
        <a href="mailto:contacto@tnasolutions.cl?subject=Contacto desde Intranet iConTel.">📧 contacto@tnasolutions.cl</a> – 
        🏠 Badajoz 45, piso 17, Las Condes, Santiago, Chile.
    </footer>

    <script type="text/javascript">
        var d = new Date(); 
        document.getElementById("Year").innerHTML = d.getFullYear();
    </script>
</body>
</html>
