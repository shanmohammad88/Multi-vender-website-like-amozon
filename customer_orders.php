<?php 
include 'includes/db.php'; 
if(!isset($_SESSION['customer_id'])){ header("Location: customer_login.php"); exit(); }
?>
<!DOCTYPE html>
<html>
<head><title>My Orders</title></head>
<body>
    <?php include 'includes/menu.php'; ?>

    <div class="container">
        <h1>📦 My Order History</h1>
        
        <table>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Items</th>
                <th>Total Cost</th>
                <th>Status</th>
            </tr>
            <?php
            $cid = $_SESSION['customer_id'];
            $sql = "SELECT * FROM orders WHERE customer_id = '$cid' ORDER BY id DESC";
            $result = mysqli_query($conn, $sql);
            
            if(mysqli_num_rows($result) > 0){
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>#".$row['id']."</td>";
                    echo "<td>".$row['order_date']."</td>";
                    echo "<td>".$row['product_list']."</td>";
                    echo "<td style='color:green; font-weight:bold;'>$".$row['total_price']."</td>";
                    echo "<td><span class='badge' style='background:blue;'>Processing</span></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center;'>No orders found. Go buy something!</td></tr>";
            }
            ?>
        </table>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>