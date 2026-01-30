<?php
session_start();
include 'config.php';

// 1. Jogosultság ellenőrzés
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 1) {
    header('Location: index.php');
    exit();
}

$success_msg = '';
$error_msg = '';

// --- 2. MÓDOSÍTÁSI LOGIKA (UPDATE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $roleID = intval($_POST['roleID']);
    $role_name = trim($_POST['role_name']);
    $pph_huf = intval($_POST['pph_huf']);

    if (!empty($role_name) && $pph_huf > 0) {
        $stmt = $conn->prepare("UPDATE role SET role_name = ?, pph_huf = ? WHERE roleID = ?");
        $stmt->bind_param("sii", $role_name, $pph_huf, $roleID);
        
        if ($stmt->execute()) {
            $success_msg = "Munkakör sikeresen frissítve!";
        } else {
            $error_msg = "Hiba történt a mentés során.";
        }
        $stmt->close();
    } else {
        $error_msg = "Kérjük, töltsön ki minden mezőt megfelelően!";
    }
}

$page_title = 'Roles';
include 'header.php';
?>

<div class="container">
    <h2>Munkakörök Kezelése</h2>

    <style>
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            max-width: 900px;
            margin: 40px auto;
            font-family: sans-serif;
        }
        h2 { text-align: center; color: #333; margin-bottom: 25px; }
        
        .success { padding: 12px; background: #e8f5e9; color: #388e3c; border-radius: 6px; margin-bottom: 20px; text-align: center; border: 1px solid #c8e6c9; }
        .error { padding: 12px; background: #ffebee; color: #d32f2f; border-radius: 6px; margin-bottom: 20px; text-align: center; border: 1px solid #ffcdd2; }

        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table thead { background: #00ffc3; color: black; }
        .table th, .table td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        .table tbody tr:hover { background: #f1f1f1; transition: 0.3s; }
        
        /* Módosítási panel stílusa */
        #editSection {
            display: none; /* Alaphelyzetben rejtett */
            background: #fdfdfd;
            padding: 20px;
            border: 2px solid #00ffc3;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn-save { background: #333; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-cancel { background: #ccc; color: #333; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px; }
        .btn-edit { background: #ffc107; color: black; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px; }
    </style>

    <?php if ($success_msg): ?> <div class="success"><?php echo $success_msg; ?></div> <?php endif; ?>
    <?php if ($error_msg): ?> <div class="error"><?php echo $error_msg; ?></div> <?php endif; ?>

    <div id="editSection">
        <h3>Munkakör módosítása</h3>
        <form method="POST">
            <input type="hidden" name="roleID" id="edit_roleID">
            <div class="form-group">
                <label>Munkakör neve:</label>
                <input type="text" name="role_name" id="edit_role_name" required>
            </div>
            <div class="form-group">
                <label>Órabér (HUF):</label>
                <input type="number" name="pph_huf" id="edit_pph_huf" required>
            </div>
            <button type="submit" name="update_role" class="btn-save">Változtatások mentése</button>
            <button type="button" class="btn-cancel" onclick="hideEdit()">Mégse</button>
        </form>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Munkakör Neve</th>
                <th>Fizetés (HUF / óra)</th>
                <th>Műveletek</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT roleID, role_name, pph_huf FROM role";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Adatok előkészítése JS számára
                    $safe_name = htmlspecialchars($row['role_name'], ENT_QUOTES);
                    echo "<tr>
                            <td>" . htmlspecialchars($row['role_name']) . "</td>
                            <td>" . number_format($row['pph_huf'], 0, ',', ' ') . " Ft</td>
                            <td>
                                <button class='btn-edit' onclick='showEdit({$row['roleID']}, \"{$safe_name}\", {$row['pph_huf']})'>Szerkesztés</button>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3' style='text-align:center;'>Nincsenek rögzített munkakörök.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
function showEdit(id, name, pph) {
    document.getElementById('editSection').style.display = 'block';
    document.getElementById('edit_roleID').value = id;
    document.getElementById('edit_role_name').value = name;
    document.getElementById('edit_pph_huf').value = pph;
    
    // Az oldal az űrlaphoz ugrik
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function hideEdit() {
    document.getElementById('editSection').style.display = 'none';
}
</script>

<?php $conn->close(); ?>
</body>
</html>