<?php
session_start();

// --- URL TISZTÍTÁS (PHP ROUTING) ---
// Bár ez egy funkciófájl, ha valaki véletlenül beírná az URL-be, tisztítsuk le
$request_uri = $_SERVER['REQUEST_URI'];
if (strpos($request_uri, '.php') !== false) {
    $clean_uri = str_replace('.php', '', $request_uri);
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: " . $clean_uri);
    exit();
}

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

// Irány a kezdőlap - tiszta URL-lel
header('Location: index');
exit();
?>