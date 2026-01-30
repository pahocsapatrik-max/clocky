<?php
session_start();
include 'config.php';

// Itt volt a hiba: 'user' helyett 'username' kell
if (!isset($_SESSION['username']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

// Opcionális: Ellenőrizzük, hogy tényleg admin-e (role === 1)
if ($_SESSION['role'] !== 1) {
    header('Location: empdashboard.php');
    exit();
}

$page_title = 'Dashboard';
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    <!-- CSS betöltése -->
    <link rel="stylesheet" href="header.css">
</head>
<body>
<h1>ÜDVÖZÖLJÜK!</h1>
<h2>ÖNNEK ADMIN JOGOSULTSÁGAI VANNAK</h2>
<?php include 'header.php'; ?>
<style>
 body {
    margin: 0;
    height: 100vh;           /* teljes képernyő magasság */
    display: flex;
    flex-direction: column;   /* függőleges elrendezés */
    justify-content: center;  /* függőleges közép */
    align-items: center;      /* vízszintes közép */
    background-color: #414141ff;   /* háttér szín, ha kell */
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




</body>
</html>
