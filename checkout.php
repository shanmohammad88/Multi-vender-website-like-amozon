<?php
include 'includes/db.php';

// 1. GUEST CHECK: We removed the "header(Location: login)" line.
// Instead, we check if the CART is empty.
if (empty($_SESSION['cart'])) { header("Location: cart.php"); exit(); }

// 2. IDENTIFY USER (Member vs Guest)
if (isset($_SESSION['customer_id'])) {
    // Member
    $cid = $_SESSION['customer_id'];
    $cname = $_SESSION['customer_name'];
    $cemail = ""; // We will ask them to confirm email in the form
    $is_guest = false;
} else {
    // Guest
    $cid = 0; // ID 0 means Guest
    $cname = "";
    $cemail = "";
    $is_guest = true;
}

// 3. CALCULATE TOTAL
$total = 0;
foreach ($_SESSION['cart'] as $id => $qty) {
    $res = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
    $p = mysqli_fetch_assoc($res);
    $price = ($p['discount_price'] > 0) ? $p['discount_price'] : $p['price'];
    $total += ($price * $qty);
}

// 4. HANDLE ORDER SUBMISSION
if (isset($_POST['place_order'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']); // Capture Email
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $zip = mysqli_real_escape_string($conn, $_POST['zip']);
    $payment = $_POST['payment_method'];
    
    $date = date('Y-m-d H:i:s');
    $full_address = "$address, $city, $zip";
    
    // Insert Order (Includes Email now)
    $sql = "INSERT INTO orders (customer_id, customer_name, email, total_price, order_date, address, city, zip, payment_method) 
            VALUES ('$cid', '$name', '$email', '$total', '$date', '$full_address', '$city', '$zip', '$payment')";
    
    if (mysqli_query($conn, $sql)) {
        $order_id = mysqli_insert_id($conn);
        
        // Insert Items
        foreach ($_SESSION['cart'] as $pid => $qty) {
            $p_res = mysqli_query($conn, "SELECT * FROM products WHERE id='$pid'");
            $prod = mysqli_fetch_assoc($p_res);
            $price = ($prod['discount_price'] > 0) ? $prod['discount_price'] : $prod['price'];
            $vid = $prod['vendor_id'];
            $pname = mysqli_real_escape_string($conn, $prod['title']);
            
            mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, product_name, price, quantity, vendor_id) 
                                 VALUES ('$order_id', '$pid', '$pname', '$price', '$qty', '$vid')");
            
            // Reduce Stock
            mysqli_query($conn, "UPDATE products SET stock = stock - $qty WHERE id='$pid'");
        }
        
        // Clear Cart & Redirect
        unset($_SESSION['cart']);
        header("Location: thank_you.php?oid=$order_id");
        exit();
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout - GCCMarket</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { background: #eaeded; font-family: Arial, sans-serif; }
        .checkout-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        
        .box { background: white; padding: 25px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ddd; }
        .box h3 { margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px; color: #c45500; font-size: 18px; }
        
        /* Form Styles */
        .form-row { display: flex; gap: 15px; }
        .form-group { flex: 1; margin-bottom: 15px; }
        label { display: block; font-size: 13px; font-weight: 700; margin-bottom: 5px; color: #111; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 3px; box-shadow: 0 1px 2px rgba(0,0,0,0.1) inset; box-sizing: border-box; }
        input:focus { border-color: #e77600; outline: none; box-shadow: 0 0 3px 2px rgba(228,121,17,0.5); }
        
        /* Payment Options */
        .pay-option { 
            border: 1px solid #ccc; padding: 15px; border-radius: 4px; margin-bottom: 10px; cursor: pointer;
            display: flex; align-items: center; gap: 10px; transition: 0.2s;
        }
        .pay-option:hover { background: #fcfcfc; border-color: #e77600; }
        .pay-option input { width: auto; box-shadow: none; }
        
        /* Summary Box */
        .summary-box { position: sticky; top: 20px; }
        .sum-row { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 13px; color: #333; }
        
        .total-row { 
            border-top: 1px solid #eee; 
            padding-top: 15px; 
            margin-top: 15px; 
            font-weight: 700; 
            color: #b12704; 
            font-size: 18px; 
            display: flex; 
            justify-content: space-between; 
        }
        
        .btn-place { 
            background: #ffd814; border: 1px solid #fcd200; width: 100%; padding: 10px; border-radius: 8px; 
            cursor: pointer; font-size: 13px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); font-weight: 500;
        }
        .btn-place:hover { background: #f7ca00; border-color: #f2c200; }

        @media(max-width: 768px) { .checkout-grid { grid-template-columns: 1fr; } .summary-box { position: static; order: -1; margin-bottom: 20px; } }
    </style>
</head>
<body>

    <div style="background: linear-gradient(to bottom, #f7f7f7, #eaeaea); padding: 15px 20px; border-bottom: 1px solid #ddd; display: flex; align-items: center; justify-content: space-between;">
        <a href="index.php" style="color: #111; font-size: 20px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 5px;">
            <i class="fas fa-shopping-bag" style="color:#febd69;"></i> GCC<span style="color:#febd69;">Market</span>
        </a>
        <div style="font-size: 20px; color: #555;"><i class="fas fa-lock"></i></div>
    </div>

    <div style="text-align: center; margin: 20px 0;">
        <h1 style="font-size: 28px; font-weight: 400; color: #111;">
            Checkout <?php if($is_guest) echo "<span style='font-size:16px; color:#777;'>(Guest)</span>"; ?>
        </h1>
    </div>

    <form method="POST" class="checkout-grid">
        
        <div>
            
            <?php if($is_guest): ?>
            <div style="background: #e7f4f5; border: 1px solid #007185; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-info-circle" style="color: #007185; font-size: 20px;"></i>
                <div>
                    <span style="font-weight: bold; color: #007185;">Already have an account?</span>
                    <a href="customer_login.php" style="color: #007185;">Sign in</a> for faster checkout.
                </div>
            </div>
            <?php endif; ?>

            <div class="box">
                <h3>1. Delivery Address</h3>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?php echo $cname; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo $cemail; ?>" placeholder="To send your receipt" required>
                </div>

                <div class="form-group">
                    <label>Street Address</label>
                    <input type="text" name="address" placeholder="Flat, House no., Building, Company, Apartment" required>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>City</label><input type="text" name="city" required></div>
                    <div class="form-group"><label>Zip Code</label><input type="text" name="zip" required></div>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" required>
                </div>
            </div>

            <div class="box">
                <h3>2. Payment Method</h3>
                
                <label class="pay-option">
                    <input type="radio" name="payment_method" value="Credit Card" checked onclick="toggleCard(true)">
                    <span style="font-weight:700;">Credit or Debit Card</span>
                    <div style="margin-left:auto; display:flex; gap:5px;">
                        <i class="fab fa-cc-visa" style="font-size:24px; color:#1a1f71;"></i>
                        <i class="fab fa-cc-mastercard" style="font-size:24px; color:#eb001b;"></i>
                    </div>
                </label>
                
                <div id="card-form" style="background: #fcfcfc; padding: 20px; border: 1px solid #e7e7e7; margin-bottom: 15px; border-radius: 4px;">
                    <div class="form-group">
                        <label>Card Number</label>
                        <input type="text" placeholder="0000 0000 0000 0000" maxlength="19">
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Expiration Date</label><input type="text" placeholder="MM/YY"></div>
                        <div class="form-group"><label>Security Code (CVV)</label><input type="password" placeholder="123" maxlength="3"></div>
                    </div>
                </div>

                <label class="pay-option">
                    <input type="radio" name="payment_method" value="COD" onclick="toggleCard(false)">
                    <span style="font-weight:700;">Cash on Delivery</span>
                </label>
            </div>
        </div>

        <div class="summary-box">
            <div class="box">
                <button type="submit" name="place_order" class="btn-place" style="margin-bottom: 15px;">Place your order</button>
                <p style="font-size:11px; color:#555; text-align:center; margin-top:0;">By placing your order, you agree to GCCMarket's privacy notice and conditions of use.</p>
                
                <hr style="border:0; border-top:1px solid #eee; margin: 15px 0;">
                
                <div class="sum-row">
                    <h3>Order Summary</h3>
                </div>
                <div class="sum-row">
                    <span>Items:</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>
                <div class="sum-row">
                    <span>Shipping & Handling:</span>
                    <span style="color: #b12704;">$0.00</span>
                </div>
                <div class="sum-row">
                    <span>Total before tax:</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>
                <div class="sum-row">
                    <span>Estimated Tax:</span>
                    <span>$0.00</span>
                </div>
                <div class="total-row">
                    <span>Order Total:</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>
            </div>
        </div>

    </form>

    <script>
        function toggleCard(show) {
            document.getElementById('card-form').style.display = show ? 'block' : 'none';
        }
    </script>

</body>
</html>