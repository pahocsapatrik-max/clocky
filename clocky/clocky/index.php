<?php
session_start();
include 'config.php';

/* =========================
   HA MÁR BE VAN JELENTKEZVE
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
        $error = 'Minden mezőt töltsön ki!';
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
            // Biztonsági megjegyzés: Élesben password_verify()-t használj!
            if ($password === trim($user['password'])) {
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = (int)$user['jogosultsag'];

                $role = (int)$user['jogosultsag'];
                if ($role === 1) {
                    header('Location: dashboard.php');
                } else {
                    header('Location: empdashboard.php');
                }
                exit;
            } else {
                $error = 'Hibás felhasználónév vagy jelszó!';
            }
        } else {
            $error = 'Hibás felhasználónév vagy jelszó!';
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-color: #0f0f0f;
            --card-bg: #1a1a1a;
            --accent-color: #00ffe1;
            --text-secondary: #888;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top right, #1e1e1e, var(--bg-color));
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }

        .login-card {
            background: var(--card-bg);
            padding: 50px 40px;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.8);
            width: 100%;
            max-width: 400px;
            text-align: center;
            position: relative;
        }

        /* Dekorációs elem */
        .login-card::before {
            content: '';
            position: absolute;
            top: -2px; left: -2px; right: -2px; bottom: -2px;
            background: linear-gradient(45deg, var(--accent-color), transparent, var(--accent-color));
            border-radius: 26px;
            z-index: -1;
            opacity: 0.1;
        }

        h1 {
            font-size: 3rem;
            font-weight: 900;
            letter-spacing: -2px;
            margin-bottom: 10px;
            color: #fff;
        }

        h1 span { color: var(--accent-color); }

        h2 {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: 40px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .form-group {
            margin-bottom: 25px;
            text-align: left;
        }

        label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-bottom: 8px;
            padding-left: 5px;
        }

        input {
            width: 100%;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #fff;
            font-size: 1rem;
            transition: 0.3s;
        }

        input:focus {
            outline: none;
            border-color: var(--accent-color);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 20px rgba(0, 255, 225, 0.1);
        }

        button {
            width: 100%;
            padding: 16px;
            background: var(--accent-color);
            color: #1a1a1a;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 800;
            text-transform: uppercase;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 255, 225, 0.3);
            filter: brightness(1.1);
        }

        .error {
            background: rgba(255, 71, 87, 0.1);
            color: #ff4757;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            border: 1px solid rgba(255, 71, 87, 0.2);
        }

        .footer-text {
            margin-top: 30px;
            font-size: 0.8rem;
            color: #444;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h1>Clocky<span>.</span></h1>
    <h2>Időmérés professzionálisan</h2>

    <?php if (isset($error)): ?>
        <div class="error">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label><i class="fas fa-user"></i> Felhasználónév</label>
            <input type="text" name="username" placeholder="felhasználónév" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-lock"></i> Jelszó</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit">Belépés <i class="fas fa-arrow-right"></i></button>
    </form>

    <div class="footer-text">
        &copy; 2026 Clocky Time Tracking System
    </div>
</div>

</body>
</html>