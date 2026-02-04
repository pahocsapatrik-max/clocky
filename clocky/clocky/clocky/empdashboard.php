<?php
session_start();
require_once 'config.php';

// Hibakeresés bekapcsolása
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* ===== 1. BELÉPÉS ELLENŐRZÉSE ===== */
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

/* ===== 2. ADATOK LEKÉRÉSE (Linkelés a NÉV alapján) ===== */
try {
    // Mivel nincs FK_userID, a name és a FK_roleID alapján kapcsoljuk össze a két táblát
    $sql = "SELECT u.ID, u.jogosultsag, u.name, e.empID, u.FK_roleID 
            FROM users u 
            LEFT JOIN emp e ON u.name = e.name 
            WHERE u.ID = ? LIMIT 1";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $userData = $stmt->get_result()->fetch_assoc();

    if ($userData) {
        $jogosultsag = (int)$userData['jogosultsag'];
        $role_id = $userData['FK_roleID']; 
        $emp_id = $userData['empID'];     
        $full_name = $userData['name'];
    } else {
        die("Hiba: Felhasználó nem található!");
    }
} catch (Exception $e) {
    die("Adatbázis hiba: " . $e->getMessage());
}

/* ===== 3. AKTÍV MUNKA ELLENŐRZÉSE ===== */
$active_work = null;
if ($emp_id) {
    $check_work = "SELECT wt_ID, start_datetime FROM worktime WHERE FK_empID = ? AND end_datetime IS NULL LIMIT 1";
    $stmt = $conn->prepare($check_work);
    $stmt->bind_param("i", $emp_id);
    $stmt->execute();
    $active_work = $stmt->get_result()->fetch_assoc();
}

/* ===== 4. INDÍTÁS / LEÁLLÍTÁS ===== */
if (isset($_POST['toggle_work'])) {
    if (!$emp_id && $jogosultsag !== 1) {
        die("Hiba: Nincs érvényes dolgozói profil ehhez a névhez: " . htmlspecialchars($full_name));
    }

    try {
        if (!$active_work) {
            // INDÍTÁS
            $insert_sql = "INSERT INTO worktime (FK_empID, FK_roleID, start_datetime) VALUES (?, ?, NOW())";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("ii", $emp_id, $role_id); 
            $stmt->execute();
        } else {
            // LEÁLLÍTÁS
            $wt_id = (int)$active_work['wt_ID'];
            $update_sql = "UPDATE worktime SET end_datetime = NOW() WHERE wt_ID = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("i", $wt_id);
            $stmt->execute();
        }
        
        $redirect = ($jogosultsag === 1) ? "dashboard.php" : "empdashboard.php";
        header("Location: $redirect");
        exit();
    } catch (Exception $e) {
        die("Hiba a mentés során: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clocky - Time Tracker</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --accent-color: #00ffe1;
            --error-color: #ff4757;
            --card-bg: #1a1a1a;
            --text-color: #ffffff;
        }

        body { 
            background: radial-gradient(circle at top right, #1e1e1e, #0f0f0f);
            font-family: 'Inter', sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
            color: var(--text-color); 
        }

        .glass-card { 
            background: var(--card-bg); 
            padding: 40px; 
            border-radius: 24px; 
            text-align: center; 
            width: 100%;
            max-width: 350px; 
            border: 1px solid rgba(255,255,255,0.05); 
            box-shadow: 0 25px 50px rgba(0,0,0,0.5); 
        }

        h2 { font-weight: 800; margin-bottom: 5px; letter-spacing: -1px; }

        .info-text { 
            color: #888; 
            font-size: 0.8rem; 
            margin-bottom: 35px; 
            line-height: 1.6;
        }

        .btn { 
            width: 100%; 
            padding: 18px; 
            font-size: 1rem; 
            cursor: pointer; 
            border: none; 
            border-radius: 15px; 
            font-weight: 800; 
            text-transform: uppercase; 
            transition: 0.3s; 
            letter-spacing: 1px;
        }

        .btn-start { 
            background: var(--accent-color); 
            color: #1a1a1a; 
            box-shadow: 0 10px 20px rgba(0, 255, 225, 0.2);
        }

        .btn-stop { 
            background: var(--error-color); 
            color: white; 
            box-shadow: 0 10px 20px rgba(255, 71, 87, 0.2);
        }

        .btn:hover { 
            transform: translateY(-4px); 
            filter: brightness(1.1);
        }

        .status-container {
            margin-top: 25px;
            padding: 15px;
            background: rgba(255,255,255,0.03);
            border-radius: 12px;
        }

        .status-dot { 
            display: inline-block; 
            width: 8px; 
            height: 8px; 
            background: var(--accent-color); 
            border-radius: 50%; 
            margin-right: 8px; 
            animation: pulse 1.5s infinite; 
        }

        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }

        .logout-link { 
            display: block; 
            margin-top: 30px; 
            color: #555; 
            text-decoration: none; 
            font-size: 0.8rem; 
            transition: 0.3s;
        }

        .logout-link:hover { color: var(--accent-color); }
    </style>
</head>
<body>
    <div class="glass-card">
        <i class="fas fa-clock" style="font-size: 2rem; color: var(--accent-color); margin-bottom: 20px;"></i>
        <h2>Szia, <?php echo htmlspecialchars($username); ?>!</h2>
        
        <div class="info-text">
            <?php echo $emp_id ? "Azonosító: #$emp_id" : "Adminisztrátor"; ?><br>
            Pozíció kód: <?php echo $role_id ?? 'Nincs megadva'; ?>
        </div>

        <?php if (!$emp_id && $jogosultsag !== 1): ?>
            <div style="background: rgba(255,71,87,0.1); color: var(--error-color); padding: 15px; border-radius: 12px; font-size: 0.85rem;">
                <i class="fas fa-exclamation-triangle"></i><br>
                Nincs a nevedhez rendelt dolgozói profil az adatbázisban!
            </div>
        <?php else: ?>
            <form method="POST">
                <?php if (!$active_work): ?>
                    <button type="submit" name="toggle_work" class="btn btn-start">
                        <i class="fas fa-play" style="margin-right: 10px;"></i> Munka indítása
                    </button>
                <?php else: ?>
                    <button type="submit" name="toggle_work" class="btn btn-stop">
                        <i class="fas fa-stop" style="margin-right: 10px;"></i> Munka leállítása
                    </button>
                    
                    <div class="status-container">
                        <span style="color: var(--accent-color); font-size: 0.85rem; font-weight: 600;">
                            <span class="status-dot"></span> Mérés folyamatban
                        </span>
                        <div style="font-size: 0.7rem; color: #666; margin-top: 5px;">
                            Kezdés: <?php echo date("H:i", strtotime($active_work['start_datetime'])); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        <?php endif; ?>

        <a href="logout.php" class="logout-link">Kijelentkezés</a>
    </div>
</body>
</html>