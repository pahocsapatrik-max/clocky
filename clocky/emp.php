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
    // Ha a checkbox be van pipálva, akkor 1, egyébként 0
    $active = isset($_POST['active']) ? 1 : 0;

    $updateStmt = $conn->prepare("UPDATE emp SET name=?, dob=?, tn=?, email=?, FK_roleID=?, active=? WHERE empID=?");
    $updateStmt->bind_param("ssssiii", $name, $dob, $tn, $email, $roleID, $active, $id);
    
    if ($updateStmt->execute()) {
        if ($active == 0) {
            $message = "<div class='alert success'>A dolgozó archiválva lett!</div>";
        } else {
            $message = "<div class='alert success'>Sikeres frissítés!</div>";
        }
    } else {
        $message = "<div class='alert error'>Hiba történt a mentéskor.</div>";
    }
    $updateStmt->close();
}

// --- 3. KERESÉSI ÉS LISTÁZÁSI LOGIKA (Csak active = 1) ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$queryStr = "
    SELECT e.empID, e.name, e.dob, e.tn, e.FK_roleID, r.role_name, e.email, e.active
    FROM emp e
    LEFT JOIN role r ON e.FK_roleID = r.roleID
    WHERE e.active = 1
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

$page_title = 'Employees';
include 'header.php';
?>

<div class="container">
    <h2>Munkaidő Nyilvántartás - Aktív Dolgozók</h2>
    
    <?php echo $message; ?>

    <style>
        .container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 1100px; margin: 40px auto; font-family: Arial, sans-serif; }
        .search-box { margin-bottom: 20px; display: flex; gap: 10px; }
        input[type="text"], input[type="email"], input[type="date"], select { padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .btn-search { padding: 10px 20px; background: #333; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        .export-actions { margin-bottom: 20px; text-align: right; }
        .btn-export { display: inline-block; text-decoration: none; padding: 10px 15px; background: #007bff; color: #fff; border-radius: 4px; font-weight: bold; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 12px; border: 1px solid #eee; text-align: left; }
        .table thead { background: #00ffcc; }
        .btn-edit { background: #ffc107; color: #000; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 13px; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        #editFormContainer { display: none; background: #f9f9f9; padding: 20px; border: 2px solid #333; margin-bottom: 20px; border-radius: 8px; }
    </style>

    <div id="editFormContainer">
        <h3>Dolgozó adatainak módosítása</h3>
        <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <input type="hidden" name="empID" id="edit_id">
            <div><label>Név:</label><br><input type="text" name="name" id="edit_name" required style="width:100%"></div>
            <div><label>Email:</label><br><input type="email" name="email" id="edit_email" required style="width:100%"></div>
            <div><label>Születési idő:</label><br><input type="date" name="dob" id="edit_dob" required style="width:100%"></div>
            <div><label>Munkaidő (tn):</label><br><input type="text" name="tn" id="edit_tn" style="width:100%"></div>
            <div>
                <label>Munkakör:</label><br>
                <select name="FK_roleID" id="edit_role" style="width:100%">
                    <?php foreach($roles as $role): ?>
                        <option value="<?php echo $role['roleID']; ?>"><?php echo htmlspecialchars($role['role_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Státusz:</label><br>
                <input type="checkbox" name="active" id="edit_active" value="1"> Aktív (Ha kiveszed, archiválásra kerül)
            </div>
            <div style="grid-column: span 2; padding-top: 10px;">
                <button type="submit" name="update_employee" class="btn-search" style="background: #28a745;">Mentés</button>
                <button type="button" onclick="document.getElementById('editFormContainer').style.display='none'" class="btn-search">Mégse</button>
            </div>
        </form>
    </div>

    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Keresés név alapján..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn-search">Keresés</button>
       
    </form>

    <div class="export-actions">
        <a href="?export=txt&search=<?php echo urlencode($search); ?>" class="btn-export">📄 Kimutatás letöltése (TXT)</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Név</th>
                <th>Születés</th>
                <th>Munkaidő</th>
                <th>Munkakör</th>
                <th>Email</th>
                <th>Műveletek</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                $result->data_seek(0);
                while ($row = $result->fetch_assoc()) {
                    $json_data = json_encode($row);
                    echo "<tr>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['dob']) . "</td>
                        <td>" . htmlspecialchars($row['tn']) . "</td>
                        <td>" . htmlspecialchars($row['role_name'] ?? 'Nincs') . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>
                            <button class='btn-edit' onclick='openEdit(" . $json_data . ")'>Szerkesztés</button>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center;'>Nincs aktív találat.</td></tr>";
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
    
    // Checkbox beállítása: ha 1, akkor bepipálva
    document.getElementById('edit_active').checked = (data.active == 1);
    
    window.scrollTo({top: 0, behavior: 'smooth'});
}
</script>

<?php
if (isset($stmt)) { $stmt->close(); }
$conn->close();
?>