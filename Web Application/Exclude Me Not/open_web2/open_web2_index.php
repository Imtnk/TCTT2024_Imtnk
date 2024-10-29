<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SDH Bank</title>
    <link rel="stylesheet" href="assets/style.css"> <!-- Linking the CSS file -->
</head>
<body>

<?php

    require_once('util.php');

    # Remove dangerous attack payloads
    $page = isset($_GET['page']) ? validate_page($_GET['page']) : 'home.php';
    $page_path = "pages/$page";

    # Only allow 3 web pages
    $allowed_pages = ['home.php', 'about_us.php', 'saving.php'];
    foreach ($allowed_pages as $allowed_page) {
        if (strpos($page_path, $allowed_page) !== false) {
            readfile($page_path);
        }
    }

?>
    <script src="assets/script.js"></script> <!-- Linking the JS file -->
    <!-- The Flag file is located in the path /flag.txt -->
    <!-- @author Siam Thanat Hack Co., Ltd. (STH) -->
</body>
</html>