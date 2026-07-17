<?php
session_start();

$_SESSION = array();

session_destroy();

$pasta_projeto = dirname($_SERVER['SCRIPT_NAME']);

if ($pasta_projeto === DIRECTORY_SEPARATOR || $pasta_projeto === '/') {
    $pasta_projeto = '';
}

header("Location: " . $pasta_projeto . "/index.php");
exit;
?>