<?php
session_start();
include 'config.php';

// 1. Jogosultság ellenőrzés
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 1) {
    header('Location: index.php');
    exit();
}

$page_title = 'Employees';

// 2. Keresési logika és Adatbázis lekérdezés
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search !== '') {
    $stmt = $conn->prepare("
        SELECT e.empID, e.name, e.dob, e.tn, r.role_name, e.email
        FROM emp e
        LEFT JOIN role r ON e.FK_roleID = r.roleID
        WHERE e.name LIKE ?
    ");
    $like = "%" . $search . "%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("
        SELECT e.empID, e.name, e.dob, e.tn, r.role_name, e.email
        FROM emp e
        LEFT JOIN role r ON e.FK_roleID = r.roleID
    ");
}

// --- EXPORTÁLÁSI LOGIKA (Minden HTML kimenet előtt!) ---
if (isset($_GET['export'])) {
    $format = $_GET['export'];
    $export_data = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $export_data[] = $row;
        }
    }

    // TXT Export
    if ($format === 'txt') {
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="kimutatas.txt"');
        echo "ID\tNév\tSzületés\tMunkaidő\tMunkakör\tEmail\r\n";
        echo str_repeat("-", 80) . "\r\n";
        foreach ($export_data as $row) {
            echo "{$row['empID']}\t{$row['name']}\t{$row['dob']}\t{$row['tn']}\t" . ($row['role_name'] ?? 'Nincs') . "\t{$row['email']}\r\n";
        }
        exit();
    }

    
   
        
        $html .= '</tbody></table>';
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("kimutatas.pdf");
        exit();
    }


// 3. Megjelenítés kezdete
include 'header.php';
?>

<div class="container">
    <h2>Összes Dolgozó</h2>

    <style>
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            max-width: 900px;
            margin: 100px auto;
        }
        h2 {
            text-align: center;
            color: #000000ff;
            margin-bottom: 20px;
        }
        .search-box {
            margin-bottom: 15px;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #00ffe5ff;
            border-radius: 6px;
        }
        .btn-search {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            background: #383738;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-search:hover {
            background: #363636;
        }
        /* Export gombok */
        .export-actions {
            margin: 20px 0;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        .btn-export {
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
            color: white;
            transition: 0.3s;
        }
        .bg-txt { background-color: #6c757d; }
        
        .btn-export:hover { opacity: 0.8; }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        .table thead {
            background: #00ffbbff;
            color: white;
        }
        .table tbody tr:hover {
            background: #00e4baff;
        }
    </style>

    <form method="GET" class="search-box">
        <input 
            type="text" 
            name="search" 
            placeholder="keresés Név alapján..." 
            value="<?php echo htmlspecialchars($search); ?>"
        >
        <button type="submit" class="btn-search">keresés</button>
    </form>

    <div class="export-actions">
        <a href="?export=txt&search=<?php echo urlencode($search); ?>" class="btn-export bg-txt">Letöltés TXT</a>
      
      
    </div>

    <table class="table">
        <thead>
            <tr>
              
                <th>teljes Név</th>
                <th>Születési Dátum</th>
                <th>Munkaidő</th>
                <th>Munkakör</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                // Pointer visszaállítása a táblázathoz
                $result->data_seek(0);
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['dob']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['tn']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['role_name'] ?? 'No Role') . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center;'>No employees found.</td></tr>";
            }

            if (isset($stmt)) {
                $stmt->close();
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>