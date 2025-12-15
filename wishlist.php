<?php 
include 'includes/db.php'; 

// SECURITY: Must be logged in
if (!isset($_SESSION['customer_id'])) { 
    header("Location: customer_login.php?msg=Please login to use Wishlist"); 
    exit(); 
}

$uid = $_SESSION['customer_id'];

// 1. ADD/REMOVE LOGIC
if(isset($_GET['add'])) {
    $pid = $_GET['add'];
    // Check if already exists
    $check = mysqli_query($conn, "SELECT * FROM wishlist WHERE user_id='$uid' AND product_id='$pid'");
    if(mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO wishlist (user_id, product_id) VALUES ('$uid', '$pid')");
        header("Location: wishlist.php?msg=Added to Wishlist");
    } else {
        header("Location: wishlist.php?msg=Already saved");
    }
    exit();
}

if(isset($_GET['remove'])) {
    $wid = $_GET['remove'];
    mysqli_query($conn, "DELETE FROM wishlist WHERE id='$wid' AND user_id='$uid'");
    header("Location: wishlist.php?msg=Removed");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Wishlist</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="assets/js/script.js" defer></script>
</head>
<body>
    <?php include 'includes/menu.php'; ?>

    <div class="container">
        <h1>❤️ My Wishlist</h1>
        
        <div class="product-grid">
            <?php
            $sql = "SELECT wishlist.id as wid, products.* FROM wishlist 
                    JOIN products ON wishlist.product_id = products.id 
                    WHERE wishlist.user_id = '$uid' ORDER BY wishlist.id DESC";
            $res = mysqli_query($conn, $sql);
            
            if(mysqli_num_rows($res) > 0) {
                while($row = mysqli_fetch_assoc($res)) {
            ?>
                <div class="card">
                    <a href="product_details.php?id=<?php echo $row['id']; ?>" style="text-decoration:none; color:inherit;">
                        <div class="card-img-container" style="height:220px; background:white; display:flex; align-items:center; justify-content:center; border-radius:12px; overflow:hidden;">
                            <img src="uploads/<?php echo $row['image']; ?>" style="max-height:180px;">
                        </div>
                        <div class="card-body" style="padding:15px 0;">
                            <h3 style="font-size:16px; margin:0;"><?php echo $row['title']; ?></h3>
                            <div style="font-weight:bold; font-size:18px;">$<?php echo $row['price']; ?></div>
                        </div>
                    </a>
                    
                    <div style="display:flex; gap:10px;">
                        <form method="POST" action="cart.php" style="flex:1;">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="product_name" value="<?php echo $row['title']; ?>">
                            <input type="hidden" name="product_price" value="<?php echo $row['price']; ?>">
                            <input type="hidden" name="vendor_id" value="<?php echo $row['vendor_id']; ?>">
                            <button type="submit" name="add_to_cart" class="btn-add" style="width:100%; border-radius:6px;">Add to Cart</button>
                        </form>
                        
                        <a href="wishlist.php?remove=<?php echo $row['wid']; ?>" style="background:#fee2e2; color:#c53030; width:40px; display:flex; align-items:center; justify-content:center; border-radius:6px; text-decoration:none;">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
            <?php 
                } 
            } else {
                echo "<p>Your wishlist is empty.</p>";
            }
            ?>
        </div>
    </div>
    
    <div id="toast-box"></div>
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.get('msg')) { showToast(urlParams.get('msg'), 'success'); }
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>