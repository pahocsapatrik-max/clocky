<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'] ?? 0;

// SQL lekérdezés a dizájnhoz optimalizálva
$sql = "SELECT 
            e.name as dolgozo_neve, 
            r.role_name, 
            w.start_datetime, 
            w.end_datetime, 
            r.pph_HUF
        FROM worktime w
        JOIN emp e ON w.FK_empID = e.empID
        JOIN role r ON w.FK_roleID = r.roleID";

if ($role == 0) {
    $sql .= " JOIN users u ON e.FK_userID = u.id WHERE u.username = '" . $conn->real_escape_string($username) . "'";
}

$sql .= " ORDER BY w.start_datetime DESC";
$result = $conn->query($sql);

include_once 'header.php'; 
?>

<style>
    :root {
        --bg-color: #0f0f0f;
        --card-bg: #1a1a1a;
        --accent-color: #00ffe1;
        --text-secondary: #888;
    }

    body {
        background: radial-gradient(circle at top right, #1e1e1e, var(--bg-color));
        color: #fff;
        font-family: 'Inter', sans-serif;
        margin: 0;
        padding: 40px 20px;
        display: flex;
        justify-content: center;
    }

    .glass-card {
        background: var(--card-bg);
        padding: 40px;
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
        width: 100%;
        max-width: 1100px;
    }

    h1.title {
        font-weight: 800;
        letter-spacing: -1px;
        margin-bottom: 30px;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    h1.title i { color: var(--accent-color); }

    .table-container { overflow-x: auto; }

    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .custom-table th {
        color: var(--text-secondary);
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        padding: 15px;
        text-align: left;
    }

    .custom-table td {
        background: rgba(255, 255, 255, 0.03);
        padding: 15px;
        border: none;
        transition: 0.3s;
    }

    .custom-table tr:hover td {
        background: rgba(255, 255, 255, 0.06);
        color: var(--accent-color);
    }

    .custom-table tr td:first-child { border-radius: 12px 0 0 12px; }
    .custom-table tr td:last-child { border-radius: 0 12px 12px 0; }

    .badge-duration {
        background: rgba(0, 255, 225, 0.1);
        color: var(--accent-color);
        padding: 5px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .badge-active {
        background: rgba(255, 188, 0, 0.1);
        color: #ffbc00;
        padding: 5px 12px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.8rem;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }

    .money-text {
        font-family: 'Mono', monospace;
        font-weight: 700;
        color: #fff;
    }
</style>

<div class="glass-card">
    <h1 class="title"><i class="fas fa-history"></i> Munkaidő Napló</h1>

    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Dolgozó</th>
                    <th>Munkakör</th>
                    <th>Kezdés</th>
                    <th>Befejezés</th>
                    <th>Időtartam</th>
                    <th>Kereset</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <?php
                            $start = new DateTime($row['start_datetime']);
                            $duration = "-";
                            $money = "-";

                            if ($row['end_datetime']) {
                                $end = new DateTime($row['end_datetime']);
                                $diff = $start->diff($end);
                                $duration = '<span class="badge-duration">' . $diff->format('%h óra %i p') . '</span>';

                                $total_seconds = $end->getTimestamp() - $start->getTimestamp();
                                $money = number_format(($total_seconds / 3600) * $row['pph_HUF'], 0, ',', ' ') . " Ft";
                            } else {
                                $duration = '<span class="badge-active">FOLYAMATBAN</span>';
                            }
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['dolgozo_neve']); ?></strong></td>
                            <td style="color: var(--text-secondary);"><?php echo htmlspecialchars($row['role_name']); ?></td>
                            <td><?php echo $start->format('Y.m.d. H:i'); ?></td>
                            <td><?php echo $row['end_datetime'] ? date('H:i', strtotime($row['end_datetime'])) : "---"; ?></td>
                            <td><?php echo $duration; ?></td>
                            <td class="money-text"><?php echo $money; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding: 50px; color: var(--text-secondary);">
                            Nincsenek rögzített adatok.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>