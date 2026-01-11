<?php //include  './checkLogin.php'; 
    $httpProtocol = !isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on' ? 'http' : 'https';
    $base = $httpProtocol . '://' . $_SERVER['HTTP_HOST'] . '/appescola/login/login_multinivel/';
   

    define('SITE_ROOT', __DIR__);
?>