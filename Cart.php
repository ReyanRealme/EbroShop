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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
     .card { background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .title { color: #136835; font-size: 22px; font-weight: bold; margin-bottom: 20px; text-align: center; }
        .form-group { margin-bottom: 15px; position: relative; } /* Added position relative */
        label { display: block; font-size: 14px; margin-bottom: 5px; color: #555; }
        
        input { width: 100%; padding: 12px; padding-right: 40px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 16px; }
        input:focus { border-color: #136835; outline: none; }
        
        /* Eye Icon Style */
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 38px; /* Adjusted to align with input center */
            cursor: pointer;
            color: #888;
            transition: 0.3s;
        }
        .toggle-password:hover { color: #136835; }

        .btn-save { width: 100%; padding: 12px; background: #136835; color: white; border: none; border-radius: 50px; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 10px; transition: 0.3s; }
        .btn-save:hover { background: #0e4d28; }
        .error { color: #e74c3c; font-size: 13px; margin-top: 5px; display: none; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; font-size: 14px; }
 
 
 
 
        /*For main header and others*/
         /* RESET */
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:Arial,Helvetica,sans-serif;background:#fff}

/* HEADER */
.header{
  background:#136835;
  height:62px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding:0 16px;
  color:#fff;
}
.left,.right{display:flex;align-items:center;gap:18px}
.icon-btn{cursor:pointer;display:flex;align-items:center}
.icon-btn svg{
  width:22px;height:22px;stroke:#fff;fill:none;stroke-width:2;
}

/* CART BADGE */
.cart{position:relative;}
.badge{
  position:absolute;
  top:-6px;
  right:-6px;
  background:#fff;
  color:#136835;
  width:18px;
  height:18px;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:11px;
  font-weight:bold;
  display:none;
}


/* EbRo logo – fully isolated */
.ebro-logo-link {
  display: flex;
  align-items: center;
  text-decoration: none;
  color: inherit;
}

/* Circle like icon */
.ebro-logo-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #f4f4f4;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

/* Image behaves like icon */
.ebro-logo-img {
  width: 100%;
  height: 100%;
  object-fit:contain;
}

/* Text area */
.ebro-logo-text {
  margin-left: 8px;
}

/* Main text */
.ebro-logo-main {
  font-size: 16px;
  font-weight: 700;
  line-height: 1.1;
}

/* Sub text */
.ebro-logo-sub {
  font-size: 11px;
  color: #ffffff;
}


/* LOGO */
.logo-area{display:flex;align-items:center;gap:10px}
.logo-circle{
  width:42px;height:42px;background:#fff;border-radius:50%;
  display:flex;align-items:center;justify-content:center;padding:5px;
}
.logo-text .main{font-size:20px;font-weight:bold}
.logo-text .sub{font-size:10px;margin-top:2px;text-transform:capitalize;}

/* OVERLAY */
#menuOverlay{
  position:fixed;top:0;left:0;width:100%;height:100%;
  background:rgba(0,0,0,0.42);
  display:none;z-index:9;
}

/* SIDE MENU (SLIDE DRAWER) */
#sideMenu{
  position:fixed;
  top:0;
  left:-320px;
  width:320px;
  height:100%;
  background:#fafafa;
  box-shadow:3px 0 8px rgba(0,0,0,0.2);
  z-index:10;
  transition:left .28s ease;
  padding:20px 22px;
  overflow-y:auto;
}

#sideMenu a {
  text-decoration: none; /* remove underline */
  color: #111;           /* text color */
}

/* MENU SECTIONS */
.menu-title{
  color:#999;
  font-size:14px;
  margin:18px 0 6px;
}

.menu-item{
  display:flex;
  align-items:center;
  gap:12px;
  padding:10px 0;
  font-size:16px;
  color:#000;
  cursor:pointer;
  border-bottom:1px solid #eee;
}
.menu-item svg{
  width:22px;height:22px;stroke:#000;fill:none;stroke-width:1.7;
}

/* LOGIN ROW */
.login-row{
  display:flex;
  align-items:center;
  gap:12px;
  padding:10px 0 20px;
  border-bottom:1px solid #ddd;
}
.login-row .user-icon{
  width:46px;height:46px;border-radius:50%;background:#eee;
  display:flex;align-items:center;justify-content:center;
}
.login-row .user-icon svg{width:28px;height:28px;stroke:#777}

/* CLOSE BUTTON */
.close-btn{
  font-size:26px;
  font-weight:bold;
  cursor:pointer;
  margin-bottom:12px;
  display:inline-block;
}


/* for text header */
@keyframes ticker-scroll{
    0%   { transform: translateX(0%); }
    100% { transform: translateX(-50%); }
  }
@media (max-width:520px){
  .ticker-track{font-size:14px}
}

/* -------------------------
   SEARCH PANEL STYLES
   ------------------------- */
#searchPanel {
  position:fixed;
  top:0; left:0;
  width:100%; height:100%;
  background:#fff;
  z-index:9999;
  display:none;
  overflow-y:auto;
  -webkit-overflow-scrolling:touch;
}

.search-top {
  padding:12px;
  display:flex;
  gap:10px;
  align-items:center;
  border-bottom:1px solid #eee;
}

.search-top input {
  flex:1;
  padding:12px;
  font-size:16px;
  border:1px solid #ccc;
  border-radius:8px;
}

.search-close-btn {
  padding:10px 14px;
  background:#136835;
  color:#fff;
  border:none;
  border-radius:8px;
  cursor:pointer;
}

/* product card design (matches your screenshot) */
.products-wrap { padding:14px; }
.products-grid {
  display:flex;
  flex-wrap:wrap;
  gap:12px;
  justify-content:space-between;
}

.product-card {
    width: 48%;
    background:#fff;
    border-radius:14px;
    box-shadow:0 3px 8px rgba(0,0,0,0.07);
    padding-bottom:8px;
    overflow:hidden;
}

.product-card img {
    width:100%;
    height:150px;
    object-fit:cover;
    display:block;
}
.product-name {
  padding:10px;
    font-size:16px;
    font-weight:600;
}
.product-price {
    padding:0 10px;
    font-size:16px;
    font-weight:bold;
    color:#111;
}
.add-btn {
    margin:12px 10px 16px 10px;
    width:calc(100% - 20px);
    padding:10px 0;
    background:#008cff;
    color:#fff;
    border:none;
    font-size:15px;
    border-radius:8px;
    cursor:pointer;
}

/* small responsiveness */
@media (max-width:520px){
  .product-card{width:100%}
  .logo-text .main{font-size:16px}
}


 /*for FAQ's*/
        .links a:hover {
    color: #000;
}

.hidden {
    display: none;
}


/*For last footer*/
.payment-logos a:hover img {
    transform: scale(1.05);
}

/* Mobile adjustment */
@media (max-width: 600px) {
    .footer-text {
        font-size: 20px;
    }

    .payment-logos a img {
        height: 34px;
    }
}


/*for bottom nav*/
  .nav-item.active {
    color: #000;
  }

  .nav-item.active svg {
    stroke: #000;
  }

  .cart {
    position: relative;
  }







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

<!-- TOP TICKER -->
<div style=" background:#136835; --white:#ffffff; --muted-white:rgba(255,255,255,0.95);">
  <div style="font-family: Helvetica Neue, Arial, sans-serif; background:#fff;">
    <header style="background:#136835; color:var(--white); width:100%; box-shadow: 0 1px 0 rgba(0,0,0,0.08);" class="site-header" role="banner">
      <div style="overflow:hidden; border-top:1px solid rgba(255,255,255,0.06);" class="ticker-wrap" aria-hidden="false">
        <div style="display:block; width:100%; padding:10px 0;" class="ticker" role="marquee">
          <div style="display:inline-block; white-space:nowrap; will-change:transform; font-weight:600; font-size:16px; color:var(--muted-white); animation: ticker-scroll 35s linear infinite;" class="ticker-track">
            <span style="display:inline-block; padding-right:60px;">Fast and Reliable delivery to Your doorstep. all over Addis Abeba. Order Now.</span>
            <span style="display:inline-block; padding-right:60px;"> በፍጥነት እንዲሁም አስተማማኝ በሆነ መንገድ ወደቤትዎ ደጃፍ እናደርሳለን. አሁኑኑ ይዘዙን</span>
            <span style="display:inline-block; padding-right:60px;">Fast and Reliable delivery to Your doorstep. all over Addis Abeba. Order Now.</span>
            <span style="display:inline-block; padding-right:60px;"> በፍጥነት እንዲሁም አስተማማኝ በሆነ መንገድ ወደቤትዎ ደጃፍ እናደርሳለን. አሁኑኑ ይዘዙን</span>
            <span style="display:inline-block; padding-right:60px;">Fast and Reliable delivery to Your doorstep. all over Addis Abeba. Order Now.</span>
            <span style="display:inline-block; padding-right:60px;"> በፍጥነት እንዲሁም አስተማማኝ በሆነ መንገድ ወደቤትዎ ደጃፍ እናደርሳለን. አሁኑኑ ይዘዙን</span>
            <span style="display:inline-block; padding-right:60px;">Fast and Reliable delivery to Your doorstep. all over Addis Abeba. Order Now.</span>
            <span style="display:inline-block; padding-right:60px;"> በፍጥነት እንዲሁም አስተማማኝ በሆነ መንገድ ወደቤትዎ ደጃፍ እናደርሳለን. አሁኑኑ ይዘዙን</span>
            <span style="display:inline-block; padding-right:60px;">Fast and Reliable delivery to Your doorstep. all over Addis Abeba. Order Now.</span>
            <span style="display:inline-block; padding-right:60px;"> በፍጥነት እንዲሁም አስተማማኝ በሆነ መንገድ ወደቤትዎ ደጃፍ እናደርሳለን. አሁኑኑ ይዘዙን</span>
          </div>
        </div>
      </div>
    </header>
  </div>
</div>

<!-- MAIN HEADER -->
<div  class="header">
  <div class="left">
    <div class="icon-btn" id="hamburgerBtn" aria-label="Open menu">
      <svg viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
    </div>

   <a href="search.php" aria-label="Search" class="icon-btn">
          <svg viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="6"/>
          <path d="M21 21l-4-4"/>
          </svg>
       </a>
  </div>

 <div class="logo-area">
  <a href="change_password.html" class="ebro-logo-link">
    
    <div class="ebro-logo-circle" title="EbRoShop">
      <img src="https://res.cloudinary.com/die8hxris/image/upload/v1765983404/j5tztu0kuzdspwmk3zfg.jpg" alt="EbRoShop Logo" class="ebro-logo-img">
    </div>

    <div class="ebro-logo-text">
      <div class="ebro-logo-main">EbRoShop.com</div>
      <div class="ebro-logo-sub">by Ebro Gurage Tone</div>
    </div>

  </a>
</div>

  <div class="right">
    <div class="icon-btn" id="accountBtn" aria-label="Account">
      <svg viewBox="0 0 24 24">
        <path d="M12 12a4 4 0 100-8 4 4 0 000 8zM4 20a8 8 0 0116 0"/>
      </svg>
    </div>

    <div class="cart icon-btn" id="cartBtn" aria-label="Cart">
      <svg viewBox="0 0 24 24">
        <path d="M3 3h2l3 12h10l3-8H6"/>
        <circle cx="9" cy="20" r="1"/>
        <circle cx="18" cy="20" r="1"/>
      </svg>
      <div class="badge" id="cartBadge">0</div>
    </div>
  </div>
</div>

<!-- OVERLAY + SIDE MENU -->
<div id="menuOverlay" aria-hidden="true"></div>

  <div id="sideMenu" aria-hidden="true">
  <span class="close-btn" id="closeMenu">✖️</span>

  <!--For signup-->
<a href="register.html" class="login-row" id="auth-link">
  <div class="user-icon" aria-hidden="true">
    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M12 12a4 4 0 100-8 4 4 0 000 8z"/>
      <path d="M4 20a8 8 0 0116 0"/>
    </svg>
  </div>
  <div id="auth-text" style="font-size:14px; font-weight: 800;color: #cc0000; font-family: 'Times New Roman', Times, serif;" data-key="common1">Signup</div>
</a>




<!-- Main Links -->
  <a href="home.php" class="menu-item">
    <!-- Home Icon -->
    <svg viewBox="0 0 24 24" width="24" height="24" fill="none"
     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
  <path d="M3 11l9-8 9 8"/>
  <path d="M5 10v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V10"/>
  <path d="M9 22V12h6v10"/>
</svg>

    <span  style="font-family: 'Times New Roman', Times, serif;" data-key="common2">Home</span>
  </a>

  <a href="about.php" class="menu-item">
    <!-- Info Icon -->
  <svg viewBox="0 0 24 24" width="24" height="24" fill="none"
     stroke="currentColor" stroke-width="2" stroke-linecap="round">
  <circle cx="12" cy="12" r="10"/>
  <line x1="12" y1="11" x2="12" y2="16"/>
  <circle cx="12" cy="8" r="1"/>
</svg>
    <span   style="font-family: 'Times New Roman', Times, serif;"  data-key="common3">About-us</span>
  </a>

  <a href="contact.html" class="menu-item">
    <!-- Contact/Phone Icon -->
    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
  <path d="M6.6 10.8a15.1 15.1 0 006.6 6.6l2.2-2.2a1 1 0 011-.24
           11.4 11.4 0 003.6.6a1 1 0 011 1v3.5a1 1 0 01-1 1
           C10.6 22 2 13.4 2 3.5a1 1 0 011-1H6.5a1 1 0 011 1
           11.4 11.4 0 00.6 3.6 1 1 0 01-.25 1z"/>
</svg>
    <span   style="font-family: 'Times New Roman', Times, serif;"  data-key="common4">Contact-us</span>
  </a>

  <a href="faq.html" class="menu-item">
    <!-- FAQ/Question Icon -->
  <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
  <path d="M12 2a10 10 0 1 0 0 20a10 10 0 0 0 0-20zm0 15a1.25 1.25 0 1 1 0-2.5a1.25 1.25 0 0 1 0 2.5zm1.1-4.6c-.9.6-1.1 1-1.1 1.6h-2
           c0-1.5.6-2.4 1.9-3.2
           .9-.6 1.4-1 1.4-1.8
           0-.9-.7-1.5-1.8-1.5
           -1 0-1.8.6-1.9 1.7H7.6
           C7.7 6.2 9.4 5 11.5 5
           c2.4 0 4.2 1.4 4.2 3.6
           0 1.6-1 2.6-2.5 3.4z"/>
</svg>
    <span   style="font-family: 'Times New Roman', Times, serif;"  data-key="common5">FAQs</span>
  </a>

  <a href="terms.html" class="menu-item">
    <!-- Document Icon -->
   <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
  <path d="M6 2h8l6 6v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/>
  <path d="M14 2v6h6"/>
  <path d="M8 13l2 2l4-4"/>
</svg>
    <span   style="font-family: 'Times New Roman', Times, serif;" data-key="common6">Terms Of Service</span>
  </a>

  <a href="returns.html" class="menu-item">
    <!-- Return/Arrow Icon -->
<svg viewBox="0 0 24 24" width="24" height="24" fill="none"
     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
 <!--First arrow (clockwise, top-right) -->
  <path d="M4.5 12a7.5 7.5 0 0 1 7.5-7.5"/>
  <polyline points="12 2 14 6 10 6"/>
  <!-- Second arrow (counter-clockwise, bottom-left) -->
  <path d="M19.5 12a7.5 7.5 0 0 1-7.5 7.5"/>
  <polyline points="12 22 10 18 14 18"/>
</svg> 
    <span   style="font-family: 'Times New Roman', Times, serif;" data-key="common7">Return Policy</span>
  </a>

  <a href="privacy.html" class="menu-item">
    <!-- Lock Icon -->
    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
  <path d="M4 3h16v18H4z"/>
  <path d="M7 7h10M7 11h10M7 15h6"/>
</svg>
    <span  style="font-family: 'Times New Roman', Times, serif;"  data-key="common8">Privacy Policy</span>
  </a>

  <!-- Products -->
  <div class="menu-title"   style="font-family: 'Times New Roman', Times, serif;" data-key="common9">Products</div>

  <a href="basicfood.php" class="menu-item">
    <!-- Food Icon -->
   <svg viewBox="0 0 24 24" width="24" height="24" fill="none"
     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
  <path d="M3 9h18l-1 10H4z"/>
  <path d="M16 4a2 2 0 1 1-4 0"/>
</svg>
    <span  style="font-family: 'Times New Roman', Times, serif;"  data-key="common10">Basic Food</span>
  </a>

  <a href="cooking.php" class="menu-item">
    <!-- Cooking Ingredients Icon -->
   <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
  <path d="M9 5h6v14a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2V5z"/>
  <path d="M9 5c0-1.5 1-3 3-3s3 1.5 3 3"/>
  <path d="M11 9h2"/>
  <path d="M11 13h2"/>
</svg>
    <span   style="font-family: 'Times New Roman', Times, serif;" data-key="common11">Cooking ingrident</span>
  </a>

  <a href="babyproduct.php" class="menu-item">
    <!-- Baby Icon -->
    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
      <circle cx="12" cy="7" r="4"/>
      <path d="M6 21v-2a6 6 0 0112 0v2"/>
    </svg>
    <span  style="font-family: 'Times New Roman', Times, serif;"  data-key="common12">Baby product</span>
  </a>

  <a href="cosmotics.php" class="menu-item">
    <!-- Health/Medical Icon -->
    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M21 10h-6l-3-6-3 6H3l5 7-2 6 6-4 6 4-2-6 5-7z"/>
    </svg>
    <span   style="font-family: 'Times New Roman', Times, serif;" data-key="common13">HealthCare</span>
  </a>

   <a href="packaged.php" class="menu-item">
    <!-- Health/Medical Icon -->
 <svg
  viewBox="0 0 24 24"
  width="24"
  height="24"
  fill="none"
  stroke="currentColor"
  stroke-width="2"
  stroke-linecap="round"
  stroke-linejoin="round"
>
  <path d="M3 7l9-4 9 4" />
  <path d="M3 7v10l9 4 9-4V7" />
  <path d="M12 3v4" />
  <path d="M12 13v8" />
</svg>
    <span  style="font-family: 'Times New Roman', Times, serif;"  data-key="common14">Packaged Goods</span>
  </a>

   <a href="powdersoap.php" class="menu-item">
    <!-- Health/Medical Icon -->
    <svg
  viewBox="0 0 24 24"
  width="24"
  height="24"
  fill="none"
  stroke="currentColor"
  stroke-width="2"
  stroke-linecap="round"
  stroke-linejoin="round"
>
  <!-- Detergent box -->
  <path d="M6 3h10l2 4v14H6z" />
  <!-- Box top -->
  <path d="M6 7h12" />
  <!-- Scoop handle -->
  <path d="M9 11c2-2 4-2 6 0" />
  <!-- Powder granules -->
  <circle cx="9" cy="16" r="0.8" />
  <circle cx="12" cy="17" r="0.8" />
  <circle cx="15" cy="16" r="0.8" />
</svg>
    <span  style="font-family: 'Times New Roman', Times, serif;"  data-key="common15">Powder Soap</span>
  </a>

    <a href="dayper.php" class="menu-item">
    <!-- Health/Medical Icon -->
    <svg
  viewBox="0 0 24 24"
  width="24"
  height="24"
  fill="none"
  stroke="currentColor"
  stroke-width="2"
  stroke-linecap="round"
  stroke-linejoin="round"
>
  <!-- Diaper outer shape -->
  <path d="M4 7c2-3 14-3 16 0v6c0 4-4 7-8 7s-8-3-8-7z" />
  <!-- Waist folds -->
  <path d="M7 7v2M17 7v2" />
  <!-- Inner absorbent area -->
  <path d="M9 12c1 2 5 2 6 0" />
</svg>

    <span   style="font-family: 'Times New Roman', Times, serif;" data-key="common16">Dayper$Wipes</span>
  </a>
  <a href="packedfood.php" class="menu-item">
    <!-- Health/Medical Icon -->
    <svg
  viewBox="0 0 24 24"
  width="24"
  height="24"
  fill="none"
  stroke="currentColor"
  stroke-width="2"
  stroke-linecap="round"
  stroke-linejoin="round"
>
  <!-- Can body -->
  <path d="M6 6c0-2 12-2 12 0v12c0 2-12 2-12 0z" />
  <!-- Top rim -->
  <path d="M6 6c0 2 12 2 12 0" />
  <!-- Bottom rim -->
  <path d="M6 18c0-2 12-2 12 0" />
  <!-- Label -->
  <path d="M9 10h6v4H9z" />
</svg>
    <span  style="font-family: 'Times New Roman', Times, serif;"  data-key="common17">Packed Foods</span>
  </a>

    <a href="otheringrident.php" class="menu-item">
    <!-- Health/Medical Icon -->
   <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
  <!-- Bowl -->
  <path d="M3 14h18" />
  <path d="M5 14c0 4 4 6 7 6s7-2 7-6" />

  <!-- Powder mound -->
  <path d="M8 14c1-2 2-3 4-3s3 1 4 3" />

  <!-- Granules -->
  <circle cx="9" cy="10" r="0.6" />
  <circle cx="12" cy="9" r="0.6" />
  <circle cx="15" cy="10" r="0.6" />
</svg>
    <span  style="font-family: 'Times New Roman', Times, serif;"  data-key="common18">Spices powders</span>
  </a>

    <a href="oil.php" class="menu-item">
    <!-- Health/Medical Icon -->
    <svg viewBox="0 0 24 24" width="24" height="24"
     fill="none" stroke="currentColor" stroke-width="2"
     stroke-linecap="round" stroke-linejoin="round">
  <!-- Bottle neck -->
  <path d="M10 2h4v3h-4z"/>
  <!-- Bottle body -->
  <path d="M9 5h6c1.5 0 2 1.5 2 3v9c0 2-2 4-5 4s-5-2-5-4V8c0-1.5.5-3 2-3z"/>
  <!-- Oil level -->
  <path d="M7 13h10"/>
  <!-- Oil drop -->
  <path d="M19 14c1.2 1.6 1.2 2.8 0 3.8a2 2 0 0 1-3 0c-1.2-1-.8-2.4 0-3.8l1.5-2z"/>
</svg>
    <span  style="font-family: 'Times New Roman', Times, serif;"  data-key="common19">Food Oils</span>
  </a>

   <a href="modes.php" class="menu-item">
    <!-- Health/Medical Icon -->
   <svg viewBox="0 0 24 24" width="24" height="24"
     fill="none" stroke="currentColor" stroke-width="2"
     stroke-linecap="round" stroke-linejoin="round">
  <!-- Pad body -->
  <rect x="6" y="3" width="12" height="18" rx="4" ry="4"/>
  <!-- Inner absorbent area -->
  <rect x="9" y="6" width="6" height="12" rx="3" ry="3"/>
  <!-- Side wings -->
  <path d="M6 9l-3 3 3 3"/>
  <path d="M18 9l3 3-3 3"/>
</svg>
    <span  style="font-family: 'Times New Roman', Times, serif;"  data-key="common20">Modes$Soft</span>
  </a>

    <a href="liquidsoap.php" class="menu-item">
    <!-- Health/Medical Icon -->
   <svg viewBox="0 0 24 24" width="24" height="24"
     fill="none" stroke="currentColor" stroke-width="2"
     stroke-linecap="round" stroke-linejoin="round">
  <!-- Pump top -->
  <path d="M10 2h4"/>
  <path d="M12 2v3"/>
  <path d="M12 5h6"/>
  <!-- Bottle body -->
  <rect x="7" y="6" width="10" height="15" rx="3"/>
  <!-- Liquid level -->
  <path d="M7 13h10"/>
  <!-- Soap drop -->
  <path d="M12 11c1.2 1.6 1.2 2.8 0 3.8a2 2 0 0 1-3 0c-1.2-1-.8-2.4 0-3.8l1.5-2z"/>
</svg>
    <span  style="font-family: 'Times New Roman', Times, serif;"  data-key="common21">Liquid soap</span>
  </a>

<div class="menu-title"  style="font-family: 'Times New Roman', Times, serif;"  data-key="common22">Additional</div>


<a href="host.html" class="menu-item">
    <!-- Health/Medical Icon -->
   <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M12 12a4 4 0 100-8 4 4 0 000 8z"/>
      <path d="M4 20a8 8 0 0116 0"/>
    </svg>
    <span   style="font-family: 'Times New Roman', Times, serif;" data-key="common23">Our Hosts</span>
  </a>


   <a href="developer.html" class="menu-item">
    <!-- Health/Medical Icon -->
   <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M12 12a4 4 0 100-8 4 4 0 000 8z"/>
      <path d="M4 20a8 8 0 0116 0"/>
    </svg>
    <span   style="font-family: 'Times New Roman', Times, serif;" data-key="common24">Developer</span>
  </a>
  <br>
<br>
<br>
</div>







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
                        <span class="u-cart-price-tag"><?php echo number_format($item['pprice'], 0); ?> ETB / (unit/kg)</span>

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
                <div class="u-summary-line"><small>Shipping</small><small>0.00 ETB</small></div>
                
                <div class="u-summary-line u-summary-total">
                    <span>Total Balance</span>
                    <span><?php echo number_format($grand_total, 0); ?> ETB</span>
                </div>
                   

               
<div style="
    max-width: 420px; 
    margin: 15px auto; 
    background: linear-gradient(135deg, #ffffff 0%, #f0fff4 100%);
    border: 1px solid #c6f6d5;
    border-radius: 16px; 
    padding: 20px; 
    box-shadow: 0 8px 20px rgba(56, 161, 105, 0.08);
    font-family: 'Inter', -apple-system, sans-serif;
">
    <div style="display: flex; align-items: center; margin-bottom: 12px;">
        <div style="
            background: #38a169; 
            width: 30px; 
            height: 30px; 
            border-radius: 8px; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            margin-right: 10px;
        ">
            <i class="fas fa-scale-balanced" style="color: white; font-size: 14px;"></i>
        </div>
        <span style="font-weight: 700; color: #22543d; font-size: 15px;">Quantity & Measurements</span>
    </div>

    <p style="margin: 0; color: #2f855a; line-height: 1.6; font-size: 13.5px;">
        Please note that the <strong style="color: #1a4731;">Quantity</strong> field adjusts based on the item type:
    </p>

    <ul style="margin: 10px 0 0 0; padding-left: 20px; color: #4a5568; font-size: 13px; line-height: 1.5;">
        <li style="margin-bottom: 5px;">
            For items sold by weight, the quantity represents <strong>Kilograms (kg)</strong>.
        </li>
        <li>
            For items sold individually, the quantity represents <strong>Units (Pieces)</strong>.
        </li>
    </ul>

    <div style="margin-top: 15px; padding-top: 10px; border-top: 1px dashed #c6f6d5;">
        <p style="margin: 0; color: #718096; font-size: 12px; font-style: italic;">
            አንድን እቃ በኪሎ ግራም የሚሸጥ ከሆነ Quantity ኪሎግራምን ያመለክታል፤ በፍሬ የሚሸጥ ከሆነ ደግሞ የፍሬ ብዛትን ያመለክታል።
        </p>
    </div>
</div>



                <a href="order.html" class="u-cart-checkout-btn">Checkout Now <i class="fa-solid fa-arrow-right" style="margin-left:8px"></i></a>
                 <a href="collection.php" class="u-cart-checkout-btn"><i class="fa-solid fa-arrow-left" style="margin-right:8px"></i>  Continue Shopping</a>
                
                
            </div>

        <?php else: ?>
            <div class="u-cart-empty">
                <div class="u-cart-empty-icon"><i class="fa-solid fa-basket-shopping"></i></div>
                <h3 style="margin-bottom:10px;">Your bag is empty</h3>
                <p style="color:#888; margin-bottom:25px;">Looks like you haven't added anything yet.</p>
                <a href="collection.php" class="u-cart-shop-btn">Continue Shopping</a>
            </div>
        <?php endif; ?>

    </div>
</div>




 


    
    








<!--For bottom nav-->

<div style=" padding-bottom: calc(72px + env(safe-area-inset-bottom));">

    <nav style="  position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 72px;
    padding-bottom: env(safe-area-inset-bottom);
    background: #fff;
    border-top: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-around;
    align-items: center;
    z-index: 9999;" class="bottom-nav">

  <a href="home.php" style="  flex: 1;
    text-align: center;
    text-decoration: none;
    color: #666;
    font-size: 12px;
    font-family: system-ui, -apple-system, sans-serif;" class="nav-item active">
    <svg style=" width: 24px;
    height: 24px;
    display: block;
    margin: 0 auto 4px;
    stroke: #666;" viewBox="0 0 24 24" fill="none" stroke-width="2">
      <path d="M3 10.5L12 3l9 7.5"/>
      <path d="M5 10v10h14V10"/>
    </svg>
    Home
  </a>

  <a href="search.php" style="  flex: 1;
    text-align: center;
    text-decoration: none;
    color: #666;
    font-size: 12px;
    font-family: system-ui, -apple-system, sans-serif;"  class="nav-item">
    <svg style=" width: 24px;
    height: 24px;
    display: block;
    margin: 0 auto 4px;
    stroke: #666;"  viewBox="0 0 24 24" fill="none" stroke-width="2">
      <circle cx="11" cy="11" r="8"/>
      <line x1="21" y1="21" x2="16.65" y2="16.65"/>
    </svg>
    Search
  </a>
  <div class="icon-btn" id="searchBtn" aria-label="Search">
      <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="6"/><path d="M21 21l-4-4"/></svg>
    </div>

  <a href="collection.php" style="  flex: 1;
    text-align: center;
    text-decoration: none;
    color: #666;
    font-size: 12px;
    font-family: system-ui, -apple-system, sans-serif;"  class="nav-item">
    <svg style=" width: 24px;
    height: 24px;
    display: block;
    margin: 0 auto 4px;
    stroke: #666;"  viewBox="0 0 24 24" fill="none" stroke-width="2">
      <rect x="3" y="3" width="7" height="7"/>
      <rect x="14" y="3" width="7" height="7"/>
      <rect x="3" y="14" width="7" height="7"/>
      <rect x="14" y="14" width="7" height="7"/>
    </svg>
    Collection
  </a>

  <a href="login.html" style="  flex: 1;
    text-align: center;
    text-decoration: none;
    color: #666;
    font-size: 12px;
    font-family: system-ui, -apple-system, sans-serif;"  class="nav-item">
    <svg style=" width: 24px;
    height: 24px;
    display: block;
    margin: 0 auto 4px;
    stroke: #666;"  viewBox="0 0 24 24" fill="none" stroke-width="2">
      <circle cx="12" cy="8" r="4"/>
      <path d="M4 21c0-4 4-7 8-7s8 3 8 7"/>
    </svg>
    Account
  </a>

  <a  style="  flex: 1;
    text-align: center;
    text-decoration: none;
    color: #666;
    font-size: 12px;
    font-family: system-ui, -apple-system, sans-serif;"  href="Cart.php" class="nav-item cart">
    <span style=" position: absolute;
    top: 8px;
    right: 28%;
    background: #000;
    color: #fff;
    font-size: 10px;
    min-width: 16px;
    height: 16px;
    line-height: 16px;
    border-radius: 50%;" class="badge">0</span>
    <svg style=" width: 24px;
    height: 24px;
    display: block;
    margin: 0 auto 4px;
    stroke: #666;"  viewBox="0 0 24 24" fill="none" stroke-width="2">
      <circle cx="9" cy="21" r="1"/>
      <circle cx="20" cy="21" r="1"/>
      <path d="M1 1h4l3.6 12.6a2 2 0 0 0 2 1.4h9.4"/>
      <path d="M7 6h15l-1.5 8H9"/>
    </svg>
    Bag
  </a>

</nav>
</div>



<div class="language-bar" style="position: fixed; top: 20px; right: 20px; display: flex; background: white; padding: 5px; border-radius: 50px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); z-index: 10000; border: 2px solid #136835;">
    
    <button onclick="updatePageLanguage('en')" id="btn-en" 
        style="border: none; padding: 8px 18px; border-radius: 50px; cursor: pointer; font-weight: 800; font-size: 13px; transition: 0.3s; background: #136835; color: white;">
        English
    </button>
    
    <div style="width: 1px; height: 20px; background: #eee; align-self: center; margin: 0 2px;"></div>
    
    <button onclick="updatePageLanguage('am')" id="btn-am" 
        style="border: none; padding: 8px 18px; border-radius: 50px; cursor: pointer; font-weight: 800; font-size: 13px; transition: 0.3s; background: transparent; color: #136835;">
        አማርኛ
    </button>
    
</div>

<script>
        /*For signup or logout*/
     // Fetch login status from your existing session checker
    fetch('check_session.php')
        .then(response => response.json())
        .then(data => {
            const authLink = document.getElementById('auth-link');
            const authText = document.getElementById('auth-text');

                          if (data.loggedIn) {
                     // Change to Logout mode
                     authLink.href = "javascript:void(0)"; 
                     
                     // Call the function we just created
                     authLink.onclick = function() {
                         processLogout();
                     };
                 
                     if (authText) { 
                         // Get "Hi" translation
                         const lang = localStorage.getItem("userLanguage") || "en";
                         const hi = ebroTranslations[lang]["common1"] === "ይመዝገቡ" ? "ሰላም" : "Hi";
                         
                         authText.innerText = hi + ", " + data.name; 
                         authText.style.color = "#cc0000"; // Red color
                         authText.style.fontWeight = "bold"; // Bold text }
                         authText.className = "logged-in-style"; // Use a CSS class for cleaner code
                     }
                 }
        })
        .catch(err => console.error("Session check failed", err));


        
    // 1. Function to Toggle Password Visibility
    function togglePass(inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.replace("fa-eye-slash", "fa-eye");
        }
    }

    // 2. Form Validation Logic
    const form = document.getElementById('passwordForm');
    const newPass = document.getElementById('new_password');
    const confPass = document.getElementById('confirm_password');
    const errorMsg = document.getElementById('matchError');
    form.onsubmit = function(e) {
        if (newPass.value !== confPass.value) {
            e.preventDefault();
            errorMsg.style.display = 'block';
        } else if (newPass.value.length < 6) {
            e.preventDefault();
            alert("New password must be at least 6 characters long.");
        }
    };




     /*header part*/
/* -------------------------
   Safe DOM ready wrapper
   ------------------------- */
(function(){
  // product data (editable)
  const defaultProducts = [
    { id:1, name:"Rooti Slice Cad...", price:1.00, image:"https://via.placeholder.com/600x400?text=Rooti+Slice", category:"Bread", tags:["slice","white"], description:"Fresh sliced white bread." },
    { id:2, name:"Rooti Qamadi...", price:1.00, image:"https://via.placeholder.com/600x400?text=Qamadi", category:"Bread", tags:["qamadi","brown"], description:"Soft brown qamadi bread." },
    { id:3, name:"Rooti Beegar...", price:0.50, image:"https://via.placeholder.com/600x400?text=Beegar", category:"Bread", tags:["beegar","round"], description:"Round beegar bread." },
    { id:4, name:"Ceesh (Arabic)", price:1.00, image:"https://via.placeholder.com/600x400?text=Ceesh", category:"Flatbread", tags:["arabic","ceesh"], description:"Arabic ceesh soft flatbread." }
  ];
  const extraProducts = [
    { id:5, name:"Pasta Italian", price:1.20, image:"https://via.placeholder.com/600x400?text=Pasta", category:"Food", tags:["pasta","italy"], description:"Premium Italian pasta." },
    { id:6, name:"Rice 5kg", price:5.00, image:"https://via.placeholder.com/600x400?text=Rice+5kg", category:"Rice", tags:["white","rice"], description:"High quality white rice." },
    { id:7, name:"Egg Pack", price:3.00, image:"https://via.placeholder.com/600x400?text=Eggs", category:"Dairy", tags:["egg","protein"], description:"Fresh farm eggs." }
  ];
  const allProducts = [...defaultProducts, ...extraProducts];

  // DOM elements
  const hamburger = document.getElementById("hamburgerBtn");
  const sideMenu = document.getElementById("sideMenu");
  const menuOverlay = document.getElementById("menuOverlay");
  const closeMenu = document.getElementById("closeMenu");
  const searchBtn = document.getElementById("searchBtn");
  const searchPanel = document.getElementById("searchPanel");
  const closeSearch = document.getElementById("closeSearch");
  const searchInput = document.getElementById("searchInput");
  const searchResults = document.getElementById("searchResults");
  const cartBtn = document.getElementById("cartBtn");
  const accountBtn = document.getElementById("accountBtn");
  const badge = document.getElementById("cartBadge");

  /* -------------------------
     Cart helpers (persistent)
     ------------------------- */
  function loadCart(){
    try{ return JSON.parse(localStorage.getItem("cartItems")) || []; } catch { return []; }
  }
  function saveCart(cart){
    localStorage.setItem("cartItems", JSON.stringify(cart));
  }
  function updateBadge(){
    const cart = loadCart();
    const total = cart.reduce((s,i)=> s + (i.qty||0), 0);
    if(total > 0){
      badge.style.display = "flex";
      badge.innerText = total;
    } else {
      badge.style.display = "none";
      badge.innerText = "0";
    }
    localStorage.setItem("cartCount", total);
  }
  // initialize badge on load
  updateBadge();

  /* -------------------------
     Quick add function
     ------------------------- */
  window.quickAdd = function(id){
    const product = allProducts.find(p => p.id == id);
    if(!product){ alert("Product not found"); return; }
    const cart = loadCart();
    const existing = cart.find(it => it.id == product.id);
    if(existing){
      existing.qty = (existing.qty || 0) + 1;
    } else {
      cart.push({
        id: product.id,
        name: product.name,
        img: product.image,
        price: parseFloat(product.price),
        qty: 1
      });
    }
    saveCart(cart);
    updateBadge();
    const short = product.name.length > 30 ? product.name.slice(0,28) + "…" : product.name;
    alert(short + " added to cart");
  };

  /* -------------------------
     Side menu handlers
     ------------------------- */
  function openMenu(){
    sideMenu.style.left = "0";
    menuOverlay.style.display = "block";
    sideMenu.setAttribute("aria-hidden","false");
  }
  function closeMenuFn(){
    sideMenu.style.left = "-320px";
    menuOverlay.style.display = "none";
    sideMenu.setAttribute("aria-hidden","true");
  }
  hamburger.addEventListener("click", openMenu);
  menuOverlay.addEventListener("click", closeMenuFn);
  closeMenu.addEventListener("click", closeMenuFn);

  /* -------------------------
     Search rendering
     ------------------------- */
  function escapeHtml(s){
    return String(s||'').replace(/[&<>"']/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  }

  function renderProducts(list){
    if(!Array.isArray(list)) list = [];
    if(list.length === 0){
      searchResults.innerHTML = "<div style='padding:18px;color:#777'>No products found</div>";
      return;
    }
    const html = list.map(p => {
      return `
        <div class="product-card" data-id="${p.id}">
          <img src="${escapeHtml(p.image)}" alt="${escapeHtml(p.name)}">
          <div class="product-name">${escapeHtml(p.name)}</div>
          <div class="product-price">${escapeHtml(Number(p.price).toFixed(2))} birr</div>
          <button class="add-btn" onclick="quickAdd(${p.id})">Quick Add</button>
        </div>
      `;
    }).join("");
    searchResults.innerHTML = "<div class='products-grid'>"+html+"</div>";
  }

  function showDefaultProducts(){
    renderProducts(defaultProducts);
  }

  /* open/close search */
  searchBtn.addEventListener("click", function(){
    searchPanel.style.display = "block";
    searchPanel.setAttribute("aria-hidden","false");
    searchInput.value = "";
    showDefaultProducts();
    setTimeout(()=> searchInput.focus(), 120);
  });
  closeSearch.addEventListener("click", function(){
    searchPanel.style.display = "none";
    searchPanel.setAttribute("aria-hidden","true");
    searchResults.innerHTML = "";
    searchInput.value = "";
  });

  /* search input handler (match name, category, description, tags) */
  searchInput.addEventListener("input", function(){
    const q = (this.value || "").trim().toLowerCase();
    if(q === ""){
      showDefaultProducts();
      return;
    }
    const results = allProducts.filter(p => {
      return (p.name || "").toLowerCase().includes(q)
        || (p.category || "").toLowerCase().includes(q)
        || (p.description || "").toLowerCase().includes(q)
        || (p.tags || []).join(" ").toLowerCase().includes(q);
    });
    renderProducts(results);
  });

  /* -------------------------
     Navigation buttons
     ------------------------- */
  accountBtn.addEventListener("click", ()=> { window.location.href = "login.html"; });
  cartBtn.addEventListener("click", ()=> { window.location.href = "Cart.php"; });

  /* Expose updateBadge so other pages can call window.updateBadge() after adding */
  window.updateCartBadge = updateBadge;

})();





    //about part Animate only when visible on scroll
    const items = document.querySelectorAll('.animate-on-scroll');
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        }
      });
    }, { threshold: 0.3 });

    items.forEach(el => observer.observe(el));



            /*for FAQ's*/
        
function toggleSection(id, header) {
    const content = document.getElementById(id);
    const icon = header.querySelector('.toggle');

    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.textContent = '−';
    } else {
        content.classList.add('hidden');
        icon.textContent = '+';
    }
}


 function updatePageLanguage(lang) {
        if (typeof ebroTranslations !== 'undefined' && ebroTranslations[lang]) {
            const selectedLanguage = ebroTranslations[lang];
            
            document.querySelectorAll('[data-key]').forEach(el => {
                const key = el.getAttribute('data-key');
                if (selectedLanguage[key]) el.innerText = selectedLanguage[key];
            });


            const btnEn = document.getElementById('btn-en');
            const btnAm = document.getElementById('btn-am');

            if (lang === 'en') {
                btnEn.style.background = "#136835";
                btnEn.style.color = "white";
                
                btnAm.style.background = "transparent";
                btnAm.style.color = "#136835";
            } else {
                btnAm.style.background = "#136835";
                btnAm.style.color = "white";
                
                btnEn.style.background = "transparent";
                btnEn.style.color = "#136835";
            }

            localStorage.setItem('userLanguage', lang);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const savedLang = localStorage.getItem('userLanguage') || 'en';
        updatePageLanguage(savedLang);
    });


</script>
<script src="languages.js"></script>
</body>
</html>