<?php
require __DIR__.'/../includes/app.php';
if($_SERVER['REQUEST_METHOD']!=='POST'||!csrf_valid()){http_response_code(405);exit('Solicitud no permitida');}
unset($_SESSION['auth_user']);session_regenerate_id(true);flash('Tu sesión se cerró correctamente.');header('Location: login.php');exit;
