<?php
session_start();
require_once 'config.php';

// --- OKOS MAGYARÍTOTT ROUTER ---
$request_uri = $_SERVER['REQUEST_URI'];
$current_file = basename($_SERVER['PHP_SELF']);

// Útvonal térkép
$routes = [
    'roles.php' => 'munkakorok',
    'index.php' => 'bejelentkezes'
];

// Ha .php kiterjesztéssel hívják meg, átirányítjuk a magyarosított aliasra
if (strpos($request_uri, '.php') !== false && !isset($_GET['api'])) {
    $pretty_name = $routes[$current_file] ?? 'munkakorok';
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: " . $pretty_name);
    exit();
}
// --- ROUTER VÉGE ---

// --- JOGOSULTSÁG ELLENŐRZÉSE ---
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 1) {
    if (isset($_GET['api'])) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Nincs jogosultsága.']);
        exit();
    }
    header('Location: bejelentkezes');
    exit();
}

$success_msg = '';
$error_msg = '';

// --- MÓDOSÍTÁS KEZELÉSE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $roleID = intval($_POST['roleID']);
    $role_name = trim($_POST['role_name']);
    $pph_huf = intval($_POST['pph_huf']);

    if (!empty($role_name) && $pph_huf > 0) {
        $stmt = $conn->prepare("UPDATE role SET role_name = ?, pph_huf = ? WHERE roleID = ?");
        $stmt->bind_param("sii", $role_name, $pph_huf, $roleID);
        
        $success = $stmt->execute();

        // RESTful API válasz kezelése
        if (isset($_GET['api'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => $success ? 'success' : 'error']);
            exit();
        }

        if ($success) {
            $success_msg = "Munkakör sikeresen frissítve!";
        } else {
            $error_msg = "Hiba történt a mentés során.";
        }
        $stmt->close();
    } else {
        $error_msg = "Kérjük, töltsön ki minden mezőt megfelelően!";
    }
}

