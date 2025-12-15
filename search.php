<?php 
include 'includes/db.php'; 

// Get the search term
if(isset($_GET['q'])) {
    $search = mysqli_real_escape_string($conn, $_GET['q']);
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Results: <?php echo $search; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="assets/js/script.js" defer></script>
    <style>
        /* Reuse minimalist Shop styles */
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 40px; }
        
        .section-header { margin-bottom: 40px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .section-header h1 { margin: 0; color: var(--dark); font-size: 24px; }
        
        /* Minimalist Card (No Box) */
        .card { background: transparent; border: none; }
        .card-img-container {
            border-radius: 16px; overflow: hidden; background: white;
            height: 240px; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: 0.3s;
        }
        .card:hover .card-img-container { transform: translateY(-5px); box-shadow: 0 15px 25px rgba(0,0,0,0.1); }
        .card-body { padding: 15px 0; }
        .card-title { font-size: 16px; margin-bottom: 5px; color: #333; }
        
        .no-results {
            text-align: center; padding: 80px; background: white; border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); color: #777;
        }
    </style>
</head>
<body>

    <?php include 'includes/menu.php'; ?>

    <div class="container">
        
        <div class="section-header">
            <?php
            // Search Logic: Check Title, Category, Description, or Vendor Name
            $sql = "SELECT * FROM products WHERE 
                    title LIKE '%$search%' OR 
                    category LIKE '%$search%' OR 
                    description LIKE '%$search%' OR 
                    vendor_name LIKE '%$search%'";
            
            $result = mysqli_query($conn, $sql);
            $count = mysqli_num_rows($result);
            ?>
            <h1>Search Results for "<span style="color:var(--primary);"><?php echo $search; ?></span>"</h1>
            <span style="color:#777;"><?php echo $count; ?> items found</span>
        </div>

        <?php if($count > 0): ?>
            <div class="product-grid">
                <?php while ($row = mysqli_fetch_array($result)) { ?>
                    
                    <div class="card">
                        <a href="product_details.php?id=<?php echo $row['id']; ?>" style="text-decoration:none; color:inherit;">
                            <div class="card-img-container">
                                <img src="uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['title']; ?>" style="max-height:200px; max-width:90%;">
                            </div>
                            
                            <div class="card-body">
                                <h3 class="card-title"><?php echo $row['title']; ?></h3>
                                <span style="font-size:12px; color:#999;">
                                    <i class="fas fa-store"></i> <?php echo $row['vendor_name']; ?>
                                </span>
                                
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:10px;">
                                    <div style="font-size:18px; font-weight:bold; color:#333;">$<?php echo $row['price']; ?></div>
                                    
                                    <?php if(isset($row['stock']) && $row['stock'] > 0): ?>
                                        <small style="color:green; font-weight:600;">In Stock</small>
                                    <?php else: ?>
                                        <small style="color:red; font-weight:600;">Out of Stock</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                        
                        <form method="POST" action="cart.php">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="product_name" value="<?php echo $row['title']; ?>">
                            <input type="hidden" name="product_price" value="<?php echo $row['price']; ?>">
                            <input type="hidden" name="vendor_id" value="<?php echo $row['vendor_id']; ?>">
                            
                            <?php if(isset($row['stock']) && $row['stock'] > 0): ?>
                                <button type="submit" name="add_to_cart" class="btn-add" style="border-radius: 8px;">Add to Cart</button>
                            <?php else: ?>
                                <button type="button" class="btn-add" style="background:#eee; color:#999; cursor:not-allowed; border-radius: 8px;">Unavailable</button>
                            <?php endif; ?>
                        </form>
                    </div>

                <?php } ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <i class="fas fa-search" style="font-size: 50px; color: #ddd; margin-bottom: 20px;"></i>
                <h2>We couldn't find anything for "<?php echo $search; ?>"</h2>
                <p>Try searching for something else, like "Watch", "Toy", or "Fashion".</p>
                <br>
                <a href="shop.php" class="btn-primary">Browse All Products</a>
            </div>
        <?php endif; ?>

    </div>

    <?php include 'includes/footer.php'; ?>

</body>
</html>