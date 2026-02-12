<?php
session_start();
require_once 'config.php';

// --- OKOS MAGYARÍTOTT ROUTER ---
$request_uri = $_SERVER['REQUEST_URI'];
// Megtisztítjuk az URI-t a perjelektől és a mappaszerkezettől a logikához
$clean_path = str_replace('/clocky/', '', parse_url($request_uri, PHP_URL_PATH));

// Útvonal térkép (Ha véletlenül .php-vel hivatkoznál rá kódban, ez segít)
$routes = [
    'emp.php' => 'dolgozok',
    'index.php' => 'bejelentkezes',
    'archivum.php' => 'archivum'
];

// Ha valaki közvetlenül a .php fájlt akarná elérni, a .htaccess már átirányította, 
// de itt is biztosítjuk a belső konzisztenciát.
$current_page = $routes[basename($_SERVER['PHP_SELF'])] ?? 'archivum';

// --- STÍLUS ÉS ROOT VÁLTOZÓK ---
$clocky_style = "
<style>
    :root {
        --bg-color: #0f0f0f;
        --card-bg: #1a1a1a;
        --accent-color: #00ffe1;
        --text-secondary: #888;
        --danger-color: #ff4757;
        --border-color: #333;
    }
    body { background: var(--bg-color); color: #fff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 40px; margin: 0; }
    .glass-card { background: var(--card-bg); padding: 30px; border-radius: 20px; max-width: 1000px; margin: auto; border: 1px solid var(--border-color); box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
    .custom-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .custom-table td, .custom-table th { padding: 15px; text-align: left; border-bottom: 1px solid #222; }
    .custom-table th { color: var(--text-secondary); font-weight: 500; text-transform: uppercase; font-size: 0.8rem; }
    .btn-restore { 
        color: var(--accent-color); 
        text-decoration: none; 
        border: 1px solid var(--accent-color); 
        padding: 8px 15px; 
        border-radius: 8px; 
        transition: all 0.3s ease;
        display: inline-block;
    }
    .btn-restore:hover { background: var(--accent-color); color: #000; box-shadow: 0 0 15px var(--accent-color); }
    .back-link { color: var(--text-secondary); text-decoration: none; transition: 0.3s; }
    .back-link:hover { color: #fff; }
    .success-msg { color: var(--accent-color); background: rgba(0, 255, 225, 0.1); padding: 10px; border-radius: 8px; margin-bottom: 20px; }
</style>";

// --- JOGOSULTSÁG KEZELÉS ---
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 1) {
    // RESTful átirányítás a bejelentkezéshez
    header('Location: /clocky/bejelentkezes');
    exit();
}

// --- RESTful MŰVELET: VISSZAÁLLÍTÁS ---
if (isset($_GET['restore_id'])) {
    $id = intval($_GET['restore_id']);
    $stmt = $conn->prepare("UPDATE emp SET active = 1 WHERE empID = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        // Sikeres művelet után tiszta URL-re ugrunk
        header('Location: /clocky/archivum?restored=1');
        exit();
    }
}

// ADATOK LEKÉRÉSE
$sql = "SELECT * FROM emp WHERE active = 0 ORDER BY name ASC";
$result = $conn->query($sql);

include_once 'header.php';
echo $clocky_style;
?>

<div class="glass-card">
    <div style="display:flex; justify-content: space-between; align-items:center; margin-bottom: 20px;">
        <h1 style="margin:0;">Archívum</h1>
        <a href="dolgozok" class="back-link">← Vissza a dolgozókhoz</a>
    </div>

    <?php if (isset($_GET['restored'])): ?>
        <div class="success-msg">Munkatárs sikeresen visszaállítva az aktív állományba!</div>
    <?php endif; ?>

    <table class="custom-table">
        <thead>
            <tr>
                <th>Név</th>
                <th>Email</th>
                <th style="text-align: right;">Művelet</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td style="text-align: right;">
                            <a href="archivum/visszaallit/<?= $row['empID'] ?>" class="btn-restore">Visszaállítás</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align: center; color: var(--text-secondary); padding: 30px;">Nincs inaktív dolgozó az archívumban.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>