<?php
session_start();
require_once 'config.php';

// --- OKOS MAGYARÍTOTT ROUTER ---
$request_uri = $_SERVER['REQUEST_URI'];
$current_file = basename($_SERVER['PHP_SELF']);

// Útvonal térkép
$routes = [
    'add_role.php' => 'uj-munkakor',
    'index.php'    => 'bejelentkezes'
];

// Ha .php kiterjesztéssel hívják meg, átirányítjuk a magyarosított aliasra
if (strpos($request_uri, '.php') !== false && !isset($_GET['api'])) {
    $pretty_name = $routes[$current_file] ?? 'uj-munkakor';
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: " . $pretty_name);
    exit();
}
// --- ROUTER VÉGE ---

// --- RESTful API LOGIKA (BACKEND) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['api'])) {
    header('Content-Type: application/json');

    // Jogosultság ellenőrzése
    if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 1) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Nincs jogosultsága a művelethez.']);
        exit();
    }

    $role_name = trim($_POST['role_name'] ?? '');
    $pph_huf = trim($_POST['pph_huf'] ?? '');

    if (empty($role_name) || empty($pph_huf)) {
        echo json_encode(['status' => 'error', 'message' => 'Minden mezőt ki kell tölteni!']);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO role (role_name, pph_huf) VALUES (?, ?)");
    $stmt->bind_param("si", $role_name, $pph_huf);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Sikeresen hozzáadva: ' . htmlspecialchars($role_name)
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Adatbázis hiba történt.']);
    }
    $stmt->close();
    exit();
}

// --- MEGJELENÍTÉS (FRONTEND) ---
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 1) {
    header('Location: bejelentkezes'); 
    exit();
}

$page_title = 'Új munkakör hozzáadása';
include_once 'header.php';
?>

<style>
    :root {
        --accent-color: #00ffe1;
        --card-bg: #1a1a1a;
        --text-color: #ffffff;
    }

    body {
        background: radial-gradient(circle at top right, #1e1e1e, #0f0f0f);
        color: var(--text-color);
        font-family: 'Inter', sans-serif;
        margin: 0;
        padding: 0;
        min-height: 100vh;
    }

    .content-wrapper {
        width: 100%;
        max-width: 800px;
        margin: 40px auto;
        padding: 20px;
        box-sizing: border-box;
    }

    .glass-card {
        background: var(--card-bg);
        padding: clamp(20px, 5vw, 40px);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
    }

    h2 { 
        font-weight: 800; 
        margin-bottom: 30px; 
        letter-spacing: -1px; 
        font-size: clamp(1.2rem, 4vw, 1.8rem);
        color: var(--text-color);
    }

    .input-grid { 
        display: grid; 
        grid-template-columns: repeat(2, 1fr); 
        gap: 20px; 
    }

    .field {
        display: flex;
        flex-direction: column;
    }

    .full-width { 
        grid-column: span 2; 
    }

    label { 
        font-size: 0.75rem; 
        text-transform: uppercase; 
        color: #888; 
        margin-bottom: 8px; 
        display: block; 
        letter-spacing: 1px;
    }

    input {
        width: 100%;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255,255,255,0.1);
        padding: 15px;
        border-radius: 12px;
        color: white;
        font-size: 1rem;
        transition: 0.3s;
        box-sizing: border-box;
    }

    input:focus { 
        border-color: var(--accent-color); 
        outline: none; 
        background: rgba(255, 255, 255, 0.1);
        box-shadow: 0 0 15px rgba(0, 255, 225, 0.1);
    }

    .action-zone { 
        margin-top: 40px; 
        display: flex; 
        justify-content: flex-start;
    }

    .btn-minimal {
        background: var(--accent-color);
        color: #000;
        border: none;
        padding: 15px 40px;
        font-weight: 700;
        font-size: 1rem;
        border-radius: 12px;
        cursor: pointer;
        transition: 0.3s;
        width: auto;
        min-width: 200px;
    }

    .btn-minimal:hover { 
        transform: translateY(-3px); 
        box-shadow: 0 10px 20px rgba(0, 255, 225, 0.3); 
    }

    .status-msg { 
        padding: 15px; 
        border-radius: 12px; 
        margin-bottom: 25px; 
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .success-lite { 
        background: rgba(0, 255, 225, 0.1); 
        color: var(--accent-color); 
        border: 1px solid var(--accent-color); 
    }

    .error-lite { 
        background: rgba(255, 71, 87, 0.1); 
        color: #ff4757; 
        border: 1px solid #ff4757; 
    }

    /* Reszponzív nézet */
    @media (max-width: 650px) { 
        .input-grid { 
            grid-template-columns: 1fr; 
        } 
        .content-wrapper {
            margin: 20px auto;
        }
        .btn-minimal {
            width: 100%;
        }
    }
</style>

<div class="content-wrapper">
    <div class="glass-card">
        <h2>Új Munkakör</h2>

        <?php if (!empty($success_msg)): ?>
            <div class="status-msg success-lite"><?= htmlspecialchars($success_msg) ?></div>
        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>
            <div class="status-msg error-lite"><?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="input-grid">
                <div class="field">
                    <label for="role_name">Munkakör Neve</label>
                    <input type="text" id="role_name" name="role_name" required placeholder="pl. Senior Developer">
                </div>

                <div class="field">
                    <label for="pph_huf">Órabér (HUF)</label>
                    <input type="number" id="pph_huf" name="pph_huf" required placeholder="pl. 5000">
                </div>
            </div>

            <div class="action-zone">
                <button type="submit" class="btn-minimal">Rögzítés</button>
            </div>
        </form>
    </div>
</div>