<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : "Clocky | Munkaidő"; ?></title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">

    <style>
        /* Reset & Alapok */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #0f0f0f;
            color: #fff;
            min-height: 100vh;
            padding-top: 75px; 
        }

        /* Menü gomb (Hamburger) és Logo konténer */
        .header-top {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .menu-toggle {
            background: #1a1a1a;
            color: white;
            border: 1px solid rgba(255,255,255,0.1);
            padding: 10px 18px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            transition: all 0.3s ease;
        }

        .menu-toggle:hover {
            background: #00ffe1;
            color: #000;
            transform: translateY(-2px);
        }

        /* Bejelentkezés dizájnát idéző Logo */
        .brand-logo {
            font-weight: 800;
            font-size: 24px;
            letter-spacing: -1px;
            color: #fff;
            text-transform: lowercase; /* Ha a login oldalon kisbetűs */
        }

        .brand-logo span {
            color: #00ffe1;
        }

        /* Navigációs konténer */
        nav {
            position: fixed;
            top: 0;
            left: -300px;
            width: 300px; 
            height: 100vh;
            background-color: #161616;
            z-index: 1050;
            overflow-y: auto;
            transition: left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 10px 0 30px rgba(0, 0, 0, 0.5);
            padding-top: 90px;
        }

        nav.open {
            left: 0;
        }

        nav ul {
            list-style: none;
            padding: 0 20px;
        }

        nav ul li {
            margin-bottom: 8px;
        }

        nav ul li a {
            text-decoration: none;
            color: #888;
            font-weight: 600;
            padding: 14px 20px;
            border-radius: 12px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        nav ul li a:hover,
        nav ul li a.active {
            background: rgba(0, 255, 225, 0.1);
            color: #00ffe1;
            transform: translateX(5px);
        }

        /* Logout gomb */
        nav ul li.logout {
            margin-top: 40px;
            border-top: 1px solid rgba(255,255,255,0.05);
            padding-top: 20px;
        }
        
        nav ul li.logout a {
            color: #ff4757;
        }
        
        nav ul li.logout a:hover {
            background: rgba(255, 71, 87, 0.1);
        }

        /* Sötétítő réteg */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(4px);
            z-index: 1000;
        }

        .overlay.active {
            display: block;
        }

        .hamburger-lines {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .bar {
            width: 18px;
            height: 2px;
            background-color: currentColor;
            border-radius: 2px;
        }
    </style>
</head>
<body>

<div id="overlay" class="overlay" onclick="toggleMenu()"></div>

<div class="header-top">
    <button class="menu-toggle" onclick="toggleMenu()">
        <div class="hamburger-lines">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
        <span>Menü</span>
    </button>
    <div class="brand-logo">clocky<span>.</span></div>
</div>

<nav id="mainNav">
    <ul>
        <li><a href="dashboard.php" class="<?php echo (isset($page_title) && $page_title=='Dashboard')?'active':''; ?>"><i class="fas fa-home"></i> Kezdőlap</a></li>
        <li><a href="emp.php" class="<?php echo (isset($page_title) && $page_title=='Employees')?'active':''; ?>"><i class="fas fa-users"></i> Dolgozók</a></li>
        <li><a href="roles.php" class="<?php echo (isset($page_title) && $page_title=='Roles')?'active':''; ?>"><i class="fas fa-briefcase"></i> Munkakörök</a></li>
        <li><a href="add_emp.php" class="<?php echo (isset($page_title) && $page_title=='addemp')?'active':''; ?>"><i class="fas fa-user-plus"></i> Dolgozó hozzáadása</a></li>
        <li><a href="add_role.php" class="<?php echo (isset($page_title) && $page_title=='addrole')?'active':''; ?>"><i class="fas fa-plus-square"></i> Munkakör hozzáadása</a></li>
        <li><a href="archivum.php" class="<?php echo (isset($page_title) && $page_title=='archivum')?'active':''; ?>"><i class="fas fa-archive"></i> Archívum</a></li>
        <li><a href="list.php" class="<?php echo (isset($page_title) && $page_title=='lista')?'active':''; ?>"><i class="fas fa-list"></i> Lista</a></li>
        <li class="logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Kijelentkezés</a></li>
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