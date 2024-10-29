<?php

function security_filter($input) {
    $decoded = '';
    $length = strlen($input);
    
    for ($i = 0; $i < $length; $i++) {
        // Check if we encounter a percent sign indicating an encoded character
        if ($input[$i] == '%' && $i + 2 < $length && ctype_xdigit($input[$i + 1]) && ctype_xdigit($input[$i + 2])) {
            // Decode the next two hexadecimal characters
            $hex = substr($input, $i + 1, 2);
            $char = chr(hexdec($hex));

            // Check for common SQL injection, XSS, and command injection patterns
            if (preg_match('/[\'";<>&|`$]/', $char)) {
                continue; // Skip potentially dangerous characters
            }

            $decoded .= $char;
            $i += 2; // Skip over the next two hex digits
        } elseif ($input[$i] == '+') {
            $decoded .= ' '; // Convert '+' to a space
        } elseif (!preg_match('/[\'";<>&|`$]/', $input[$i])) {
            // Append only if the character is not part of common attack vectors
            $decoded .= $input[$i];
        }
    }

    // Additional filtering for potential malicious patterns
    $decoded = preg_replace('/(select|insert|update|delete|drop|union|--|#|\.\.\/|<script|>|alert\(|onerror|onload)/i', '', $decoded);

    return $decoded;
}

function validate_page($page) {
    // Forward Slash (/) and Backslash (\) are NOT ALLOWED in $page
    if (strpos($page, '/') === false && strpos($page, '\\') === false) {
        // Moreover, remove dangerous characters and string patterns
        $secure_page = security_filter($page);
        return $secure_page;    
    }
    // $page is not secure, return default home.php page
    return 'home.php';
    
}
// @author Siam Thanat Hack Co., Ltd. (STH)
?>