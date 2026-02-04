<?php
session_start();
require_once 'config.php';

// Hibakeresés bekapcsolása fejlesztés alatt
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* ===== 1. BELÉPÉS ELLENŐRZÉSE ===== */
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

/* ===== 2. ADATOK LEKÉRÉSE (USERS + EMP ÖSSZEKÖTVE) ===== */
try {
    // Lekérjük az empID-t is, mert a worktime táblának valószínűleg erre van szüksége
    $sql = "SELECT u.ID, u.jogosultsag, e.empID, e.FK_roleID 
            FROM users u 
            LEFT JOIN emp e ON u.ID = e.FK_userID 
            WHERE u.ID = ? LIMIT 1";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $userData = $stmt->get_result()->fetch_assoc();

    if ($userData) {
        $jogosultsag = (int)$userData['jogosultsag'];
        $role_id = $userData['FK_roleID']; // Lehet NULL
        $emp_id = $userData['empID'];     // Lehet NULL, ha admin
    } else {
        die("Hiba: Felhasználó nem található!");
    }
} catch (Exception $e) {
    die("Adatbázis hiba: " . $e->getMessage());
}

/* ===== 3. AKTÍV MUNKA ELLENŐRZÉSE ===== */
// Itt az empID alapján keressük a nyitott munkaidőt
$active_work = null;
if ($emp_id) {
    $check_work = "SELECT wt_ID FROM worktime WHERE FK_empID = ? AND end_datetime IS NULL LIMIT 1";
    $stmt = $conn->prepare($check_work);
    $stmt->bind_param("i", $emp_id);
    $stmt->execute();
    $active_work = $stmt->get_result()->fetch_assoc();
}

/* ===== 4. INDÍTÁS / LEÁLLÍTÁS ===== */
if (isset($_POST['toggle_work'])) {
    if (!$emp_id && $jogosultsag !== 1) {
        die("Hiba: Ehhez a felhasználóhoz nincs alkalmazotti profil rendelve!");
    }

    try {
        if (!$active_work) {
            // INDÍTÁS
            $insert_sql = "INSERT INTO worktime (FK_empID, FK_roleID, start_datetime) VALUES (?, ?, NOW())";
            $stmt = $conn->prepare($insert_sql);
            
            // A bind_param-nál az "i" típus akkor is működik, ha az érték null, 
            // feltéve, hogy az adatbázis oszlop engedi a NULL-t.
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
        
        // Átirányítás a jogosultságnak megfelelően
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
    <style>
        body { background: #0f0f0f; font-family: 'Inter', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; color: white; }
        .card { background: #1a1a1a; padding: 40px; border-radius: 24px; text-align: center; width: 320px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 20px 40px rgba(0,0,0,0.6); }
        .btn { width: 100%; padding: 16px; font-size: 16px; cursor: pointer; border: none; border-radius: 12px; font-weight: 800; text-transform: uppercase; transition: 0.3s; margin-top: 10px; }
        .btn-start { background: #00ffe1; color: #1a1a1a; }
        .btn-stop { background: #ff4757; color: white; }
        .btn:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,255,225,0.2); }
        .status-dot { display: inline-block; width: 10px; height: 10px; background: #00ffe1; border-radius: 50%; margin-right: 5px; animation: pulse 1.5s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }
        .info-text { color: #555; font-size: 0.75rem; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="card">
        <h2 style="margin-bottom: 10px;">Szia, <?php echo htmlspecialchars($username); ?>!</h2>
        <p class="info-text">
            Státusz: <?php echo $emp_id ? "Dolgozó (ID: $emp_id)" : "Adminisztrátor"; ?><br>
            Role ID: <?php echo $role_id ?? 'Nincs'; ?>
        </p>

        <?php if (!$emp_id && $jogosultsag !== 1): ?>
            <p style="color: #ff4757;">Nincs hozzárendelt dolgozói profil!</p>
        <?php else: ?>
            <form method="POST">
                <?php if (!$active_work): ?>
                    <button type="submit" name="toggle_work" class="btn btn-start">Munka indítása</button>
                <?php else: ?>
                    <button type="submit" name="toggle_work" class="btn btn-stop">Munka leállítása</button>
                    <div style="margin-top: 20px; color: #00ffe1; font-size: 0.8rem; font-weight: bold;">
                        <span class="status-dot"></span> Mérés folyamatban...
                    </div>
                <?php endif; ?>
            </form>
        <?php endif; ?>

        <a href="logout.php" style="display: block; margin-top: 30px; color: #444; text-decoration: none; font-size: 0.8rem;">Kijelentkezés</a>
    </div>
</body>
</html>