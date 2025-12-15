<?php
include 'includes/db.php';
if (!isset($_SESSION['vendor_id'])) { header("Location: vendor_login.php"); exit(); }

$vendor_id = $_SESSION['vendor_id'];
$vendor_name = isset($_SESSION['vendor_name']) ? $_SESSION['vendor_name'] : "Vendor";

// Data Fetching...
$v_query = mysqli_query($conn, "SELECT shop_cover FROM vendors WHERE id='$vendor_id'");
$v_data = mysqli_fetch_assoc($v_query);
$cover_image = $v_data['shop_cover'];

// Stats Logic...
$rev_res = mysqli_query($conn, "SELECT SUM(price) as earnings, COUNT(*) as total_sold FROM order_items WHERE vendor_id='$vendor_id'");
$rev_data = mysqli_fetch_assoc($rev_res);
$my_earnings = $rev_data['earnings'] ? $rev_data['earnings'] : 0;
$items_sold = $rev_data['total_sold'];
$prod_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM products WHERE vendor_id='$vendor_id'");
$active_products = mysqli_fetch_assoc($prod_res)['total'];

// Form Handlers (Keep your existing logic here for upload/delete/update/add)
if(isset($_POST['upload_cover'])) {
    $target = "uploads/" . basename($_FILES['cover']['name']);
    if(move_uploaded_file($_FILES['cover']['tmp_name'], $target)) {
        mysqli_query($conn, "UPDATE vendors SET shop_cover='{$_FILES['cover']['name']}' WHERE id='$vendor_id'");
        header("Location: vendor_dashboard.php?msg=Cover Updated"); exit();
    }
}
if (isset($_GET['delete'])) {
    mysqli_query($conn, "DELETE FROM products WHERE id='{$_GET['delete']}' AND vendor_id='$vendor_id'");
    header("Location: vendor_dashboard.php?msg=Product Deleted"); exit();
}
if(isset($_POST['update_status'])) {
    mysqli_query($conn, "UPDATE order_items SET status='{$_POST['status']}' WHERE id='{$_POST['item_id']}'");
    header("Location: vendor_dashboard.php?msg=Status Updated"); exit();
}
if (isset($_POST['add_product'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $price = $_POST['price']; $discount = $_POST['discount_price'] ?? 0;
    $img = $_FILES['images']['name'][0]; 
    move_uploaded_file($_FILES['images']['tmp_name'][0], "uploads/".$img);
    
    $sql = "INSERT INTO products (title, price, discount_price, image, vendor_name, vendor_id, category, description, stock) 
            VALUES ('$title', '$price', '$discount', '$img', '$vendor_name', '$vendor_id', '{$_POST['category']}', '{$_POST['description']}', '{$_POST['stock']}')";
    if(mysqli_query($conn, $sql)){
        $pid = mysqli_insert_id($conn);
        // Gallery logic here...
        header("Location: vendor_dashboard.php?msg=Product Live!"); exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vendor Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="assets/js/script.js" defer></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
</head>
<body>
    <?php include 'includes/menu.php'; ?>

    <div class="hero" style="padding: 40px 20px; min-height: 200px; display: flex; align-items: center; justify-content: center; position: relative; background-size: cover; background-position: center; <?php if($cover_image) echo "background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('uploads/$cover_image');"; else echo "background: var(--dark);"; ?>">
        <div style="text-align: center; color: white; z-index: 2;">
            <h1>👋 Hello, <?php echo $vendor_name; ?></h1>
            <form method="POST" enctype="multipart/form-data">
                <label class="btn-cover"><i class="fas fa-camera"></i> Edit Cover</label>
                <input type="file" name="cover" style="display:none;" onchange="this.form.submit()">
                <input type="hidden" name="upload_cover" value="1">
            </form>
        </div>
    </div>

    <div class="container" style="margin-top: -40px; position: relative; z-index: 10;">
        
        <div class="stats-grid">
            <div class="stat-card"><div class="icon-circle" style="background:#27ae60;"><i class="fas fa-wallet"></i></div><div><h3>$<?php echo number_format($my_earnings); ?></h3><p>Earnings</p></div></div>
            <div class="stat-card"><div class="icon-circle" style="background:#2980b9;"><i class="fas fa-shopping-bag"></i></div><div><h3><?php echo $items_sold; ?></h3><p>Sold</p></div></div>
            <div class="stat-card"><div class="icon-circle" style="background:#f39c12;"><i class="fas fa-box"></i></div><div><h3><?php echo $active_products; ?></h3><p>Products</p></div></div>
        </div>

        <div class="dashboard-flex" style="display: flex; gap: 30px;">
            
            <div style="flex: 2; min-width: 0;"> <div class="section-box">
                    <div class="section-title">🚚 Recent Orders</div>
                    <div class="table-wrapper"> <table>
                            <thead><tr><th>ID</th><th>Product</th><th>Status</th><th>Action</th></tr></thead>
                            <tbody>
                            <?php
                            $res = mysqli_query($conn, "SELECT order_items.*, orders.customer_name FROM order_items JOIN orders ON order_items.order_id = orders.id WHERE order_items.vendor_id = '$vendor_id' ORDER BY order_items.id DESC LIMIT 5");
                            if(mysqli_num_rows($res) > 0) {
                                while($row = mysqli_fetch_assoc($res)) {
                                    echo "<tr>
                                            <td>#{$row['order_id']}</td>
                                            <td>{$row['product_name']}</td>
                                            <td><span class='badge'>{$row['status']}</span></td>
                                            <td>
                                                <form method='POST' style='display:flex; gap:5px;'>
                                                    <input type='hidden' name='item_id' value='{$row['id']}'>
                                                    <select name='status' style='padding:5px;'><option>Pending</option><option>Packing</option><option>Delivered</option></select>
                                                    <button type='submit' name='update_status' class='btn-new' style='padding:5px;'>OK</button>
                                                </form>
                                            </td>
                                          </tr>";
                                }
                            } else { echo "<tr><td colspan='4' style='text-align:center;'>No orders.</td></tr>"; }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="section-box">
                    <div class="section-title">
                        <span>📦 Inventory</span>
                        <button onclick="document.getElementById('addProductForm').style.display='block'" class="btn-new"><i class="fas fa-plus"></i> Add</button>
                    </div>

                    <div id="addProductForm" style="display:none; margin-bottom:20px; background:#f9f9f9; padding:15px; border-radius:8px;">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-grid">
                                <div><label>Title</label><input type="text" name="title" required></div>
                                <div><label>Price</label><input type="number" name="price" required></div>
                            </div>
                            <div class="form-grid">
                                <div><label>Category</label><select name="category"><option>Electronics</option><option>Fashion</option><option>Home</option><option>Toys</option><option>Beauty</option></select></div>
                                <div><label>Stock</label><input type="number" name="stock" required></div>
                            </div>
                            <label>Description</label><textarea name="description" rows="2" required></textarea>
                            <label>Image</label><input type="file" name="images[]" multiple required>
                            <button type="submit" name="add_product" class="btn-new" style="width:100%; margin-top:10px;">Publish</button>
                        </form>
                    </div>

                    <div class="table-wrapper"> <table>
                            <thead><tr><th>Img</th><th>Details</th><th>Price</th><th>Action</th></tr></thead>
                            <tbody>
                            <?php
                            $res = mysqli_query($conn, "SELECT * FROM products WHERE vendor_id='$vendor_id' ORDER BY id DESC");
                            while ($r = mysqli_fetch_assoc($res)) {
                                echo "<tr>
                                        <td><img src='uploads/{$r['image']}' style='width:40px; border-radius:4px;'></td>
                                        <td>{$r['title']}</td>
                                        <td>${$r['price']}</td>
                                        <td><a href='vendor_dashboard.php?delete={$r['id']}' style='color:red;'>Del</a></td>
                                      </tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>