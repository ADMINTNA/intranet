<?php
header('Content-Type: text/plain; charset=utf-8');
echo "Hostname: " . gethostname() . "\n";
echo "Server IP: " . $_SERVER['SERVER_ADDR'] . "\n";
echo "Current User (PHP): " . get_current_user() . "\n";
echo "Home folder: " . getenv('HOME') . "\n";
echo "Realpath: " . realpath(__DIR__) . "\n";
echo "\n--- DNs check ---\n";
echo "cleveland.icontel.cl IP: " . gethostbyname('cleveland.icontel.cl') . "\n";
echo "icontel.cl IP: " . gethostbyname('icontel.cl') . "\n";
