<?php
// --- 1. SET SESSION LIFETIME (30 Days = 2592000 seconds) ---
$session_expiration = 2592000; 
ini_set('session.gc_maxlifetime', $session_expiration);
ini_set('session.cookie_lifetime', $session_expiration);

// --- 2. MAKE COOKIE SECURE ---
session_set_cookie_params([
    'lifetime' => $session_expiration,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,     // Only sends over HTTPS (Render uses HTTPS)
    'httponly' => true,   // Protects against XSS attacks
    'samesite' => 'Lax'
]);

// --- 3. START THE SESSION ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

session_start();

// --- 1. SECURITY LOCK ---
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

include 'db.php';

// Get and Secure the product ID
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); 
    // Using a prepared statement for security
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    if (!$product) {
        header("Location: admin_dashboard.php?msg=Product Not Found");
        exit();
    }
} else {
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - EBRO Shop</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f2f5; padding: 40px; color: #333; }
        .edit-container { max-width: 500px; margin: auto; background: white; padding: 35px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        h2 { color: #0076ad; margin-top: 0; text-align: center; margin-bottom: 25px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; font-size: 14px; }
        input, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; transition: 0.3s; font-size: 15px; }
        input:focus, select:focus { border-color: #0076ad; outline: none; box-shadow: 0 0 0 3px rgba(0,118,173,0.1); }
        .btn-update { width: 100%; padding: 14px; background: #0076ad; color: white; border: none; cursor: pointer; font-weight: bold; margin-top: 15px; text-transform: uppercase; border-radius: 8px; transition: 0.3s; }
        .btn-update:hover { background: #005a85; transform: translateY(-1px); }
        .btn-cancel { display: block; text-align: center; margin-top: 20px; color: #888; text-decoration: none; font-size: 14px; }
        .btn-cancel:hover { color: #ff4d4d; }
        .preview-box { text-align: center; margin-bottom: 25px; background: #fafafa; padding: 15px; border-radius: 8px; border: 1px dashed #ddd; }
        .preview-box img { max-width: 140px; height: 140px; object-fit: cover; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="edit-container">
    <h2>Edit Product</h2>

    <div class="preview-box">
        <p style="font-size: 11px; color: #999; margin-top: 0;">CURRENT PREVIEW</p>
        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" onerror="this.src='https://via.placeholder.com/150?text=No+Image'" alt="Preview">
    </div>

    <form action="manage_products.php" method="POST">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required autofocus>
        </div>

        <div class="form-group">
            <label>Price (ETB)</label>
            <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required>
        </div>

        <div class="form-group">
            <label>Cloudinary Image URL</label>
            <input type="text" name="image_url" value="<?php echo htmlspecialchars($product['image_url']); ?>" required>
        </div>

        <div class="form-group">
            <label>Category</label>
            <select name="category" required>
                <?php
                $categories = [
                    'baby' => 'Baby Products',
                    'basicfood' => 'Basic Foods',
                    'packedfood' => 'Packed Foods',
                    'oil' => 'Food Oils',
                    'Cookingitems' => 'Cooking Ingredients',
                    'spiecsPowder' => 'Spiecs Powder',
                    'Dayper&Wipes' => 'Dayper&Wipes',
                    'Cosmotics' => 'Cosmotics',
                    'LiquidSoap' => 'Liquid Soap',
                    'PowderSoap' => 'Powder Soap',
                    'Modes&Soft' => 'Modes&Soft',
                    'PackagedGoods' => 'Packaged Goods'
                ];
                foreach ($categories as $val => $displayName) {
                    $selected = ($product['category'] == $val) ? 'selected' : '';
                    echo "<option value='$val' $selected>$displayName</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>Stock Status</label>
            <select name="status">
                <option value="available" <?php if($product['status'] == 'available') echo 'selected'; ?>>Available</option>
                <option value="sold_out" <?php if($product['status'] == 'sold_out') echo 'selected'; ?>>Sold Out</option>
            </select>
        </div>

        <button type="submit" class="btn-update">UPDATE PRODUCT</button>
        <a href="admin_dashboard.php" class="btn-cancel">← Back to Dashboard</a>
    </form>
</div>

</body>
</html>