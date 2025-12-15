<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include_once 'includes/db.php'; // Ensure language logic is loaded

// Cart Count
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

// User Name
$user_display = $lang['signin'];
if (isset($_SESSION['customer_name'])) { $user_display = substr($_SESSION['customer_name'], 0, 10); } 
elseif (isset($_SESSION['vendor_name'])) { $user_display = substr($_SESSION['vendor_name'], 0, 10); }
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700&family=Cairo:wght@400;700&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="assets/img/favicon.png">

<style>
    /* --- DYNAMIC LANGUAGE CSS --- */
    :root { --nav-bg: #131921; --nav-hover: #232f3e; --primary: #febd69; }
    
    /* Font Switching */
    body { margin: 0; padding-top: 0; font-family: '<?php echo ($curr_lang == "ar") ? "Cairo" : "Outfit"; ?>', sans-serif !important; }
    * { box-sizing: border-box; }

    /* Navbar Container */
    .navbar {
        background-color: var(--nav-bg); height: 60px; display: flex; align-items: center; justify-content: space-between;
        padding: 0 15px; color: white; position: sticky; top: 0; z-index: 1000;
    }

    /* RTL Support */
    .navbar[dir="rtl"] { direction: rtl; }
    .navbar[dir="rtl"] .nav-logo { margin-right: 0; margin-left: 10px; }
    
    /* Logo */
    .nav-logo { text-decoration: none; color: white; font-size: 22px; font-weight: 700; display: flex; align-items: center; margin-right: 10px; white-space: nowrap; }
    .nav-logo span { color: var(--primary); }

    /* Language Button */
    .lang-switch {
        color: white; text-decoration: none; padding: 5px 8px; border: 1px solid rgba(255,255,255,0.3); 
        border-radius: 2px; font-size: 13px; font-weight: bold; margin: 0 5px; display: flex; align-items: center; gap: 5px;
    }
    .lang-switch:hover { border-color: white; }

    /* Search Bar */
    .nav-search {
        flex: 1; max-width: 800px; height: 40px; display: flex; background-color: white; border-radius: 4px; overflow: hidden; margin: 0 15px;
    }
    .nav-search input { width: 100%; height: 100%; padding: 0 15px; border: none; outline: none; font-size: 15px; }
    .nav-search button { background-color: var(--primary); border: none; width: 50px; height: 100%; cursor: pointer; font-size: 18px; display: flex; align-items: center; justify-content: center; }
    
    /* Search Bar RTL Flip */
    .navbar[dir="rtl"] .nav-search button { border-radius: 0; } 

    /* Right Links */
    .nav-tools { display: flex; align-items: center; gap: 10px; }
    .nav-item { color: white; text-decoration: none; padding: 5px 8px; border: 1px solid transparent; border-radius: 2px; display: flex; flex-direction: column; justify-content: center; line-height: 1.2; }
    .nav-item:hover { border-color: white; }
    .nav-line-1 { font-size: 12px; opacity: 0.9; }
    .nav-line-2 { font-size: 14px; font-weight: 700; white-space: nowrap; }

    /* Cart */
    .nav-cart { display: flex; flex-direction: row; align-items: flex-end; position: relative; }
    .cart-count { position: absolute; top: -5px; left: 10px; color: var(--primary); font-size: 14px; font-weight: bold; }
    
    /* Secondary Menu */
    .sub-navbar { background-color: #232f3e; height: 40px; display: flex; align-items: center; padding: 0 15px; overflow-x: auto; white-space: nowrap; }
    .sub-navbar[dir="rtl"] { direction: rtl; }
    .sub-link { color: white; text-decoration: none; font-size: 14px; padding: 6px 10px; margin: 0 5px; font-weight: 500; }
    .sub-link:hover { border: 1px solid white; border-radius: 2px; }

    /* Mobile */
    @media (max-width: 768px) {
        .navbar { flex-wrap: wrap; height: auto; padding: 10px 15px; }
        .nav-logo { order: 1; margin-right: auto; margin-left: 0; }
        .navbar[dir="rtl"] .nav-logo { margin-left: auto; margin-right: 0; }
        .nav-tools { order: 2; gap: 5px; }
        .nav-search { order: 3; width: 100%; max-width: 100%; margin: 10px 0 0 0; display: flex !important; }
        .nav-line-1, .cart-text { display: none; }
    }
</style>

<nav class="navbar" dir="<?php echo $dir; ?>">
    
    <a href="index.php" class="nav-logo">
        <i class="fas fa-shopping-bag" style="margin:0 5px;"></i>GCCMarket<span>Place</span>
    </a>

    <?php if($curr_lang == 'en'): ?>
        <a href="?lang=ar" class="lang-switch">🇦🇪 AR</a>
    <?php else: ?>
        <a href="?lang=en" class="lang-switch">🇺🇸 EN</a>
    <?php endif; ?>

    <div class="nav-search">
        <form action="search.php" method="GET" style="display:flex; width:100%; height:100%;">
            <input type="text" name="q" placeholder="<?php echo $lang['search_placeholder']; ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <div class="nav-tools">

        <?php if(isset($_SESSION['customer_id'])): ?>
            <a href="customer_orders.php" class="nav-item">
                <span class="nav-line-1"><?php echo $lang['hello']; ?>, <?php echo $user_display; ?></span>
                <span class="nav-line-2"><?php echo $lang['returns']; ?> & <?php echo $lang['orders']; ?></span>
            </a>
            <a href="customer_login.php?logout=true" class="nav-item">
                <span class="nav-line-1"><?php echo $lang['account']; ?></span>
                <span class="nav-line-2" style="color:#ff9999;"><?php echo $lang['signout']; ?></span>
            </a>

        <?php elseif(isset($_SESSION['vendor_id'])): ?>
            <a href="vendor_dashboard.php" class="nav-item">
                <span class="nav-line-1"><?php echo $lang['seller_zone']; ?></span>
                <span class="nav-line-2"><?php echo $lang['dashboard']; ?></span>
            </a>
            <a href="vendor_login.php?logout=true" class="nav-item">
                <span class="nav-line-2" style="color:#ff9999;"><?php echo $lang['exit']; ?></span>
            </a>

        <?php else: ?>
            <a href="customer_login.php" class="nav-item">
                <span class="nav-line-1"><?php echo $lang['hello']; ?>, <?php echo $lang['signin']; ?></span>
                <span class="nav-line-2"><?php echo $lang['account']; ?></span>
            </a>
            <a href="vendor_login.php" class="nav-item">
                <span class="nav-line-1"><?php echo $lang['sell']; ?></span>
                <span class="nav-line-2">& <?php echo $lang['grow']; ?></span>
            </a>
        <?php endif; ?>

        <a href="cart.php" class="nav-item nav-cart">
            <div class="cart-icon-box" style="position:relative; font-size:28px;">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count"><?php echo $cart_count; ?></span>
            </div>
            <span class="cart-text nav-line-2" style="margin:0 5px;"><?php echo $lang['cart']; ?></span>
        </a>

    </div>
</nav>

<div class="sub-navbar" dir="<?php echo $dir; ?>">
    <a href="shop.php" class="sub-link"><i class="fas fa-bars"></i> <?php echo $lang['all']; ?></a>
    <a href="flash_sale.php" class="sub-link"><?php echo $lang['todays_deals']; ?></a>
    <a href="shop.php?cat=Electronics" class="sub-link"><?php echo $lang['electronics']; ?></a>
    <a href="shop.php?cat=Fashion" class="sub-link"><?php echo $lang['fashion']; ?></a>
    <a href="shop.php?cat=Home" class="sub-link"><?php echo $lang['home']; ?></a>
    <a href="shop.php?cat=Beauty" class="sub-link"><?php echo $lang['beauty']; ?></a>
    <a href="shop.php?cat=Toys" class="sub-link"><?php echo $lang['toys']; ?></a>
</div>