<?php
// 1. ADATBÁZIS KAPCSOLAT ÉS HEADER
include 'config.php'; // Feltételezve, hogy a config.php-ban van a kapcsolat

// Jogosultság ellenőrzés (opcionális, de ajánlott)
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 1) {
    header('Location: index.php');
    exit();
}

// 2. VISSZAÁLLÍTÁS KEZELÉSE
$message = "";
if (isset($_GET['restore_id'])) {
    $id = intval($_GET['restore_id']);
    if ($conn->query("UPDATE emp SET active = 1 WHERE empID = $id")) {
        $message = "<div class='alert success'>Dolgozó sikeresen visszaállítva az aktív állományba!</div>";
    }
}

// 3. ARCHIVÁLT DOLGOZÓK LEKÉRDEZÉSE (active = 0)
$sql = "SELECT * FROM emp WHERE active = 0 ORDER BY name ASC";
$result = $conn->query($sql);

include 'header.php';
?>

<style>
    :root {
        --bg-color: #0f0f0f;
        --card-bg: #1a1a1a;
        --accent-color: #00ffe1;
        --text-secondary: #888;
        --danger-color: #ff4757;
    }

    body {
        background: radial-gradient(circle at top right, #1e1e1e, var(--bg-color));
        color: #fff;
        font-family: 'Inter', sans-serif;
        padding: 40px 20px;
    }

    .glass-card {
        background: var(--card-bg);
        padding: 40px;
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
        max-width: 1100px;
        margin: 0 auto;
    }

    .header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    h1 {
        font-weight: 800;
        letter-spacing: -1px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    h1 i { color: var(--text-secondary); }

    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .custom-table th {
        color: var(--text-secondary);
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 15px;
        text-align: left;
    }

    .custom-table td {
        background: rgba(255, 255, 255, 0.02);
        padding: 15px;
        border: none;
    }

    .custom-table tr td:first-child { border-radius: 12px 0 0 12px; }
    .custom-table tr td:last-child { border-radius: 0 12px 12px 0; }

    .btn-restore {
        background: transparent;
        color: var(--accent-color);
        border: 1px solid var(--accent-color);
        padding: 8px 16px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: bold;
        transition: 0.3s;
    }

    .btn-restore:hover {
        background: var(--accent-color);
        color: #000;
        box-shadow: 0 0 15px rgba(0, 255, 225, 0.4);
    }

    .alert {
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 25px;
        text-align: center;
        font-weight: bold;
    }

    .success {
        background: rgba(0, 255, 225, 0.1);
        color: var(--accent-color);
        border: 1px solid var(--accent-color);
    }

    .empty-state {
        text-align: center;
        padding: 60px;
        color: var(--text-secondary);
    }

    .empty-state i {
        font-size: 3rem;
        display: block;
        margin-bottom: 20px;
        opacity: 0.3;
    }
</style>

<div class="glass-card">
    <div class="header-flex">
        <h1><i class="fas fa-archive"></i> Archivált Dolgozók</h1>
        <a href="emp.php" style="color: var(--text-secondary); text-decoration: none; font-size: 0.9rem;">
            <i class="fas fa-arrow-left"></i> Vissza az aktívakhoz
        </a>
    </div>

    <?= $message ?>

    <div style="overflow-x: auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Név</th>
                    <th>Email</th>
                    <th>Születési dátum</th>
                    <th style="text-align: right;">Műveletek</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                            <td style="color: var(--text-secondary);"><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= $row['dob'] ?></td>
                            <td style="text-align: right;">
                                <a href="?restore_id=<?= $row['empID'] ?>" class="btn-restore" onclick="return confirm('Biztosan visszaállítja ezt a dolgozót?')">
                                    Visszaállítás
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <i class="fas fa-folder-open"></i>
                                Nincsenek archivált dolgozók a rendszerben.
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $conn->close(); ?>