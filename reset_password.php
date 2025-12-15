<?php
include 'includes/db.php';

$msg = "";
$error = false;

// 1. Verify Token from URL
if (isset($_GET['token']) && isset($_GET['type'])) {
    $token = $_GET['token'];
    $type = $_GET['type'];
    $table = ($type == 'vendor') ? 'vendors' : 'users';
    
    // Check if token matches and is not expired
    $sql = "SELECT * FROM $table WHERE reset_token='$token' AND token_expire > NOW()";
    $res = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($res) == 0) {
        $error = true;
        $msg = "❌ This link is invalid or has expired.";
    }
} else {
    header("Location: index.php"); exit();
}

// 2. Handle New Password Submission
if (isset($_POST['change_password'])) {
    $pass = $_POST['new_pass'];
    
    // Update Password and Clear Token
    $update = "UPDATE $table SET password='$pass', reset_token=NULL, token_expire=NULL WHERE reset_token='$token'";
    mysqli_query($conn, $update);
    
    $redirect = ($type == 'vendor') ? 'vendor_login.php' : 'customer_login.php';
    echo "<script>alert('Password Changed Successfully!'); window.location.href='$redirect';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Set New Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background:#f0f2f5; display:flex; align-items:center; justify-content:center; height:100vh;">

    <div class="auth-container" style="background:white; max-width:400px; padding:30px; border-radius:12px; box-shadow:0 5px 15px rgba(0,0,0,0.1);">
        
        <?php if($error): ?>
            <div style="text-align:center;">
                <i class="fas fa-times-circle" style="font-size:50px; color:#e02424;"></i>
                <h3>Link Expired</h3>
                <p><?php echo $msg; ?></p>
                <a href="forgot_password.php" class="btn-primary">Try Again</a>
            </div>
        <?php else: ?>
            <div style="text-align:center; margin-bottom:20px;">
                <i class="fas fa-key" style="font-size:40px; color:#27ae60;"></i>
                <h2>New Password</h2>
                <p>Create a new secure password.</p>
            </div>

            <form method="POST">
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="new_pass" placeholder="New Password" required style="width:100%;">
                </div>
                <button type="submit" name="change_password" class="btn-add" style="width:100%;">Update Password</button>
            </form>
        <?php endif; ?>

    </div>

</body>
</html>