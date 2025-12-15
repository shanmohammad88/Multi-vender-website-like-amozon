<?php
include 'includes/db.php';

// 1. SECURITY: Ensure ID exists
if (!isset($_GET['id'])) { die("Invalid Invoice ID"); }
$oid = mysqli_real_escape_string($conn, $_GET['id']);

// 2. FETCH ORDER DETAILS
$order_sql = "SELECT * FROM orders WHERE id = '$oid'";
$order_res = mysqli_query($conn, $order_sql);

if (mysqli_num_rows($order_res) == 0) { die("Invoice Not Found"); }
$order = mysqli_fetch_assoc($order_res);

// 3. FETCH ITEMS
$items_sql = "SELECT * FROM order_items WHERE order_id = '$oid'";
$items_res = mysqli_query($conn, $items_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice #<?php echo $oid; ?></title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #555; max-width: 800px; margin: auto; padding: 20px; }
        
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #131921; }
        .logo span { color: #febd69; }
        
        .invoice-info { text-align: right; }
        .invoice-info h1 { margin: 0; color: #333; font-size: 20px; text-transform: uppercase; }
        
        .billing-grid { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .bill-to h3 { font-size: 14px; text-transform: uppercase; color: #999; margin-bottom: 10px; }
        .bill-to p { margin: 0; font-size: 14px; line-height: 1.5; color: #333; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #f8f9fa; color: #333; font-weight: bold; padding: 12px; text-align: left; font-size: 13px; border-bottom: 1px solid #ddd; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        .text-right { text-align: right; }
        
        .totals { width: 300px; margin-left: auto; }
        .total-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; }
        .grand-total { font-weight: bold; font-size: 18px; color: #131921; border-top: 2px solid #333; padding-top: 10px; margin-top: 10px; }
        
        .footer { text-align: center; font-size: 12px; color: #999; margin-top: 50px; border-top: 1px solid #eee; padding-top: 20px; }
        
        /* PRINT BUTTON (Hidden when printing) */
        .btn-print { background: #febd69; border: none; padding: 10px 20px; font-weight: bold; cursor: pointer; border-radius: 4px; display: block; margin: 0 auto 20px auto; }
        @media print { .btn-print { display: none; } body { padding: 0; } }
    </style>
</head>
<body>

    <button onclick="window.print()" class="btn-print">🖨️ Print / Save as PDF</button>

    <div class="header">
        <div class="logo">GCC<span>Market</span></div>
        <div class="invoice-info">
            <h1>Invoice</h1>
            <p>#INV-<?php echo str_pad($oid, 5, '0', STR_PAD_LEFT); ?></p>
            <p>Date: <?php echo date('d M, Y', strtotime($order['order_date'])); ?></p>
        </div>
    </div>

    <div class="billing-grid">
        <div class="bill-to">
            <h3>Billed To:</h3>
            <p><strong><?php echo $order['customer_name']; ?></strong></p>
            <p><?php echo $order['address']; ?></p>
            <p><?php echo $order['city'] . ', ' . $order['zip']; ?></p>
            <p><?php echo isset($order['email']) ? $order['email'] : ''; ?></p>
        </div>
        <div class="bill-to" style="text-align:right;">
            <h3>Payment Method:</h3>
            <p><?php echo $order['payment_method']; ?></p>
            <p style="color: green;">✔ Paid / Confirmed</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item Description</th>
                <th class="text-right">Price</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $subtotal = 0;
            while($item = mysqli_fetch_assoc($items_res)) {
                $line_total = $item['price'] * $item['quantity'];
                $subtotal += $line_total;
            ?>
            <tr>
                <td><?php echo $item['product_name']; ?></td>
                <td class="text-right">$<?php echo number_format($item['price'], 2); ?></td>
                <td class="text-right"><?php echo $item['quantity']; ?></td>
                <td class="text-right">$<?php echo number_format($line_total, 2); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>$<?php echo number_format($subtotal, 2); ?></span>
        </div>
        <div class="total-row">
            <span>Shipping:</span>
            <span>$0.00</span>
        </div>
        <div class="total-row grand-total">
            <span>Total:</span>
            <span>$<?php echo number_format($order['total_price'], 2); ?></span>
        </div>
    </div>

    <div class="footer">
        <p>Thank you for shopping with GCCMarket.</p>
        <p>This is a computer-generated receipt.</p>
    </div>

    <script>
        // Optional: Auto-trigger print dialog on load
        window.onload = function() { window.print(); }
    </script>

</body>
</html>