<?php
session_start();
require_once 'config.php';

// --- OKOS MAGYARÍTOTT ROUTER ---
$request_uri = $_SERVER['REQUEST_URI'];

// Csak akkor irányítunk át, ha a felhasználó konkrétan a .php fájlt hívta meg
// és NEM API hívás történik (hogy a beküldés működjön).
if (strpos($request_uri, 'add_emp.php') !== false && !isset($_GET['api'])) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: uj-dolgozo");
    exit();
}
// --- ROUTER VÉGE ---

// --- RESTful API LOGIKA (BACKEND) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['api'])) {
    header('Content-Type: application/json');
    
    // Jogosultság ellenőrzése az API híváshoz
    if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 1) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Nincs jogosultsága a művelethez.']);
        exit();
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $dob = $_POST['dob'] ?? NULL;
    $tn = $_POST['tn'] ?? 0;
    $roleID = $_POST['roleID'] ?? NULL;

    if (empty($name) || empty($email) || empty($roleID)) {
        echo json_encode(['status' => 'error', 'message' => 'Minden kötelező mezőt ki kell tölteni!']);
        exit();
    }

    $conn->begin_transaction();
    try {
        // Felhasználónév generálása
        $email_parts = explode('@', $email)[0];
        $clean_username = preg_replace("/[^A-Za-z0-9]/", '', $email_parts);
        $default_username = strtolower(substr($clean_username, 0, 8));
        
        // Biztonságosabb jelszó generálás
        $alphabet = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $pass = array(); 
        $alphaLength = strlen($alphabet) - 1; 
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        $default_password = implode($pass);

        // 1. Mentés a users táblába
        $user_stmt = $conn->prepare("INSERT INTO users (username, password, name, jogosultsag, FK_roleID) VALUES (?, ?, ?, 0, ?)");
        $user_stmt->bind_param("sssi", $default_username, $default_password, $name, $roleID);
        $user_stmt->execute();
        
        // 2. Mentés az emp táblába
        $emp_stmt = $conn->prepare("INSERT INTO emp (name, email, dob, tn, FK_roleID, active) VALUES (?, ?, ?, ?, ?, 1)");
        $emp_stmt->bind_param("ssssi", $name, $email, $dob, $tn, $roleID);
        $emp_stmt->execute();

        // 3. Email küldése (A tiszta bejelentkezes URL-t használva)
        $subject = "Sikeres regisztráció - Clocky ";
        $from_email = "clockytimer@gmail.com"; 
// --- BEÁLLÍTÁSOK ---
 

// --- 1. KÉP BEÁLLÍTÁSA ---
// Ide a képed elérési útját írd be (pl. img/logo.png)
$path = 'logo.png'; 
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
// Ez alakítja át a képet szöveggé, amit az email el tud olvasni:
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

$message = "
<html>
<head>
    <style>
        body { 
            background-color: #0f0f0f; 
            color: #ffffff; 
            padding: 20px; 
            font-family: 'Inter', sans-serif; 
            margin: 0;
        }
        .login-card {
            background-color: #1a1a1a;
            border-radius: 24px;
            padding: 45px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            max-width: 500px;
            margin: 40px auto;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.8);
        }
        /* Clocky Felirat Stílusa */
        h1 {
            font-size: 42px;
            font-weight: 900;
            letter-spacing: -2px;
            color: #ffffff;
            margin: 0;
            display: inline-block;
        }
        h1 span {
            display: inline-block;
            width: 14px;
            height: 14px;
            background-color: #00ffe1;
            margin-left: 8px;
            border-radius: 2px;
        }
        .welcome-text {
            font-size: 26px;
            font-weight: 800;
            display: block;
            margin-bottom: 5px;
            color: #ffffff;
        }
        .highlight-name {
            color: #00ffe1;
        }
        .data-box {
            background: rgba(255, 255, 255, 0.03);
            padding: 20px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            text-align: left;
            margin-bottom: 15px;
        }
        .label {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            color: #888;
            letter-spacing: 2px;
            margin-bottom: 5px;
            display: block;
        }
        .value {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
        }
        .warning-section {
            background: rgba(0, 255, 225, 0.05);
            border: 1px solid rgba(0, 255, 225, 0.2);
            border-radius: 16px;
            padding: 25px;
            margin: 35px 0;
        }
        .btn {
            background: #00ffe1;
            color: #1a1a1a;
            padding: 20px 50px;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 900;
            text-transform: uppercase;
            font-size: 14px;
            display: inline-block;
            box-shadow: 0 10px 20px rgba(0, 255, 225, 0.2);
        }
    </style>
</head>
<body>
    <div class='login-card'>
        
        <div style='margin-bottom: 40px;'>
            <h1>Clocky<span></span></h1>
        </div>

        <span class='welcome-text'>Szia, <span class='highlight-name'>$name</span>!</span>
        <p style='color: #888; margin-bottom: 35px; margin-top: 0;'>Sikeresen regisztráltak a rendszerbe.</p>

        <div class='data-box'>
            <span class='label'>Felhasználónév</span>
            <div class='value'>$default_username</div>
        </div>
        <div class='data-box' style='margin-bottom: 30px;'>
            <span class='label'>Ideiglenes jelszó</span>
            <div class='value' style='font-family: monospace; color: #00ffe1;'>$default_password</div>
        </div>

        <div class='warning-section'>
            <div style='color: #00ffe1; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px;'>Biztonsági értesítés</div>
            <p style='font-size: 16px; font-weight: bold; color: #ffffff; margin-bottom: 10px;'></p>
            <p style='margin: 0; font-size: 15px; line-height: 1.5; color: #888;'>
                A fiókja védelme érdekében a belépés után <strong style='color: #00ffe1; text-decoration: underline;'>azonnal változtassa meg a jelszavát</strong> a profiljánál!
            </p>
        </div>

        <div style='text-align: center;'>
            <a href='http://localhost/clocky/bejelentkezes' class='btn'>Belépés a fiókba</a>
        </div>

        <div style='text-align: center; color: #444; font-size: 11px; margin-top: 50px; border-top: 1px solid #222; padding-top: 25px;'>
            &copy; " . date("Y") . " Clocky Timer Systems
        </div>
    </div>
