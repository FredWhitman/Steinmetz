<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['user'] = null;
$_SESSION['access_level'] = null;

session_destroy();

header("Location: /index.php");
exit();
