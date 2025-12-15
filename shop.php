<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Shop - MarketPlace</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="assets/js/script.js" defer></script>
    <style>
        /* PAGE SPECIFIC STYLES */
        .main-layout { display: flex; gap: 30px; margin-top: 40px; position: relative; }
        .sidebar { width: 260px; flex-shrink: 0; }
        
        .category-box {
            background: white; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 20px; position: sticky; top: 100px; border: 1px solid rgba(0,0,0,0.05);
        }
        .category-box h3 { border-bottom: 2px solid var(--primary); padding-bottom: 10px; margin-bottom: 15px; font-size: 18px; color: var(--dark); }
        .category-list { list-style: none; padding: 0; margin: 0; }
        .category-list li { margin-bottom: 8px; }
        .category-list a { display: flex; align-items: center; padding: 10px; color: #555; text-decoration: none; border-radius: 8px; transition: 0.3s; font-weight: 500; }
        .category-list a:hover, .category-list a.active { background: #fff8e1; color: #d38d06; padding-left: 15px; }
        .category-list i { width: 25px; text-align: center; margin-right: 10px; opacity: 0.7; }

        .main-content { flex-grow: 1; }
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .section-title { font-size: 24px; font-weight: 700; color: var(--dark); margin: 0; position: relative; }
        .section-title::after { content: ""; position: absolute; bottom: -12px; left: 0; width: 60px; height: 3px; background: var(--primary); border-radius: 2px; }
        
        /* Grid */
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 40px; padding-bottom: 50px; }

        /* --- NEW MINIMALIST CARD STYLES (No Box) --- */
        .card {
            background: transparent !important; /* Remove white box */
            box-shadow: none !important;        /* Remove shadow */
            border: none !important;            /* Remove border */
            border-radius: 0 !important;
        }

        /* Style the image container */
        .card-img-container {
            border-radius: 16px; /* Round the image corners */
            overflow: hidden;
            position: relative;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); /* Subtle shadow on image only */
            border-bottom: none !important;
            transition: 0.3s;
        }
        
        .card:hover .card-img-container {
            transform: translateY(-5px);
            box-shadow: 0 15px 25px rgba(0,0,0,0.1);
        }

        /* Clean up the text area */
        .card-body {
            padding: 15px 0 !important; /* Remove side padding */
            background: transparent !important;
        }

        .card-footer {
            padding: 0 !important;
            background: transparent !important;
        }
    </style>
</head>
<body>

    <?php include 'includes/menu.php'; ?>

    <div class="hero" style="height: 150px; padding: 20px; display:flex; align-items:center; justify-content:center; animation: none; background: var(--dark);">
        <div style="text-align:center;">
            <h1>Browse Our Catalog</h1>
            <p style="color:#ccc;">Find exactly what you need.</p>
        </div>
    </div>

    <div class="container main-layout">
        
        <div class="sidebar">
            <div class="category-box">
                <h3><i class="fas fa-list"></i> Categories</h3>
                <ul class="category-list">
                    <li><a href="shop.php" class="<?php echo !isset($_GET['cat']) ? 'active' : ''; ?>">
                        <i class="fas fa-th-large"></i> All Products
                    </a></li>
                    <li><a href="shop.php?cat=Electronics" class="<?php echo (isset($_GET['cat']) && $_GET['cat']=='Electronics') ? 'active' : ''; ?>">
                        <i class="fas fa-mobile-alt"></i> Electronics
                    </a></li>
                    <li><a href="shop.php?cat=Fashion" class="<?php echo (isset($_GET['cat']) && $_GET['cat']=='Fashion') ? 'active' : ''; ?>">
                        <i class="fas fa-tshirt"></i> Fashion
                    </a></li>
                    <li><a href="shop.php?cat=Home" class="<?php echo (isset($_GET['cat']) && $_GET['cat']=='Home') ? 'active' : ''; ?>">
                        <i class="fas fa-couch"></i> Home & Living
                    </a></li>
                    <li><a href="shop.php?cat=Beauty" class="<?php echo (isset($_GET['cat']) && $_GET['cat']=='Beauty') ? 'active' : ''; ?>">
                        <i class="fas fa-spa"></i> Beauty
                    </a></li>
                    <li><a href="shop.php?cat=Toys" class="<?php echo (isset($_GET['cat']) && $_GET['cat']=='Toys') ? 'active' : ''; ?>">
                        <i class="fas fa-gamepad"></i> Toys
                    </a></li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            
            <div class="section-header">
                <?php
                if(isset($_GET['cat'])){
                    $cat_name = mysqli_real_escape_string($conn, $_GET['cat']);
                    echo "<h2 class='section-title'>Category: <span style='color:var(--primary)'>$cat_name</span></h2>";
                    $sql = "SELECT * FROM products WHERE category = '$cat_name' ORDER BY id DESC";
                } else {
                    echo "<h2 class='section-title'>🔥 All Products</h2>";
                    $sql = "SELECT * FROM products ORDER BY id DESC";
                }

                $result = mysqli_query($conn, $sql);
                $count = mysqli_num_rows($result);
                ?>
                <span style="color:#777; font-size:14px;"><?php echo $count; ?> Results</span>
            </div>
            
            <?php if($count > 0): ?>
                <div class="product-grid">
                    <?php while ($row = mysqli_fetch_array($result)) { ?>
                        
                        <div class="card">
                            <a href="product_details.php?id=<?php echo $row['id']; ?>" style="text-decoration:none; color:inherit;">
                                <div class="card-img-container" style="height:240px; background:white; display:flex; align-items:center; justify-content:center;">
                                    <img src="uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['title']; ?>" style="max-height:200px; max-width:90%;">
                                </div>
                                
                                <div class="card-body">
                                    <h3 class="card-title" style="font-size:16px; margin-bottom:5px;"><?php echo $row['title']; ?></h3>
                                    <span class="card-vendor" style="font-size:12px; color:#999;">
                                        <i class="fas fa-store"></i> <?php echo $row['vendor_name']; ?>
                                    </span>
                                    
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:10px;">
                                        <div class="card-price" style="font-size:18px; color:#333;">$<?php echo $row['price']; ?></div>
                                        
                                        <?php if(isset($row['stock']) && $row['stock'] > 0): ?>
                                            <small style="color:green; font-weight:600;">In Stock</small>
                                        <?php else: ?>
                                            <small style="color:red; font-weight:600;">Out of Stock</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>

                            <div class="card-footer">
                                <form method="POST" action="cart.php">
                                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="product_name" value="<?php echo $row['title']; ?>">
                                    <input type="hidden" name="product_price" value="<?php echo $row['price']; ?>">
                                    <input type="hidden" name="vendor_id" value="<?php echo $row['vendor_id']; ?>">
                                    
                                    <?php if(isset($row['stock']) && $row['stock'] > 0): ?>
                                        <button type="submit" name="add_to_cart" class="btn-add" style="border-radius: 8px;">
                                            Add to Cart
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn-add" style="background:#eee; color:#999; cursor:not-allowed; border-radius: 8px;">Unavailable</button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>

                    <?php } ?>
                </div>
            <?php else: ?>
                <div class="no-products">
                    <i class="fas fa-box-open" style="font-size: 50px; color: #ddd; margin-bottom: 20px;"></i>
                    <h3>No Products Found</h3>
                    <p>Try clearing filters or search again.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>