<?php
session_start();
require_once 'config.php';

// Hibakeresés bekapcsolása
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* ===== 1. BELÉPÉS ELLENŐRZÉSE ===== */
// Itt a 'jogosultsag' mezőt nézzük a belépéshez (0 = user)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] != 0) {
    header('Location: index.php');
    exit;
}

$username = $_SESSION['username'];

/* ===== 2. ADATOK ELŐKÉSZÍTÉSE ===== */
try {
    // Lekérjük a USERS táblából az ID-t és a MUNKAKÖR AZONOSÍTÓT (FK_roleID)
    $user_sql = "SELECT ID, FK_roleID FROM USERS WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($user_sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $userData = $stmt->get_result()->fetch_assoc();

    if (!$userData) {
        die("Hiba: Felhasználó nem található!");
    }

    $user_id = (int)$userData['ID'];
    $role_id = $userData['FK_roleID']; // Ez a munkakör ID-ja

    // Ellenőrizzük, hogy van-e munkaköre (ne legyen NULL)
    if (is_null($role_id)) {
        die("Hiba: Nincs munkakör (FK_roleID) beállítva a felhasználóhoz a USERS táblában!");
    }

} catch (Exception $e) {
    die("Hiba az adatok beolvasásakor: " . $e->getMessage());
}

/* ===== 3. AKTÍV MUNKA ELLENŐRZÉSE ===== */
$check_work = "SELECT wt_ID FROM worktime WHERE FK_empID = ? AND end_datetime IS NULL LIMIT 1";
$stmt = $conn->prepare($check_work);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$active_work = $stmt->get_result()->fetch_assoc();

/* ===== 4. INDÍTÁS / LEÁLLÍTÁS KEZELÉSE ===== */
if (isset($_POST['toggle_work'])) {
    try {
        if (!$active_work) {
            // MUNKA INDÍTÁSA
            $insert_sql = "INSERT INTO worktime (FK_empID, FK_roleID, start_datetime) VALUES (?, ?, NOW())";
            $stmt = $conn->prepare($insert_sql);
            // Itt adjuk át a két külön azonosítót
            $stmt->bind_param("ii", $user_id, $role_id); 
            $stmt->execute();
        } else {
            // MUNKA LEÁLLÍTÁSA
            $wt_id = (int)$active_work['wt_ID'];
            $update_sql = "UPDATE worktime SET end_datetime = NOW() WHERE wt_ID = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("i", $wt_id);
            $stmt->execute();
        }
        header("Location: empdashboard.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        die("Adatbázis hiba mentéskor: " . $e->getMessage() . " (Próbált RoleID: $role_id)");
    }
}

/* ===== 5. LOGOUT ===== */
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Dolgozói Dashboard</title>
    <style>
        body { background: #00ffe1; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: #1a1a1a; color: white; padding: 40px; border-radius: 20px; text-align: center; width: 350px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .btn { width: 100%; padding: 15px; font-size: 18px; cursor: pointer; border: none; border-radius: 10px; font-weight: bold; text-transform: uppercase; transition: 0.3s; }
        .btn-start { background: #00ffe1; color: #1a1a1a; }
        .btn-stop { background: #ff4444; color: white; }
        .btn:hover { opacity: 0.8; transform: scale(1.02); }
        .status { margin-top: 20px; color: #00ffe1; font-weight: bold; }
    </style>
</head>
<body>

    <div class="card">
        <h1 style="color: #00ffe1; margin-bottom: 10px;">Szia, <?php echo htmlspecialchars($username); ?>!</h1>
        <p style="color: #888; margin-bottom: 30px;">
            User ID: <?php echo $user_id; ?> | Munkakör ID: <?php echo $role_id; ?>
        </p>

        <form method="POST">
            <?php if (!$active_work): ?>
                <button type="submit" name="toggle_work" class="btn btn-start">Munka indítása</button>
            <?php else: ?>
                <button type="submit" name="toggle_work" class="btn btn-stop">Munka leállítása</button>
                <div class="status">● Mérés folyamatban...</div>
            <?php endif; ?>
        </form>

        <a href="?logout=1" style="display: block; margin-top: 25px; color: #444; text-decoration: none;">Kijelentkezés</a>
    </div>

</body>
</html>