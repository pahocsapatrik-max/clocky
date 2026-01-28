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
            background: linear-gradient(135deg, #226a60ff 0%, #00ffb7ff 100%);
            padding-top: 70px; 
        }

        /* Menü konténer */
        nav {
            width: 100%;
            background-color: #000000ff;
            padding: 12px 20px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(53, 0, 177, 0.2);
            display: flex;
            align-items: center;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 15px;
            width: 100%;
            align-items: center;
        }

        nav ul li {
            display: inline-block;
        }

        nav ul li a {
            text-decoration: none;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: #000000ff;
            display: inline-block;
        }

        /* Aktív és hover effektek */
        nav ul li a:hover,
        nav ul li a.active {
            background: linear-gradient(135deg,  0%, #454446ff 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(195, 0, 255, 0.2);
        }

        /* Logout gomb külön stílus, piros és a szélre tolva */
        nav ul li.logout {
            margin-left: auto; /* a szélére tolja */
        }
        nav ul li.logout a {
            background: #d32f2f;
        }
        nav ul li.logout a:hover {
            background: #b71c1c;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(247, 0, 255, 0.2);
        }
    </style>
</head>
<body>

<nav>
    <ul>
        <li><a href="dashboard.php" class="<?php echo ($page_title=='Dashboard')?'active':''; ?>">Kezdőlap</a></li>
        <li><a href="emp.php" class="<?php echo ($page_title=='Employees')?'active':''; ?>">Dolgozók</a></li>
        <li><a href="roles.php" class="<?php echo ($page_title=='Roles')?'active':''; ?>">Munkakörök</a></li>
        <li><a href="add_emp.php" class="<?php echo ($page_title=='addemp')?'active':''; ?>">Dolgozók hozzáadás</a></li>
        <li><a href="add_role.php" class="<?php echo ($page_title=='addrole')?'active':''; ?>">Munkakör Hozzáadása</a></li>
        <li class="logout"><a href="logout.php">Kijelentkezés</a></li>
    </ul>
</nav>
