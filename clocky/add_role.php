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

$page_title = 'Add Role';
include 'header.php';
?>

<div class="content-wrapper">
    <div class="form-container">
        <h2>Új Munkakör</h2>
        

        <?php if (!empty($success_msg)): ?>
            <div class="status-msg success-lite"><?= htmlspecialchars($success_msg) ?></div>
        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>
            <div class="status-msg error-lite"><?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="auth-form">
            <div class="input-stack">
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
                <button type="submit" class="btn-minimal">Munkakör mentése</button>
            </div>
        </form>
    </div>
</div>

<style>
    body {
        background-color: #fcfcfc;
        color: #2c3e50;
        font-family: 'Inter', -apple-system, sans-serif;
    }

    .content-wrapper {
        max-width: 600px;
        margin: 100px auto;
        padding: 0 20px;
    }

    h2 {
        font-size: 2.2rem;
        font-weight: 800;
        margin-bottom: 8px;
        color: #1a1a1a;
    }

    .subtitle {
        color: #888;
        margin-bottom: 45px;
    }

    .input-stack {
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .field {
        display: flex;
        flex-direction: column;
    }

    label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
        color: #666;
        padding-left: 5px;
    }

    input {
        background-color: #ffffff; /* Fehér háttér az írható résznek */
        border: none;
        border-bottom: 2px solid #eee;
        padding: 15px 12px; /* Több hely az írásnak */
        font-size: 1rem;
        transition: all 0.2s ease-in-out;
        border-radius: 4px 4px 0 0; /* Finom kerekítés felül */
    }

    input:focus {
        outline: none;
        background-color: #fff;
        border-bottom-color: #00ffbbff;
        box-shadow: 0 4px 12px rgba(0, 255, 187, 0.05); /* Nagyon enyhe türkiz ragyogás */
    }

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .action-zone {
        margin-top: 50px;
    }

    .btn-minimal {
        background: #1a1a1a;
        color: #fff;
        border: none;
        padding: 16px 40px;
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

    /* Üzenetek */
    .status-msg {
        padding: 15px;
        margin-bottom: 30px;
        border-radius: 4px;
        font-size: 0.9rem;
    }
    .success-lite { background: #f0fff4; color: #22543d; border-left: 4px solid #00ffbbff; }
    .error-lite { background: #fff5f5; color: #822727; border-left: 4px solid #feb2b2; }
</style>