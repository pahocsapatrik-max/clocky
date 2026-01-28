<?php
session_start();
include 'config.php';
// Ellenőrizzük, hogy be van-e lépve ÉS admin-e (role === 1)
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 1) {
    header('Location: index.php');
    exit();
}
$page_title = 'Employees'; // Itt add meg az aktuális oldal nevét a menühöz
include 'header.php';
?>
<?
$page_title = 'Roles';
include 'header.php';


$success_msg = '';
$error_msg = '';
if (isset($_GET['success'])) $success_msg = "Role added successfully!";
if (isset($_GET['error'])) $error_msg = "Failed to add role. Try again.";

?>

<div class="container">
    <h2>Összes Munkakör</h2>

    <style>
        /* Táblázat stílus */
        .table {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 30px;
        }
        .table thead {
            background: linear-gradient(135deg, #00fff2ff 0%, #0cffb6ff 100%);
            color: white;
        }
        .table th, .table td {
            padding: 12px 15px;S
            text-align: left;
        }
        .table tbody tr {
            border-bottom: 1px solid #e0e0e0;
            transition: background-color 0.3s ease;
        }
        .table tbody tr:hover {
            background: linear-gradient(135deg, #00ffccff 0%, #06baceff 100%);
            color: white;
        }
        .table tbody tr:last-child {
            border-bottom: none;
        }

        /* Form és container stílus */
        .container {
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
            color: #000000ff;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
            color: #333;
        }
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        input:focus {
            border-color: #5b5a5cff;
            outline: none;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #454446ff;
            color: white;
            font-weight: bold;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background: #525252ff;
        }

        /* Üzenetek */
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
                <th>Fizetés Forintban</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT roleID, role_name, pph_huf FROM role";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                           
                            <td>" . htmlspecialchars($row['role_name']) . "</td>
                            <td>" . htmlspecialchars($row['pph_huf']) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No roles found</td></tr>";
            }
            ?>
        </tbody>
    </table>

    

</body>
</html>
