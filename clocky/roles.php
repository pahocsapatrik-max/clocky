<?php
session_start();
include 'config.php';

// 1. Jogosultság ellenőrzés
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 1) {
    header('Location: index.php');
    exit();
}

$page_title = 'Roles';
include 'header.php';

// Sikeres/Sikertelen művelet üzenetek kezelése
$success_msg = '';
$error_msg = '';
if (isset($_GET['success'])) $success_msg = "Munkakör sikeresen hozzáadva!";
if (isset($_GET['error'])) $error_msg = "Hiba történt a mentés során. Próbálja újra!";
?>

<div class="container">
    <h2>Összes Munkakör</h2>

    <style>
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            max-width: 900px;
            margin: 100px auto 50px auto;
            font-family: sans-serif;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }
        
        /* Üzenetek stílusa */
        .success {
            padding: 12px;
            background: #e8f5e9;
            color: #388e3c;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #c8e6c9;
        }
        .error {
            padding: 12px;
            background: #ffebee;
            color: #d32f2f;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #ffcdd2;
        }

        /* Táblázat stílusa (az előző oldal alapján) */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table thead {
            background: #00ffc3ff; /* Azonos türkiz szín */
            color: black;
        }
        .table th, .table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        .table tbody tr:hover {
            background: #f1f1f1;
            transition: 0.3s;
        }
        
        /* Form elemek (ha esetleg bővítenéd) */
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 10px;
        }
        .btn-action {
            width: 100%;
            padding: 12px;
            background: #333;
            color: black;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-action:hover {
            background: #444;
        }
    </style>

    <?php if (!empty($success_msg)): ?>
        <div class="success"><?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
        <div class="error"><?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <table class="table">
        <thead>
            <tr>
                <th>Munkakör Neve</th>
                <th>Fizetés (HUF / óra)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT roleID, role_name, pph_huf FROM role";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['role_name']) . "</td>
                            <td>" . number_format($row['pph_huf'], 0, ',', ' ') . " Ft</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='2' style='text-align:center;'>Nincsenek rögzített munkakörök.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php $conn->close(); ?>
</body>
</html>