<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : "Clocky"; ?></title>
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #226a60 0%, #00ffb7 100%);
            padding-top: 20px; 
            min-height: 100vh;
        }

        /* Menü gomb (Hamburger) */
        .menu-toggle {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100; /* Magasabb, mint a nav */
            background: #000;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .menu-toggle:hover {
            background: #222;
        }

        /* Navigációs konténer - Oldalsáv stílus */
        nav {
            position: fixed;
            top: 0;
            left: -280px; /* Alaphelyzetben teljesen kint van balra */
            width: 280px; 
            height: 100vh; /* Teljes magasság */
            background-color: #000000;
            z-index: 1000;
            overflow-y: auto; /* Ha túl sok a menüpont, görgethető legyen */
            transition: left 0.4s cubic-bezier(0.05, 0.74, 0.2, 0.99); /* Rugalmasabb mozgás */
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.5);
            padding-top: 80px; /* Hely a gombnak felül */
        }

        /* Amikor a menü nyitva van */
        nav.open {
            left: 0; /* Beúszik a képernyőre */
        }

        nav ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            width: 100%;
            padding: 0 20px;
        }

        nav ul li {
            width: 100%;
            margin-bottom: 10px;
        }

        nav ul li a {
            text-decoration: none;
            color: #ccc;
            font-weight: bold;
            padding: 14px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: #111;
            display: block;
        }

        /* Aktív és hover effektek */
        nav ul li a:hover,
        nav ul li a.active {
            background: linear-gradient(135deg, #333 0%, #222 100%);
            transform: translateX(8px);
            color: #00ffb7;
            box-shadow: -5px 0 0 #00ffb7; /* Kis dekor csík az oldalán */
        }

        /* Logout gomb stílusa a lista alján */
        nav ul li.logout {
            margin-top: auto; /* Lehetőség szerint lejjebb tolja */
            padding-top: 20px;
            padding-bottom: 30px;
        }
        
        nav ul li.logout a {
            background: #441111;
            color: #ff8888;
        }
        
        nav ul li.logout a:hover {
            background: #d32f2f;
            color: white;
        }

        /* Sötétítő réteg a tartalom felett, amikor nyitva a menü */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        .overlay.active {
            display: block;
        }

        /* Hamburger ikon vonalak */
        .bar {
            display: block;
            width: 20px;
            height: 2px;
            background-color: white;
            margin: 4px 0;
            transition: 0.4s;
        }
    </style>
</head>
<body>

<div id="overlay" class="overlay" onclick="toggleMenu()"></div>

<button class="menu-toggle" onclick="toggleMenu()">
    <div>
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </div>
    <span>Menü</span>
</button>

<nav id="mainNav">
    <ul>
        <li><a href="dashboard.php" class="<?php echo ($page_title=='Dashboard')?'active':''; ?>">Kezdőlap</a></li>
        <li><a href="emp.php" class="<?php echo ($page_title=='Employees')?'active':''; ?>">Dolgozók</a></li>
        <li><a href="roles.php" class="<?php echo ($page_title=='Roles')?'active':''; ?>">Munkakörök</a></li>
        <li><a href="add_emp.php" class="<?php echo ($page_title=='addemp')?'active':''; ?>">Dolgozó hozzáadása</a></li>
        <li><a href="add_role.php" class="<?php echo ($page_title=='addrole')?'active':''; ?>">Munkakör hozzáadása</a></li>
        <li><a href="archivum.php" class="<?php echo ($page_title=='archivum')?'active':''; ?>">Archívum</a></li>
        <li><a href="list.php" class="<?php echo ($page_title=='lista')?'active':''; ?>">lista</a></li>
        <li class="logout"><a href="logout.php">Kijelentkezés</a></li>
    </ul>
</nav>

<script>
    function toggleMenu() {
        const nav = document.getElementById('mainNav');
        const overlay = document.getElementById('overlay');
        nav.classList.toggle('open');
        overlay.classList.toggle('active');
    }
</script>

</body>
</html>