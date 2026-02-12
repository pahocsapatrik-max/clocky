<?php
session_start();
// --- OKOS MAGYARÍTOTT ROUTER ---
$request_uri = $_SERVER['REQUEST_URI'];
$current_file = basename($_SERVER['PHP_SELF']);

// Útvonal térkép
$routes = [
    'index.php' => 'bejelentkezes'
   
];

// Minden session változó ürítése
$_SESSION = array();

// Ha van session süti, azt is érdemes érvényteleníteni
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// A szerver oldali session megsemmisítése
session_destroy();

// Irány a kezdőlap
header('Location: index.php');
exit();
?>