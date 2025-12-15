<?php 
include 'includes/db.php'; 
include 'includes/ai_engine.php';

$lang_code = isset($curr_lang) ? $curr_lang : 'en';
$direction = isset($dir) ? $dir : 'ltr';

// 1. DATA PRE-LOADING (Optimization)
$current_user = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : 0;

// Fetch Flash Deal
$deal_res = mysqli_query($conn, "SELECT * FROM flash_deal WHERE id=1 LIMIT 1");
$deal = mysqli_fetch_assoc($deal_res);

// Fetch AI Recs
$ai_products = get_recommendations($conn, $current_user);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>MarketPlace: Online Shopping for Electronics, Apparel, Computers, Books, DVDs & more</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="assets/js/script.js" defer></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Shop online for electronics, apparel, computers, books, DVDs & more">
</head>
<body>

    <?php include 'includes/menu.php'; ?>

    <div class="amz-hero">
        <button class="slider-btn prev" onclick="moveSlide(-1)">&#10094;</button>
        <div class="slider-track" id="sliderTrack">
            <img src="assets/img/slider1.jpg" class="hero-img">
            <img src="assets/img/slider2.png" class="hero-img">
            <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?auto=format&fit=crop&w=2000&q=80" class="hero-img">
        </div>
        <button class="slider-btn next" onclick="moveSlide(1)">&#10095;</button>
    </div>

    <div class="content-layer">
        
        <div class="card-grid">
            
            <div class="amz-card">
                <h2><?php echo isset($_SESSION['customer_id']) ? "Pick up where you left off" : "Top picks for you"; ?></h2>
                <div class="quad-grid">
                    <?php
                    // Ensure we have 4 items
                    $count = count($ai_products);
                    if($count < 4) {
                        // Fill gap with random
                        $fill = mysqli_query($conn, "SELECT * FROM products ORDER BY RAND() LIMIT " . (4-$count));
                        while($f = mysqli_fetch_assoc($fill)) { $ai_products[] = $f; }
                    }
                    // Loop 4 items
                    for($i=0; $i<4; $i++) { $p = $ai_products[$i]; 
                    ?>
                    <a href="product_details.php?id=<?php echo $p['id']; ?>" class="quad-item">
                        <img src="uploads/<?php echo $p['image']; ?>">
                        <span class="quad-lbl"><?php echo substr($p['title'], 0, 15); ?>...</span>
                    </a>
                    <?php } ?>
                </div>
                <a href="shop.php" class="cta-link">See more recommendations</a>
            </div>

            <div class="amz-card">
                <h2>Deal of the Day</h2>
                <?php if($deal && $deal['status'] == 1) { ?>
                    <a href="shop.php?cat=<?php echo $deal['category_link']; ?>" style="height:100%; display:flex; flex-direction:column;">
                        <img src="uploads/<?php echo $deal['image']; ?>" class="single-img" style="height: 240px; object-fit: contain;">
                        <div style="margin-top:10px;">
                            <span class="badge-deal">UP TO 50% OFF</span>
                            <span class="deal-txt">Ends in <span id="timer">...</span></span>
                        </div>
                        <p style="font-size:13px; color:#0F1111; margin:5px 0;"><?php echo $deal['title']; ?></p>
                    </a>
                    <script>
                        setInterval(()=>{
                            let diff = new Date("<?php echo $deal['end_date']; ?>") - new Date();
                            if(diff>0) document.getElementById('timer').innerText = new Date(diff).toISOString().substr(11, 8);
                        }, 1000);
                    </script>
                <?php } else { ?>
                    <img src="assets/img/slider2.jpg" class="single-img">
                    <p style="font-size:13px;">Browse today's deals</p>
                <?php } ?>
                <a href="shop.php" class="cta-link">See all deals</a>
            </div>

            <div class="amz-card">
                <h2>Electronics & Gadgets</h2>
                <div class="quad-grid">
                    <?php
                    $elec = mysqli_query($conn, "SELECT * FROM products WHERE category='Electronics' LIMIT 4");
                    while($e = mysqli_fetch_assoc($elec)) {
                    ?>
                    <a href="product_details.php?id=<?php echo $e['id']; ?>" class="quad-item">
                        <img src="uploads/<?php echo $e['image']; ?>">
                        <span class="quad-lbl">Tech</span>
                    </a>
                    <?php } ?>
                </div>
                <a href="shop.php?cat=Electronics" class="cta-link">See more</a>
            </div>

            <div class="amz-card">
                <?php if(!isset($_SESSION['customer_id'])) { ?>
                    <h2>Sign in for the best experience</h2>
                    <div style="flex-grow:1; display:flex; flex-direction:column; justify-content:center; align-items:center;">
                        <a href="customer_login.php" class="btn-yellow">Sign in securely</a>
                        <img src="https://m.media-amazon.com/images/G/01/sell/images/prime-boxes-2.png" style="width:100%; max-width:200px;">
                    </div>
                <?php } else { ?>
                    <h2>Track your Orders</h2>
                    <div style="flex-grow:1; display:flex; flex-direction:column; justify-content:center;">
                        <img src="https://m.media-amazon.com/images/G/01/US-hq/2022/img/Amazon_Exports/XCM_Manual_1469118_W_Export_Dashboard_Order_Tracking_379x304_1X._SY304_CB627684078_.jpg" class="single-img" style="height:200px; object-fit:contain;">
                        <p style="font-size:13px;">View recent orders and status.</p>
                    </div>
                    <a href="customer_orders.php" class="cta-link">Go to Orders</a>
                <?php } ?>
            </div>

        </div> <div class="strip-box">
            <div class="strip-header">
                <h3>⚡ Hot Deals from Vendors</h3>
                <a href="shop.php">See all deals</a>
            </div>
            <div class="h-slider">
                <?php
                $hot_q = mysqli_query($conn, "SELECT * FROM products WHERE discount_price > 0 ORDER BY RAND() LIMIT 10");
                while($h = mysqli_fetch_assoc($hot_q)){
                    $off = round((($h['price']-$h['discount_price'])/$h['price'])*100);
                ?>
                <a href="product_details.php?id=<?php echo $h['id']; ?>" class="prod-thumb">
                    <div style="background:#f7f7f7; padding:10px; margin-bottom:5px;">
                        <img src="uploads/<?php echo $h['image']; ?>">
                    </div>
                    <span class="badge-deal"><?php echo $off; ?>% off</span>
                    <span class="deal-txt">Deal</span>
                    <div class="price-row">
                        <span>$<?php echo floor($h['discount_price']); ?></span><sup><?php echo substr(($h['discount_price'] - floor($h['discount_price'])) * 100, 0, 2); ?></sup>
                    </div>
                    <div class="old-price">List: $<?php echo $h['price']; ?></div>
                    <div style="font-size:13px; color:#0F1111; height:36px; overflow:hidden; margin-top:5px;"><?php echo $h['title']; ?></div>
                </a>
                <?php } ?>
            </div>
        </div>

        <div class="strip-box">
            <div class="strip-header">
                <h3>Recommended For You</h3>
                <a href="shop.php">View more</a>
            </div>
            <div class="h-slider">
                <?php
                // Use AI logic or fallback
                $rec_q = mysqli_query($conn, "SELECT * FROM products ORDER BY RAND() LIMIT 15");
                while($r = mysqli_fetch_assoc($rec_q)){
                ?>
                <a href="product_details.php?id=<?php echo $r['id']; ?>" class="prod-thumb">
                    <img src="uploads/<?php echo $r['image']; ?>">
                    <div style="font-size:13px; color:#007185; margin-top:5px; height:36px; overflow:hidden;"><?php echo $r['title']; ?></div>
                    <div style="color:#b12704; font-size:12px; font-weight:700;">$<?php echo $r['price']; ?></div>
                </a>
                <?php } ?>
            </div>
        </div>

        <?php if(!isset($_SESSION['customer_id'])) { ?>
        <div class="signin-box">
            <p style="font-size:13px; margin-bottom:5px;">See personalized recommendations</p>
            <a href="customer_login.php" class="btn-yellow">Sign in</a>
            <p style="font-size:11px; margin-top:5px;">New customer? <a href="customer_register.php" style="color:#007185;">Start here.</a></p>
        </div>
        <?php } ?>

    </div> <?php include 'includes/footer.php'; ?>

    <script>
        // Hero Slider Logic
        let slideIdx = 0;
        function moveSlide(dir) {
            const track = document.getElementById('sliderTrack');
            const slides = document.querySelectorAll('.hero-img');
            slideIdx += dir;
            if(slideIdx >= slides.length) slideIdx = 0;
            if(slideIdx < 0) slideIdx = slides.length - 1;
            track.style.transform = `translateX(-${slideIdx * 100}%)`;
        }
        setInterval(() => moveSlide(1), 5000);
    </script>

</body>
</html>