<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Contact Us - MarketPlace</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="assets/js/script.js" defer></script>
    
    <style>
        /* Contact Page Specific Styles */
        .contact-wrapper {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-wrap: wrap;
        }

        .contact-info {
            background: var(--dark);
            color: white;
            flex: 1;
            padding: 40px;
            min-width: 300px;
            position: relative;
            overflow: hidden;
        }
        
        /* Decorative Circle in background */
        .contact-info::before {
            content: "";
            position: absolute;
            top: -50px; right: -50px;
            width: 150px; height: 150px;
            background: var(--primary);
            border-radius: 50%;
            opacity: 0.2;
        }

        .contact-info h3 { color: var(--primary); font-size: 24px; margin-bottom: 20px; }
        .contact-info p { opacity: 0.8; margin-bottom: 30px; line-height: 1.6; }
        
        .info-item { display: flex; align-items: center; margin-bottom: 20px; font-size: 15px; }
        .info-item i { 
            width: 40px; height: 40px; 
            background: rgba(255,255,255,0.1); 
            border-radius: 50%; 
            display: flex; justify-content: center; align-items: center;
            color: var(--primary);
            margin-right: 15px;
        }

        .contact-form {
            flex: 1.5;
            padding: 40px;
            min-width: 300px;
        }

        /* Input Group with Icons */
        .input-group { position: relative; margin-bottom: 20px; }
        .input-group i {
            position: absolute;
            left: 15px;
            top: 15px;
            color: #aaa;
        }
        .input-group input, .input-group textarea {
            width: 100%;
            padding: 12px 12px 12px 45px; /* Space for icon */
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            background: #f9f9f9;
        }
        .input-group input:focus, .input-group textarea:focus {
            background: white;
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 5px rgba(254, 189, 105, 0.4);
        }
    </style>
</head>
<body>

    <?php include 'includes/menu.php'; ?>

    <div class="hero" style="height: 250px; padding: 40px; display:flex; align-items:center; justify-content:center;">
        <div style="text-align:center;">
            <h1>Contact Support</h1>
            <p>We are here to help you 24/7. Reach out to us anytime.</p>
        </div>
    </div>

    <div class="container" style="margin-top: -80px; position: relative; z-index: 10;">
        
        <div class="contact-wrapper">
            
            <div class="contact-info">
                <h3>Get in Touch</h3>
                <p>Have questions about selling your products or tracking an order? Our team is ready to assist you.</p>
                
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Shuwaikh Industrial, Kuwait</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <span>+965 1234 5678</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <span>support@market.com</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <span>Mon - Fri, 9:00 AM - 6:00 PM</span>
                </div>
            </div>

            <div class="contact-form">
                <h2 style="color: #333; margin-bottom: 20px;">Send us a Message</h2>
                
                <form onsubmit="event.preventDefault(); sendMessage();">
                    
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" placeholder="Your Full Name" required>
                    </div>

                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" placeholder="Email Address" required>
                    </div>
                    
                    <div class="input-group">
                        <i class="fas fa-tag"></i>
                        <input type="text" placeholder="Subject (Optional)">
                    </div>

                    <div class="input-group">
                        <i class="fas fa-comment-alt"></i>
                        <textarea rows="5" placeholder="How can we help you?" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn-add" style="width: auto; padding-left: 30px; padding-right: 30px;">
                        Send Message <i class="fas fa-paper-plane" style="margin-left:5px;"></i>
                    </button>
                </form>
            </div>

        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        function sendMessage() {
            // Using your advanced script.js function
            showToast("Message Sent! We will contact you shortly.", "success");
            
            // Optional: Clear form
            document.querySelector("form").reset();
        }
    </script>

</body>
</html>