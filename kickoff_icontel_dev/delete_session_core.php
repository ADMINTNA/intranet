<?php
// Script temporal para eliminar session_core.php
if (file_exists('session_core.php')) {
    if (unlink('session_core.php')) {
        echo "session_core.php eliminado correctamente";
    } else {
        echo "Error al eliminar session_core.php";
    }
} else {
    echo "session_core.php no existe";
}
?>
