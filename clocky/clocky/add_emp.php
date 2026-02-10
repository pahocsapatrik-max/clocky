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

    if (!empty($name) && !empty($email) && !empty($roleID)) {
        
        $conn->begin_transaction();

        try {
            $default_username = explode('@', $email)[0];
            $default_password = "1234"; 
            $default_jogosultsag = 0; 

            // 1. Beszúrás a USERS táblába
            // Oszlopok: username, password, name, jogosultsag, FK_roleID
            $user_stmt = $conn->prepare("INSERT INTO users (username, password, name, jogosultsag, FK_roleID) VALUES (?, ?, ?, ?, ?)");
            $user_stmt->bind_param("sssii", $default_username, $default_password, $name, $default_jogosultsag, $roleID);
            $user_stmt->execute();
            
            // 2. Beszúrás az EMP táblába 
            // Az általad megadott oszlopok: name, dob, tn, FK_roleID, email, active
            // (Az empID autoincrement, az active-ot fix 1-re állítjuk)
            $stmt = $conn->prepare("INSERT INTO emp (name, email, dob, tn, FK_roleID, active) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("ssssi", $name, $email, $dob, $tn, $roleID);
            
            if ($stmt->execute()) {
                $conn->commit();
                $success_msg = "Sikeres mentés! <br> Felhasználónév: <strong>$default_username</strong> | Jelszó: <strong>$default_password</strong>";
            } else {
                throw new Exception("Hiba történt az emp tábla mentésekor: " . $conn->error);
            }

        } catch (Exception $e) {
            $conn->rollback();
            $error_msg = "Adatbázis hiba: " . $e->getMessage();
        }
    } else {
        $error_msg = "Minden kötelező mezőt ki kell tölteni!";
    }
}

$page_title = 'Új dolgozó rögzítése';
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --accent-color: #00ffe1;
            --card-bg: #1a1a1a;
            --text-color: #ffffff;
        }

        body {
            background: radial-gradient(circle at top right, #1e1e1e, #0f0f0f);
            color: var(--text-color);
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .content-wrapper {
            width: 100%;
            max-width: 800px;
            margin: 60px auto;
            padding: 20px;
            box-sizing: border-box;
        }

        .glass-card {
            background: var(--card-bg);
            padding: clamp(20px, 5vw, 40px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }

        h2 { 
            font-weight: 800; 
            margin-bottom: 35px; 
            letter-spacing: -1px; 
            font-size: clamp(1.4rem, 5vw, 2rem);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        h2 i { color: var(--accent-color); }

        .input-grid { 
            display: grid; 
            grid-template-columns: repeat(2, 1fr); 
            gap: 25px; 
        }

        .field { display: flex; flex-direction: column; }
        .full-width { grid-column: span 2; }

        label { 
            font-size: 0.7rem; 
            text-transform: uppercase; 
            color: #888; 
            margin-bottom: 10px; 
            letter-spacing: 1.5px;
            font-weight: 600;
        }

        input, select {
            width: 100%;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255,255,255,0.08);
            padding: 15px;
            border-radius: 14px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        input:focus, select:focus { 
            border-color: var(--accent-color); 
            outline: none; 
            background: rgba(255, 255, 255, 0.07);
            box-shadow: 0 0 20px rgba(0, 255, 225, 0.15);
        }

        select option { background-color: #1a1a1a; }

        .action-zone { 
            margin-top: 40px; 
            display: flex; 
            justify-content: center;
        }

        .btn-minimal {
            background: var(--accent-color);
            color: #000;
            border: none;
            padding: 18px 50px;
            font-weight: 800;
            font-size: 1rem;
            border-radius: 15px;
            cursor: pointer;
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            width: 100%;
            max-width: 350px;
        }

        .btn-minimal:hover { 
            transform: translateY(-4px); 
            box-shadow: 0 12px 25px rgba(0, 255, 225, 0.4); 
            filter: brightness(1.1);
        }

        .status-msg { 
            padding: 18px; 
            border-radius: 15px; 
            margin-bottom: 30px; 
            font-size: 0.95rem;
            border-left: 5px solid;
        }

        .success-lite { 
            background: rgba(0, 255, 225, 0.05); 
            color: var(--accent-color); 
            border-color: var(--accent-color); 
        }

        .error-lite { 
            background: rgba(255, 71, 87, 0.05); 
            color: #ff4757; 
            border-color: #ff4757; 
        }

        @media (max-width: 650px) { 
            .input-grid { grid-template-columns: 1fr; } 
            .full-width { grid-column: span 1; } 
            .content-wrapper { margin: 20px auto; }
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="content-wrapper">
    <div class="glass-card">
        <h2><i class="fas fa-user-plus"></i> Új Munkatárs</h2>
        
        <?php if (!empty($success_msg)): ?>
            <div class="status-msg success-lite"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>
            <div class="status-msg error-lite"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form method="POST">
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
                <button type="submit" class="btn-minimal">Rögzítés </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>