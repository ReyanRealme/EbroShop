<?php
include 'db.php';

// 1. Session & Access Control
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
$user_id = $_SESSION['user_id'];

// 2. Logic: Update Quantity
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['u_action_update'])) {
    $c_id = (int)$_POST['u_cart_id'];
    $new_q = (int)$_POST['u_qty'];
    if ($new_q <= 0) {
        $conn->query("DELETE FROM cart WHERE id = $c_id AND user_id = $user_id");
    } else {
        $conn->query("UPDATE cart SET quantity = $new_q WHERE id = $c_id AND user_id = $user_id");
    }
    header("Location: Cart.php");
    exit();
}

// 3. Logic: Remove Item
if (isset($_GET['u_remove_id'])) {
    $c_id = (int)$_GET['u_remove_id'];
    $conn->query("DELETE FROM cart WHERE id = $c_id AND user_id = $user_id");
    header("Location: Cart.php");
    exit();
}

// 4. Fetch Data
$sql = "SELECT c.id as cid, c.quantity as cqty, p.name as pname, p.price as pprice, p.image_url as pimg 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = '$user_id'";
$res = $conn->query($sql);
$grand_total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* UNIQUE CSS SYSTEM - ONLY AFFECTS CART CONTENT */
        .u-cart-wrapper {
            background-color: #fcfcfc;
            min-height: 80vh;
            padding: 24px 16px 120px 16px;
            font-family: 'Inter', -apple-system, system-ui, sans-serif;
        }

        .u-cart-container {
            max-width: 500px;
            margin: 0 auto;
        }

        .u-cart-header {
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .u-cart-header h1 { font-size: 24px; font-weight: 800; color: #111; margin:0; }
        .u-cart-badge { background: #136835; color: #fff; font-size: 12px; padding: 4px 10px; border-radius: 50px; }

        /* Modern Product Card */
        .u-cart-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 16px;
            margin-bottom: 16px;
            display: flex;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            border: 1px solid #f0f0f0;
            position: relative;
        }

        .u-cart-img-area {
            width: 85px; height: 85px;
            background: #f8f8f8;
            border-radius: 14px;
            overflow: hidden;
            display: flex; align-items: center; justify-content: center;
        }
        .u-cart-img-area img { width: 80%; height: 80%; object-fit: contain; }

        .u-cart-info { flex: 1; margin-left: 16px; }
        .u-cart-name { font-size: 15px; font-weight: 700; color: #222; margin-bottom: 4px; display: block; padding-right: 25px;}
        .u-cart-price-tag { font-size: 13px; color: #888; margin-bottom: 12px; display: block;}

        .u-cart-delete-btn {
            position: absolute; top: 16px; right: 16px;
            color: #ff5252; text-decoration: none; font-size: 18px;
        }

        /* Logic Row */
        .u-cart-action-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .u-cart-qty-box {
            display: flex; align-items: center;
            background: #f4f4f4; border-radius: 10px; padding: 3px;
        }
        .u-cart-btn {
            width: 28px; height: 28px; border: none; background: #fff;
            border-radius: 7px; cursor: pointer; font-weight: bold;
            color: #136835; box-shadow: 0 2px 5px rgba(0,0,0,0.06);
        }
        .u-cart-num { padding: 0 12px; font-weight: 800; font-size: 14px; }

        .u-cart-subtotal { font-size: 17px; font-weight: 800; color: #136835; }

        /* Summary Area */
        .u-cart-summary {
            background: #fff; border-radius: 24px; padding: 24px;
            margin-top: 32px; border: 1px solid #eee;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .u-summary-line { display: flex; justify-content: space-between; margin-bottom: 12px; color: #666; font-weight: 500;}
        .u-summary-total { font-size: 20px; font-weight: 800; color: #111; margin-top: 10px; padding-top: 15px; border-top: 1px dashed #ddd; }

        .u-cart-checkout-btn {
            display: block; width: 100%; background: #136835; color: #fff;
            text-align: center; padding: 18px; text-decoration: none;
            border-radius: 16px; font-weight: 700; font-size: 16px;
            margin-top: 20px; box-shadow: 0 8px 20px rgba(19,104,53,0.25);
        }

        /* Empty State */
        .u-cart-empty { text-align: center; padding: 60px 0; }
        .u-cart-empty-icon { font-size: 60px; color: #e0e0e0; margin-bottom: 20px; }
        .u-cart-shop-btn { 
            display: inline-block; background: #222; color: #fff; 
            padding: 14px 30px; border-radius: 50px; text-decoration: none; font-weight: 700;
        }
    </style>
</head>
<body>

<div class="u-cart-wrapper">
    <div class="u-cart-container">
        
        <header class="u-cart-header">
            <h1>Shopping Bag</h1>
            <span class="u-cart-badge"><?php echo $res->num_rows; ?> Items</span>
        </header>

        <?php if ($res->num_rows > 0): ?>
            <div class="u-cart-list">
                <?php while($item = $res->fetch_assoc()): 
                    $line = $item['pprice'] * $item['cqty'];
                    $grand_total += $line;
                ?>
                <div class="u-cart-card">
                    <div class="u-cart-img-area">
                        <img src="<?php echo htmlspecialchars($item['pimg']); ?>" alt="item">
                    </div>
                    
                    <div class="u-cart-info">
                        <a href="Cart.php?u_remove_id=<?php echo $item['cid']; ?>" class="u-cart-delete-btn" onclick="return confirm('Remove?')">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                        
                        <span class="u-cart-name"><?php echo htmlspecialchars($item['pname']); ?></span>
                        <span class="u-cart-price-tag"><?php echo number_format($item['pprice'], 0); ?> ETB / unit</span>

                        <div class="u-cart-action-row">
                            <form action="Cart.php" method="POST" class="u-cart-qty-box">
                                <input type="hidden" name="u_cart_id" value="<?php echo $item['cid']; ?>">
                                <input type="hidden" name="u_action_update" value="1">
                                
                                <button type="submit" name="u_qty" value="<?php echo $item['cqty'] - 1; ?>" class="u-cart-btn">−</button>
                                <span class="u-cart-num"><?php echo $item['cqty']; ?></span>
                                <button type="submit" name="u_qty" value="<?php echo $item['cqty'] + 1; ?>" class="u-cart-btn">+</button>
                            </form>

                            <span class="u-cart-subtotal"><?php echo number_format($line, 0); ?> ETB</span>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <div class="u-cart-summary">
                <div class="u-summary-line"><span>Subtotal</span><span><?php echo number_format($grand_total, 0); ?> ETB</span></div>
                <div class="u-summary-line"><small>Shipping</small><small>Calculated at checkout</small></div>
                
                <div class="u-summary-line u-summary-total">
                    <span>Grand Total</span>
                    <span><?php echo number_format($grand_total, 0); ?> ETB</span>
                </div>
                
                <a href="order.html" class="u-cart-checkout-btn">Checkout Now <i class="fa-solid fa-arrow-right" style="margin-left:8px"></i></a>
            </div>

        <?php else: ?>
            <div class="u-cart-empty">
                <div class="u-cart-empty-icon"><i class="fa-solid fa-basket-shopping"></i></div>
                <h3 style="margin-bottom:10px;">Your bag is empty</h3>
                <p style="color:#888; margin-bottom:25px;">Looks like you haven't added anything yet.</p>
                <a href="babyproduct.php" class="u-cart-shop-btn">Go Shopping</a>
            </div>
        <?php endif; ?>

    </div>
</div>

</body>
</html>