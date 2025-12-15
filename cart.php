<?php 
include 'includes/db.php'; 

// --- 1. SELF-HEALING DATA FIX (Prevents Crashes) ---
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $first_key = array_key_first($_SESSION['cart']);
    $first_val = $_SESSION['cart'][$first_key];
    
    // If the cart contains Arrays (Old Code) instead of Numbers (New Code), reset it.
    if (is_array($first_val)) {
        unset($_SESSION['cart']);
        $_SESSION['cart'] = [];
        // Optional: Redirect to refresh page
        header("Location: cart.php?msg=Cart_Updated");
        exit();
    }
}

// --- 2. ADD TO CART LOGIC ---
if (isset($_POST['add_to_cart'])) {
    $pid = $_POST['product_id'];
    $qty = 1;

    // AI Tracker
    $u_id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : 0;
    $cat_q = mysqli_query($conn, "SELECT category FROM products WHERE id='$pid'");
    if(mysqli_num_rows($cat_q) > 0){
        $cat_r = mysqli_fetch_assoc($cat_q);
        $p_cat = $cat_r['category'];
        mysqli_query($conn, "INSERT INTO user_behavior (user_id, product_id, action_type, interest_score, category) VALUES ('$u_id', '$pid', 'cart', 5, '$p_cat')");
    }

    if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }

    if (isset($_SESSION['cart'][$pid])) {
        $_SESSION['cart'][$pid] += $qty;
    } else {
        $_SESSION['cart'][$pid] = $qty;
    }
    header("Location: cart.php?msg=Added to Cart");
    exit();
}

// --- 3. HANDLE UPDATES ---
if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php?msg=Cart Cleared");
    exit();
}

if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit();
}

if (isset($_POST['update_qty'])) {
    foreach ($_POST['qty'] as $id => $qty) {
        if ($qty < 1) { unset($_SESSION['cart'][$id]); } 
        else { $_SESSION['cart'][$id] = $qty; }
    }
    header("Location: cart.php");
    exit();
}

$cart_empty = empty($_SESSION['cart']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { background: #eaeded; }
        .cart-container { max-width: 1200px; margin: 20px auto; display: grid; grid-template-columns: 3fr 1fr; gap: 20px; padding: 0 10px; }
        .cart-box { background: white; padding: 20px; border-radius: 4px; }
        .cart-title { font-size: 24px; border-bottom: 1px solid #ddd; padding-bottom: 15px; margin-bottom: 15px; }
        .cart-item { display: flex; border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 20px; gap: 20px; }
        .item-img { width: 120px; height: 120px; object-fit: contain; }
        .item-details h3 { margin: 0 0 5px 0; color: #007185; font-size: 18px; }
        .item-price { font-weight: 700; font-size: 18px; color: #b12704; }
        .subtotal-box { background: white; padding: 20px; border-radius: 4px; height: fit-content; }
        .btn-checkout { background: #ffd814; border: 1px solid #fcd200; width: 100%; padding: 10px; border-radius: 8px; cursor: pointer; text-align: center; display: block; text-decoration: none; color: black; }
        @media(max-width: 768px) { .cart-container { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <?php include 'includes/menu.php'; ?>

    <div class="cart-container">
        
        <div class="cart-box">
            <h1 class="cart-title">Shopping Cart</h1>
            
            <?php if ($cart_empty): ?>
                <div style="text-align:center; padding: 40px;">
                    <h2>Your cart is empty</h2>
                    <p>Check out today's deals and add items to your cart.</p>
                    <a href="shop.php" class="btn-checkout" style="width:200px; margin:20px auto;">Start Shopping</a>
                </div>
            <?php else: ?>
                <form method="POST">
                    <?php
                    $total = 0;
                    $count = 0;
                    foreach ($_SESSION['cart'] as $id => $qty) {
                        // SAFETY: Ensure ID is valid before querying
                        if(!is_numeric($id)) continue; 

                        $res = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
                        
                        // SAFETY: Ensure product still exists in DB
                        if(mysqli_num_rows($res) > 0) {
                            $row = mysqli_fetch_assoc($res);
                            $price = ($row['discount_price'] > 0) ? $row['discount_price'] : $row['price'];
                            $sub = $price * $qty;
                            $total += $sub;
                            $count += $qty;
                    ?>
                    <div class="cart-item">
                        <img src="uploads/<?php echo $row['image']; ?>" class="item-img">
                        <div class="item-details" style="width:100%;">
                            <a href="product_details.php?id=<?php echo $id; ?>" style="text-decoration:none;">
                                <h3><?php echo $row['title']; ?></h3>
                            </a>
                            <div style="color: #007600; font-size: 12px; margin-bottom: 10px;">In Stock</div>
                            <div style="display:flex; justify-content:space-between;">
                                <div>
                                    Qty: <input type="number" name="qty[<?php echo $id; ?>]" value="<?php echo $qty; ?>" style="width:40px; text-align:center;">
                                    <button type="submit" name="update_qty" style="background:none; border:none; color:#007185; cursor:pointer;">Update</button>
                                    <span style="color:#ddd;">|</span>
                                    <a href="cart.php?remove=<?php echo $id; ?>" style="color:#007185; text-decoration:none;">Delete</a>
                                </div>
                                <div class="item-price">$<?php echo $price; ?></div>
                            </div>
                        </div>
                    </div>
                    <?php 
                        } // End If Product Exists
                    } // End Foreach
                    ?>
                </form>
            <?php endif; ?>
        </div>

        <?php if (!$cart_empty): ?>
        <div class="subtotal-box">
            <div style="font-size:18px; margin-bottom:20px;">
                Subtotal (<?php echo $count; ?> items): <strong>$<?php echo number_format($total, 2); ?></strong>
            </div>
            <a href="checkout.php" class="btn-checkout">Proceed to checkout</a>
        </div>
        <?php endif; ?>

    </div>

</body>
</html>