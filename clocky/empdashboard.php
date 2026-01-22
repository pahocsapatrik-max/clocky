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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Felhasználói Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #8300fd;
            font-family: 'Roboto', sans-serif;
            color: white;
            text-align: center;
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        h1 {
            font-size: 50px;
            margin-bottom: 10px;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.3);
        }

        h2 {
            font-size: 24px;
            font-weight: 300;
            opacity: 0.9;
            margin-bottom: 30px;
        }

        /* --- KIJELENTKEZÉS GOMB DESIGN --- */
        .logout-form {
            list-style: none;
            padding: 0;
        }

        .logout-btn {
            background: #ff0000ff;
            color: #ffffffff;
            border: none;
            padding: 12px 35px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .logout-btn:hover {
            background: #f0f0f0;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }

        .logout-btn:active {
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>ÜDVÖZÖLJÜK!</h1>
        <h2>Önnek sima jogosultságai vannak</h2>

        <form method="POST" class="logout-form">
            <button type="submit" name="logout" class="logout-btn">Kijelentkezés</button>
        </form>
    </div>

</body>
</html>