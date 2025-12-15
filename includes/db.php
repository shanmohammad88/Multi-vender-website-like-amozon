<?php
$conn = mysqli_connect("localhost", "root", "", "shop_db");
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }
session_start(); // Starts the "memory" for Cart and Login


// --- LANGUAGE LOGIC ---
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Check if user clicked a language link
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// 2. Set Default if not set
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}

// 3. Define Direction (LTR or RTL)
$curr_lang = $_SESSION['lang'];
$dir = ($curr_lang == 'ar') ? 'rtl' : 'ltr';

// 4. Load the file
include_once "languages/" . $curr_lang . ".php";

?>