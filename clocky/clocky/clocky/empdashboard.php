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

/* ===== 2. ADATOK LEKÉRÉSE ===== */
try {
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

/* ===== 3. AKTÍV MUNKA ÉS SZÜNET ELLENŐRZÉSE ===== */
$active_work = null;
if ($emp_id) {
    $check_work = "SELECT wt_ID, start_datetime, startbreak_time, endbreak_time FROM worktime WHERE FK_empID = ? AND end_datetime IS NULL LIMIT 1";
    $stmt = $conn->prepare($check_work);
    $stmt->bind_param("i", $emp_id);
    $stmt->execute();
    $active_work = $stmt->get_result()->fetch_assoc();
}

/* ===== 4. INDÍTÁS / LEÁLLÍTÁS / SZÜNET KEZELÉSE ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['toggle_work'])) {
            if (!$emp_id && $jogosultsag !== 1) {
                die("Hiba: Nincs érvényes dolgozói profil!");
            }

            if (!$active_work) {
                $insert_sql = "INSERT INTO worktime (FK_empID, FK_roleID, start_datetime) VALUES (?, ?, NOW())";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param("ii", $emp_id, $role_id); 
                $stmt->execute();
            } else {
                $wt_id = (int)$active_work['wt_ID'];
                $update_sql = "UPDATE worktime SET end_datetime = NOW() WHERE wt_ID = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("i", $wt_id);
                $stmt->execute();
            }
        } 
        elseif (isset($_POST['start_break']) && $active_work) {
            $wt_id = (int)$active_work['wt_ID'];
            $update_sql = "UPDATE worktime SET startbreak_time = NOW(), endbreak_time = NULL WHERE wt_ID = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("i", $wt_id);
            $stmt->execute();
        }
        elseif (isset($_POST['end_break']) && $active_work) {
            $wt_id = (int)$active_work['wt_ID'];
            $update_sql = "UPDATE worktime SET endbreak_time = NOW() WHERE wt_ID = ?";
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
            --break-color: #f1c40f;
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
            margin-bottom: 10px;
        }

        .btn-start { background: var(--accent-color); color: #1a1a1a; box-shadow: 0 10px 20px rgba(0, 255, 225, 0.2); }
        .btn-break { background: var(--break-color); color: #1a1a1a; box-shadow: 0 10px 20px rgba(241, 196, 15, 0.2); }
        .btn-stop { background: var(--error-color); color: white; box-shadow: 0 10px 20px rgba(255, 71, 87, 0.2); }

        .btn:hover { transform: translateY(-4px); filter: brightness(1.1); }

        .status-container {
            margin-top: 25px;
            padding: 15px;
            background: rgba(255,255,255,0.03);
            border-radius: 12px;
        }

        .status-dot { 
            display: inline-block; 
            width: 8px; height: 8px; 
            background: var(--accent-color); 
            border-radius: 50%; 
            margin-right: 8px; 
            animation: pulse 1.5s infinite; 
        }
        .status-dot.break { background: var(--break-color); }

        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }

        .timer-display {
            display: block;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--accent-color);
            margin-bottom: 5px;
            font-family: 'Courier New', Courier, monospace;
        }

        .break-timer {
            font-size: 1.1rem;
            color: var(--break-color);
            margin-bottom: 15px;
            font-weight: 600;
        }

        .logout-link { 
            display: inline-flex; align-items: center; justify-content: center;
            margin-top: 35px; padding: 10px 20px; color: #888; 
            text-decoration: none; font-size: 0.85rem; font-weight: 600;
            transition: all 0.3s ease; border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px; background: rgba(255, 255, 255, 0.02);
        }
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
                Nincs a nevedhez rendelt dolgozói profil!
            </div>
        <?php else: ?>
            <form method="POST" id="workForm">
                <?php if (!$active_work): ?>
                    <button type="submit" name="toggle_work" class="btn btn-start">
                        <i class="fas fa-play" style="margin-right: 10px;"></i> Munka indítása
                    </button>
                <?php else: 
                    $on_break = ($active_work['startbreak_time'] !== null && $active_work['endbreak_time'] === null);
                    
                    // PHP-ban kiszámoljuk a timestamp-eket az JS-nek (ezredmásodpercben)
                    $workStartTs = strtotime($active_work['start_datetime']) * 1000;
                    $breakStartTs = $on_break ? strtotime($active_work['startbreak_time']) * 1000 : 0;
                    $serverNowTs = time() * 1000;
                ?>
                    <div id="counter" class="timer-display">00:00:00</div>
                    
                    <?php if ($on_break): ?>
                        <div id="breakCounter" class="break-timer">Szünet: 30:00</div>
                        <button type="submit" name="end_break" class="btn btn-start">
                            <i class="fas fa-play" style="margin-right: 10px;"></i> Szünet vége
                        </button>
                    <?php else: ?>
                        <button type="submit" name="start_break" class="btn btn-break">
                            <i class="fas fa-coffee" style="margin-right: 10px;"></i> Szünet indítása
                        </button>
                        <button type="submit" name="toggle_work" class="btn btn-stop" onclick="return confirm('Biztosan befejezi a munkát?')">
                            <i class="fas fa-stop" style="margin-right: 10px;"></i> Munka leállítása
                        </button>
                    <?php endif; ?>
                    
                    <div class="status-container">
                        <span style="color: <?php echo $on_break ? 'var(--break-color)' : 'var(--accent-color)'; ?>; font-size: 0.85rem; font-weight: 600;">
                            <span class="status-dot <?php echo $on_break ? 'break' : ''; ?>"></span> 
                            <?php echo $on_break ? 'Szüneten vagy' : 'Mérés folyamatban'; ?>
                        </span>
                        <div style="font-size: 0.7rem; color: #666; margin-top: 5px;">
                            Kezdés: <?php echo date("H:i", strtotime($active_work['start_datetime'])); ?>
                        </div>
                    </div>

                    <script>
                        // Biztonságos timestamp átadás
                        const workStart = <?php echo $workStartTs; ?>;
                        const breakStart = <?php echo $breakStartTs; ?>;
                        const serverNow = <?php echo $serverNowTs; ?>;
                        const timeOffset = serverNow - Date.now();

                        function updateTimers() {
                            const now = Date.now() + timeOffset;

                            // 1. Munkaidő számláló
                            const workDiff = now - workStart;
                            if (workDiff > 0) {
                                let h = Math.floor(workDiff / 3600000);
                                let m = Math.floor((workDiff % 3600000) / 60000);
                                let s = Math.floor((workDiff % 60000) / 1000);
                                document.getElementById("counter").innerHTML = 
                                    (h<10?'0':'')+h + ":" + (m<10?'0':'')+m + ":" + (s<10?'0':'')+s;
                            }

                            // 2. Szünet visszaszámláló
                            if (breakStart > 0) {
                                const thirtyMinutes = 30 * 60 * 1000;
                                const breakDiff = (breakStart + thirtyMinutes) - now;
                                
                                let totalSeconds = Math.floor(breakDiff / 1000);
                                let prefix = "Szünet: ";
                                
                                if (totalSeconds < 0) {
                                    totalSeconds = Math.abs(totalSeconds);
                                    prefix = "Túllépés: -";
                                    document.getElementById("breakCounter").style.color = "var(--error-color)";
                                }

                                let bm = Math.floor(totalSeconds / 60);
                                let bs = totalSeconds % 60;
                                document.getElementById("breakCounter").innerHTML = 
                                    prefix + (bm<10?'0':'')+bm + ":" + (bs<10?'0':'')+bs;
                            }
                        }

                        setInterval(updateTimers, 1000);
                        updateTimers();
                    </script>
                <?php endif; ?>
            </form>
        <?php endif; ?>

        <a href="logout.php" class="logout-link">Kijelentkezés</a>
    </div>

    <script>
    const publicKey = 'BDe_6-NlX_Z8Y6vN_E0_v5pM1B9xQ5F1v0X8y2u3v4W5x6y7z8A9b0c1d2e3f4g5h6i7j8k9l0m1n2o3p4q5r6s';
    if ('serviceWorker' in navigator && 'PushManager' in window) {
        navigator.serviceWorker.register('sw.js').then(function(reg) {
            reg.pushManager.getSubscription().then(function(sub) {
                if (sub === null) {
                    reg.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: publicKey
                    }).then(function(newSub) {
                        fetch('save_subscription.php', {
                            method: 'POST',
                            body: JSON.stringify(newSub),
                            headers: { 'Content-Type': 'application/json' }
                        });
                    });
                }
            });
        });
    }
    </script>
</body>
</html>