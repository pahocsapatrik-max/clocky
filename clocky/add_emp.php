<?php
session_start();
include 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 1) {
    header('Location: index.php');
    exit();
}

$success_msg = "";
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $dob = $_POST['dob'] ?? NULL;
    $tn = $_POST['tn'] ?? 0;
    $roleID = $_POST['roleID'] ?? NULL;

    if (!empty($name) && !empty($email)) {
        $stmt = $conn->prepare("INSERT INTO emp (name, email, dob, tn, FK_roleID) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $name, $email, $dob, $tn, $roleID);
        
        if ($stmt->execute()) {
            $success_msg = "Sikeresen felvetted az alkalmazottat!";
        } else {
            $error_msg = "Hiba történt: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error_msg = "Kérlek töltsd ki a kötelező mezőket!";
    }
}

$page_title = 'Add Employee';
include 'header.php';
?>

<div class="content-wrapper">
    <div class="form-container">
        <h2>Új Dolgozó Hozzáadása</h2>
        <?php if (!empty($success_msg)): ?>
            <div class="status-msg success-lite"><?php echo htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>
            <div class="status-msg error-lite"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <div class="input-grid">
                <div class="field">
                    <label>Teljes Név</label>
                    <input type="text" name="name" placeholder="Kovács János" required>
                </div>

                <div class="field">
                    <label>Email Cím</label>
                    <input type="email" name="email" placeholder="janos@cegnev.hu" required>
                </div>

                <div class="field">
                    <label>Születési Dátum</label>
                    <input type="date" name="dob">
                </div>

                <div class="field">
                    <label>Munkaidő </label>
                    <input type="number" name="tn" placeholder="40">
                </div>

                <div class="field full-width">
                    <label>Munkakör</label>
                    <select name="roleID">
                        <option value="" disabled selected>Válasszon pozíciót...</option>
                        <?php
                        $roles = $conn->query("SELECT roleID, role_name FROM role");
                        while($r = $roles->fetch_assoc()) {
                            echo "<option value='{$r['roleID']}'>{$r['role_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="action-zone">
                <button type="submit" class="btn-minimal">Alkalmazott rögzítése</button>
            </div>
        </form>
    </div>
</div>

<style>
    body {
        background-color: #fcfcfc; /* Nagyon világos szürke, majdnem fehér */
        color: #2c3e50;
    }

    .content-wrapper {
        max-width: 800px;
        margin: 80px auto;
        padding: 0 20px;
    }

    .form-container {
        /* Nincs Box, nincs árnyék, csak tiszta elrendezés */
        background: transparent;
    }

    h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 5px;
        color: #1a1a1a;
    }

    .subtitle {
        color: #888;
        margin-bottom: 40px;
        font-size: 0.95rem;
    }

    .input-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
    }

    .field {
        display: flex;
        flex-direction: column;
    }

    .full-width {
        grid-column: span 2;
    }

    label {
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        color: #555;
    }

    input, select {
        background: #fff;
        border: none;
        border-bottom: 2px solid #eee; /* Csak egy alsó vonal, autentikusabb */
        padding: 12px 5px;
        font-size: 1rem;
        transition: all 0.3s ease;
        border-radius: 0;
    }

    input:focus, select:focus {
        outline: none;
        border-bottom-color: #00ffbbff;
        background: #f9f9f9;
    }

    .action-zone {
        margin-top: 50px;
        text-align: left;
    }

    .btn-minimal {
        background: #1a1a1a;
        color: #fff;
        border: none;
        padding: 15px 40px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 4px;
    }

    .btn-minimal:hover {
        background: #00ffbbff;
        color: #1a1a1a;
    }

    /* Üzenetek stílusa */
    .status-msg {
        padding: 15px;
        margin-bottom: 30px;
        font-weight: 500;
        border-radius: 4px;
    }
    .success-lite { background: #e6fffa; color: #234e52; border-left: 4px solid #00ffbbff; }
    .error-lite { background: #fff5f5; color: #822727; border-left: 4px solid #feb2b2; }

    @media (max-width: 600px) {
        .input-grid { grid-template-columns: 1fr; }
        .full-width { grid-column: span 1; }
    }
</style>

</body>
</html>