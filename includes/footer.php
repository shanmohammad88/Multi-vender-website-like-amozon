<style>
    /* Footer Design */
    .site-footer {
        background-color: #131921; /* Dark Amazon Blue */
        color: white;
        padding: 50px 0 20px 0;
        font-family: 'Poppins', sans-serif;
        margin-top: 50px;
    }

    .footer-container {
        max-width: 1200px;
        margin: auto;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
    }

    .footer-section {
        flex: 1;
        min-width: 200px;
    }

    .footer-section h3 {
        color: #febd69; /* Accent Yellow */
        font-size: 18px;
        margin-bottom: 20px;
        border-bottom: 2px solid #febd69;
        display: inline-block;
        padding-bottom: 5px;
    }

    .footer-section p {
        font-size: 14px;
        color: #ccc;
        line-height: 1.6;
    }

    .footer-section ul {
        list-style: none;
        padding: 0;
    }

    .footer-section ul li {
        margin-bottom: 10px;
    }

    .footer-section a {
        color: #ddd;
        text-decoration: none;
        font-size: 14px;
        transition: 0.3s;
    }

    .footer-section a:hover {
        color: #febd69;
        padding-left: 5px; /* Slight movement effect */
    }

    .copyright-bar {
        text-align: center;
        margin-top: 40px;
        padding-top: 20px;
        border-top: 1px solid #3a4553;
        font-size: 13px;
        color: #777;
    }
</style>

<footer class="site-footer">
    <div class="footer-container">
        
        <div class="footer-section">
            <h3>About MarketPlace</h3>
            <p>
                We are the world's leading multi-vendor platform. 
                Our mission is to connect buyers and sellers in a secure 
                and professional environment.
            </p>
        </div>

        <div class="footer-section">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="index.php">🏠 Home Page</a></li>
                <li><a href="cart.php">🛒 Shopping Cart</a></li>
                <li><a href="vendor_login.php">💼 Vendor Login</a></li>
                
            </ul>
        </div>

        <div class="footer-section">
            <h3>Contact Us</h3>
            <ul style="color: #ccc; font-size: 14px;">
                <li>📍 Shuwaikh Industrial, Kuwait</li>
                <li>📧 support@market.com</li>
                <li>📞 +965 1234 5678</li>
            </ul>
        </div>

    </div>

    <div class="copyright-bar">
        &copy; <?php echo date("Y"); ?> MarketPlace. All Rights Reserved. | Designed for Success
    </div>
</footer>