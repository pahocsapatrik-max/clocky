<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'] ?? 0;

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
        --accent-color: #00ffe1;
        --card-bg: #1a1a1a;
        --text-dim: #888;
        --input-bg: #252525;
    }

    body {
        background: #0f0f0f;
        color: #fff;
        font-family: 'Inter', sans-serif;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        box-sizing: border-box;
    }

    .glass-card {
        background: var(--card-bg);
        border-radius: 20px;
        border: 1px solid rgba(255,255,255,0.05);
        padding: clamp(15px, 4vw, 30px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    /* KERESŐ MEZŐ STÍLUS */
    .search-container {
        margin-bottom: 25px;
        position: relative;
    }

    .search-input {
        width: 100%;
        background: var(--input-bg);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 12px;
        padding: 15px 20px 15px 45px;
        color: white;
        font-family: 'Inter', sans-serif;
        font-size: 0.95rem;
        outline: none;
        transition: 0.3s;
        box-sizing: border-box;
    }

    .search-input:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 15px rgba(0, 255, 225, 0.1);
    }

    .search-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-dim);
        font-size: 0.9rem;
    }

    h1.title {
        font-size: clamp(1.3rem, 5vw, 1.8rem);
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .responsive-table {
        width: 100%;
        border-collapse: collapse;
    }

    .responsive-table thead tr {
        border-bottom: 2px solid rgba(255,255,255,0.05);
    }

    .responsive-table th {
        text-align: left;
        padding: 15px;
        color: var(--text-dim);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .responsive-table td {
        padding: 15px;
        border-bottom: 1px solid rgba(255,255,255,0.03);
        font-size: 0.95rem;
    }

    @media screen and (max-width: 850px) {
        .responsive-table thead { display: none; }
        .responsive-table, .responsive-table tbody, .responsive-table tr, .responsive-table td {
            display: block;
            width: 100%;
        }
        .responsive-table tr {
            margin-bottom: 20px;
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 10px;
        }
        .responsive-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            padding: 12px 10px;
            text-align: right;
        }
        .responsive-table td:last-child { border-bottom: none; }
        .responsive-table td::before {
            content: attr(data-label);
            font-weight: 600;
            color: var(--text-dim);
            text-transform: uppercase;
            font-size: 0.7rem;
            text-align: left;
        }
    }

    .badge { padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 700; }
    .badge-duration { background: rgba(0, 255, 225, 0.1); color: var(--accent-color); }
    .badge-active { background: rgba(255, 188, 0, 0.1); color: #ffbc00; }
    .money { font-family: monospace; font-weight: bold; color: #fff; }
</style>

<div class="container">
    <div class="glass-card">
        <h1 class="title"><i class="fas fa-history" style="color: var(--accent-color);"></i> Munkaidő Napló</h1>

        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="logSearch" class="search-input" placeholder="Keresés név vagy munkakör alapján...">
        </div>

        <table class="responsive-table" id="workTable">
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
                            $duration_html = '<span class="badge badge-active">FOLYAMATBAN</span>';
                            $money_val = "---";

                            if ($row['end_datetime']) {
                                $end = new DateTime($row['end_datetime']);
                                $diff = $start->diff($end);
                                $duration_html = '<span class="badge badge-duration">' . $diff->format('%hó %ip') . '</span>';
                                
                                $sec = $end->getTimestamp() - $start->getTimestamp();
                                $money_val = number_format(($sec / 3600) * $row['pph_HUF'], 0, ',', ' ') . " Ft";
                            }
                        ?>
                        <tr class="log-row">
                            <td data-label="Dolgozó" class="search-name"><strong><?php echo htmlspecialchars($row['dolgozo_neve']); ?></strong></td>
                            <td data-label="Munkakör" class="search-role"><?php echo htmlspecialchars($row['role_name']); ?></td>
                            <td data-label="Kezdés"><?php echo $start->format('Y.m.d. H:i'); ?></td>
                            <td data-label="Befejezés"><?php echo $row['end_datetime'] ? date('H:i', strtotime($row['end_datetime'])) : "---"; ?></td>
                            <td data-label="Időtartam"><?php echo $duration_html; ?></td>
                            <td data-label="Kereset" class="money"><?php echo $money_val; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr id="noDataRow"><td colspan="6" style="text-align:center;">Nincs adat.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('logSearch').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('.log-row');
        
        rows.forEach(row => {
            const name = row.querySelector('.search-name').textContent.toLowerCase();
            const role = row.querySelector('.search-role').textContent.toLowerCase();
            
            if (name.includes(filter) || role.includes(filter)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });
</script>