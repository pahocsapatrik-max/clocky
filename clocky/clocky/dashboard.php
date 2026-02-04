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
 /* Importálunk egy modernebb betűtípust */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700;900&display=swap');

:root {
    --bg-color: #0f0f0f;         /* Mélyfekete háttér */
    --card-bg: #1a1a1a;          /* Kártyák színe */
    --accent-color: #00ffe1;     /* A te türkizszíned */
    --text-primary: #ffffff;
    --text-secondary: #a0a0a0;   /* Halványabb szürke a kevésbé fontos infóknak */
    --danger: #ff4757;
}

body {
    margin: 0;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: radial-gradient(circle at top right, #1e1e1e, var(--bg-color));
    font-family: 'Inter', sans-serif;
    color: var(--text-primary);
}

/* FŐCÍMEK - Sokkal letisztultabb */
h1 {
    font-weight: 900;
    font-size: 3.5rem;
    letter-spacing: -2px;
    margin-bottom: 0.5rem;
    background: linear-gradient(to bottom, #fff, #888);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

h2 {
    font-weight: 300;
    font-size: 1.2rem;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 4px;
    margin-bottom: 2rem;
}

/* KÁRTYA STÍLUS (Ezt használd a táblázathoz és a dashboardhoz is) */
.glass-card {
    background: var(--card-bg);
    padding: 3rem;
    border-radius: 24px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    width: 100%;
    max-width: 450px;
    text-align: center;
    transition: transform 0.3s ease;
}

/* PROFI GOMBOK */
.btn-main {
    background: var(--accent-color);
    color: #000;
    padding: 1.2rem 2.5rem;
    border-radius: 12px;
    border: none;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 0 20px rgba(0, 255, 225, 0.2);
    text-transform: uppercase;
}

.btn-main:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 255, 225, 0.4);
    filter: brightness(1.1);
}

/* TÁBLÁZAT FINOMÍTÁS */
table {
    width: 100%;
    border-spacing: 0 10px; /* Szétválasztott sorok */
}

tr {
    background: rgba(255, 255, 255, 0.02);
    transition: 0.2s;
}

tr:hover {
    background: rgba(255, 255, 255, 0.05);
}

td, th {
    padding: 1.2rem;
    border: none;
}

th {
    font-size: 0.8rem;
    color: var(--text-secondary);
    letter-spacing: 1px;
}


</style>




</body>
</html>
