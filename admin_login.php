<?php
include 'includes/db.php';

if (isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    
    $result = mysqli_query($conn, "SELECT * FROM admins WHERE username='$user' AND password='$pass'");
    if (mysqli_num_rows($result) == 1) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Access Denied: Wrong Admin Credentials');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Super Admin Login</title></head>
<body style="background:#222; color:white; font-family:sans-serif; display:flex; justify-content:center; align-items:center; height:100vh;">

    <div style="background:#333; padding:40px; border-radius:10px; text-align:center;">
        <h2 style="color:red;">SUPER ADMIN ACCESS</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Admin Username" required style="padding:10px; margin:10px; width:200px;"><br>
            <input type="password" name="password" placeholder="Password" required style="padding:10px; margin:10px; width:200px;"><br>
            <button type="submit" name="login" style="padding:10px 20px; background:red; color:white; border:none; cursor:pointer; font-weight:bold;">ENTER SYSTEM</button>
        </form>
        <br>
        <a href="index.php" style="color:gray;">Back to Website</a>
    </div>

</body>
</html>