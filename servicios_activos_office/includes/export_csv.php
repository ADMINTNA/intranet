<?PHP
    include_once("../includes/simple_html_dom.php");

    session_start();
    if(isset($_SESSION['tabla'])) $table = $_SESSION['tabla'];
    else {
        echo "No se encontró datos html a convertir.";
        exit();
    }

    $fileName="Contactos_Clientes_Servicios_Activos.csv";
    header('Content-type: application/ms-excel');
    header("Content-Disposition: attachment; filename=$fileName");

    $fp = fopen("fileName", "w");
    $csvString="";

    $html = str_get_html(trim($table));
    
    foreach($html->find('tr') as $element)
    {

        $td = array();
        foreach( $element->find('th') as $row)
        {
            $row->plaintext="\"$row->plaintext\"";
            $td [] = $row->plaintext;
        }
        $td=array_filter($td);
        $csvString.=implode(",", $td);

        $td = array();
        foreach( $element->find('td') as $row)
        {
            $row->plaintext="\"$row->plaintext\"";
            $td [] = $row->plaintext;
        }
        $td=array_filter($td);
        $csvString.=implode(",", $td)."\n";
    }
    echo $csvString;
    fclose($fp);
 
    echo  "<script type='text/javascript'>";
    echo "window.close();";
    echo "</script>"; 
?>    
    