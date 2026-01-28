<?php
session_start();

/* ===== BEJELENTKEZÉS ÉS ROLE ELLENŐRZÉSE ===== */
$role = (int)($_SESSION['role'] ?? -1);
if (!isset($_SESSION['logged_in'], $_SESSION['role']) || $_SESSION['logged_in'] !== true || $role !== 0) {
    header('Location: index.php');
    exit;
}

/* ===== LOGOUT KEZELÉS ===== */
// Ez a rész kezeli, ha rákattintasz a kijelentkezésre
// Ha külön logout.php-ra küldöd, akkor ez a blokk elhagyható, 
// de így egy fájlban marad minden.
if (isset($_GET['logout'])) {
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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #00ffe1ff; /* Ciánkék háttér */
            font-family: 'Roboto', sans-serif;
            overflow: hidden;
        }

        h1 {
            color: white;
            font-size: 60px;
            text-shadow: 2px 2px 6px rgba(0,0,0,0.5);
            margin: 0;
            text-align: center;
        }

        h2 {
            color: white;
            font-size: 40px;
            text-shadow: 2px 2px 6px rgba(0,0,0,0.5);
            margin: 0 0 30px 0;
            text-align: center;
        }

        /* --- Kijelentkezés gomb dizájn --- */
        .logout-wrapper {
            position: absolute;
            top: 20px;
            right: 20px;
            list-style: none; /* Li pont eltüntetése */
        }

        .btn-logout {
            display: inline-block;
            padding: 12px 28px;
            background-color: #1a1a1aff; /* Sötét háttér */
            color: #00ffe1ff;           /* Cián szöveg */
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border-radius: 50px;       /* Kapszula forma */
            border: 2px solid #1a1a1aff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transition: all 0.3s ease-in-out;
            cursor: pointer;
        }

        .btn-logout:hover {
            background-color: white;
            color: #1a1a1aff;
            border-color: white;
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0,0,0,0.4);
        }
    </style>
</head>
<body>

    <div class="logout-wrapper">
        <a href="?logout=1" class="btn-logout">Kijelentkezés</a>
    </div>

    <h1>ÜDVÖZÖLJÜK!</h1>
    <h2>ÖNNEK SIMA JOGOSULTSÁGAI VANNAK</h2>

</body>
</html>