</body>
</html>";
        $headers = "From: Clocky Timer <$from_email>\r\nReply-To: $from_email\r\n";
        $headers .= "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";

        $mail_sent = @mail($email, $subject, $message, $headers);
        $conn->commit();

        echo json_encode([
            'status' => 'success', 
            'message' => 'Sikeres mentés!' . ($mail_sent ? ' Az emailt elküldtük.' : ' De az email küldése nem sikerült.')
        ]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Hiba történt: ' . $e->getMessage()]);
    }
    exit();
}

// --- MEGJELENÍTÉS (FRONTEND) ---
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 1) {
    header('Location: bejelentkezes'); 
    exit();
}

$page_title = 'Új dolgozó hozzáadása';
include_once 'header.php';
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Új munkatárs | Clocky</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --accent-color: #00ffe1; --card-bg: #1a1a1a; --text-color: #ffffff; }
        body { background: radial-gradient(circle at top right, #1e1e1e, #0f0f0f); color: var(--text-color); font-family: 'Inter', sans-serif; margin: 0; min-height: 100vh; display: flex; flex-direction: column; }
        .content-wrapper { width: 100%; max-width: 800px; margin: 60px auto; padding: 20px; box-sizing: border-box; }
        .glass-card { background: var(--card-bg); padding: clamp(20px, 5vw, 40px); border-radius: 24px; border: 1px solid rgba(255, 255, 255, 0.05); box-shadow: 0 25px 50px rgba(0,0,0,0.5); position: relative; overflow: hidden; }
        h2 { font-weight: 800; margin-bottom: 35px; letter-spacing: -1px; display: flex; align-items: center; gap: 15px; }
        h2 i { color: var(--accent-color); }
        .input-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 25px; }
        .field { display: flex; flex-direction: column; }
        .full-width { grid-column: span 2; }
        label { font-size: 0.7rem; text-transform: uppercase; color: #888; margin-bottom: 10px; letter-spacing: 1.5px; font-weight: 600; }
        input, select { width: 100%; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255,255,255,0.08); padding: 15px; border-radius: 14px; color: white; font-size: 1rem; transition: 0.3s; box-sizing: border-box; }
        input:focus { border-color: var(--accent-color); outline: none; background: rgba(255, 255, 255, 0.07); }
        .btn-minimal { background: var(--accent-color); color: #000; border: none; padding: 18px 50px; font-weight: 800; border-radius: 15px; cursor: pointer; transition: 0.3s; text-transform: uppercase; width: 100%; }
        .btn-minimal:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0, 255, 225, 0.3); }
        .btn-minimal:disabled { opacity: 0.5; cursor: not-allowed; }
        #response-msg { padding: 18px; border-radius: 15px; margin-bottom: 30px; display: none; font-size: 0.95rem; border-left: 5px solid; animation: slideIn 0.3s ease; }
        .success { background: rgba(0, 255, 225, 0.05); color: var(--accent-color); border-color: var(--accent-color); }
        .error { background: rgba(255, 71, 87, 0.05); color: #ff4757; border-color: #ff4757; }
        @keyframes slideIn { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        @media (max-width: 650px) { .input-grid { grid-template-columns: 1fr; } .full-width { grid-column: span 1; } }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="content-wrapper">
    <div class="glass-card">
        <h2><i class="fas fa-user-plus"></i> Új Munkatárs</h2>
        
        <div id="response-msg"></div>

        <form id="addEmployeeForm">
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
                        while($r = $roles->fetch_assoc()) echo "<option value='{$r['roleID']}'>{$r['role_name']}</option>";
                        ?>
                    </select>
                </div>
            </div>
            <div class="action-zone" style="margin-top: 40px;">
                <button type="submit" id="submitBtn" class="btn-minimal">Dolgozó Rögzítése</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('addEmployeeForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = e.target;
    const btn = document.getElementById('submitBtn');
    const msgDiv = document.getElementById('response-msg');
    const formData = new FormData(form);

    // Gomb tiltása és visszajelzés indítása
    btn.disabled = true;
    btn.innerText = 'Mentés folyamatban...';
    msgDiv.style.display = 'none';

    try {
        const response = await fetch('?api=1', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        msgDiv.innerText = result.message;
        msgDiv.className = result.status === 'success' ? 'success' : 'error';
        msgDiv.style.display = 'block';
        msgDiv.classList.add(result.status);

        if (result.status === 'success') {
            form.reset(); // Form ürítése siker esetén
        }
    } catch (error) {
        msgDiv.innerText = 'Hálózati hiba történt. Próbálja újra!';
        msgDiv.className = 'error';
        msgDiv.style.display = 'block';
    } finally {
        btn.disabled = false;
        btn.innerText = 'Dolgozó Rögzítése';
    }
});
</script>

</body>
</html>