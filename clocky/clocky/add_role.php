<?php
session_start();
include 'config.php';

// 1. Jogosultság ellenőrzés
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
        
        $conn->begin_transaction();

        try {
            $default_username = explode('@', $email)[0];
            $default_password = "1234"; 
            $default_role = 0; 

            $user_stmt = $conn->prepare("INSERT INTO users (username, password, name, jogosultsag) VALUES (?, ?, ?, ?)");
            $user_stmt->bind_param("sssi", $default_username, $default_password, $name, $default_role);
            $user_stmt->execute();
            
            $new_user_id = $conn->insert_id;

            $stmt = $conn->prepare("INSERT INTO emp (name, email, dob, tn, FK_roleID, FK_userID, active) VALUES (?, ?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("ssssii", $name, $email, $dob, $tn, $roleID, $new_user_id);
            
            if ($stmt->execute()) {
                $conn->commit();
                $success_msg = "Sikeres mentés! Felhasználónév: <strong>$default_username</strong> | Jelszó: <strong>$default_password</strong>";
            } else {
                throw new Exception("Hiba történt az emp tábla mentésekor.");
            }

        } catch (Exception $e) {
            $conn->rollback();
            $error_msg = "Hiba történt: " . $e->getMessage();
        }
    } else {
        $error_msg = "Kérlek töltsd ki a kötelező mezőket (Név, Email)!";
    }
}

$page_title = 'Új dolgozó';
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="header.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="content-wrapper">
    <div class="glass-card">
        <h2><i class="fas fa-user-plus"></i> Új Munkatárs Rögzítése</h2>
        
        <?php if (!empty($success_msg)): ?>
            <div class="status-msg success-lite"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>
            <div class="status-msg error-lite"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <div class="input-grid">
                <div class="field">
                    <label>Teljes Név</label>
                    <input type="text" name="name" placeholder="Példa János" required>
                </div>

                <div class="field">
                    <label>Email Cím</label>
                    <input type="email" name="email" placeholder="pelda@ceg.hu" required>
                </div>

                <div class="field">
                    <label>Születési Dátum</label>
                    <input type="date" name="dob">
                </div>

                <div class="field">
                    <label>Heti órakeret (tn)</label>
                    <input type="number" name="tn" placeholder="40">
                </div>

                <div class="field full-width">
                    <label>Munkakör / Pozíció</label>
                    <select name="roleID" required>
                        <option value="" disabled selected>Válassz pozíciót...</option>
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
                <button type="submit" class="btn-minimal">Dolgozó rögzítése</button>
            </div>
        </form>
    </div>
</div>

<style>
    :root {
        --accent-color: #00ffe1;
        --card-bg: #1a1a1a;
        --text-color: #ffffff;
    }

    /* Alapbeállítások a reszponzivitáshoz */
    body {
        background: radial-gradient(circle at top right, #1e1e1e, #0f0f0f);
        color: var(--text-color);
        font-family: 'Inter', sans-serif;
        margin: 0;
        padding: 0;
        min-height: 100vh;
        overflow-x: hidden; /* Megakadályozza az oldalsó görgetést */
    }

    .content-wrapper {
        width: 100%;
        max-width: 850px; /* Kicsit szélesebb az olvashatóságért */
        margin: 0 auto;
        padding: 20px;
        box-sizing: border-box;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: calc(100vh - 80px); /* Header magasságát leszámítva középre húzza */
    }

    .glass-card {
        background: var(--card-bg);
        padding: clamp(20px, 6vw, 50px);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        width: 100%;
        box-sizing: border-box;
    }

    h2 { 
        font-weight: 800; 
        margin-top: 0;
        margin-bottom: 35px; 
        letter-spacing: -1px; 
        font-size: clamp(1.4rem, 5vw, 2.2rem);
        text-align: center;
    }

    /* Grid rendszer finomítása */
    .input-grid { 
        display: grid; 
        grid-template-columns: repeat(2, 1fr); 
        gap: clamp(15px, 3vw, 25px); 
    }

    .field {
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .full-width { 
        grid-column: span 2; 
    }

    label { 
        font-size: 0.7rem; 
        text-transform: uppercase; 
        color: #aaa; 
        margin-bottom: 10px; 
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    input, select {
        width: 100%;
        background: rgba(255, 255, 255, 0.07);
        border: 1px solid rgba(255,255,255,0.15);
        padding: 14px;
        border-radius: 12px;
        color: #fff;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-sizing: border-box;
        -webkit-appearance: none; /* iOS fix */
    }

    select option {
        background-color: #1a1a1a;
        color: white;
    }

    input:focus, select:focus { 
        border-color: var(--accent-color); 
        outline: none; 
        background: rgba(255, 255, 255, 0.12);
        box-shadow: 0 0 0 3px rgba(0, 255, 225, 0.1);
    }

    .action-zone { 
        margin-top: 45px; 
        display: flex; 
        justify-content: center;
    }

    .btn-minimal {
        background: var(--accent-color);
        color: #000;
        border: none;
        padding: 18px 50px;
        font-weight: 800;
        font-size: 1.1rem;
        border-radius: 14px;
        cursor: pointer;
        transition: 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        width: 100%;
        max-width: 400px;
        text-transform: uppercase;
    }

    .btn-minimal:hover { 
        transform: translateY(-4px); 
        box-shadow: 0 15px 30px rgba(0, 255, 225, 0.4); 
        filter: brightness(1.1);
    }

    .status-msg { 
        padding: 18px; 
        border-radius: 15px; 
        margin-bottom: 30px; 
        font-size: 0.95rem;
        text-align: center;
        border: 1px solid transparent;
    }

    .success-lite { 
        background: rgba(0, 255, 225, 0.1); 
        color: var(--accent-color); 
        border-color: rgba(0, 255, 225, 0.3); 
    }

    .error-lite { 
        background: rgba(255, 71, 87, 0.1); 
        color: #ff4757; 
        border-color: rgba(255, 71, 87, 0.3); 
    }

    /* MÉDIA LEKÉRDEZÉSEK */

    /* Táblagép és kisebb asztali gép */
    @media (max-width: 800px) {
        .content-wrapper {
            margin-top: 20px;
        }
    }

    /* Mobil nézet (Váltás egy oszlopra) */
    @media (max-width: 650px) { 
        .input-grid { 
            grid-template-columns: 1fr; 
            gap: 15px;
        } 
        .full-width { 
            grid-column: span 1; 
        } 
        .glass-card {
            border-radius: 0; /* Mobilon gyakran jobban néz ki a teljes szélességű kártya */
            padding: 25px 20px;
            border: none;
            background: transparent;
            box-shadow: none;
        }
        .content-wrapper {
            padding: 10px;
            align-items: flex-start;
        }
        h2 {
            margin-bottom: 25px;
        }
        .btn-minimal {
            max-width: 100%;
            padding: 16px;
        }
    }

    /* Extra kicsi kijelzők (régebbi iPhone-ok) */
    @media (max-width: 380px) {
        input, select {
            padding: 12px;
            font-size: 0.9rem;
        }
    }
</style>

</body>
</html>