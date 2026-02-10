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
            overflow-x: hidden;
        }

        /* --- SZEKCIÓ VÁLTÁS ANIMÁCIÓ --- */
        .page-transition {
            animation: smoothAppear 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
        }

        @keyframes smoothAppear {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        /* ------------------------------- */

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

        .brand-logo {
            font-weight: 800;
            font-size: 24px;
            letter-spacing: -1px;
            color: #fff;
            text-transform: lowercase;
        }

        .brand-logo span {
            color: #00ffe1;
        }

        nav {
            position: fixed;
            top: 10px;
            bottom: 10px;
            left: -320px;
            width: 280px; 
            background: linear-gradient(145deg, #161616 0%, #0a0a0a 100%);
            z-index: 1050;
            overflow-y: auto;
            transition: left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 20px 0 40px rgba(0, 0, 0, 0.7);
            padding-top: 80px;
            border-radius: 0 24px 24px 0;
            border-right: 1px solid rgba(0, 255, 225, 0.1);
        }

        nav.open {
            left: 0;
        }

        nav ul {
            list-style: none;
            padding: 0 15px;
        }

        nav ul li {
            margin-bottom: 5px;
        }

        nav ul li a {
            text-decoration: none;
            color: #aaa;
            font-weight: 800;
            font-size: 14px;
            text-transform: lowercase;
            letter-spacing: -0.3px;
            padding: 16px 20px;
            border-radius: 16px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        nav ul li a i {
            font-size: 18px;
            width: 25px;
            text-align: center;
        }

        nav ul li a:hover,
        nav ul li a.active {
            background: rgba(0, 255, 225, 0.08);
            color: #00ffe1;
            transform: translateX(8px);
        }

        nav ul li.logout {
            margin-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.05);
            padding-top: 15px;
        }
        
        nav ul li.logout a {
            color: #ff4757;
        }
        
        nav ul li.logout a:hover {
            background: rgba(255, 71, 87, 0.08);
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(6px);
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
<body class="page-transition"> <div id="overlay" class="overlay" onclick="toggleMenu()"></div>

<div class="header-top">
    <button class="menu-toggle" onclick="toggleMenu()">
        <div class="hamburger-lines">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
        <span>menü</span>
    </button>
    <div class="brand-logo">clocky<span>.</span></div>
</div>

<nav id="mainNav">
    <ul>
        <li><a href="dashboard.php" class="<?php echo (isset($page_title) && $page_title=='Dashboard')?'active':''; ?>"><i class="fas fa-home"></i> kezdőlap</a></li>
        <li><a href="emp.php" class="<?php echo (isset($page_title) && $page_title=='Employees')?'active':''; ?>"><i class="fas fa-users"></i> dolgozók</a></li>
        <li><a href="roles.php" class="<?php echo (isset($page_title) && $page_title=='Roles')?'active':''; ?>"><i class="fas fa-briefcase"></i> munkakörök</a></li>
        <li><a href="add_emp.php" class="<?php echo (isset($page_title) && $page_title=='addemp')?'active':''; ?>"><i class="fas fa-user-plus"></i> dolgozó hozzáadása</a></li>
        <li><a href="add_role.php" class="<?php echo (isset($page_title) && $page_title=='addrole')?'active':''; ?>"><i class="fas fa-plus-square"></i> munkakör hozzáadása</a></li>
        <li><a href="archivum.php" class="<?php echo (isset($page_title) && $page_title=='archivum')?'active':''; ?>"><i class="fas fa-archive"></i> archívum</a></li>
        <li><a href="list.php" class="<?php echo (isset($page_title) && $page_title=='lista')?'active':''; ?>"><i class="fas fa-list"></i> lista</a></li>
        <li class="logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> kijelentkezés</a></li>
    </ul>
</nav>

<script>
    function toggleMenu() {
        const nav = document.getElementById('mainNav');
        const overlay = document.getElementById('overlay');
        nav.classList.toggle('open');
        overlay.classList.toggle('active');
    }

    // Opcionális: Ha szeretnéd, hogy kattintáskor is legyen egy kis "kijelentkező" animáció
    document.querySelectorAll('nav a').forEach(link => {
        link.addEventListener('click', function(e) {
            if (!this.href.includes('#') && !this.target) {
                document.body.style.opacity = '0';
                document.body.style.transition = 'opacity 0.3s ease';
            }
        });
    });
</script>
</body>
</html>