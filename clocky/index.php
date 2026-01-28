<?php
session_start();
include 'config.php';

/* =========================
   HA MÁR BE VAN JELENTKEZVE
   -> STABIL, LOOPMENTES REDIRECT
========================= */
if (isset($_SESSION['logged_in'], $_SESSION['role']) && $_SESSION['logged_in'] === true) {
    $role = (int)$_SESSION['role'];
    if ($role === 1) {
        header('Location: dashboard.php');
        exit;
    } elseif ($role === 0) {
        header('Location: empdashboard.php');
        exit;
    }
}

/* =========================
   BEJELENTKEZÉS KEZELÉSE
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Username and password are required.';
    } else {
        $stmt = $conn->prepare(
            'SELECT id, username, password, name, jogosultsag 
             FROM users 
             WHERE username = ?'
        );
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if ($password === trim($user['password'])) {
                // SESSION ADATOK
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = (int)$user['jogosultsag']; // 0 = user, 1 = admin

                // ÁTIRÁNYÍTÁS
                $role = (int)$user['jogosultsag'];
                if ($role === 1) {
                    header('Location: dashboard.php');
                } else {
                    header('Location: empdashboard.php');
                }
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        } else {
            $error = 'Invalid username or password.';
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Clocky</title>
<style>
* {margin:0; padding:0; box-sizing:border-box;}
body {font-family: Arial,sans-serif; background: linear-gradient(0, 255, 213, 0.95); min-height:100vh; display:flex; justify-content:center; align-items:center;}
.login-container {background: rgba(105, 105, 105, 0.95); padding:40px; border-radius:16px; width:100%; max-width:400px; text-align:center;}
h1 {font-size:48px;color:rgba(255, 255, 255, 0.95);}
h2 {font-size:20px;color:rgba(255, 255, 255, 0.95);}
.form-group {margin-bottom:20px;text-align:left;}
label {font-weight:bold;}
input {width:100%;padding:12px;border-radius:8px;border:1px solid #ccc;}
button {width:100%;padding:14px;background:#3a383bff;color:rgba(255, 255, 255, 0.95);;border:none;border-radius:8px;font-weight:bold;cursor:pointer;}
.error {background:#ffe5e5;color:#d32f2f;padding:12px;border-radius:8px;margin-bottom:15px;}
</style>
</head>
<body>

<div class="login-container">
    <h1>Clocky 🕐</h1>
    <h2>Tartsd Nyílván Az időd!</h2>

    <?php if (isset($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Felhasználónév</label>
            <input type="text" name="username" required>
        </div>

        <div class="form-group">
            <label>jelszó</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
