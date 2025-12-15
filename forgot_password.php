<?php
include 'includes/db.php';

// 1. DETECT ROLE FROM URL (Default to Customer if missing)
$role = isset($_GET['role']) ? $_GET['role'] : 'customer';

// Set variables based on role
if($role == 'vendor') {
    $table = 'vendors';
    $type_val = 'vendor';
    $page_title = "Vendor Password Reset";
    $back_link = "vendor_login.php";
    $bg_icon = "fa-store"; // Shop Icon
} else {
    $table = 'users';
    $type_val = 'user';
    $page_title = "Customer Password Reset";
    $back_link = "customer_login.php";
    $bg_icon = "fa-user"; // User Icon
}

$msg = "";

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Check Email in the SPECIFIC table only
    // Note: Ensure your vendors table has an 'email' column. 
    // If vendors login with username, change 'email' to 'username' in the query below.
    $check = mysqli_query($conn, "SELECT * FROM $table WHERE email='$email'");
    
    if (mysqli_num_rows($check) > 0) {
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));
        
        mysqli_query($conn, "UPDATE $table SET reset_token='$token', token_expire='$expiry' WHERE email='$email'");
        
        // Generate Link (Localhost Simulation)
        $reset_link = "http://localhost/market/reset_password.php?token=$token&type=$type_val";
        $msg = "✅ Link Generated! <a href='$reset_link' style='color:#007185; font-weight:bold;'>Click here to Reset Password</a>";
        
    } else {
        $msg = "❌ We could not find a $role account with that email.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $page_title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background:#f0f2f5; display:flex; align-items:center; justify-content:center; height:100vh;">

    <div class="auth-container" style="background:white; max-width:400px; padding:40px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.05); text-align:center;">
        
        <div style="margin-bottom:20px;">
            <div style="width:60px; height:60px; background:#fff3cd; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 15px auto;">
                <i class="fas <?php echo $bg_icon; ?>" style="font-size:30px; color:#febd69;"></i>
            </div>
            <h2 style="font-size:22px; color:#111; margin:0;"><?php echo $page_title; ?></h2>
            <p style="color:#777; font-size:13px; margin-top:5px;">Enter your email to verify your identity.</p>
        </div>

        <?php if($msg != ""): ?>
            <div style="background:#e8f5e9; color:#1e4620; padding:15px; border-radius:5px; margin-bottom:20px; font-size:13px; border:1px solid #c3e6cb; text-align:left;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="type" value="<?php echo $type_val; ?>">

            <div class="input-group" style="margin-bottom:20px;">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Enter your email address" required style="width:100%; padding:12px 12px 12px 40px; border:1px solid #ccc; border-radius:4px;">
            </div>

            <button type="submit" name="submit" class="btn-add" style="width:100%;">Send Reset Link</button>
        </form>

        <div style="margin-top:20px; font-size:13px;">
            <a href="<?php echo $back_link; ?>" style="color:#007185; text-decoration:none;">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
        </div>
    </div>

</body>
</html>