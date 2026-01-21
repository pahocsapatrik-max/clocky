<?php
session_start();

/* ===== BEJELENTKEZÉS ÉS ROLE ELLENŐRZÉSE ===== */
$role = (int)($_SESSION['role'] ?? -1);
if (!isset($_SESSION['logged_in'], $_SESSION['role']) || $_SESSION['logged_in'] !== true || $role !== 0) {
    header('Location: index.php');
    exit;
}

/* ===== LOGOUT KEZELÉS ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
<meta charset="UTF-8">
<title>Felhasználói Dashboard</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f4f4;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}
.box {
    background: white;
    padding: 30px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}
button {
    margin-top: 25px;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    background: #c0392b;
    color: white;
}
button:hover {
    background: #e74c3c;
}
</style>
</head>
<body>
<h1>ÜDVÖZÖLJÜK!</h1>
<h2>ÖNNEK SIMA JOGOSULTSÁGAI VANNAK</h2>

<style>
 body {
    margin: 0;
    height: 100vh;           /* teljes képernyő magasság */
    display: flex;
    flex-direction: column;   /* függőleges elrendezés */
    justify-content: center;  /* függőleges közép */
    align-items: center;      /* vízszintes közép */
    background-color: #8300fdff;   /* háttér szín, ha kell */
}

h1 {
    color: white;
    font-family: 'Roboto', sans-serif;
    font-size: 60px;
    text-shadow: 2px 2px 6px rgba(0,0,0,0.5);
    margin: 0; /* ne legyen extra margó */
}

h2 {
    color: white;
    font-family: 'Roboto', sans-serif;
    font-size: 40px;          /* kisebb, alatta */
    text-shadow: 2px 2px 6px rgba(0,0,0,0.5);
    margin: 0;
}


</style>

<li class="logout"><a href="logout.php">Kijelentkezés</a></li>


</body>
</html>
