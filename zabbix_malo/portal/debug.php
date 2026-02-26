<?php
session_name('icontel_intranet_sess');
session_set_cookie_params(0, '/', '.icontel.cl', false, true);
session_start();
echo "<h1>Debug Session</h1>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>