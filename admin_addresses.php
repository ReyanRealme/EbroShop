<?php
include 'db.php';

// Security
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// 1. FULL NAME SEARCH FIX: Allows searching with spaces like "Rebyu Dejene"
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Search across concatenated name, city, and phone

$sql = "SELECT u.first_name, u.last_name, u.email, u.phone, 
               a.address1, a.address2, a.city, a.country
        FROM users u
        INNER JOIN addresses a ON u.id = a.user_id 
        WHERE (CONCAT(u.first_name, ' ', u.last_name) LIKE '%$search%' 
        OR a.city LIKE '%$search%' 
        OR u.phone LIKE '%$search%')
        ORDER BY u.id DESC";

$result = $conn->query($sql);

// Fallback for column spelling (adress1 vs address1)
if (!$result) {
    $sql = str_replace("address1", "adress1", $sql);
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Delivery Admin | Ebroshop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Roboto, sans-serif; background: #f0f2f5; margin: 0; padding: 12px; }
        .admin-card { background: #fff; border-radius: 16px; padding: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border-top: 6px solid #136835; }
        
        .search-container { display: flex; align-items: center; background: #f1f3f5; padding: 12px; border-radius: 12px; margin: 15px 0; border: 1px solid #e2e8f0; }
        .search-container input { border: none; background: transparent; width: 100%; outline: none; font-size: 16px; }
        
        .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; min-width: 480px; }
        th { text-align: left; padding: 12px 8px; font-size: 11px; text-transform: uppercase; color: #64748b; background: #fafafa; border-bottom: 2px solid #eee; }
        td { padding: 12px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        
        .user-name { font-weight: 700; color: #136835; font-size: 14px; display: block; }
        .addr-info { font-size: 13px; color: #475569; line-height: 1.4; }
        
        /* Copy Button Styling */
        .copy-btn { background: #136835; color: white; border: none; padding: 6px 10px; border-radius: 6px; font-size: 11px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; margin-top: 5px; transition: 0.2s; }
        .copy-btn:active { transform: scale(0.95); opacity: 0.8; }
        
        .city-badge { background: #e6fffa; color: #136835; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 800; }
        .phone-link { color: #136835; text-decoration: none; font-weight: 800; font-size: 13px; border: 1px solid #136835; padding: 4px 8px; border-radius: 6px; }

        .back-nav { text-decoration: none; color: #64748b; font-size: 13px; font-weight: 600; display: inline-block; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="admin-card">
    <a href="admin_dashboard.php" class="back-nav"><i class="fa fa-arrow-left"></i> Dashboard</a>
    <h2 style="margin:0; font-size:22px; color:#1e293b;">Delivery Directory</h2>

    <form method="GET" class="search-container">
        <i class="fa fa-search" style="color:#136835; margin-right:10px;"></i>
        <input type="text" name="search" placeholder="Search full name (e.g. Rebyu Dejene)" value="<?php echo htmlspecialchars($search); ?>">
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Full Address</th>
                    <th>City</th>
                    <th>Contact</th>
                </tr>
            </thead>
            <tbody>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
           <?php 
    // 1. Get the address parts
    $main_addr = $row['address1'] ?? $row['adress1'] ?? '';
    $extra_addr = $row['address2'] ?? '';
    $city = $row['city'] ?? '';

    // 2. Format for MAPS: "Address 1, Address 2, City"
    // We remove "Customer:" and "Phone:" so Maps can find the location immediately
                  $map_address = $main_addr;
                  if (!empty($extra_addr)) { $map_address .= " " . $extra_addr; }
                  $map_address .= ", " . $city;
              
                  // Clean up any double spaces and escape for JavaScript
                  $copy_for_maps = addslashes(trim($map_address));
              ?>
        
           <tr>
                <td>
                    <span class="user-name"><?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></span>
                    <small style="color:#94a3b8; font-size:11px;"><?php echo htmlspecialchars($row['email']); ?></small>
                </td>
                  <td class="addr-info">
        <?php echo htmlspecialchars($main_addr); ?><br>
        
        <button class="copy-btn" onclick="copyToClipboard('<?php echo $copy_for_maps; ?>', this)">
            <i class="fa fa-location-dot"></i> Copy for Maps
        </button>
    </td>
                <td>
                    <span class="city-badge"><?php echo htmlspecialchars($row['city']); ?></span>
                </td>
                <td>
                    <a href="tel:<?php echo $row['phone']; ?>" class="phone-link">
                        <i class="fa fa-phone"></i> Call
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="4" style="text-align:center; padding:30px; color:#94a3b8;">No results found.</td></tr>
    <?php endif; ?>
</tbody>
        </table>
    </div>
</div>

<script>
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(function() {
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa fa-check"></i> Copied!';
        btn.style.background = '#28a745';
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.style.background = '#136835';
        }, 2000);
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>

</body>
</html>