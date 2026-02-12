<?php
session_start();
require_once 'config.php';

// JOGOSULTSÁG ELLENŐRZÉSE
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 1) {
    header('Location: bejelentkezes'); // A .htaccess miatt mehet a szép név
    exit();
}

// RESTful VISSZAÁLLÍTÁS LOGIKA
// A .htaccess a háttérben ide rakja a számot: ?restore_id=X
if (isset($_GET['restore_id'])) {
    $id = intval($_GET['restore_id']);
    $stmt = $conn->prepare("UPDATE emp SET active = 1 WHERE empID = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Visszadobjuk a sima listára egy sikeres jelzéssel
        header('Location: /clocky/archivum?restored=1');
        exit();
    }
}

// ADATOK LEKÉRÉSE
$sql = "SELECT * FROM emp WHERE active = 0 ORDER BY name ASC";
$result = $conn->query($sql);

include_once 'header.php';
?>

<style>
    :root {
        --bg-color: #0f0f0f;
        --card-bg: #1a1a1a;
        --accent-color: #00ffe1;
        --text-secondary: #888;
        --danger-color: #ff4757;
    }
    body { background: var(--bg-color); color: #fff; font-family: sans-serif; padding: 40px; }
    .glass-card { background: var(--card-bg); padding: 30px; border-radius: 20px; max-width: 1000px; margin: auto; border: 1px solid #333; }
    .custom-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .custom-table td, .custom-table th { padding: 15px; text-align: left; border-bottom: 1px solid #222; }
    .btn-restore { 
        color: var(--accent-color); 
        text-decoration: none; 
        border: 1px solid var(--accent-color); 
        padding: 8px 15px; 
        border-radius: 8px; 
        transition: 0.3s;
    }
    .btn-restore:hover { background: var(--accent-color); color: #000; }
</style>

<div class="glass-card">
    <div style="display:flex; justify-content: space-between; align-items:center;">
        <h1>Archívum</h1>
        <a href="dolgozok" style="color: var(--text-secondary); text-decoration: none;">← Vissza</a>
    </div>

    <?php if (isset($_GET['restored'])): ?>
        <p style="color: var(--accent-color); font-weight: bold;">Sikeres visszaállítás!</p>
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
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td style="text-align: right;">
                        <a href="archivum/visszaallit/<?= $row['empID'] ?>" class="btn-restore">Visszaállítás</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>