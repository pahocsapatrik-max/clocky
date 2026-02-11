<?php
session_start();

// --- OKOS MAGYARÍTOTT ROUTER ---
$request_uri = $_SERVER['REQUEST_URI'];
$current_file = basename($_SERVER['PHP_SELF']);

// Útvonal térkép
$routes = [
    'list.php'  => 'lista',
    'index.php' => 'bejelentkezes'
];

// Ha .php kiterjesztéssel hívják meg, átirányítjuk a magyarosított aliasra (lista)
if (strpos($request_uri, '.php') !== false && !isset($_GET['api'])) {
    $pretty_name = $routes[$current_file] ?? 'lista';
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: " . $pretty_name);
    exit();
}
// --- ROUTER VÉGE ---

require_once 'config.php';

// --- RESTful API LOGIKA (BACKEND) ---
if (isset($_GET['api'])) {
    header('Content-Type: application/json');

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
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
        // Biztonságosabb lekérdezés a felhasználónév alapján
        $sql .= " JOIN users u ON e.name = u.name WHERE u.username = '" . $conn->real_escape_string($username) . "'";
    }

    $sql .= " ORDER BY w.start_datetime DESC";
    $result = $conn->query($sql);

    $data = [];
    while($row = $result->fetch_assoc()) {
        $start = new DateTime($row['start_datetime']);
        $duration = "FOLYAMATBAN";
        $money = "---";

        if ($row['end_datetime']) {
            $end = new DateTime($row['end_datetime']);
            $diff = $start->diff($end);
            $duration = $diff->format('%h ó %i p');
            
            $sec = $end->getTimestamp() - $start->getTimestamp();
            $money = number_format(($sec / 3600) * $row['pph_HUF'], 0, ',', ' ') . " Ft";
            $end_time = $end->format('H:i');
        } else {
            $end_time = "---";
        }

        $data[] = [
            'name' => htmlspecialchars($row['dolgozo_neve']),
            'role' => htmlspecialchars($row['role_name']),
            'start' => $start->format('Y.m.d. H:i'),
            'end' => $end_time,
            'duration' => $duration,
            'money' => $money,
            'is_active' => !$row['end_datetime']
        ];
    }
    echo json_encode($data);
    exit;
}

// --- MEGJELENÍTÉS (FRONTEND) ---
if (!isset($_SESSION['logged_in'])) {
    header('Location: bejelentkezes'); // Magyarított átirányítás
    exit;
}

$page_title = "Munkaidő Lista";
include_once 'header.php'; 
?>

<style>
    :root {
        --accent-color: #00ffe1;
        --card-bg: #1a1a1a;
        --text-dim: #888;
        --input-bg: #252525;
    }

    body { background: #0f0f0f; color: #fff; font-family: 'Inter', sans-serif; }

    .container { width: 100%; max-width: 1200px; margin: 0 auto; padding: 20px; box-sizing: border-box; }

    .glass-card {
        background: var(--card-bg);
        border-radius: 20px;
        border: 1px solid rgba(255,255,255,0.05);
        padding: clamp(15px, 4vw, 30px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    .search-container { margin-bottom: 25px; position: relative; }

    .search-input {
        width: 100%; background: var(--input-bg); border: 1px solid rgba(255,255,255,0.1);
        border-radius: 12px; padding: 15px 20px 15px 45px; color: white;
        font-size: 0.95rem; outline: none; transition: 0.3s; box-sizing: border-box;
    }

    .search-input:focus { border-color: var(--accent-color); box-shadow: 0 0 15px rgba(0, 255, 225, 0.1); }

    .search-icon { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: var(--text-dim); }

    h1.title { font-size: clamp(1.3rem, 5vw, 1.8rem); margin-bottom: 25px; display: flex; align-items: center; gap: 12px; }

    .responsive-table { width: 100%; border-collapse: collapse; }
    .responsive-table th { text-align: left; padding: 15px; color: var(--text-dim); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid rgba(255,255,255,0.05); }
    .responsive-table td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.03); font-size: 0.95rem; }

    .badge { padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 700; }
    .badge-duration { background: rgba(0, 255, 225, 0.1); color: var(--accent-color); }
    .badge-active { background: rgba(255, 188, 0, 0.1); color: #ffbc00; }
    .money { font-family: monospace; font-weight: bold; }

    /* Mobil nézet */
    @media screen and (max-width: 850px) {
        .responsive-table thead { display: none; }
        .responsive-table tr { display: block; margin-bottom: 20px; background: rgba(255,255,255,0.02); border-radius: 15px; padding: 10px; border: 1px solid rgba(255,255,255,0.05); }
        .responsive-table td { display: flex; justify-content: space-between; align-items: center; text-align: right; padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .responsive-table td:last-child { border-bottom: none; }
        .responsive-table td::before { content: attr(data-label); font-weight: 600; color: var(--text-dim); font-size: 0.7rem; text-transform: uppercase; }
    }
</style>

<div class="container">
    <div class="glass-card">
        <h1 class="title"><i class="fas fa-history" style="color: var(--accent-color);"></i> Munkaidő Napló</h1>

        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="logSearch" class="search-input" placeholder="Keresés név vagy munkakör alapján...">
        </div>

        <table class="responsive-table">
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
            <tbody id="workTableBody">
                </tbody>
        </table>
    </div>
</div>

<script>
    let workData = [];

    async function fetchLogs() {
        try {
            const response = await fetch('?api=1');
            workData = await response.json();
            renderTable(workData);
        } catch (error) {
            console.error('Hiba az adatok betöltésekor:', error);
        }
    }

    function renderTable(data) {
        const tbody = document.getElementById('workTableBody');
        tbody.innerHTML = '';

        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Nincs megjeleníthető adat.</td></tr>';
            return;
        }

        data.forEach(item => {
            const durationBadge = item.is_active 
                ? `<span class="badge badge-active">FOLYAMATBAN</span>`
                : `<span class="badge badge-duration">${item.duration}</span>`;

            const row = `
                <tr>
                    <td data-label="Dolgozó"><strong>${item.name}</strong></td>
                    <td data-label="Munkakör">${item.role}</td>
                    <td data-label="Kezdés">${item.start}</td>
                    <td data-label="Befejezés">${item.end}</td>
                    <td data-label="Időtartam">${durationBadge}</td>
                    <td data-label="Kereset" class="money">${item.money}</td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }

    document.getElementById('logSearch').addEventListener('input', function(e) {
        const filter = e.target.value.toLowerCase();
        const filteredData = workData.filter(item => 
            item.name.toLowerCase().includes(filter) || 
            item.role.toLowerCase().includes(filter)
        );
        renderTable(filteredData);
    });

    // Kezdeti betöltés
    fetchLogs();
</script>