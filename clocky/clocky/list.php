<?php
session_start();
require_once 'config.php';

// Ellenőrzés: be van-e jelentkezve?
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Session adatok
$user_id = $_SESSION['user_id'] ?? 0; 
$role = $_SESSION['role'] ?? 0; 

// SQL Lekérdezés
$sql = "SELECT 
            u.username, 
            r.role_name, 
            w.start_datetime, 
            w.end_datetime, 
            r.pph_HUF
        FROM worktime w
        JOIN USERS u ON w.FK_empID = u.ID
        JOIN role r ON w.FK_roleID = r.roleID";

if ($role == 0) {
    $sql .= " WHERE w.FK_empID = " . intval($user_id);
}

$sql .= " ORDER BY w.start_datetime DESC";
$result = $conn->query($sql);

// Itt hívjuk be a közös fejlécet
include_once 'header.php'; 
?>

<style>
    .list-container {
        padding: 40px 20px;
        display: flex;
        justify-content: center;
        background-color: #00ffe1; /* Háttérszín, ha a header nem fedné le */
        min-height: 100vh;
    }
    .data-card { 
        background: #ffffffff; 
        color: black; 
        padding: 40px; 
        border-radius: 30px; 
        box-shadow: 0 15px 40px rgba(0,0,0,0.4); 
        width: 100%;
        max-width: 1100px;
    }
    h1.title { 
        color: #00ffe1; 
        text-align: center; 
        margin-bottom: 30px; 
        text-transform: uppercase;
    }
    table { width: 100%; border-collapse: collapse; }
    th { color: #000000ff; padding: 15px; text-align: left; border-bottom: 2px solid #333; }
    td { padding: 15px; border-bottom: 1px solid #222; }
    .money-green { color: #129600ff; font-weight: bold; }
    .working-now { color: #ffbc00; font-style: italic; }
</style>

<div class="list-container">
    <div class="data-card">
        <h1 class="title">Munkaidő Napló</h1>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Dolgozó</th>
                        <th>Munkakör</th>
                        <th>Dátum / Kezdés</th>
                        <th>Befejezés</th>
                        <th>Időtartam</th>
                        <th>Kereset</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <?php
                            $start = new DateTime($row['start_datetime']);
                            $duration = "-";
                            $money = "-";

                            if ($row['end_datetime']) {
                                $end = new DateTime($row['end_datetime']);
                                $diff = $start->diff($end);
                                $duration = $diff->format('%hó %ip');

                                $total_hours = ($end->getTimestamp() - $start->getTimestamp()) / 3600;
                                $money = number_format($total_hours * $row['pph_HUF'], 0, ',', ' ') . " Ft";
                            } else {
                                $duration = '<span class="working-now">Aktív...</span>';
                            }
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['username']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['role_name']); ?></td>
                            <td><?php echo $start->format('Y.m.d. H:i'); ?></td>
                            <td><?php echo $row['end_datetime'] ? date('H:i', strtotime($row['end_datetime'])) : "-"; ?></td>
                            <td><?php echo $duration; ?></td>
                            <td class="money-green"><?php echo $money; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
// Ha van footer.php-d is, itt behívhatod:
// include_once 'footer.php'; 
?>