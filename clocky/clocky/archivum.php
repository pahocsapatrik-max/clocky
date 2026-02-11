<?php
session_start();
require_once 'config.php';

// --- URL TISZTÍTÁS ÉS ÁTIRÁNYÍTÁS ---
$request_uri = $_SERVER['REQUEST_URI'];
// Ha valaki véletlenül mégis .php-val írná be, átvisszük a tiszta névre
if (strpos($request_uri, '.php') !== false && !isset($_GET['api'])) {
    $clean_uri = str_replace('.php', '', $request_uri);
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: " . $clean_uri);
    exit();
}

// --- RESTful API LOGIKA (BACKEND) ---
if (isset($_GET['restore_id'])) {
    $id = intval($_GET['restore_id']);
    
    if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 1) {
        if (isset($_GET['api'])) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Nincs jogosultsága.']);
            exit();
        }
        header('Location: bejelentkezes'); // .php mentes link
        exit();
    }

    $stmt = $conn->prepare("UPDATE emp SET active = 1 WHERE empID = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if (isset($_GET['api'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Dolgozó visszaállítva.']);
            exit();
        }
        // Visszairányítás a PHP-mentes 'archivum' oldalra
        header('Location: archivum?restored=1');
        exit();
    }
}

// Jogosultság ellenőrzése
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 1) {
    header('Location: bejelentkezes');
    exit();
}

$sql = "SELECT * FROM emp WHERE active = 0 ORDER BY name ASC";
$result = $conn->query($sql);

$page_title = 'Archívum - Clocky';
include_once 'header.php';
?>

<style>
    :root {
        --bg-color: #0f0f0f;
        --card-bg: #1a1a1a;
        --accent-color: #00ffe1;
        --text-secondary: #888;
    }

    body {
        background: radial-gradient(circle at top right, #1e1e1e, var(--bg-color));
        color: #fff;
        font-family: 'Inter', sans-serif;
        padding: 40px 20px;
        margin: 0;
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
        font-weight: 900;
        letter-spacing: -1.5px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 2.2rem;
    }

    h1 i { color: var(--accent-color); }

    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px;
    }

    .custom-table th {
        color: var(--text-secondary);
        text-transform: uppercase;
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 1px;
        padding: 10px 20px;
    }

    .custom-table td {
        background: rgba(255, 255, 255, 0.02);
        padding: 20px;
    }

    .custom-table tr td:first-child { border-radius: 16px 0 0 16px; }
    .custom-table tr td:last-child { border-radius: 0 16px 16px 0; }

    .btn-restore {
        background: rgba(0, 255, 225, 0.1);
        color: var(--accent-color);
        border: 1px solid rgba(0, 255, 225, 0.3);
        padding: 10px 20px;
        border-radius: 12px;
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        transition: 0.3s;
    }

    .btn-restore:hover {
        background: var(--accent-color);
        color: #000;
        box-shadow: 0 0 20px rgba(0, 255, 225, 0.4);
    }

    .alert-success {
        background: rgba(0, 255, 225, 0.1);
        color: var(--accent-color);
        padding: 15px 25px;
        border-radius: 16px;
        border: 1px solid rgba(0, 255, 225, 0.2);
        margin-bottom: 30px;
    }

    .back-link {
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>

<div class="glass-card">
    <div class="header-flex">
        <h1><i class="fas fa-box-archive"></i> Archivum</h1>
        <a href="dolgozok" class="back-link"> <i class="fas fa-chevron-left"></i> Vissza
        </a>
    </div>

    <?php if (isset($_GET['restored'])): ?>
        <div class="alert-success">
            <i class="fas fa-check-circle"></i> Dolgozó sikeresen visszaállítva!
        </div>
    <?php endif; ?>

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
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= $row['dob'] ?></td>
                            <td style="text-align: right;">
                                <a href="?restore_id=<?= $row['empID'] ?>" class="btn-restore" onclick="return confirm('Visszaállítja?')">
                                    Visszaállítás
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 50px; color: var(--text-secondary);">
                            Az archívum üres.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
$conn->close(); 
?>