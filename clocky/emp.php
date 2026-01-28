<?php
session_start();
include 'config.php';

// 1. Jogosultság ellenőrzés
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 1) {
    header('Location: index.php');
    exit();
}

// 2. Keresési logika és adatbázis lekérdezés
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$queryStr = "
    SELECT e.empID, e.name, e.dob, e.tn, r.role_name, e.email
    FROM emp e
    LEFT JOIN role r ON e.FK_roleID = r.roleID
";

if ($search !== '') {
    $stmt = $conn->prepare($queryStr . " WHERE e.name LIKE ?");
    $like = "%" . $search . "%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($queryStr);
}

// --- 3. EXPORTÁLÁSI LOGIKA (TXT formátum) ---
if (isset($_GET['export']) && $_GET['export'] === 'txt') {
    $filename = "dolgozo_kimutatas_" . date('Y-m-d') . ".txt";
    
    header('Content-Type: text/plain; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Fejléc az importáláshoz (Tabulátorral elválasztva)
    echo "ID\tNev\tSzuletes\tMunkaido\tMunkakor\tEmail\r\n";
    
    if ($result && $result->num_rows > 0) {
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()) {
            echo $row['empID'] . "\t";
            echo $row['name'] . "\t";
            echo $row['dob'] . "\t";
            echo $row['tn'] . "\t";
            echo ($row['role_name'] ?? 'Nincs') . "\t";
            echo $row['email'] . "\r\n";
        }
    }
    exit();
}

$page_title = 'Employees';
include 'header.php';
?>

<div class="container">
    <h2>Munkaidő Nyilvántartás - Dolgozók</h2>

    <style>
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            max-width: 1000px;
            margin: 40px auto;
            font-family: Arial, sans-serif;
        }
        .search-box { margin-bottom: 20px; display: flex; gap: 10px; }
        input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .btn-search {
            padding: 10px 20px;
            background: #333;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .export-actions {
            margin-bottom: 20px;
            text-align: right;
        }
        .btn-export {
            display: inline-block;
            text-decoration: none;
            padding: 10px 15px;
            background: #007bff;
            color: #fff;
            border-radius: 4px;
            font-weight: bold;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 12px;
            border: 1px solid #eee;
            text-align: left;
        }
        .table thead { background: #00ffccff; }
        .table tr:hover { background: #fafafa; }
    </style>

    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Keresés név alapján..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn-search">Keresés</button>
    </form>

    <div class="export-actions">
        <a href="?export=txt&search=<?php echo urlencode($search); ?>" class="btn-export">
            📄 Kimutatás letöltése (TXT)
        </a>
    </div>

    <table class="table">
        <thead>
            <tr>
              
                <th>Név</th>
                <th>Születés</th>
                <th>Munkaidő</th>
                <th>Munkakör</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                $result->data_seek(0);
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                      
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['dob']) . "</td>
                        <td>" . htmlspecialchars($row['tn']) . "</td>
                        <td>" . htmlspecialchars($row['role_name'] ?? 'Nincs') . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center;'>Nincs találat.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php
if (isset($stmt)) { $stmt->close(); }
$conn->close();
?>