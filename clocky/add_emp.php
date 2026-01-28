<?php
session_start();
include 'config.php';

// 1. Jogosultság ellenőrzés
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 1) {
    header('Location: index.php');
    exit();
}

// 2. Változók inicializálása (Ezzel tűnik el a Warning!)
$success_msg = "";
$error_msg = "";

// 3. Adatmentés feldolgozása (Ha megnyomták a gombot)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $dob = $_POST['dob'] ?? '';
    $tn = $_POST['tn'] ?? '';
    $roleID = $_POST['roleID'] ?? '';

    if (!empty($name) && !empty($email)) {
        $stmt = $conn->prepare("INSERT INTO emp (name, email, dob, tn, FK_roleID) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $name, $email, $dob, $tn, $roleID);
        
        if ($stmt->execute()) {
            $success_msg = "Sikeresen felvetted az Alkalmazottat";
        } else {
            $error_msg = "Error: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error_msg = "Please fill in all required fields!";
    }
}

$page_title = 'Add Employee';
include 'header.php';
?>

<div class="container">
    <h2>Új Dolgozó Hozzáadása</h2>

    <style>
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            max-width: 500px;
            margin: 100px auto;
        }
        h2 { text-align: center; color: #000000ff; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
        .btn-submit { width: 100%; padding: 12px; background: #2a2a2aff; color: white; border: none; border-radius: 6px; cursor: pointer; margin-top: 10px; }
        .btn-submit:hover { background: #484848ff; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 6px; margin-bottom: 15px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 6px; margin-bottom: 15px; }
    </style>

    <?php if (!empty($success_msg)): ?>
        <div class="success"><?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
        <div class="error"><?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>teljes Név</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Születési Dátum</label>
            <input type="date" name="dob">
        </div>
        <div class="form-group">
            <label>Munkaidő</label>
            <input type="number" name="tn">
        </div>
        <div class="form-group">
            <label>Munkakör</label>
            <select name="roleID">
                <?php
                // Szerepkörök lekérése a legördülőhöz
                $roles = $conn->query("SELECT roleID, role_name FROM role");
                while($r = $roles->fetch_assoc()) {
                    echo "<option value='{$r['roleID']}'>{$r['role_name']}</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn-submit">Add Employee</button>
    </form>
</div>

</body>
</html>