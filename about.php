<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>About Us - MarketPlace</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="assets/js/script.js" defer></script>
    <style>
        /* About Page Specific Styles */
        .about-section {
            display: flex;
            align-items: center;
            gap: 40px;
            margin: 50px 0;
        }
        
        .about-text { flex: 1; }
        .about-text h2 { font-size: 2.5rem; color: var(--dark); margin-bottom: 20px; }
        .about-text p { font-size: 1.1rem; color: #555; line-height: 1.8; margin-bottom: 20px; }

        .about-image { 
            flex: 1; 
            height: 400px;
            background: url('assets/img/slider1.jpg'); /* Reusing your existing image */
            background-size: cover;
            background-position: center;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            position: relative;
        }

        /* Decorative Box behind image */
        .about-image::before {
            content: "";
            position: absolute;
            bottom: -20px; right: -20px;
            width: 100%; height: 100%;
            border: 5px solid var(--primary);
            border-radius: 20px;
            z-index: -1;
        }

        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .feature-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: 0.3s;
            border-top: 4px solid transparent;
        }

        .feature-box:hover {
            transform: translateY(-10px);
            border-top: 4px solid var(--primary);
        }

        .feature-icon {
            font-size: 40px;
            color: var(--primary);
            margin-bottom: 20px;
            background: rgba(254, 189, 105, 0.2);
            width: 80px; height: 80px;
            line-height: 80px;
            border-radius: 50%;
            display: inline-block;
        }

        /* Stats Section */
        .stats-bar {
            background: var(--dark);
            color: white;
            padding: 40px;
            border-radius: 12px;
            display: flex;
            justify-content: space-around;
            text-align: center;
            flex-wrap: wrap;
        }
        .stat-item h3 { font-size: 3rem; margin: 0; color: var(--primary); }
        .stat-item p { margin: 0; opacity: 0.8; font-size: 1.2rem; }

        @media(max-width: 768px) {
            .about-section { flex-direction: column; }
            .about-image { width: 100%; height: 300px; }
        }
    </style>
</head>
<body>

    <?php include 'includes/menu.php'; ?>

    <div class="hero" style="height: 250px; padding: 40px; display:flex; align-items:center; justify-content:center;">
        <div style="text-align:center;">
            <h1>Our Story</h1>
            <p>Empowering Commerce in the GCC and Beyond.</p>
        </div>
    </div>

    <div class="container" style="margin-top: -50px; position: relative; z-index: 10;">
        
        <div class="features-grid">
            <div class="feature-box">
                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                <h3>Secure Shopping</h3>
                <p>We use advanced encryption and strict vendor verification (Civil ID) to ensure your safety.</p>
            </div>
            <div class="feature-box">
                <div class="feature-icon"><i class="fas fa-shipping-fast"></i></div>
                <h3>Fast Delivery</h3>
                <p>Our integrated logistics network ensures your products arrive on time, every time.</p>
            </div>
            <div class="feature-box">
                <div class="feature-icon"><i class="fas fa-headset"></i></div>
                <h3>24/7 Support</h3>
                <p>Our dedicated support team is always available to help you with any questions.</p>
            </div>
        </div>

        <div class="about-section">
            <div class="about-text">
                <h2>Who We Are</h2>
                <p>
                    Founded with a mission to connect buyers and sellers across the region, <strong>MarketPlace</strong> is the premier multi-vendor platform for the GCC.
                </p>
                <p>
                    We believe in the power of small business. By providing local vendors with a world-class platform to sell their goods, we are building a thriving community of entrepreneurs and satisfied customers.
                </p>
                <a href="contact.php" class="btn-primary">Contact Us</a>
            </div>
            <div class="about-image"></div>
        </div>

        <div class="stats-bar">
            <div class="stat-item">
                <h3>10k+</h3>
                <p>Active Users</p>
            </div>
            <div class="stat-item">
                <h3>500+</h3>
                <p>Trusted Vendors</p>
            </div>
            <div class="stat-item">
                <h3>99%</h3>
                <p>Happy Customers</p>
            </div>
        </div>
        <br><br>

    </div>

    <?php include 'includes/footer.php'; ?>

</body>
</html>