<?php 
include 'includes/db.php'; 

// SECURITY: Kick out if not Admin
if (!isset($_SESSION['admin_logged_in'])) { header("Location: index.php"); exit(); }

// --- 1. FLASH DEAL UPDATE LOGIC ---
if(isset($_POST['update_deal'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $date = $_POST['end_date']; 
    $link = $_POST['category_link'];
    $status = $_POST['status'];
    
    $img_sql = "";
    if(!empty($_FILES['deal_image']['name'])){
        $image = $_FILES['deal_image']['name'];
        move_uploaded_file($_FILES['deal_image']['tmp_name'], "uploads/" . $image);
        $img_sql = ", image='$image'";
    }

    $sql = "UPDATE flash_deal SET title='$title', description='$desc', end_date='$date', category_link='$link', status='$status' $img_sql WHERE id=1";
    mysqli_query($conn, $sql);
    header("Location: " . $_SERVER['PHP_SELF'] . "?msg=Flash Deal Updated");
    exit();
}

// --- 2. CATEGORY MANAGER LOGIC ---
if(isset($_POST['add_category'])) {
    $cat_name = mysqli_real_escape_string($conn, $_POST['cat_name']);
    if(!empty($cat_name)){
        $check = mysqli_query($conn, "SELECT * FROM categories WHERE name='$cat_name'");
        if(mysqli_num_rows($check) == 0){
            mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$cat_name')");
            header("Location: " . $_SERVER['PHP_SELF'] . "?msg=Category Added"); exit();
        } else { echo "<script>alert('Category already exists!');</script>"; }
    }
}

if(isset($_GET['delete_cat'])) {
    $id = $_GET['delete_cat'];
    mysqli_query($conn, "DELETE FROM categories WHERE id='$id'");
    header("Location: " . $_SERVER['PHP_SELF'] . "?msg=Category Deleted"); exit();
}

// --- 3. DELETE LOGIC ---
if (isset($_GET['delete_vendor'])) {
    $id = $_GET['delete_vendor'];
    mysqli_query($conn, "DELETE FROM vendors WHERE id='$id'");
    mysqli_query($conn, "DELETE FROM products WHERE vendor_id='$id'");
    header("Location: " . $_SERVER['PHP_SELF'] . "?msg=Vendor Banned"); exit();
}

if (isset($_GET['delete_product'])) {
    $id = $_GET['delete_product'];
    mysqli_query($conn, "DELETE FROM products WHERE id='$id'");
    header("Location: " . $_SERVER['PHP_SELF'] . "?msg=Product Removed"); exit();
}

// FETCH DATA
$deal_res = mysqli_query($conn, "SELECT * FROM flash_deal WHERE id=1");
$deal = mysqli_fetch_assoc($deal_res);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Super Admin - Command Center</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="assets/js/script.js" defer></script>
    
    <style>
        /* --- PROFESSIONAL ADMIN CSS --- */
        :root { --sidebar-bg: #1a1c23; --bg-color: #f4f6f8; --card-bg: #ffffff; --primary: #7e3af2; --dark-text: #333; }
        body { font-family: 'Outfit', sans-serif; background: var(--bg-color); margin: 0; display: flex; color: var(--dark-text); }
        
        /* SIDEBAR */
        .sidebar { width: 260px; background: var(--sidebar-bg); height: 100vh; position: fixed; color: white; padding: 30px 20px; z-index: 100; }
        .brand { font-size: 22px; font-weight: bold; color: white; margin-bottom: 40px; display: flex; align-items: center; gap: 10px; letter-spacing: 1px; }
        .brand span { color: var(--primary); }
        .menu-item { display: flex; align-items: center; gap: 15px; padding: 12px 15px; color: #a0aec0; text-decoration: none; border-radius: 8px; margin-bottom: 10px; transition: 0.3s; font-size: 15px; }
        .menu-item:hover, .menu-item.active { background: var(--primary); color: white; transform: translateX(5px); }

        /* MAIN CONTENT */
        .main-content { margin-left: 260px; padding: 30px; flex: 1; width: calc(100% - 260px); }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 26px; }
        .header p { color: #777; margin: 5px 0 0; font-size: 14px; }
        .admin-badge { background: #e9d8fd; color: #553c9a; padding: 5px 15px; border-radius: 20px; font-weight: 600; font-size: 13px; }

        /* CARDS & CONTAINERS */
        .dashboard-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 25px; margin-bottom: 30px; }
        
        .card { background: var(--card-bg); padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; }
        .card-header h3 { margin: 0; font-size: 18px; color: #1a202c; display: flex; align-items: center; gap: 10px; }

        /* STATS ROW */
        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-box { background: white; padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.03); }
        .stat-icon { width: 50px; height: 50px; border-radius: 10px; display: flex; justify-content: center; align-items: center; font-size: 20px; }
        .stat-txt h4 { margin: 0; font-size: 24px; color: #2d3748; }
        .stat-txt span { font-size: 13px; color: #718096; }

        /* FORMS */
        label { display: block; font-size: 12px; color: #718096; margin-bottom: 5px; font-weight: 600; text-transform: uppercase; }
        input, select { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #e2e8f0; border-radius: 6px; font-family: inherit; font-size: 14px; box-sizing: border-box; }
        input:focus, select:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(126, 58, 242, 0.1); }
        
        .btn-main { background: var(--primary); color: white; border: none; padding: 12px; width: 100%; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .btn-main:hover { background: #6c2bd9; }

        /* TABLES */
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; background: #f8fafc; color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; color: #475569; font-size: 14px; }
        .action-btn { text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; }
        .btn-danger { background: #fef2f2; color: #ef4444; }
        .btn-danger:hover { background: #ef4444; color: white; }

        /* SPECIFIC: CATEGORY LIST */
        .cat-list { max-height: 250px; overflow-y: auto; padding-right: 5px; }
        .cat-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
        .cat-item:last-child { border: none; }

        /* RESPONSIVE */
        @media (max-width: 1024px) { .dashboard-grid { grid-template-columns: 1fr; } .stats-row { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 768px) { .sidebar { display: none; } .main-content { margin-left: 0; width: 100%; } }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand"><i class="fas fa-shield-alt"></i> Admin<span>Panel</span></div>
        <a href="#" class="menu-item active"><i class="fas fa-th-large"></i> Overview</a>
        <a href="index.php" class="menu-item"><i class="fas fa-globe"></i> Live Website</a>
        <div style="margin-top: auto;">
            <a href="admin_login.php" class="menu-item" style="color: #fc8181;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        
        <div class="header">
            <div>
                <h1>Dashboard Overview</h1>
                <p>Real-time data and management tools.</p>
            </div>
            <span class="admin-badge"><i class="fas fa-user-shield"></i> Super Admin</span>
        </div>

        <div class="stats-row">
            <?php 
                $v_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM vendors"));
                $p_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM products"));
                $o_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM orders"));
                $rev_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_price) as total FROM orders"));
                $revenue = $rev_row['total'] ? $rev_row['total'] : 0;
            ?>
            <div class="stat-box">
                <div class="stat-icon" style="background:#e0f2fe; color:#0284c7;"><i class="fas fa-store"></i></div>
                <div class="stat-txt"><h4><?php echo $v_count; ?></h4><span>Active Vendors</span></div>
            </div>
            <div class="stat-box">
                <div class="stat-icon" style="background:#dcfce7; color:#16a34a;"><i class="fas fa-box"></i></div>
                <div class="stat-txt"><h4><?php echo $p_count; ?></h4><span>Products</span></div>
            </div>
            <div class="stat-box">
                <div class="stat-icon" style="background:#f3e8ff; color:#9333ea;"><i class="fas fa-shopping-cart"></i></div>
                <div class="stat-txt"><h4><?php echo $o_count; ?></h4><span>Total Orders</span></div>
            </div>
            <div class="stat-box">
                <div class="stat-icon" style="background:#ffedd5; color:#ea580c;"><i class="fas fa-dollar-sign"></i></div>
                <div class="stat-txt"><h4>$<?php echo number_format($revenue); ?></h4><span>Revenue</span></div>
            </div>
        </div>

        <div class="dashboard-grid">
            
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-bolt" style="color:#f59e0b;"></i> Flash Deal Manager</h3>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                        <div>
                            <label>Deal Title</label>
                            <input type="text" name="title" value="<?php echo $deal['title']; ?>" required>
                        </div>
                        <div>
                            <label>End Date</label>
                            <input type="datetime-local" name="end_date" value="<?php echo date('Y-m-d\TH:i', strtotime($deal['end_date'])); ?>" required>
                        </div>
                    </div>
                    
                    <label>Description</label>
                    <input type="text" name="description" value="<?php echo $deal['description']; ?>" required>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                        <div>
                            <label>Target Category</label>
                            <select name="category_link">
                                <?php 
                                $cat_res_deal = mysqli_query($conn, "SELECT * FROM categories");
                                while($crow = mysqli_fetch_assoc($cat_res_deal)){
                                    $sel = ($deal['category_link'] == $crow['name']) ? 'selected' : '';
                                    echo "<option value='".$crow['name']."' $sel>".$crow['name']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label>Status</label>
                            <select name="status">
                                <option value="1" <?php if($deal['status']==1) echo 'selected'; ?>>Active</option>
                                <option value="0" <?php if($deal['status']==0) echo 'selected'; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <label>Update Banner Image</label>
                    <input type="file" name="deal_image">
                    
                    <button type="submit" name="update_deal" class="btn-main">Save Deal Settings</button>
                </form>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-tags" style="color:#3b82f6;"></i> Categories</h3>
                </div>
                
                <form method="POST" style="display:flex; gap:10px; margin-bottom:20px;">
                    <input type="text" name="cat_name" placeholder="New Category..." required style="margin:0;">
                    <button type="submit" name="add_category" class="btn-main" style="width:auto; padding:0 20px;"><i class="fas fa-plus"></i></button>
                </form>

                <div class="cat-list">
                    <?php
                    $cat_q = mysqli_query($conn, "SELECT * FROM categories ORDER BY id DESC");
                    while($c = mysqli_fetch_assoc($cat_q)){
                    ?>
                    <div class="cat-item">
                        <span style="font-weight:500;"><?php echo $c['name']; ?></span>
                        <a href="?delete_cat=<?php echo $c['id']; ?>" class="action-btn btn-danger" onclick="return confirm('Delete category?')"><i class="fas fa-trash"></i></a>
                    </div>
                    <?php } ?>
                </div>
            </div>

        </div>

        <div class="card" style="margin-bottom: 30px;">
            <div class="card-header">
                <h3><i class="fas fa-users-cog"></i> Vendor Management</h3>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Vendor</th><th>Owner</th><th>Phone</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php
                        $res = mysqli_query($conn, "SELECT * FROM vendors");
                        while($row = mysqli_fetch_assoc($res)){
                            $initial = strtoupper(substr($row['username'], 0, 1));
                        ?>
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div style="width:30px; height:30px; background:#7e3af2; color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px;"><?php echo $initial; ?></div>
                                    <span style="font-weight:600;"><?php echo $row['username']; ?></span>
                                </div>
                            </td>
                            <td><?php echo $row['owner_name']; ?></td>
                            <td><?php echo $row['phone']; ?></td>
                            <td><span style="background:#def7ec; color:#03543f; padding:2px 8px; border-radius:10px; font-size:11px;">Active</span></td>
                            <td><a href="?delete_vendor=<?php echo $row['id']; ?>" class="action-btn btn-danger" onclick="return confirm('Ban Vendor?')">Ban</a></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div id="toast-box"></div>
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.get('msg')) {
            showToast(urlParams.get('msg'), 'success');
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    </script>

</body>
</html>