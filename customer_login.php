<?php
include 'includes/db.php';

// HANDLE LOGOUT
if(isset($_GET['logout'])) {
    unset($_SESSION['customer_id']);
    unset($_SESSION['customer_name']);
    header("Location: customer_login.php");
    exit();
}

// HANDLE REGISTER
if (isset($_POST['register'])) {
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $addr = $_POST['address'];

    $check = mysqli_query($conn, "SELECT * FROM customers WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        // Use URL parameter for Toast Notification
        header("Location: customer_login.php?error=Email already registered");
    } else {
        $sql = "INSERT INTO customers (full_name, email, password, address) VALUES ('$name', '$email', '$pass', '$addr')";
        if(mysqli_query($conn, $sql)) {
            header("Location: customer_login.php?msg=Account created! Please Login");
        }
    }
}

// HANDLE LOGIN
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM customers WHERE email='$email' AND password='$pass'");
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['customer_id'] = $row['id'];
        $_SESSION['customer_name'] = $row['full_name'];
        header("Location: index.php?msg=Welcome back " . $row['full_name']); // Redirect to Home
        exit();
    } else {
        header("Location: customer_login.php?error=Wrong Email or Password");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Login - MarketPlace</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="assets/js/script.js" defer></script>
    
    <style>
        /* PAGE SPECIFIC STYLES (Split Screen) */
        body { background-color: #f0f2f5; height: 100vh; overflow: hidden; margin: 0; }

        .split-screen {
            display: flex;
            height: 100vh;
            width: 100vw;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        /* LEFT SIDE: SHOPPING VISUALS */
        .left-panel {
            flex: 1;
            /* Using the Shopping Image we generated earlier */
            background: linear-gradient(rgba(30, 60, 114, 0.8), rgba(42, 82, 152, 0.8)), url('assets/img/slider1.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 40px;
            text-align: center;
        }

        .left-panel h1 { font-size: 3.5rem; margin-bottom: 10px; color: white; text-shadow: 0 2px 10px rgba(0,0,0,0.3); }
        .left-panel p { font-size: 1.2rem; opacity: 0.9; max-width: 400px; line-height: 1.6; }
        .glass-btn {
            margin-top: 20px;
            padding: 10px 25px;
            border: 1px solid rgba(255,255,255,0.5);
            background: rgba(255,255,255,0.1);
            color: white;
            border-radius: 30px;
            backdrop-filter: blur(5px);
            transition: 0.3s;
            text-decoration: none;
        }
        .glass-btn:hover { background: white; color: #1e3c72; }

        /* RIGHT SIDE: FORMS */
        .right-panel {
            flex: 1;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .auth-container {
            width: 100%;
            max-width: 400px;
            padding: 40px;
        }

        /* FORM TOGGLE BUTTONS */
        .toggle-box {
            display: flex;
            background: #f1f1f1;
            border-radius: 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .toggle-btn {
            flex: 1;
            padding: 12px;
            text-align: center;
            cursor: pointer;
            font-weight: 600;
            z-index: 1;
            transition: color 0.3s;
            color: #555;
        }
        
        /* The Moving Slider Background (Blue for Customers) */
        #btn-slider {
            position: absolute;
            top: 0; left: 0;
            height: 100%; width: 50%;
            background: linear-gradient(45deg, #1e3c72, #2a5298);
            border-radius: 30px;
            transition: 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }
        
        .toggle-btn.active-text { color: white; }

        /* Form Styling */
        .input-group { position: relative; margin-bottom: 20px; }
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #1e3c72; /* Blue Icon */
            z-index: 2;
        }
        .input-group input { 
            padding-left: 45px !important; 
            margin: 0; 
            width: 100%;
            background: #f9f9f9;
            border: 1px solid #eee;
        }
        .input-group input:focus { background: white; border-color: #1e3c72; }

        /* Animations */
        .auth-form { display: none; animation: fadeUp 0.5s ease; }
        .auth-form.active { display: block; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .split-screen { flex-direction: column; height: auto; overflow-y: auto; }
            .left-panel { padding: 60px 20px; min-height: 300px; }
            .right-panel { padding: 40px 20px; }
        }
    </style>
</head>
<body>

    <div class="split-screen">
        
        <div class="left-panel">
            <h1>Shop Your World</h1>
            <p>Join millions of customers. Discover the best products at the best prices with fast delivery.</p>
            <a href="index.php" class="glass-btn">&larr; Continue as Guest</a>
        </div>

        <div class="right-panel">
            <div class="auth-container">
                
                <h2 style="text-align: center; margin-bottom: 10px; color: #1e3c72;">Welcome Back!</h2>
                <p style="text-align: center; color: #777; margin-bottom: 30px; font-size: 14px;">Please login to your account</p>

                <div class="toggle-box">
                    <div id="btn-slider"></div>
                    <div class="toggle-btn" id="loginBtn" onclick="showLogin()" style="color:white;">Login</div>
                    <div class="toggle-btn" id="regBtn" onclick="showRegister()">Sign Up</div>
                </div>

                <form id="loginForm" class="auth-form active" method="POST">
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="text" name="email" placeholder="Email Address" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <div style="text-align:right; margin-bottom:15px;">
    <a href="forgot_password.php?role=customer" style="color:#007185; font-size:12px;">Forgot Password?</a>
</div>
                    
                    <button type="submit" name="login" class="btn-add" style="background: linear-gradient(45deg, #1e3c72, #2a5298); color: white;">
                        Login Securely
                    </button>
                    
                    <div style="text-align: center; margin-top: 20px;">
                        <a href="#" style="font-size: 13px; color: #777;">Forgot Password?</a>
                    </div>
                </form>

                <form id="registerForm" class="auth-form" method="POST">
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" name="full_name" placeholder="Full Name" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="text" name="email" placeholder="Email Address" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-map-marker-alt"></i>
                        <input type="text" name="address" placeholder="Shipping Address" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Create Password" required>
                    </div>
                    
                    <button type="submit" name="register" class="btn-add" style="background: linear-gradient(45deg, #1e3c72, #2a5298); color: white;">
                        Create Account
                    </button>
                    
                    <div style="text-align: center; margin-top: 20px;">
                        <p style="font-size: 12px; color: #777;">By signing up, you agree to our Terms.</p>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <script>
        // JAVASCRIPT FOR TOGGLE ANIMATION
        var x = document.getElementById("loginForm");
        var y = document.getElementById("registerForm");
        var z = document.getElementById("btn-slider");
        var lBtn = document.getElementById("loginBtn");
        var rBtn = document.getElementById("regBtn");

        function showRegister() {
            x.classList.remove("active");
            y.classList.add("active");
            z.style.left = "50%";
            lBtn.style.color = "#555";
            rBtn.style.color = "white";
        }

        function showLogin() {
            y.classList.remove("active");
            x.classList.add("active");
            z.style.left = "0";
            lBtn.style.color = "white";
            rBtn.style.color = "#555";
        }
    </script>

</body>
</html>