<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="header.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700;900&display=swap');

        :root {
            --bg-color: #0f0f0f;
            --card-bg: #1a1a1a;
            --accent-color: #00ffe1;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --danger: #ff4757;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: radial-gradient(circle at top right, #1e1e1e, var(--bg-color));
            font-family: 'Inter', sans-serif;
            color: var(--text-primary);
            padding: 20px;
            box-sizing: border-box;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        /* FŐCÍMEK RESPONSIVE MÉRETEZÉSE */
        h1 {
            font-weight: 900;
            font-size: clamp(2rem, 8vw, 3.5rem); /* Dinamikusan változik a kijelzőhöz */
            letter-spacing: -2px;
            margin-bottom: 0.5rem;
            background: linear-gradient(to bottom, #fff, #888);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        h2 {
            font-weight: 300;
            font-size: clamp(0.9rem, 3vw, 1.2rem);
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 2rem;
        }

        /* KÁRTYA STÍLUS - Responsive szélességgel */
        .glass-card {
            background: var(--card-bg);
            padding: clamp(1.5rem, 5vw, 3rem);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 90%;
            max-width: 1000px; /* Megnövelve, hogy a táblázat elférjen */
            margin: 0 auto;
            transition: transform 0.3s ease;
            overflow-x: auto; /* Ha a táblázat mégis túl széles lenne */
        }

        /* PROFI GOMBOK */
        .btn-main {
            background: var(--accent-color);
            color: #000;
            padding: 1rem 2rem;
            border-radius: 12px;
            border: none;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 0 20px rgba(0, 255, 225, 0.2);
            text-transform: uppercase;
            width: auto;
            min-width: 200px;
        }

        .btn-main:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 255, 225, 0.4);
        }

        /* RESPONSIVE TÁBLÁZAT */
        table {
            width: 100%;
            border-spacing: 0 10px;
            margin-top: 20px;
        }

        th {
            font-size: 0.8rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            padding: 1rem;
        }

        td {
            padding: 1rem;
            background: rgba(255, 255, 255, 0.02);
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.05);
        }

        /* MOBIL NÉZET (600px alatt) */
        @media (max-width: 600px) {
            /* A táblázatot kártyákká alakítjuk mobilon */
            table, thead, tbody, th, td, tr { 
                display: block; 
            }
            
            thead tr { 
                position: absolute;
                top: -9999px;
                left: -9999px;
            }
            
            tr { 
                margin-bottom: 15px; 
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 12px;
            }
            
            td { 
                border: none;
                position: relative;
                padding-left: 50%; 
                text-align: right;
            }
            
            td:before { 
                position: absolute;
                left: 15px;
                width: 45%; 
                padding-right: 10px; 
                white-space: nowrap;
                content: attr(data-label); /* Kell majd a HTML-be data-label! */
                text-align: left;
                font-weight: bold;
                color: var(--accent-color);
            }
        }
        
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="container">
        <h1>ÜDVÖZÖLJÜK!</h1>
        <h2>Önnek admin jogosultságai vannak</h2>

  
            
        </div>
    </div>

</body>
</html>