<?php
session_start();
include 'config.php';

// 1. Jogosultság ellenőrzés
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 1) {
    header('Location: index.php');
    exit();
}

$page_title = 'Employees';

// 2. Keresési logika és Adatbázis lekérdezés (még a HTML előtt!)
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

// 3. Header behúzása
include 'header.php';
?>

<div class="container">
    <h2>All Employees</h2>

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
            color: #b50ddf;
            margin-bottom: 20px;
        }
        .search-box {
            margin-bottom: 15px;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #d40ee6;
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
            background: #a407e2;
            color: white;
        }
        .table tbody tr:hover {
            background: #f3e5f5;
        }
    </style>

    <form method="GET" class="search-box">
        <input 
            type="text" 
            name="search" 
            placeholder="Search by name..." 
            value="<?php echo htmlspecialchars($search); ?>"
        >
        <button type="submit" class="btn-search">Search</button>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>DOB</th>
                <th>Work Hours</th>
                <th>Role</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // 4. Adatok kiírása
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['empID']) . "</td>";
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

            // Kapcsolat lezárása
            if (isset($stmt)) {
                $stmt->close();
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>