<?php
include 'includes/db.php';
if (!isset($_GET['oid'])) { header("Location: index.php"); exit(); }
$oid = $_GET['oid'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmed</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="background:#eaeded; padding-top: 50px;">

    <div class="container" style="max-width: 600px; background: white; padding: 40px; border-radius: 8px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        
        <div style="font-size: 60px; color: #2ecc71; margin-bottom: 20px;">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1 style="margin: 0; color: #111;">Order Confirmed!</h1>
        <p style="font-size: 18px; color: #555;">Thank you for shopping with us.</p>
        
        <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin: 30px 0; text-align: left;">
            <p><strong>Order ID:</strong> #<?php echo $oid; ?></p>
            <p><strong>Estimated Delivery:</strong> <?php echo date('d M, Y', strtotime('+3 days')); ?></p>
            <p>We have sent a confirmation email to you.</p>
        </div>

        <div style="display: flex; gap: 10px; justify-content: center;">
            <a href="invoice.php?id=<?php echo $oid; ?>" target="_blank" class="btn-primary" style="text-decoration:none; padding: 12px 25px;">
                <i class="fas fa-file-invoice"></i> Download Invoice
            </a>
            
            <a href="shop.php" class="btn-add" style="text-decoration:none; padding: 12px 25px;">
                Continue Shopping
            </a>
        </div>

    </div>

</body>
</html>