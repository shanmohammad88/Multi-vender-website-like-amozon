<?php
include 'includes/db.php';

// LOGOUT
if(isset($_GET['logout'])) { session_destroy(); header("Location: vendor_login.php"); exit(); }

// REGISTER LOGIC (Updated with Civil ID & Details)
if (isset($_POST['register'])) {
    $user     = $_POST['username'];      // Login Username
    $pass     = $_POST['password'];      // Login Password
    $owner    = $_POST['owner_name'];    // Real Name
    $store    = $_POST['username'];      // We use username as Store Name for simplicity
    $civil_id = $_POST['civil_id'];      // Civil ID
    $phone    = $_POST['phone'];         // Phone Number
    $address  = $_POST['address'];       // Address

    // Check if username taken
    $check = mysqli_query($conn, "SELECT * FROM vendors WHERE username='$user'");
    if (mysqli_num_rows($check) > 0) { 
        echo "<script>alert('Store Name/Username already taken!');</script>"; 
    } else {
        // Insert all details
        $sql = "INSERT INTO vendors (username, password, owner_name, civil_id, phone, address) 
                VALUES ('$user', '$pass', '$owner', '$civil_id', '$phone', '$address')";
        
        if(mysqli_query($conn, $sql)) {
            echo "<script>alert('Registration Successful! Please Login.');</script>";
        } else {
            echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
        }
    }
}

// LOGIN LOGIC
if (isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $result = mysqli_query($conn, "SELECT * FROM vendors WHERE username='$user' AND password='$pass'");
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['vendor_id'] = $row['id'];
        $_SESSION['vendor_name'] = $row['username'];
        header("Location: vendor_dashboard.php");
        exit();
    } else { echo "<script>alert('Wrong Login Details');</script>"; }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vendor Portal - MarketPlace</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* PAGE STYLES */
        body { background-color: #f0f2f5; height: 100vh; overflow: hidden; margin: 0; font-family: 'Poppins', sans-serif; }
        .split-screen { display: flex; height: 100vh; width: 100vw; }
        
        .left-panel {
            flex: 1;
            /* UPDATED IMAGE: Professional Workspace */
            background: linear-gradient(rgba(19, 25, 33, 0.85), rgba(19, 25, 33, 0.85)), url('https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1920&q=80');
            background-size: cover; background-position: center;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            color: white; padding: 40px; text-align: center;
        }
        .left-panel h1 { font-size: 3rem; margin-bottom: 10px; color: white; font-weight: 700; }
        .left-panel p { font-size: 1.1rem; opacity: 0.8; max-width: 400px; line-height: 1.6; }

        .right-panel {
            flex: 1; background: white; display: flex; justify-content: center; align-items: center;
            overflow-y: auto; /* Allow scrolling if form is long */
        }

        .auth-container { width: 100%; max-width: 450px; padding: 40px; }

        /* TOGGLE BUTTONS */
        .toggle-box { display: flex; background: #eee; border-radius: 30px; margin-bottom: 30px; position: relative; overflow: hidden; }
        .toggle-btn { flex: 1; padding: 12px; text-align: center; cursor: pointer; font-weight: 600; z-index: 1; transition: color 0.3s; }
        #btn-slider { position: absolute; top: 0; left: 0; height: 100%; width: 50%; background: linear-gradient(45deg, var(--primary), #f0a025); border-radius: 30px; transition: 0.3s; }

        .input-group { position: relative; margin-bottom: 15px; }
        .input-group i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #aaa; z-index: 2; }
        .input-group input { padding-left: 45px !important; margin: 0; width: 100%; box-sizing: border-box; }

        .auth-form { display: none; animation: fadeUp 0.5s ease; }
        .auth-form.active { display: block; }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 768px) { .split-screen { flex-direction: column; height: auto; overflow: auto; } .left-panel { padding: 60px 20px; min-height: 300px; } }
    </style>
</head>
<body>

    <div class="split-screen">
        <div class="left-panel">
            <h1>Grow Your Business</h1>
            <p>Join thousands of vendors. Register with your Civil ID and Store Details to start selling securely today.</p>
            <br>
            <a href="index.php" class="btn" style="border:1px solid rgba(255,255,255,0.3); color:white; padding: 10px 20px; border-radius: 5px; text-decoration: none; transition: 0.3s;">&larr; Back to Shop</a>
        </div>

        <div class="right-panel">
            <div class="auth-container">
                <h2 style="text-align: center; margin-bottom: 20px; color: #333;">Vendor Portal</h2>

                <div class="toggle-box">
                    <div id="btn-slider"></div>
                    <div class="toggle-btn" onclick="showLogin()">Login</div>
                    <div class="toggle-btn" onclick="showRegister()">Register</div>
                </div>

                <form id="loginForm" class="auth-form active" method="POST">
                    <div class="input-group"><i class="fas fa-store"></i><input type="text" name="username" placeholder="Store Name / Username" required></div>
                    <div class="input-group"><i class="fas fa-lock"></i><input type="password" name="password" placeholder="Password" required></div>
                    <div style="text-align:right; margin-bottom:15px;">
    <a href="forgot_password.php?role=vendor" style="color:#007185; font-size:12px;">Forgot Password?</a>
</div>
                    <button type="submit" name="login" class="btn-add" style="width: 100%;">Access Dashboard</button>
                </form>

                <form id="registerForm" class="auth-form" method="POST">
                    <p style="font-size: 12px; color: #777; margin-bottom: 10px; font-weight: 600;">STORE CREDENTIALS</p>
                    <div class="input-group"><i class="fas fa-store"></i><input type="text" name="username" placeholder="Store Name (Login Username)" required></div>
                    <div class="input-group"><i class="fas fa-key"></i><input type="password" name="password" placeholder="Create Password" required></div>
                    
                    <p style="font-size: 12px; color: #777; margin-bottom: 10px; margin-top: 15px; font-weight: 600;">LEGAL DETAILS</p>
                    <div class="input-group"><i class="fas fa-user"></i><input type="text" name="owner_name" placeholder="Full Owner Name" required></div>
                    <div class="input-group"><i class="fas fa-id-card"></i><input type="text" name="civil_id" placeholder="Civil ID Number" required></div>
                    <div class="input-group"><i class="fas fa-phone"></i><input type="text" name="phone" placeholder="Phone Number" required></div>
                    <div class="input-group"><i class="fas fa-map-marker-alt"></i><input type="text" name="address" placeholder="Store Address" required></div>

                    <button type="submit" name="register" class="btn-primary" style="width: 100%;">Submit Registration</button>
                </form>

            </div>
        </div>
    </div>

    <script>
        var x = document.getElementById("loginForm");
        var y = document.getElementById("registerForm");
        var z = document.getElementById("btn-slider");
        function showRegister() { x.classList.remove("active"); y.classList.add("active"); z.style.left = "50%"; }
        function showLogin() { y.classList.remove("active"); x.classList.add("active"); z.style.left = "0"; }
    </script>
</body>
</html>