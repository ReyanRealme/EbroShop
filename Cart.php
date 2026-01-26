<?php
include 'db.php';

// 1. Force Login Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Handle Cart Updates (Quantity change or Removal) BEFORE fetching
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
    $cart_id = (int)$_POST['cart_id'];
    $new_qty = (int)$_POST['qty'];
    
    if ($new_qty <= 0) {
        $conn->query("DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id");
    } else {
        $conn->query("UPDATE cart SET quantity = $new_qty WHERE id = $cart_id AND user_id = $user_id");
    }
    // Refresh page to show changes
    header("Location: Cart.php");
    exit();
}

if (isset($_GET['remove'])) {
    $cart_id = (int)$_GET['remove'];
    $conn->query("DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id");
    header("Location: Cart.php");
    exit();
}

// 3. Fetch Items from Database
// We join 'cart' table with 'products' table to get details
$sql = "SELECT c.id as cart_id, c.quantity, p.name, p.price, p.image_url 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = '$user_id'";
$cart_items = $conn->query($sql);
$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Shopping Cart</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
   
<style>
/* Keeping your exact CSS styles */
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:Arial,Helvetica,sans-serif;background:#fff}

/* Header Styles */
.header{ background:#136835; height:62px; display:flex; align-items:center; justify-content:space-between; padding:0 16px; color:#fff; }
.left,.right{display:flex;align-items:center;gap:18px}
.icon-btn{cursor:pointer;display:flex;align-items:center}
.icon-btn svg{width:22px;height:22px;stroke:#fff;fill:none;stroke-width:2;}

/* Cart Specific Styles */
.cart-wrapper{max-width:600px;margin:auto; margin-bottom: 100px;}
.cart-header{display:flex;justify-content:space-between;align-items:center;padding:18px;border-bottom:1px solid #eee}
.cart-header h2{margin:0;font-size:20px;font-weight:700}

.cart-empty{text-align:center;padding:60px 20px;}
.cart-empty p{color:#aaa;font-size:18px}
.cart-empty a{display:inline-block;margin-top:25px;padding:14px 20px;border:2px solid #000;text-decoration:none;color:#000;font-weight:700}

.cart-body{padding:15px;}

.product{display:flex;gap:12px;border:1px solid #eee;padding:12px;margin-bottom:12px; background: #fff; border-radius: 8px;}
.product img{width:80px;height:80px;object-fit:contain}
.info{flex:1}
.info h4{margin:0 0 5px;font-size:15px; color: #333;}
.info small{color:#999}

.controls{display:flex;align-items:center;justify-content:space-between;margin-top:10px}
.qty-form{display:flex;align-items:center;border:1px solid #ddd; border-radius: 4px; overflow: hidden;}
.qty-btn{width:32px;height:32px;border:none;background:#f9f9f9;font-size:18px;cursor:pointer; color: #333;}
.qty-input {width: 40px; text-align: center; border: none; font-weight: bold; pointer-events: none;}

.price{font-weight:700;font-size:16px; color: #136835;}
.remove{font-size:20px;cursor:pointer;color:#cc0000; text-decoration: none; padding: 0 10px;}

.summary{border-top:1px solid #eee;padding:18px; background: #f9f9f9; border-radius: 8px; margin-top: 20px;}
.summary-row{display:flex;justify-content:space-between;margin:10px 0}
.total{font-size:22px;color:#136835;font-weight:700}

.checkout{background:#136835;color:#fff;border:none;width:100%;padding:14px;font-size:16px;font-weight:700;cursor:pointer; border-radius: 5px; text-decoration: none; display: block; text-align: center;}
.continue{display:block;width:100%;margin-top:10px;padding:12px;border:2px solid #000;background:#fff;font-weight:700;text-align:center;text-decoration:none;color:#000; border-radius: 5px;}
.note{font-size:12px;color:#999;margin-top:8px}

/* Bottom Nav Styles */
.nav-item.active { color: #000; }
.nav-item.active svg { stroke: #000; }
</style>
</head>

<body>

<div class="header">
  <div class="left">
    <a href="home.html" class="icon-btn"><svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></a>
  </div>
  <div style="font-weight: bold; font-size: 18px;">My Shopping Bag</div>
  <div class="right"></div>
</div>

<div class="cart-wrapper">
  
  <?php if ($cart_items->num_rows > 0): ?>
      
      <div class="cart-body">
        
        <?php while($row = $cart_items->fetch_assoc()): 
            $line_total = $row['price'] * $row['quantity'];
            $total_price += $line_total;
        ?>
            <div class="product">
                <img src="<?php echo !empty($row['image_url']) ? htmlspecialchars($row['image_url']) : 'https://via.placeholder.com/80'; ?>" alt="Product">
                
                <div class="info">
                    <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                    <div class="controls">
                        
                        <form action="Cart.php" method="POST" class="qty-form">
                            <input type="hidden" name="cart_id" value="<?php echo $row['cart_id']; ?>">
                            <input type="hidden" name="update_cart" value="1">
                            
                            <button type="submit" name="qty" value="<?php echo $row['quantity'] - 1; ?>" class="qty-btn">−</button>
                            
                            <input type="text" value="<?php echo $row['quantity']; ?>" class="qty-input">
                            
                            <button type="submit" name="qty" value="<?php echo $row['quantity'] + 1; ?>" class="qty-btn">+</button>
                        </form>

                        <span class="price">ETB <?php echo number_format($line_total, 0); ?></span>
                        
                        <a href="Cart.php?remove=<?php echo $row['cart_id']; ?>" class="remove" onclick="return confirm('Remove this item?');">✕</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>

        <div class="summary">
            <strong data-key="cart4">ORDER SUMMARY</strong>

            <div class="summary-row">
                <strong data-key="cart6">TOTAL:</strong>
                <span class="total">ETB <?php echo number_format($total_price, 2); ?></span>
            </div>

            <div style="text-align:center;" class="note" data-key="cart7">Review your items before ordering</div>

            <a href="order.html" class="checkout" data-key="cart8">ORDER NOW</a>
            <a class="continue" href="babyproduct.php" data-key="cart9">CONTINUE SHOPPING</a>
        </div>
      </div>

  <?php else: ?>
      
      <div class="cart-empty">
        <i class="fa-solid fa-basket-shopping" style="font-size: 50px; color: #ddd; margin-bottom: 20px;"></i>
        <p data-key="cart2">Your Bag is empty</p>
        <a href="babyproduct.php" data-key="cart3">START SHOPPING</a>
      </div>

  <?php endif; ?>

</div>

<div style="padding-bottom: calc(72px + env(safe-area-inset-bottom));">
    <nav style="position: fixed; bottom: 0; left: 0; width: 100%; height: 72px; background: #fff; border-top: 1px solid #e0e0e0; display: flex; justify-content: space-around; align-items: center; z-index: 9999;">
        <a href="home.html" style="flex: 1; text-align: center; text-decoration: none; color: #666; font-size: 12px;">
            <svg style="width: 24px; height: 24px; display: block; margin: 0 auto 4px; stroke: #666;" viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M3 10.5L12 3l9 7.5"/><path d="M5 10v10h14V10"/></svg> Home
        </a>
        <a href="Cart.php" style="flex: 1; text-align: center; text-decoration: none; color: #000; font-size: 12px;">
             <svg style="width: 24px; height: 24px; display: block; margin: 0 auto 4px; stroke: #000;" viewBox="0 0 24 24" fill="none" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l3.6 12.6a2 2 0 0 0 2 1.4h9.4"/><path d="M7 6h15l-1.5 8H9"/></svg> Bag
        </a>
    </nav>
</div>

</body>
</html>