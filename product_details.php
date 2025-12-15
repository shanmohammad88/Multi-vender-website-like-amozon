<?php 
include 'includes/db.php'; 

// 1. GET PRODUCT DETAILS FIRST (Moved to top)
if(isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT * FROM products WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) > 0){ 
        $product = mysqli_fetch_assoc($result); 
    } else { 
        // Product doesn't exist
        header("Location: index.php"); 
        exit(); 
    }
} else { 
    header("Location: index.php"); 
    exit(); 
}

// 2. AI TRACKER: LOG VIEW (+1 Point)
// Now it is safe to run this because $product exists!
$u_id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : 0;
$p_id = $product['id']; 
$p_cat = $product['category'];

// Insert Log
mysqli_query($conn, "INSERT INTO user_behavior (user_id, product_id, action_type, interest_score, category) 
                     VALUES ('$u_id', '$p_id', 'view', 1, '$p_cat')");


// 3. HANDLE REVIEW SUBMISSION
if(isset($_POST['submit_review'])) {
    $name = isset($_SESSION['customer_name']) ? $_SESSION['customer_name'] : "Guest User";
    $rating = $_POST['rating'];
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    
    mysqli_query($conn, "INSERT INTO reviews (product_id, customer_name, rating, comment) VALUES ('$id', '$name', '$rating', '$comment')");
    header("Location: product_details.php?id=$id&msg=Review Added");
    exit();
}

// 4. GET RATINGS DATA
$rev_sql = "SELECT * FROM reviews WHERE product_id = '$id' ORDER BY id DESC";
$rev_res = mysqli_query($conn, $rev_sql);
$rev_count = mysqli_num_rows($rev_res);

// Calculate Average Rating
$avg_sql = "SELECT AVG(rating) as avg_rating FROM reviews WHERE product_id = '$id'";
$avg_res = mysqli_fetch_assoc(mysqli_query($conn, $avg_sql));
$average_rating = round($avg_res['avg_rating'], 1);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $product['title']; ?> - MarketPlace</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="assets/js/script.js" defer></script>
    <style>
        /* PAGE STYLES */
        body { background-color: white !important; overflow-x: hidden; }
        .full-page-wrapper { display: flex; min-height: 100vh; width: 100%; }
        
        /* LEFT: VISUALS */
        .product-visuals { flex: 1.5; background-color: #f8f9fa; padding: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative; }
        .main-img { width: auto; max-width: 90%; height: 60vh; object-fit: contain; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.1)); transition: 0.3s; }
        .gallery-thumbs { margin-top: 30px; display: flex; gap: 15px; justify-content: center; }
        .thumb { width: 70px; height: 70px; border-radius: 8px; cursor: pointer; border: 2px solid transparent; object-fit: cover; background: white; transition: 0.2s; }
        .thumb:hover, .thumb.active { border-color: var(--primary); transform: translateY(-3px); }

        /* RIGHT: DETAILS */
        .product-details { flex: 1; padding: 60px 50px; background: white; display: flex; flex-direction: column; border-left: 1px solid #eee; overflow-y: auto; height: 100vh; }

        .back-link { position: absolute; top: 30px; left: 30px; z-index: 10; text-decoration: none; color: #333; font-weight: 600; background: white; padding: 10px 20px; border-radius: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: 0.3s; }
        .back-link:hover { background: var(--primary); }

        /* Typography & badges */
        .category-tag { color: #777; text-transform: uppercase; letter-spacing: 2px; font-size: 12px; font-weight: 700; margin-bottom: 10px; }
        h1 { font-size: 3rem; margin: 0 0 10px 0; color: #111; line-height: 1.1; }
        .vendor-row { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; color: #555; }
        
        .price-area { display: flex; align-items: center; gap: 20px; margin-bottom: 30px; }
        .big-price { font-size: 2.5rem; font-weight: 800; color: #111; }
        .stock-tag { padding: 5px 15px; border-radius: 50px; font-weight: 600; font-size: 14px; }
        .in-stock { background: #e8f5e9; color: #2e7d32; }
        .out-stock { background: #ffebee; color: #c62828; }

        /* NEW: WISHLIST BUTTON STYLE */
        .wishlist-btn {
            width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            background: #ffebee; color: #e02424; text-decoration: none; font-size: 20px; transition: 0.3s;
            border: 1px solid #ffcdd2;
        }
        .wishlist-btn:hover { background: #e02424; color: white; transform: scale(1.1); }

        /* STAR RATING STYLE */
        .rating-stars { color: #ffc107; font-size: 18px; margin-bottom: 20px; }
        .rating-stars span { color: #333; font-size: 14px; font-weight: normal; margin-left: 5px; }

        /* REVIEWS SECTION */
        .reviews-section { margin-top: 40px; border-top: 1px solid #eee; padding-top: 30px; }
        .review-card { background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 15px; }
        .review-header { display: flex; justify-content: space-between; margin-bottom: 5px; font-weight: bold; font-size: 14px; }
        .review-stars { color: #ffc107; font-size: 12px; }
        
        /* Review Form */
        .review-form { background: #fff; border: 1px solid #eee; padding: 20px; border-radius: 12px; margin-bottom: 30px; }
        .star-input { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 5px; }
        .star-input input { display: none; }
        .star-input label { font-size: 24px; color: #ddd; cursor: pointer; transition: 0.2s; }
        .star-input input:checked ~ label { color: #ffc107; }
        .star-input label:hover, .star-input label:hover ~ label { color: #ffc107; }

        .btn-buy { width: 100%; padding: 18px; font-size: 16px; font-weight: 700; background: var(--dark); color: white; border: none; cursor: pointer; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; margin-top: 20px; }
        .btn-buy:hover { background: var(--primary); color: black; }

        @media (max-width: 900px) { .full-page-wrapper { flex-direction: column; } .product-details { height: auto; border-left: none; } .main-img { height: 300px; } }
    </style>
</head>
<body>

    <?php include 'includes/menu.php'; ?>

    <div class="full-page-wrapper">
        <a href="shop.php" class="back-link">← Back</a>

        <div class="product-visuals">
            <img id="mainImage" src="uploads/<?php echo $product['image']; ?>" class="main-img">
            <div class="gallery-thumbs">
                <img src="uploads/<?php echo $product['image']; ?>" class="thumb active" onclick="changeImage(this)">
                <?php
                $pid = $product['id'];
                $g_res = mysqli_query($conn, "SELECT * FROM product_gallery WHERE product_id='$pid'");
                while($g_row = mysqli_fetch_assoc($g_res)){
                    if($g_row['image_name'] != $product['image']){
                        echo "<img src='uploads/".$g_row['image_name']."' class='thumb' onclick='changeImage(this)'>";
                    }
                }
                ?>
            </div>
        </div>

        <div class="product-details">
            <div class="category-tag"><?php echo $product['category']; ?></div>
            <h1><?php echo $product['title']; ?></h1>
            
            <div class="rating-stars">
                <?php 
                    for($i=1; $i<=5; $i++) {
                        if($i <= $average_rating) echo '<i class="fas fa-star"></i>';
                        elseif($i - 0.5 <= $average_rating) echo '<i class="fas fa-star-half-alt"></i>';
                        else echo '<i class="far fa-star" style="color:#ddd;"></i>';
                    }
                ?>
                <span>(<?php echo $rev_count; ?> Reviews)</span>
            </div>

            <div class="vendor-row"><i class="fas fa-store"></i> Sold by: <strong><?php echo $product['vendor_name']; ?></strong></div>

            <div class="price-area">
                <div class="big-price">$<?php echo $product['price']; ?></div>
                
                <a href="wishlist.php?add=<?php echo $product['id']; ?>" class="wishlist-btn" title="Add to Wishlist">
                    <i class="far fa-heart"></i>
                </a>

                <?php if(isset($product['stock']) && $product['stock'] > 0): ?>
                    <span class="stock-tag in-stock">IN STOCK</span>
                <?php else: ?>
                    <span class="stock-tag out-stock">SOLD OUT</span>
                <?php endif; ?>
            </div>

            <div style="font-size: 1rem; line-height: 1.8; color: #444; margin-bottom: 20px;">
                <?php echo nl2br($product['description']); ?>
            </div>

            <form method="POST" action="cart.php">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="product_name" value="<?php echo $product['title']; ?>">
                <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
                <input type="hidden" name="vendor_id" value="<?php echo $product['vendor_id']; ?>">
                
                <?php if(isset($product['stock']) && $product['stock'] > 0): ?>
                    <button type="submit" name="add_to_cart" class="btn-buy">Add to Cart</button>
                <?php else: ?>
                    <button type="button" class="btn-buy" style="background:#eee; color:#999; cursor:not-allowed;">Unavailable</button>
                <?php endif; ?>
            </form>

            <div class="reviews-section">
                <h3 style="margin-bottom: 20px;">Customer Reviews</h3>

                <div class="review-form">
                    <h4 style="margin: 0 0 10px 0;">Write a Review</h4>
                    <form method="POST">
                        <div class="star-input" style="justify-content: flex-end; margin-bottom: 10px;">
                            <input type="radio" name="rating" id="star5" value="5" required><label for="star5" title="5 stars"><i class="fas fa-star"></i></label>
                            <input type="radio" name="rating" id="star4" value="4"><label for="star4" title="4 stars"><i class="fas fa-star"></i></label>
                            <input type="radio" name="rating" id="star3" value="3"><label for="star3" title="3 stars"><i class="fas fa-star"></i></label>
                            <input type="radio" name="rating" id="star2" value="2"><label for="star2" title="2 stars"><i class="fas fa-star"></i></label>
                            <input type="radio" name="rating" id="star1" value="1"><label for="star1" title="1 star"><i class="fas fa-star"></i></label>
                        </div>
                        <textarea name="comment" rows="3" placeholder="How was the product?" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-family:inherit;"></textarea>
                        <button type="submit" name="submit_review" class="btn-add" style="margin-top:10px; padding: 10px 20px; width: auto;">Post Review</button>
                    </form>
                </div>

                <?php 
                if($rev_count > 0) {
                    mysqli_data_seek($rev_res, 0);
                    while($rev = mysqli_fetch_assoc($rev_res)) {
                ?>
                    <div class="review-card">
                        <div class="review-header">
                            <span><?php echo $rev['customer_name']; ?></span>
                            <span style="color:#999; font-weight:normal; font-size:12px;"><?php echo date('M d, Y', strtotime($rev['created_at'])); ?></span>
                        </div>
                        <div class="review-stars">
                            <?php for($i=0; $i<$rev['rating']; $i++) echo '<i class="fas fa-star"></i>'; ?>
                        </div>
                        <p style="margin: 5px 0 0 0; color:#555; font-size:14px;"><?php echo $rev['comment']; ?></p>
                    </div>
                <?php 
                    }
                } else {
                    echo "<p style='color:#777; font-style:italic;'>No reviews yet. Be the first to review!</p>";
                }
                ?>
            </div>

        </div>
    </div>
    
    <script>
        function changeImage(element) {
            document.getElementById('mainImage').src = element.src;
            document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
            element.classList.add('active');
        }
        
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.get('msg')) { showToast(urlParams.get('msg'), 'success'); }
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>