<?php 
include 'includes/db.php'; 

// Add to Cart
if (isset($_POST['add_to_cart'])) {
    $item = array('id' => $_POST['product_id'], 'name' => $_POST['product_name'], 'price' => $_POST['product_price']);
    if (!isset($_SESSION['cart'])) { $_SESSION['cart'][0] = $item; } 
    else { $count = count($_SESSION['cart']); $_SESSION['cart'][$count] = $item; }
    header("Location: cart.php"); exit();
}

// Clear Cart
if (isset($_GET['clear'])) { unset($_SESSION['cart']); header("Location: cart.php"); }

// Checkout
if (isset($_POST['checkout'])) {
    $name = $_POST['customer_name'];
    $total = $_POST['total_bill'];
    $list = "";
    foreach ($_SESSION['cart'] as $k => $v) { $list .= $v['name'] . ", "; }
    
    mysqli_query($conn, "INSERT INTO orders (customer_name, total_price, product_list) VALUES ('$name', '$total', '$list')");
    unset($_SESSION['cart']);
    echo "<script>alert('Order Placed Successfully!'); window.location.href='index.php';</script>";
}
?>
<!DOCTYPE html>
<html>
<head><title>My Cart</title></head>
<body>
    <?php include 'includes/menu.php'; ?>
    <div class="container">
        <h1>Your Shopping Cart</h1>
        <a href="cart.php?clear=true" style="color: red; float: right;">Empty Cart</a>
        
        <table style="width: 100%; border-collapse: collapse; background: white; margin-top: 10px;">
            <tr style="background: #333; color: white;">
                <th style="padding: 10px;">Product</th>
                <th style="padding: 10px;">Price</th>
            </tr>
            <?php
            $total = 0;
            if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $key => $val) {
                    echo "<tr><td style='padding:10px; border:1px solid #ddd;'>".$val['name']."</td>";
                    echo "<td style='padding:10px; border:1px solid #ddd;'>$".$val['price']."</td></tr>";
                    $total += $val['price'];
                }
            }
            ?>
            <tr>
                <td align="right" style="padding:10px;"><strong>Total:</strong></td>
                <td style="padding:10px;"><strong>$<?php echo $total; ?></strong></td>
            </tr>
        </table>

        <?php if($total > 0): ?>
            <div style="background: #e9ecef; padding: 20px; margin-top: 20px; width: 300px;">
                <h3>Checkout</h3>
                <form method="POST">
                    <label>Your Name:</label><br>
                    <input type="text" name="customer_name" required style="width: 95%; padding: 8px; margin-bottom: 10px;"><br>
                    <input type="hidden" name="total_bill" value="<?php echo $total; ?>">
                    <button type="submit" name="checkout" style="width: 100%; padding: 10px; background: #28a745; color: white; border: none; cursor: pointer;">Place Order</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>