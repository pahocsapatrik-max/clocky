<?php
// 1. ADATBÁZIS KAPCSOLAT ÉS HEADER
$conn = new mysqli("localhost", "root", "", "clocky");

if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}
$conn->set_charset("utf8");

// Itt hívjuk be a közös fejlécet
include 'header.php';

// 2. AUTOMATIKUS ARCHIVÁLÁS 
// (A 'dob' születési dátum, így az automatikus részt inaktiváltam, hogy ne archiváljon mindenkit)
$twoYearsAgo = date('Y-m-d', strtotime('-2 years'));
// $conn->query("UPDATE emp SET active = 0 WHERE valamilyen_datum_mezo < '$twoYearsAgo' AND active = 1");

// 3. VISSZAÁLLÍTÁS KEZELÉSE
$message = "";
if (isset($_GET['restore_id'])) {
    $id = intval($_GET['restore_id']);
    if ($conn->query("UPDATE emp SET active = 1 WHERE empID = $id")) {
        $message = "<div class='alert success'>Dolgozó sikeresen visszaállítva az aktív állományba!</div>";
    }
}

// 4. ARCHIVÁLT DOLGOZÓK LEKÉRDEZÉSE (active = 0)
$sql = "SELECT * FROM emp WHERE active = 0 ORDER BY name ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Clocky - Archívum</title>
    <style>
        body { background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 1100px; margin: 40px auto; font-family: Arial, sans-serif; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 12px; border: 1px solid #eee; text-align: left; }
        .table thead { background: #00ffcc; }
        .btn-edit { background: #ffc107; color: #000; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 13px; font-weight: bold; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; font-family: sans-serif; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        h1 { margin: 0; color: #333; }
        /* Export gomb stílusa */
        .btn-export { display: inline-block; text-decoration: none; padding: 10px 15px; background: #007bff; color: #fff; border-radius: 4px; font-weight: bold; border: none; cursor: pointer; }
        .export-actions { margin-bottom: 20px; text-align: right; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-flex">
        <h1>Archivált Dolgozók</h1>
        </div>

    <?= $message ?>

   

    <table class="table">
        <thead>
            <tr>
                <th>Név</th>
                <th>Email</th>
                <th>Születési dátum</th>
                <th>Műveletek</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= $row['dob'] ?></td>
                        <td>
                            <a href="?restore_id=<?= $row['empID'] ?>" class="btn-edit" onclick="return confirm('Biztosan visszaállítja ezt a dolgozót?')">Visszaállítás</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align:center; padding: 40px; color: #999;">
                        Nincsenek archivált dolgozók a rendszerben.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
<?php $conn->close(); ?>