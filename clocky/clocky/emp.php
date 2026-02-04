<?php
session_start();
include 'config.php';

// 1. Jogosultság ellenőrzés
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 1) {
    header('Location: index.php');
    exit();
}

$message = "";

// --- 2. MÓDOSÍTÁSI LOGIKA (UPDATE) ---
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
    
    if ($updateStmt->execute()) {
        $message = ($active == 0) 
            ? "<div class='alert success'>A dolgozó archiválva lett!</div>" 
            : "<div class='alert success'>Sikeres frissítés!</div>";
    } else {
        $message = "<div class='alert error'>Hiba történt a mentéskor.</div>";
    }
    $updateStmt->close();
}

// --- 3. KERESÉSI ÉS LISTÁZÁSI LOGIKA ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// JAVÍTÁS: Listázzuk azokat is, akiknél az active NULL vagy 1 (így látszanak az új munkások is)
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

// --- 4. EXPORTÁLÁSI LOGIKA ---
if (isset($_GET['export']) && $_GET['export'] === 'txt') {
    $filename = "dolgozo_kimutatas_" . date('Y-m-d') . ".txt";
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

$roles_result = $conn->query("SELECT * FROM role");
$roles = [];
while($r = $roles_result->fetch_assoc()) { $roles[] = $r; }

$page_title = 'Munkatársak';
include 'header.php';
?>

<div class="container glass-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2><i class="fas fa-user-tie"></i> Aktív Dolgozók</h2>
       
    </div>
    
    <?php echo $message; ?>

    <style>
        :root {
            --bg-color: #0f0f0f;
            --card-bg: #1a1a1a;
            --accent-color: #00ffe1;
            --text-secondary: #888;
        }

        .glass-card {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            max-width: 1100px;
            margin: 40px auto;
            color: #fff;
        }

        .search-box { margin-bottom: 25px; display: flex; gap: 10px; }
        
        input[type="text"], input[type="email"], input[type="date"], select { 
            background: rgba(255,255,255,0.05); 
            border: 1px solid rgba(255,255,255,0.1); 
            padding: 10px; 
            border-radius: 8px; 
            color: #fff;
        }

        .btn-search { padding: 10px 20px; background: #333; color: #fff; border: none; border-radius: 8px; cursor: pointer; transition: 0.3s; }
        .btn-search:hover { filter: brightness(1.2); }

        .table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
        .table th { color: var(--text-secondary); text-transform: uppercase; font-size: 0.8rem; padding: 12px; text-align: left; }
        .table td { background: rgba(255, 255, 255, 0.02); padding: 15px; border: none; }
        .table tr td:first-child { border-radius: 10px 0 0 10px; }
        .table tr td:last-child { border-radius: 0 10px 10px 0; }

        .btn-edit { background: var(--accent-color); color: #000; padding: 6px 15px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        
        #editFormContainer { 
            display: none; 
            background: rgba(255,255,255,0.03); 
            padding: 25px; 
            border: 1px solid var(--accent-color); 
            margin-bottom: 30px; 
            border-radius: 12px; 
        }

        .alert { padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: bold; }
        .success { background: rgba(0, 255, 225, 0.1); color: var(--accent-color); }
        .error { background: rgba(255, 71, 87, 0.1); color: #ff4757; }
    </style>

    <div id="editFormContainer">
        <h3 style="color: var(--accent-color); margin-bottom: 15px;">Módosítás</h3>
        <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <input type="hidden" name="empID" id="edit_id">
            <div><label>Név:</label><br><input type="text" name="name" id="edit_name" required style="width:100%"></div>
            <div><label>Email:</label><br><input type="email" name="email" id="edit_email" required style="width:100%"></div>
            <div><label>Születési idő:</label><br><input type="date" name="dob" id="edit_dob" required style="width:100%"></div>
            <div><label>Heti óra:</label><br><input type="text" name="tn" id="edit_tn" style="width:100%"></div>
            <div>
                <label>Munkakör:</label><br>
                <select name="FK_roleID" id="edit_role" style="width:100%">
                    <?php foreach($roles as $role): ?>
                        <option value="<?php echo $role['roleID']; ?>"><?php echo htmlspecialchars($role['role_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" name="active" id="edit_active" value="1" style="width: 18px; height: 18px;"> 
                <label>Aktív státusz</label>
            </div>
            <div style="grid-column: span 2; display: flex; gap: 10px; margin-top: 10px;">
                <button type="submit" name="update_employee" class="btn-edit">Mentés</button>
                <button type="button" onclick="document.getElementById('editFormContainer').style.display='none'" class="btn-search">Mégse</button>
            </div>
        </form>
    </div>

    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Dolgozó keresése..." value="<?php echo htmlspecialchars($search); ?>" style="flex-grow: 1;">
        <button type="submit" class="btn-search">Keresés</button>
        <a href="?export=txt&search=<?php echo urlencode($search); ?>" class="btn-search" style="text-decoration: none; background: #444;">TXT Export</a>
    </form>

    <table class="table">
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
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $json_data = json_encode($row);
                    echo "<tr>
                        <td><strong>" . htmlspecialchars($row['name']) . "</strong></td>
                        <td>" . htmlspecialchars($row['dob']) . "</td>
                        <td>" . htmlspecialchars($row['tn']) . " óra</td>
                        <td><span style='color: var(--accent-color)'>" . htmlspecialchars($row['role_name'] ?? 'Nincs') . "</span></td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>
                            <button class='btn-edit' onclick='openEdit(" . $json_data . ")'>Szerkesztés</button>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center; padding: 30px; color: var(--text-secondary);'>Nincs megjeleníthető aktív dolgozó.</td></tr>";
            }
            ?>
        </tbody>
    </table>
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