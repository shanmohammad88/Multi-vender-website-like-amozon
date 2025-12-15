# Multi-vender-website-like-amozon
e-Market is a full-stack, enterprise-level e-commerce application built with PHP and MySQL. It features a custom AI Behavioral Recommendation Engine that scores user interest in real-time. Key features include a fully responsive Amazon-style UI, dynamic English/Arabic RTL layout switching, a robust Vendor Dashboard with analytics, Super Admin controls, Flash Deal management, and automated PDF invoicing.


# 🛒 GCCMarket - Multi-Vendor E-Commerce Platform

GCCMarket is a full-stack e-commerce solution built with PHP and MySQL, designed specifically for the Gulf region. It features a custom **AI Behavioral Recommendation Engine**, full English/Arabic (RTL) support, and a comprehensive Vendor/Admin dashboard system.

![GCCMarket Logo](assets/img/logo.png)

## 🚀 Key Features
* **AI Engine:** Tracks user behavior to recommend products automatically.
* **Bilingual System:** One-click toggle between English and Arabic (with RTL layout).
* **Multi-Vendor:** Sellers have their own dashboard to manage products and orders.
* **Super Admin:** Manage flash deals, categories, and ban illegal vendors.
* **Smart Checkout:** Guest checkout, Invoice generation (PDF), and Stock management.

---

## 🛠️ Installation Steps

Follow these steps to run the project on your local machine.

### 1. Prerequisites
You need a local server environment.
* Download and install **XAMPP** (recommended) or WAMP/MAMP.

### 2. Set Up the Files
1.  Download this repository as a **ZIP file** or clone it using Git.
2.  Extract the folder.
3.  Rename the folder to `market`.
4.  Copy the `market` folder and paste it into your XAMPP `htdocs` directory:
    * *Windows:* `C:\xampp\htdocs\market`
    * *Mac:* `/Applications/XAMPP/htdocs/market`

### 3. Set Up the Database
1.  Open **XAMPP Control Panel** and start **Apache** and **MySQL**.
2.  Go to your browser and type: `http://localhost/phpmyadmin`
3.  Click **New** and create a database named **`shop_db`**.
4.  Click on `shop_db` -> Click **Import** tab.
5.  Choose the `database.sql` file included in this project folder.
6.  Click **Go**.

### 4. Configuration (Optional)
If your MySQL has a password (default XAMPP has no password), open `includes/db.php` and update it:
```php
$conn = mysqli_connect("localhost", "root", "YOUR_PASSWORD", "shop_db");
