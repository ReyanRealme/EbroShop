<?php
include 'db.php';
// Function to get count from your 'products' table
function getCategoryCount($category, $conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category = ?");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_row();
    return $row[0];
}

// Get live data for your EbRo categories
$total_all = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
$total_baby = getCategoryCount('BABY', $conn);
$total_cooking = getCategoryCount('COOKINGITEMS', $conn);
$total_basic = getCategoryCount('BASICFOOD', $conn);
$total_packedfood = getCategoryCount('PACKEDFOOD', $conn);
$total_oil = getCategoryCount('OIL', $conn);
$total_spiecs = getCategoryCount('SPIECSPOWDER', $conn);
$total_dayper = getCategoryCount('DAYPER&WIPES', $conn);
$total_Cosmotics = getCategoryCount('COSMOTICS', $conn);
$total_liquidsoap = getCategoryCount('LIQUIDSOAP', $conn);
$total_powdersoap = getCategoryCount('POWDERSOAP', $conn);
$total_modes = getCategoryCount('MODES&SOFT', $conn);
$total_packaged = getCategoryCount('PACKAGEDGOODS', $conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <title>Collection</title>

  <style>
/*header*/
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
.logo-text .sub{font-size:10px;margin-top:2px;text-transform:uppercase}

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
    object-fit:contain;
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



 /*product page*/
    * {
      box-sizing: border-box;
      font-family: Arial, Helvetica, sans-serif;
    }

    body {
      margin: 0;
      background: #f5f5f5;
    }

    /* Breadcrumb */
    .breadcrumb {
      padding: 16px;
      background: #fff;
      font-size: 14px;
    }

    .breadcrumb a {
      text-decoration: none;
      color: #000;
    }

    /* Title */
    .page-title {
      padding: 20px 16px;
      font-size: 28px;
      font-weight: bold;
      background: #fff;
    }

    /* Controls */
    .controls {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 16px;
      background: #fff;
      border-top: 1px solid #eee;
    }

    .view-icons {
      display: flex;
      align-items: center;
      gap: 8px;
      white-space: nowrap;
    }

    .view-icons button {
      width: 42px;
      height: 34px;
      border: 1px solid #ccc;
      background: #fff;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0;
    }

    .view-icons button.active {
      border-color: #000;
    }

    select {
      padding: 6px 8px;
      font-size: 14px;
    }

    /* Products */
    .products {
      display: grid;
      grid-template-columns: repeat(2, 1fr); /* default: two products */
      gap: 16px;
      padding: 16px;
    }

    .products.single {
      grid-template-columns: 1fr;
    }

    .product {
      background: #fff;
      border-radius: 6px;
      padding: 16px;
    }

    .product img {
      width: 100%;
      height: 180px;
      object-fit: contain;
    }

    .product h4 {
      margin: 12px 0 6px;
      font-size: 16px;
    }

    .price {
      font-size: 22px;
      font-weight: bold;
      margin-bottom: 12px;
    }

    .btn {
      display: block;
      text-align: center;
      background: #08377e;
      color: #fff;
      padding: 12px;
      border-radius: 25px;
      text-decoration: none;
      font-weight: bold;
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




    /*For No more product*/
    .no-more-product {
  /* Layout */
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  padding: 10px 0; /* Adjust vertical spacing to match your screen height */
  
  /* Borders */
  border-top: 1px solid #d3d3d3;
  border-bottom: 1px solid #d3d3d3;
  
  
  /* Typography */
  color: #808080;
  font-family: 'Arial', sans-serif; /* A clean sans-serif matches the image */
  font-weight: 700;
  font-size: 20px;
  letter-spacing: 0px;
  text-transform: uppercase;
  
  /* Background */
  background-color: #ffffff;
}



  </style>
</head>
<body>
    <!--Header-->

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
  <a href="about.html" class="ebro-logo-link">
    
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

<!-- SEARCH PANEL -->
<div id="searchPanel" aria-hidden="true">
  <div class="search-top">
    <input id="searchInput" type="text" placeholder="Search products..." aria-label="Search products">
    <button class="search-close-btn" id="closeSearch">Close</button>
  </div>

  <div class="products-wrap">
    <div id="searchResults" class="products-grid">
      <!-- products will render here -->
    </div>
  </div>
</div>






  <!-- Breadcrumb -->
  <div class="breadcrumb">
    
    <a href="home.php"><b style="font-size: medium;">Home</b></a> &gt; <b style="color: #8d8888;">Collection
  </div>

  <!-- Title -->
  <div style="text-align: center; margin-top: 0px; color: #185282;" class="page-title" data-key="Collection1">Catagories</div>

  <!-- Controls -->
  <div class="controls">
    <div class="view-icons">
      <!-- List view icon -->
      <button id="oneCol" title="List View">
        <svg width="24" height="16" viewBox="0 0 24 16" xmlns="http://www.w3.org/2000/svg">
          <rect y="1" width="24" height="2" fill="#666" />
          <rect y="7" width="24" height="2" fill="#666" />
          <rect y="13" width="24" height="2" fill="#666" />
        </svg>
      </button>

      <!-- Grid view icon -->
      <button id="twoCol" class="active" title="Grid View">
        <svg width="24" height="16" viewBox="0 0 24 16" xmlns="http://www.w3.org/2000/svg">
          <rect x="2" y="1" width="8" height="14" fill="#666" />
          <rect x="14" y="1" width="8" height="14" fill="#666" />
        </svg>
      </button>
    </div>

   
<select id="sort">
  <option value="default" selected>Default</option>
  <option value="az">Alphabet A–Z</option>
  <option value="za">Alphabet Z–A</option>
</select>


  </div>

  <!-- Products -->
  <div class="products" id="products">
  <div class="product"
     data-id="2000"
     data-name="basic Foods"
     data-price="2000">

  <img src="https://res.cloudinary.com/die8hxris/image/upload/v1765982579/evjtsvqkvldgvvmd3ysp.jpg">

  <h3 style="color:#111; text-align: center;" data-key="basicfood1">basic Foods</h3>
  <p style="color: #185282;"><?php echo $total_basic; ?>
    <span data-key="collection2">products found</span></p>
<br>
  <a href="basicfood.php"
   class="btn" data-key="collection3"
    >
     Shop now
  </a>
</div>


  <div class="product"
     data-id="2001"
     data-name="Packed Foods"
     data-price="2001">

  <img src="https://res.cloudinary.com/die8hxris/image/upload/v1765994814/nyrmtfsropad9nfdzcoj.jpg">

  <h3 style="color:#111; text-align: center;" data-key="Packed">Packed Foods</h3>
  <p style="color: #185282;"><?php echo $total_packedfood; ?> <span data-key="collection2">products found</span></p>
<br>
  <a  href="packedfood.php"
   class="btn" data-key="collection3"
    >
     Shop now
  </a>
</div>
 
 <div class="product"
     data-id="2002"
     data-name="Packed Foods"
     data-price="2002">

  <img src="https://res.cloudinary.com/die8hxris/image/upload/v1766037404/rywjqsflbnzcgcjl6ksc.png">

  <h3 style="text-align: center; color:#111" data-key="oil">Food Oils</h3>
  <p style="color: #185282;"><?php echo $total_oil; ?>  <span data-key="collection2">products found</span></p>
<br>
  <a  href="oil.php"
   class="btn" data-key="collection3"
    >
     Shop now
  </a>
</div>

 <div class="product"
     data-id="2003"
     data-name="Cooking items"
     data-price="2003">

  <img src="https://res.cloudinary.com/die8hxris/image/upload/v1765997285/todusadqs8x9qmgprmzr.jpg">

  <h3 style="text-align: center; color:#111" data-key="cookingingrident">Cooking items</h3>
  <p style="color: #185282;"><?php echo $total_cooking; ?>  <span data-key="collection2">products found</span></p>
<br>
  <a  href="cooking.php"
   class="btn" data-key="collection3"
    >
     Shop now
  </a>
</div>

 <div class="product"
     data-id="2004"
     data-name="Spices powders"
     data-price="2004">

  <img src="https://res.cloudinary.com/die8hxris/image/upload/v1765998005/a2av0q7gm2xjpc9jmkw2.jpg">

  <h3 style="text-align: center; color:#111" data-key="other1">Spices powders</h3>
  <p style="color: #185282;"><?php echo $total_spiecs; ?>  <span data-key="collection2">products found</span></p>
<br>
  <a  href="otheringrident.php"
   class="btn" data-key="collection3"
    >
     Shop now
  </a>
</div>

 <div class="product"
     data-id="2005"
     data-name="Baby Products"
     data-price="2005">

  <img src="https://res.cloudinary.com/die8hxris/image/upload/v1766037147/rkwxrxokurza1m4wsdlp.jpg">

  <h3 style="text-align: center; color:#111" data-key="babyproduct2">Baby Products</h3>
  <p style="color: #185282;"><?php echo $total_baby; ?>  <span data-key="collection2">products found</span></p>
<br>
  <a  href="babyproduct.php"
   class="btn" data-key="collection3"
    >
     Shop now
  </a>
</div>

 <div class="product"
     data-id="2006"
     data-name="Dayper and Wipes"
     data-price="2006">

  <img src="https://res.cloudinary.com/die8hxris/image/upload/v1766038304/mzrwavgcf09vh0rtycze.jpg">

  <h3 style="text-align: center; color:#111" data-key="Dayper">Dayper&Wipes</h3>
  <p style="color: #185282;"><?php echo $total_dayper; ?>  <span data-key="collection2">products found</span></p>
<br>
  <a  href="dayper.php"
   class="btn" data-key="collection3"
    >
     Shop now
  </a>
</div>

 <div class="product"
     data-id="2007"
     data-name="Cosmotics"
     data-price="2007">

  <img src="https://res.cloudinary.com/die8hxris/image/upload/v1766838399/augtqol2uwhqyoevbc25.jpg">

  <h3 style="text-align: center; color:#111" data-key="Cosmotics">Cosmotics</h3>
  <p style="color: #185282;"><?php echo $total_Cosmotics; ?>  <span data-key="collection2">products found</span></p>
<br>
  <a  href="cosmotics.php"
   class="btn" data-key="collection3"
    >
     Shop now
  </a>
</div>

 <div class="product"
     data-id="2008"
     data-name="Liquid soap"
     data-price="2008">

  <img src="https://res.cloudinary.com/die8hxris/image/upload/v1766040441/ymygehaukqhvpuezdpv9.jpg">

  <h3 style="text-align: center; color:#111" data-key="Liquid">Liquid soap</h3>
  <p style="color: #185282;"><?php echo $total_liquidsoap; ?>  <span data-key="collection2">products found</span></p>
<br>
  <a  href="liquidsoap.php"
   class="btn" data-key="collection3"
    >
     Shop now
  </a>
</div>

 <div class="product"
     data-id="2009"
     data-name="Laundery Powders"
     data-price="2009">

  <img src="https://res.cloudinary.com/die8hxris/image/upload/v1766041417/o0naa6476jcshxvzvuoc.jpg">

  <h3 style="text-align: center;color:#111" data-key="Powder">Powder soap</h3>
  <p style="color: #185282;"><?php echo $total_powdersoap; ?>  <span data-key="collection2">products found</span></p>
<br>
  <a  href="powdersoap.php"
   class="btn" data-key="collection3"
    >
     Shop now
  </a>
</div>

 <div class="product"
     data-id="2010"
     data-name="Modes and Soft"
     data-price="2010">

  <img src="https://res.cloudinary.com/die8hxris/image/upload/v1766042545/pgkxnbwum9blvyfwy5oa.jpg">

  <h3 style="text-align: center; color:#111" data-key="Modes">Modes&Softs </h3>
  <p style="color: #185282;"><?php echo $total_modes; ?>  <span data-key="collection2">products found</span></p>
<br>
  <a  href="modes.php"
   class="btn" data-key="collection3"
    >
     Shop now
  </a>
</div>

 <div class="product"
     data-id="2011"
     data-name="Packaged Goods"
     data-price="2011">

  <img src="https://res.cloudinary.com/die8hxris/image/upload/v1766043575/vrgmwqovhajffzvdabh4.jpg">

  <h3 style="text-align: center;color:#111" data-key="Packaged">Bottle Goods</h3>
  <p style="color: #185282;"><?php echo $total_packaged; ?>  <span data-key="collection2">products found</span></p>
<br>
  <a  href="packaged.php"
   class="btn" data-key="collection3">
     Shop now
  </a>
</div>

</div>
  <div class="no-more-product" data-key="babyproduct3">
  NO MORE Catagories
</div>
    



<img src="https://res.cloudinary.com/die8hxris/image/upload/v1765983301/wwa0hvys9hynad7fju9u.jpg" width="400px" height="150px"/>
<!--FAQ's-->
 <div style="   margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: #f7f7f7;
    color: #222;">

<div style="  padding: 20px;" class="footer">

    <h2 style="    font-size: 28px;
    font-weight: 700;
    margin-bottom: 20px;" data-key="commonB1">CONNECT WITH US</h2>

    <div style="    display: flex;
    gap: 24px;
    font-size: 32px;
    margin-bottom: 24px;" class="social-icons">
        <a style=" color: #222;
    text-decoration: none;" href="https://www.facebook.com/profile.php?id=100083927815758"><i class="fab fa-facebook-f"></i></a>
        <a style=" color: #222;
    text-decoration: none;" href="#"><i class="fab fa-instagram"></i></a>
    </div>



    

    <!-- Ebroshop Section (CLOSED by default) -->
    <div style="   border-top: 1px solid #ddd;
    padding: 18px 0;" class="section">
        <div style=" display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;" class="section-header" onclick="toggleSection('adeeg', this)">
            <h3 style="  margin: 0;
    font-size: 24px;
    font-weight: 700;">Ebroshop</h3>
            <span style="  font-size: 26px;" class="toggle">+</span>
        </div>

        <div style="margin-top: 16px;" class="links hidden" id="adeeg">
          <a style="  display: block;
    text-decoration: none;
    color: #9a9a9a;
    font-size: 20px;
    margin: 14px 0;" href="home.php" data-key="commonB4">Home</a>

          <a style="  display: block;
    text-decoration: none;
    color: #9a9a9a;
    font-size: 20px;
    margin: 14px 0;" href="about.php" data-key="commonB5">About-us</a>
           
           <a style="  display: block;
    text-decoration: none;
    color: #9a9a9a;
    font-size: 20px;
    margin: 14px 0;" href="faq.html" data-key="commonB6">FAQs</a>
            <a style="  display: block;
    text-decoration: none;
    color: #9a9a9a;
    font-size: 20px;
    margin: 14px 0;" href="contact.html" data-key="commonB7">Contact Us</a>
        </div>
    </div>

    <!-- Store Policy -->
    <div style="   border-top: 1px solid #ddd;
    padding: 18px 0;" class="section">
        <div style=" display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;" class="section-header" onclick="toggleSection('store', this)">
            <h3 style="  margin: 0;
    font-size: 24px;
    font-weight: 700;" data-key="commonB8">Store Policy</h3>
            <span style="  font-size: 26px;" class="toggle">+</span>
        </div>

        <div style="margin-top: 16px;" class="links hidden" id="store">
           <a style="  display: block;
    text-decoration: none;
    color: #9a9a9a;
    font-size: 20px;
    margin: 14px 0;" href="terms.html" data-key="commonB9">Terms Of Service</a>
           
           <a style="  display: block;
    text-decoration: none;
    color: #9a9a9a;
    font-size: 20px;
    margin: 14px 0;" href="privacy.html" data-key="commonB10">Privacy Policy</a>
         
            <a style="  display: block;
    text-decoration: none;
    color: #9a9a9a;
    font-size: 20px;
    margin: 14px 0;" href="returns.html" data-key="commonB11">Return Policy</a>
        </div>
    </div>

    <!-- Contact Us -->
    <div style=" border-top: 1px solid #ddd;
    padding: 18px 0;" class="section">
        <div style=" display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;" class="section-header" onclick="toggleSection('contact', this)">
            <h3 style="  margin: 0;
    font-size: 24px;
    font-weight: 700;" data-key="commonB12">Contact Us</h3>
            <span style="  font-size: 26px;" class="toggle">+</span>
        </div>

        <div style="margin-top: 16px;" class="links hidden" id="contact">
             <a style="  display: block;
    text-decoration: none;
    color: #000000;
    font-size: 20px;
    margin: 14px 0;">
     <span data-key="commonB13">Phone:</span>
      <b style="color: #676767;" data-key="commonB16">+251970130755</b></a>
           <a style="  display: block;
    text-decoration: none;
    color: #000000;
    font-size: 20px;
    margin: 14px 0;" >
    <span data-key="commonB14">Email: </span>
    <b style="color: #676767;" data-key="commonB17">ebroshoponline@gmail.com</b></a>
        </div>
    </div>

</div>
</div>



<!--Last footer-->

<div style="  margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: #ffffff;">
<div style=" text-align: center;
    padding: 40px 20px 30px;" class="footer-bottom">

    <!-- Logo -->
    <div class="footer-logo">
        <img style="  max-width: 160px;
    margin-bottom: 24px;" src="https://res.cloudinary.com/die8hxris/image/upload/v1765983370/zz6tex5swhmvjff8aphf.jpg" alt="Ebroshop Logo">
    </div>

    <!-- Copyright -->
    <div style="  color: #828080;
    font-size: 28px;
    font-weight: 400;
    margin-bottom: 40px;" class="footer-text">
        <span data-key="commonB15">EbRoShop.Com©2025 All Rights Reserved</span>
        <a href="developer.html" data-key="commonB18" style="text-decoration: none; color: #828080;"></a>
    </div>

    <!-- Payment / Partner Logos (Clickable) -->
    <div style=" display: flex;
    justify-content: center;
    align-items: center;
    gap: 32px;
    flex-wrap: wrap;" class="payment-logos">
        <a href="tel:*889#" target="_self">
            <img style=" height: 40px;
    object-fit: contain;
    transition: transform 0.2s ease;" src="https://res.cloudinary.com/die8hxris/image/upload/v1765983122/fqldh9ima4wy9hrsjkhv.jpg" alt="CBE">
        </a>

        <a href="tel:*127#" target="_self">
            <img style=" height: 40px;
    object-fit: contain;
    transition: transform 0.2s ease;" src="https://res.cloudinary.com/die8hxris/image/upload/v1765983555/e6oydnwyeoxag8vhar1w.jpg" alt="Telebirr">
        </a>
    </div>
</div>
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


<div class="language-bar" style="position:absolute; top: 110px; right: 0px; display: flex; background: white; padding: 5px; border-radius: 50px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); z-index: 10000; border: 2px solid #136835;">
    
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


        
    
/*header*/
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
function addToCart(e, name, price, image){
  e.preventDefault();

  let cart = JSON.parse(localStorage.getItem("cartItems")) || [];

  const existing = cart.find(p => p.name === name);
  if(existing){
    existing.qty += 1;
  } else {
    cart.push({
      name,
      price,
      qty: 1,
      image // ✅ CLOUDINARY URL
    });
  }

  localStorage.setItem("cartItems", JSON.stringify(cart));
  alert(name + " added to cart");
}

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



  /*for product page*/
    const productsEl = document.getElementById('products');
    const oneCol = document.getElementById('oneCol');
    const twoCol = document.getElementById('twoCol');

    // View toggle
    oneCol.onclick = () => {
      productsEl.classList.add('single');
      oneCol.classList.add('active');
      twoCol.classList.remove('active');
    };

    twoCol.onclick = () => {
      productsEl.classList.remove('single');
      twoCol.classList.add('active');
      oneCol.classList.remove('active');
    };

   


document.addEventListener('DOMContentLoaded', () => {
  const productsEl = document.getElementById('products');
  const itemsOriginal = [...productsEl.children]; // Save original order

  document.getElementById('sort').onchange = function () {
    let sorted;

    switch (this.value) {
      case 'az':
        sorted = [...itemsOriginal].sort((a, b) => a.dataset.name.localeCompare(b.dataset.name));
        break;
      case 'za':
        sorted = [...itemsOriginal].sort((a, b) => b.dataset.name.localeCompare(a.dataset.name));
        break;
      case 'priceLow':
        sorted = [...itemsOriginal].sort((a, b) => a.dataset.price - b.dataset.price);
        break;
      case 'priceHigh':
        sorted = [...itemsOriginal].sort((a, b) => b.dataset.price - a.dataset.price);
        break;
      default:
        sorted = itemsOriginal; // Default order
        break;
    }

    sorted.forEach(el => productsEl.appendChild(el));
  };
});




    // Quick add → add to cart + redirect
function addToCart(e, btn) {
  e.preventDefault();

  const product = btn.closest(".product");
  if (!product) return;

  const item = {
    id: product.dataset.id,
    name: product.dataset.name,
    price: Number(product.dataset.price),
    image: product.querySelector("img").src,
    qty: 1
  };

  let cart = JSON.parse(localStorage.getItem("cartItems")) || [];

  const existing = cart.find(p => p.id === item.id);
  if (existing) {
    existing.qty += 1;
  } else {
    cart.push(item);
  }

  localStorage.setItem("cartItems", JSON.stringify(cart));

  // ✅ redirect always works
  window.location.href = "Cart.php";
}



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
