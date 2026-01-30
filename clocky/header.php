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
            z-index: 1001;
            background: #000;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 20px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        /* Navigációs konténer */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px; /* Menü szélessége */
            height: auto;
            max-height: 0; /* Alaphelyzetben rejtve (összecsukva) */
            background-color: #000000;
            z-index: 1000;
            overflow: hidden;
            transition: max-height 0.4s ease-in-out, padding 0.3s ease;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.3);
            border-bottom-right-radius: 10px;
            padding-top: 60px; /* Hely a gombnak */
        }

        /* Amikor a menü nyitva van */
        nav.open {
            max-height: 500px; /* Elég nagy érték a lenyíláshoz */
            padding-bottom: 20px;
        }

        nav ul {
            list-style: none;
            display: flex;
            flex-direction: column; /* Egymás alá rendezés */
            width: 100%;
            padding: 0 15px;
        }

        nav ul li {
            width: 100%;
            margin-bottom: 8px;
        }

        nav ul li a {
            text-decoration: none;
            color: white;
            font-weight: bold;
            padding: 12px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: #1a1a1a;
            display: block; /* Teljes szélességű kattintható felület */
        }

        /* Aktív és hover effektek */
        nav ul li a:hover,
        nav ul li a.active {
            background: linear-gradient(135deg, #454446 0%, #222 100%);
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0, 255, 183, 0.2);
            color: #00ffb7;
        }

        /* Logout gomb stílusa */
        nav ul li.logout a {
            background: #d32f2f;
            margin-top: 10px;
        }
        nav ul li.logout a:hover {
            background: #b71c1c;
            color: white;
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
        <li class="logout"><a href="logout.php">Kijelentkezés</a></li>
    </ul>
</nav>

<script>
    function toggleMenu() {
        const nav = document.getElementById('mainNav');
        nav.classList.toggle('open');
    }

    // Kattintás a menün kívül bezárja azt
    document.addEventListener('click', function(event) {
        const nav = document.getElementById('mainNav');
        const btn = document.querySelector('.menu-toggle');
        if (!nav.contains(event.target) && !btn.contains(event.target)) {
            nav.classList.remove('open');
        }
    });
</script>

</body>
</html>