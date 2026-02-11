<?php
session_start();
require_once 'config.php';

// --- OKOS MAGYARÍTOTT ROUTER ---
$request_uri = $_SERVER['REQUEST_URI'];
$current_file = basename($_SERVER['PHP_SELF']);

// Útvonal térkép
$routes = [
    'emp.php'    => 'dolgozok',
    'index.php'  => 'bejelentkezes'
];

// Ha .php kiterjesztéssel hívják meg, átirányítjuk a magyarosított aliasra
if (strpos($request_uri, '.php') !== false && !isset($_GET['api']) && !isset($_GET['export'])) {
    $pretty_name = $routes[$current_file] ?? 'dolgozok';
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: " . $pretty_name);
    exit();
}
// --- ROUTER VÉGE ---

// --- RESTful API & ADATKEZELÉS (BACKEND) ---
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 1) {
    if (isset($_GET['api'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Nincs jogosultság']);
        exit;
    }
    header('Location: bejelentkezes');
    exit();
}

$message = "";

// Szerkesztés kezelése
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_employee'])) {
    $id = intval($_POST['empID']);
    $name = trim($_POST['name']);
    $dob = $_POST['dob'];
    $tn = $_POST['tn'];
    $email = trim($_POST['email']);
    $roleID = intval($_POST['FK_roleID']);
    $active = isset($_POST['active']) ? 1 : 0;

    $updateStmt = $conn->prepare("UPDATE emp SET name=?, dob=?, tn=?, email=?, FK_roleID=?, active=? WHERE empID=?");
    $updateStmt->bind_param("ssssiii", $name, $dob, $tn, $email, $roleID, $active, $id);
    
    $success = $updateStmt->execute();

    if (isset($_GET['api'])) {
        header('Content-Type: application/json');
        echo json_encode(['status' => $success ? 'success' : 'error']);
        exit;
    }

    if ($success) {
        $message = ($active == 0) 
            ? "<div class='alert success'>A dolgozó archiválva lett!</div>" 
            : "<div class='alert success'>Sikeres frissítés!</div>";
    } else {
        $message = "<div class='alert error'>Hiba történt a mentéskor.</div>";
    }
    $updateStmt->close();
}

// Keresés és lekérdezés
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$queryStr = "
    SELECT e.empID, e.name, e.dob, e.tn, e.FK_roleID, r.role_name, e.email, e.active
    FROM emp e
    LEFT JOIN role r ON e.FK_roleID = r.roleID
    WHERE (e.active = 1 OR e.active IS NULL)
";

if ($search !== '') {
    $stmt = $conn->prepare($queryStr . " AND e.name LIKE ?");
    $like = "%" . $search . "%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($queryStr);
}

// Export TXT
if (isset($_GET['export']) && $_GET['export'] === 'txt') {
    $filename = "dolgozok_" . date('Y-m-d') . ".txt";
    header('Content-Type: text/plain; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo "ID\tNev\tSzuletes\tMunkaido\tMunkakor\tEmail\r\n";
    if ($result && $result->num_rows > 0) {
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()) {
            echo $row['empID'] . "\t" . $row['name'] . "\t" . $row['dob'] . "\t" . $row['tn'] . "\t" . ($row['role_name'] ?? 'Nincs') . "\t" . $row['email'] . "\r\n";
        }
    }
    exit();
}

// Szerepkörök lekérése a módosító ablakhoz
$roles_result = $conn->query("SELECT * FROM role");
$roles = [];
while($r = $roles_result->fetch_assoc()) { $roles[] = $r; }

// --- MEGJELENÍTÉS (FRONTEND) ---
$page_title = 'Munkatársak';
include_once 'header.php';
?>

<style>
    :root {
        --bg-color: #0f0f0f;
        --card-bg: #1a1a1a;
        --accent-color: #00ffe1;
        --text-secondary: #888;
    }

    .glass-card {
        background: var(--card-bg);
        padding: clamp(15px, 5vw, 30px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        max-width: 1100px;
        margin: 20px auto;
        color: #fff;
    }

    /* Kereső sáv */
    .search-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 25px;
    }

    .search-container input {
        flex: 1;
        min-width: 200px;
    }

    input[type="text"], input[type="email"], input[type="date"], select { 
        background: rgba(255,255,255,0.05); 
        border: 1px solid rgba(255,255,255,0.1); 
        padding: 12px; 
        border-radius: 10px; 
        color: #fff;
        font-size: 16px;
    }

    /* Gombok */
    .btn {
        padding: 12px 20px;
        border-radius: 10px;
        border: none;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-primary { background: var(--accent-color); color: #000; }
    .btn-secondary { background: rgba(255,255,255,0.1); color: #fff; }
    .btn-edit-small { background: rgba(0, 255, 225, 0.1); color: var(--accent-color); border: 1px solid var(--accent-color); padding: 6px 12px; }

    /* Szerkesztő Form */
    #editFormContainer { 
        display: none; 
        background: rgba(0, 255, 225, 0.03); 
        padding: 25px; 
        border: 1px solid var(--accent-color); 
        margin-bottom: 30px; 
        border-radius: 16px; 
        animation: slideDown 0.4s ease;
    }

    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
    }

    /* Táblázat Reszponzivitás */
    .custom-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
    .custom-table th { color: var(--text-secondary); text-transform: uppercase; font-size: 0.75rem; padding: 12px; text-align: left; }
    .custom-table td { background: rgba(255, 255, 255, 0.02); padding: 15px; }

    @media (max-width: 850px) {
        .custom-table thead { display: none; }
        .custom-table tr { display: block; margin-bottom: 15px; border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; overflow: hidden; }
        .custom-table td { display: flex; justify-content: space-between; text-align: right; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .custom-table td::before { content: attr(data-label); font-weight: 600; color: var(--text-secondary); text-transform: uppercase; font-size: 0.7rem; }
        .custom-table td:last-child { border-bottom: none; justify-content: center; }
    }

    .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; }
    .success { background: rgba(0, 255, 225, 0.1); color: var(--accent-color); border: 1px solid var(--accent-color); }
    .error { background: rgba(255, 71, 87, 0.1); color: #ff4757; border: 1px solid #ff4757; }
</style>

<div class="glass-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-weight: 800;"><i class="fas fa-user-tie" style="color: var(--accent-color);"></i> Dolgozók</h2>
    </div>
    
    <?php echo $message; ?>

    <div id="editFormContainer">
        <h3 style="color: var(--accent-color); margin-bottom: 20px;">Dolgozó módosítása</h3>
        <form method="POST">
            <input type="hidden" name="empID" id="edit_id">
            <div class="form-grid">
                <div><label>Név</label><input type="text" name="name" id="edit_name" required style="width:100%"></div>
                <div><label>Email</label><input type="email" name="email" id="edit_email" required style="width:100%"></div>
                <div><label>Születési idő</label><input type="date" name="dob" id="edit_dob" required style="width:100%"></div>
                <div><label>Heti óraszám</label><input type="text" name="tn" id="edit_tn" style="width:100%"></div>
                <div>
                    <label>Munkakör</label>
                    <select name="FK_roleID" id="edit_role" style="width:100%">
                        <?php foreach($roles as $role): ?>
                            <option value="<?php echo $role['roleID']; ?>"><?php echo htmlspecialchars($role['role_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; padding-top: 25px;">
                    <input type="checkbox" name="active" id="edit_active" value="1" style="width: 20px; height: 20px;"> 
                    <label>Aktív alkalmazott</label>
                </div>
            </div>
            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <button type="submit" name="update_employee" class="btn btn-primary">Mentés</button>
                <button type="button" onclick="document.getElementById('editFormContainer').style.display='none'" class="btn btn-secondary">Mégse</button>
            </div>
        </form>
    </div>

    <form method="GET" class="search-container">
        <input type="text" name="search" placeholder="Keresés névre..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-secondary">Keresés</button>
        <a href="?export=txt&search=<?php echo urlencode($search); ?>" class="btn btn-secondary"><i class="fas fa-file-export"></i>&nbsp;TXT Export</a>
    </form>

    <div style="overflow-x: auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Név</th>
                    <th>Születés</th>
                    <th>Heti óra</th>
                    <th>Munkakör</th>
                    <th>Email</th>
                    <th>Műveletek</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php $json_data = json_encode($row); ?>
                        <tr>
                            <td data-label="Név"><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                            <td data-label="Születés"><?php echo htmlspecialchars($row['dob']); ?></td>
                            <td data-label="Heti óra"><?php echo htmlspecialchars($row['tn']); ?> óra</td>
                            <td data-label="Munkakör"><span style="color: var(--accent-color)"><?php echo htmlspecialchars($row['role_name'] ?? 'Nincs'); ?></span></td>
                            <td data-label="Email"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <button class="btn btn-edit-small" onclick='openEdit(<?php echo $json_data; ?>)'>Szerkesztés</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center; padding: 40px; color: var(--text-secondary);">Nincs találat.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function openEdit(data) {
    document.getElementById('editFormContainer').style.display = 'block';
    document.getElementById('edit_id').value = data.empID;
    document.getElementById('edit_name').value = data.name;
    document.getElementById('edit_email').value = data.email;
    document.getElementById('edit_dob').value = data.dob;
    document.getElementById('edit_tn').value = data.tn;
    document.getElementById('edit_role').value = data.FK_roleID;
    document.getElementById('edit_active').checked = (data.active == 1 || data.active == null);
    window.scrollTo({top: 0, behavior: 'smooth'});
}
</script>

<?php
if (isset($stmt)) { $stmt->close(); }
$conn->close();
?>