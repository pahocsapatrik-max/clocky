<?php
session_start();
include 'config.php';

// 1. Jogosultság ellenőrzés
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 1) {
    header('Location: index.php');
    exit();
}

// 2. Üzenet változók inicializálása
$success_msg = "";
$error_msg = "";

// 3. ADATMENTÉS (POST kérés feldolgozása)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role_name = trim($_POST['role_name'] ?? '');
    $pph_huf = trim($_POST['pph_huf'] ?? '');

    if (!empty($role_name) && !empty($pph_huf)) {
        // SQL lekérdezés előkészítése (Prepared Statement a biztonságért)
        $stmt = $conn->prepare("INSERT INTO role (role_name, pph_huf) VALUES (?, ?)");
        $stmt->bind_param("si", $role_name, $pph_huf); // s = string, i = integer

        if ($stmt->execute()) {
            $success_msg = "Sikeresen hozzáadva: " . htmlspecialchars($role_name);
        } else {
            $error_msg = "Hiba történt a mentés során: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error_msg = "Minden mezőt ki kell tölteni!";
    }
}

$page_title = 'Add Role';
include 'header.php'; // Csak egyszer hívjuk be a headert!
?>

<style>
/* A CSS-ed marad változatlanul */
header a {
    position: relative;
    z-index: 1;
}

.container {
    position: relative;
    z-index: 10;
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    max-width: 900px;
    width: 95%;
    margin: 100px auto 50px auto;
}

h2 {
    text-align: center;
    color: #373737ff;
    margin-bottom: 25px;
}

.form-group {
    margin-bottom: 20px;
}

label {
    font-weight: bold;
    display: block;
    margin-bottom: 6px;
}

input {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
}

button {
    width: 100%;
    padding: 12px;
    background: #4e4e4eff;
    color: white;
    font-weight: bold;
    font-size: 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.success {
    padding: 12px;
    background: #e8f5e9;
    color: #388e3c;
    border-radius: 6px;
    margin-bottom: 15px;
    text-align: center;
}

.error {
    padding: 12px;
    background: #ffebee;
    color: #d32f2f;
    border-radius: 6px;
    margin-bottom: 15px;
    text-align: center;
}
</style>

<div class="container">

    <?php if (!empty($success_msg)): ?>
        <div class="success"><?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
        <div class="error"><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <h2>Új Munkakör Hozzáadása</h2>

    <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <div class="form-group">
            <label for="role_name">Munakör Neve:</label>
            <input type="text" id="role_name" name="role_name" required placeholder=>
        </div>

        <div class="form-group">
            <label for="pph_huf">fizetés/óra :</label>
            <input type="number" id="pph_huf" name="pph_huf" required placeholder=>
        </div>

        <button type="submit">Munkakör hozzáadása</button>
    </form>

</div>
</body>
</html>