// --- MEGJELENÍTÉS (FRONTEND) ---
$page_title = 'Munkakörök módosítása';
include_once 'header.php';
?>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

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
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .main-container {
        width: 100%;
        max-width: 1000px;
        margin: 20px auto;
        padding: 0 15px;
        box-sizing: border-box;
    }

    .glass-card {
        background: var(--card-bg);
        padding: clamp(15px, 5vw, 40px);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
        width: 100%;
        box-sizing: border-box;
    }

    h2 { font-weight: 800; letter-spacing: -1px; margin-bottom: 30px; text-align: center; font-size: clamp(1.4rem, 4vw, 2rem); }

    /* SZERKESZTŐ PANEL */
    #editSection {
        display: none;
        background: rgba(0, 255, 225, 0.05);
        padding: 20px;
        border-radius: 16px;
        border: 1px solid var(--accent-color);
        margin-bottom: 30px;
    }

    .form-group { margin-bottom: 15px; }
    label { font-size: 0.75rem; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 5px; display: block; }
    
    input[type="text"], input[type="number"] {
        width: 100%;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 12px;
        border-radius: 10px;
        color: #fff;
        font-size: 16px; /* 16px alá ne menjen mobilon, mert belenagyít az iPhone */
        box-sizing: border-box;
    }

    .btn-row { display: flex; gap: 10px; margin-top: 20px; }
    .btn-save { background: var(--accent-color); color: #000; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 700; cursor: pointer; flex: 1; }
    .btn-cancel { background: #333; color: #fff; border: none; padding: 12px 20px; border-radius: 10px; cursor: pointer; flex: 1; }

    /* TÁBLÁZAT MOBIL-OPTIMALIZÁLVA */
    .table-wrapper { width: 100%; overflow: hidden; }
    .custom-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
    .custom-table th { color: var(--text-secondary); text-transform: uppercase; font-size: 0.7rem; padding: 12px; text-align: left; }
    .custom-table td { background: rgba(255, 255, 255, 0.03); padding: 15px; border: none; }

    /* MOBIL NÉZET (Váltás kártyákra) */
    @media (max-width: 600px) {
        .custom-table thead { display: none; } /* Fejléc elrejtése */
        .custom-table tr { display: block; margin-bottom: 15px; }
        .custom-table td { 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            text-align: right; 
            padding: 12px 15px;
            border-radius: 0 !important;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .custom-table td::before { 
            content: attr(data-label); 
            font-weight: 600; 
            color: var(--text-secondary);
            text-transform: uppercase;
            font-size: 0.7rem;
            float: left;
        }
        .custom-table tr td:first-child { border-radius: 12px 12px 0 0 !important; }
        .custom-table tr td:last-child { border-radius: 0 0 12px 12px !important; border-bottom: none; }
        
        .btn-edit { width: 100%; text-align: center; justify-content: center; }
    }

    .btn-edit { 
        display: inline-flex;
        align-items: center;
        background: rgba(0, 255, 225, 0.1); 
        color: var(--accent-color); 
        border: 1px solid var(--accent-color); 
        padding: 8px 16px; 
        border-radius: 8px; 
        cursor: pointer; 
        font-weight: 600;
        text-decoration: none;
    }

    .status-msg { padding: 15px; border-radius: 12px; margin-bottom: 20px; text-align: center; font-size: 0.9rem; }
    .success { background: rgba(0, 255, 225, 0.15); color: var(--accent-color); border: 1px solid var(--accent-color); }
    .error { background: rgba(255, 71, 87, 0.15); color: #ff4757; border: 1px solid #ff4757; }
</style>

<div class="main-container">
    <div class="glass-card">
        <h2>Munkakörök Kezelése</h2>

        <?php if ($success_msg): ?> <div class="status-msg success"><?php echo $success_msg; ?></div> <?php endif; ?>
        <?php if ($error_msg): ?> <div class="status-msg error"><?php echo $error_msg; ?></div> <?php endif; ?>

        <div id="editSection">
            <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 1.1rem; color: var(--accent-color);">Szerkesztés</h3>
            <form method="POST">
                <input type="hidden" name="roleID" id="edit_roleID">
                <div class="form-group">
                    <label>Munkakör neve</label>
                    <input type="text" name="role_name" id="edit_role_name" required>
                </div>
                <div class="form-group">
                    <label>Órabér (HUF)</label>
                    <input type="number" name="pph_huf" id="edit_pph_huf" required>
                </div>
                <div class="btn-row">
                    <button type="submit" name="update_role" class="btn-save">Mentés</button>
                    <button type="button" class="btn-cancel" onclick="hideEdit()">Mégse</button>
                </div>
            </form>
        </div>

        <div class="table-wrapper">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Munkakör</th>
                        <th>Órabér</th>
                        <th style="text-align: right;">Művelet</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT roleID, role_name, pph_huf FROM role";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $safe_name = htmlspecialchars($row['role_name'], ENT_QUOTES);
                            echo "<tr>
                                    <td data-label='Munkakör'><strong>" . htmlspecialchars($row['role_name']) . "</strong></td>
                                    <td data-label='Órabér'>" . number_format($row['pph_huf'], 0, ',', ' ') . " Ft</td>
                                    <td style='text-align: right;'>
                                        <button class='btn-edit' onclick='showEdit({$row['roleID']}, \"{$safe_name}\", {$row['pph_huf']})'>
                                            Szerkesztés
                                        </button>
                                    </td>
                                  </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function showEdit(id, name, pph) {
    const section = document.getElementById('editSection');
    section.style.display = 'block';
    document.getElementById('edit_roleID').value = id;
    document.getElementById('edit_role_name').value = name;
    document.getElementById('edit_pph_huf').value = pph;
    
    // Simán odaúsztatjuk a nézetet
    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function hideEdit() {
    document.getElementById('editSection').style.display = 'none';
}
</script>

<?php $conn->close(); ?>