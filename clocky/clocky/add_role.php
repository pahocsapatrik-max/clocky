<?php
session_start();
include 'config.php';

// Jogosultság ellenőrzés
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 1) {
    header('Location: index.php');
    exit();
}

$success_msg = "";
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role_name = trim($_POST['role_name'] ?? '');
    $pph_huf = trim($_POST['pph_huf'] ?? '');

    if (!empty($role_name) && !empty($pph_huf)) {
        $stmt = $conn->prepare("INSERT INTO role (role_name, pph_huf) VALUES (?, ?)");
        $stmt->bind_param("si", $role_name, $pph_huf);

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

$page_title = 'Új Munkakör';
include 'header.php';
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
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .content-wrapper {
        width: 100%;
        max-width: 500px;
        padding: 20px;
    }

    .glass-card {
        background: var(--card-bg);
        padding: 40px;
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
    }

    h2 {
        font-size: 1.8rem;
        font-weight: 800;
        margin-bottom: 30px;
        letter-spacing: -1px;
        color: #fff;
        text-align: center;
    }

    .field {
        display: flex;
        flex-direction: column;
        margin-bottom: 25px;
    }

    label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
        color: var(--text-secondary);
    }

    input {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 15px;
        font-size: 1rem;
        color: #fff;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    input:focus {
        outline: none;
        border-color: var(--accent-color);
        background: rgba(255, 255, 255, 0.08);
        box-shadow: 0 0 15px rgba(0, 255, 225, 0.1);
    }

    .btn-minimal {
        width: 100%;
        background: var(--accent-color);
        color: #1a1a1a;
        border: none;
        padding: 16px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        border-radius: 12px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-minimal:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 255, 225, 0.2);
        filter: brightness(1.1);
    }

    .status-msg {
        padding: 15px;
        margin-bottom: 25px;
        border-radius: 12px;
        font-size: 0.9rem;
        text-align: center;
        font-weight: 600;
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
</style>

<div class="content-wrapper">
    <div class="glass-card">
        <h2><i class="fas fa-briefcase" style="color: var(--accent-color); margin-right: 10px;"></i>Új Munkakör</h2>

        <?php if (!empty($success_msg)): ?>
            <div class="status-msg success-lite"><?= htmlspecialchars($success_msg) ?></div>
        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>
            <div class="status-msg error-lite"><?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="field">
                <label for="role_name">Munkakör Neve</label>
                <input type="text" id="role_name" name="role_name" required placeholder="pl. Senior Developer">
            </div>

            <div class="field">
                <label for="pph_huf">Órabér (HUF / óra)</label>
                <input type="number" id="pph_huf" name="pph_huf" required placeholder="pl. 5000">
            </div>

            <button type="submit" class="btn-minimal">Munkakör mentése</button>
        </form>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="emp.php" style="color: var(--text-secondary); text-decoration: none; font-size: 0.85rem;">
                <i class="fas fa-arrow-left"></i> Vissza a munkatársakhoz
            </a>
        </div>
    </div>
</div>