<?php
include 'db.php';

// Security: Only allow Admin to see this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// 1. Handle Search Logic
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// 2. SQL Query: Join Users with the Addresses table
// We fetch names from 'users' and location details from 'addresses'
$sql = "SELECT u.first_name, u.last_name, u.email, 
               a.address, a.city, a.phone 
        FROM users u
        INNER JOIN addresses a ON u.id = a.user_id 
        WHERE (u.first_name LIKE '%$search%' 
        OR u.last_name LIKE '%$search%' 
        OR a.city LIKE '%$search%'
        OR a.phone LIKE '%$search%')
        ORDER BY a.city ASC";

$result = $conn->query($sql);
$total_entries = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Delivery Addresses</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f8fafc; margin: 0; padding: 20px; color: #334155; }
        .container { max-width: 1100px; margin: 0 auto; background: #ffffff; border-radius: 20px; padding: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; }
        .title-area h2 { margin: 0; color: #136835; font-size: 24px; text-transform: uppercase; }
        .search-form { display: flex; align-items: center; background: #f1f5f9; padding: 5px 15px; border-radius: 30px; border: 1px solid #cbd5e1; width: 350px; }
        .search-form input { border: none; background: transparent; padding: 10px; outline: none; width: 100%; font-size: 14px; }
        .search-form button { background: none; border: none; cursor: pointer; color: #136835; }
        .table-wrapper { overflow-x: auto; border-radius: 12px; border: 1px solid #e2e8f0; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th { background: #f8fafc; padding: 15px; text-align: left; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #475569; border-bottom: 2px solid #e2e8f0; }
        td { padding: 16px 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .city-badge { background: #eef6ff; color: #0076ad; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; }
        .back-link { text-decoration: none; color: #0076ad; font-weight: 800; font-size: 13px; margin-bottom: 20px; display: inline-block; }
    </style>
</head>
<body>

<div class="container">
    <a href="admin_dashboard.php" class="back-link"><i class="fa fa-arrow-left"></i> Dashboard</a>

    <div class="admin-header">
        <div class="title-area">
            <h2><i class="fa fa-map-marker-alt"></i> Delivery Addresses</h2>
            <p>Data fetched from separate address table: <?php echo $total_entries; ?> entries</p>
        </div>

        <form method="GET" class="search-form">
            <button type="submit"><i class="fa fa-search"></i></button>
            <input type="text" name="search" placeholder="Search by name or city..." value="<?php echo htmlspecialchars($search); ?>">
        </form>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Street Address</th>
                    <th>City</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>