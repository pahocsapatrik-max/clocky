<?php
session_start();
include 'config.php';


if (!isset($_SESSION['user']) || !$_SESSION['logged_in']) {
    header('Location: index.php');
    exit();
}
$page_title = 'Users';
include 'header.php';
?>

<div class="container">
<h2>All users</h2>
<style>
    .table {
        width: 100%;
        border-collapse: collapse;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }
    
    .table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
    }
    
    .table tbody tr {
        border-bottom: 1px solid #e0e0e0;
        transition: background-color 0.3s ease;
    }
    
    .table tbody tr:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .table td {
        padding: 12px 15px;
    }
    
    .table tbody tr:last-child {
        border-bottom: none;
    }
</style>
<table class="table table-bordered">
   <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            
        </tr>
    </thead>
    <tbody>
        <?php
       $sql = "SELECT ID, nev FROM users";
$result = $conn->query($sql);


        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['ID']) . "</td>
                        <td>" . htmlspecialchars($row['nev']) . "</td>
                       
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No roles found</td></tr>";
        }
        ?>
    </tbody>
</table>