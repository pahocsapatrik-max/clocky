<?php
session_start();

/* ===== BEJELENTKEZÉS ÉS ROLE ELLENŐRZÉSE ===== */
$role = (int)($_SESSION['role'] ?? -1);
if (!isset($_SESSION['logged_in'], $_SESSION['role']) || $_SESSION['logged_in'] !== true || $role !== 0) {
    header('Location: index.php');
    exit;
}

/* ===== LOGOUT KEZELÉS ===== */
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
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #00ffe1; /* Ciánkék háttér */
            font-family: 'Roboto', sans-serif;
            overflow: hidden;
        }

        /* --- MENÜ GOMB (Hamburger) --- */
        .menu-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: #1a1a1a;
            color: #00ffe1;
            border: none;
            padding: 12px 20px;
            border-radius: 50px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }

        .menu-toggle:hover {
            background-color: white;
            color: #1a1a1a;
            transform: scale(1.05);
        }

        /* Hamburger vonalak */
        .hamburger-lines {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }
        .line {
            width: 18px;
            height: 2px;
            background-color: currentColor;
            display: block;
        }

        /* --- NAVIGÁCIÓS PANEL --- */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            background-color: #1a1a1a;
            z-index: 1000;
            max-height: 0; /* Alaphelyzetben zárva */
            overflow: hidden;
            transition: max-height 0.4s ease-in-out;
            border-bottom-right-radius: 20px;
            box-shadow: 5px 0 20px rgba(0,0,0,0.4);
            padding-top: 0;
        }

        nav.open {
            max-height: 400px; /* Lenyíló magasság */
            padding-top: 80px; /* Hely a gomb alatt */
            padding-bottom: 20px;
        }

        nav ul {
            list-style: none;
            padding: 0 20px;
        }

        nav ul li {
            margin-bottom: 10px;
        }

        nav ul li a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            font-weight: 700;
            border-radius: 10px;
            transition: all 0.3s ease;
            background: #262626;
        }

        nav ul li a:hover {
            background: #00ffe1;
            color: #1a1a1a;
            transform: translateX(10px);
        }

        /* Kijelentkezés a menüben */
        nav ul li.logout-item a {
            background: #d32f2f;
            color: white;
            margin-top: 20px;
        }
        nav ul li.logout-item a:hover {
            background: #b71c1c;
        }

        /* --- TARTALOM --- */
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
            margin: 0;
            text-align: center;
        }
    </style>
</head>
<body>

    <button class="menu-toggle" onclick="toggleMenu()">
        <div class="hamburger-lines">
            <span class="line"></span>
            <span class="line"></span>
            <span class="line"></span>
        </div>
        Menü
    </button>

    <nav id="userNav">
        <ul>
            
            <li class="logout-item"><a href="?logout=1">Kijelentkezés</a></li>
        </ul>
    </nav>

    <h1>ÜDVÖZÖLJÜK!</h1>
    <h2>ÖNNEK SIMA JOGOSULTSÁGAI VANNAK</h2>

    <script>
        function toggleMenu() {
            const nav = document.getElementById('userNav');
            nav.classList.toggle('open');
        }

        // Bezárás, ha a menün kívülre kattintunk
        document.addEventListener('click', function(event) {
            const nav = document.getElementById('userNav');
            const btn = document.querySelector('.menu-toggle');
            if (!nav.contains(event.target) && !btn.contains(event.target)) {
                nav.classList.remove('open');
            }
        });
    </script>

</body>
</html>