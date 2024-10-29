<?php
define('SECRET_KEY', 'CHANGE_ME');

function generateHMAC($remark, $phone, $queue) {
    return hash_hmac('sha256', "$remark|$phone|$queue", SECRET_KEY);
}

function verifyHMAC($remark, $phone, $queue, $sig) {
    return hash_equals(generateHMAC($remark, $phone, $queue), $sig);
}

// Prevent all Web Attack WAF/Filter Bypasses with AI
function removeBadChars($input, $encoded = true)
{
    $reallybad = [];

    if ($encoded) {
        $reallybad[] = '/%0[0-8bcef]/';
        $reallybad[] = '/%1[0-9a-f]/';
    }

    $reallybad[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';

    do {
        $input = preg_replace($reallybad, '', $input, -1, $c);
    } while ($c);

    return $input;
}


// Prevent all OWASP Top 10 Security Risks
function preventWebAttacks($input, $replacement = '') {
    // SQL Injection keywords
    $sqliKeywords = [
        'union', 'select', 'insert', 'update', 'delete', 'drop', 
        'alter', 'truncate', 'from', 'information_schema', 'table', 'database', 
        'benchmark', 'sleep', 'load_file', 'outfile', 'char(', '--', '#', ';', 
        'or 1=1', 'or 1=0', 'and 1=1', 'and 1=0', "' OR '1'='1", 'limit'
    ];

    // Cross-Site Scripting (XSS) keywords
    $xssKeywords = [
        'script', 'alert', 'onerror', 'onload', 'onmouseover', 'onclick', 
        'document.cookie', 'document.domain', 'window.location', 'javascript:', 
        'eval(', 'src=', '<svg', 'expression(', 'vbscript:', 'iframe', '<img', 'innerHTML', 
        '<object', '<embed', '<link', '<style', 'localStorage', 'sessionStorage'
    ];

    // Command Injection keywords
    $commandInjectionKeywords = [
        'curl', 'wget', 'ping', 'nslookup', 'whoami', 'dig', 'traceroute', 
        'netstat', 'ifconfig', 'ipconfig', 'cat', 'ls', 'rm', '&&', 
        ';', '`', '$(', '<', '||', '&', 'chmod', 'chown', 'scp', 'ftp', 'tftp'
    ];

    // Path Traversal keywords
    $pathTraversalKeywords = [
        '../', '..\\', '/etc/passwd', '/etc/shadow', '/etc/hosts', 
        'C:\\windows\\system32', 'boot.ini', 'win.ini', '/proc/self/environ', 
        'sys', 'proc', 'var', 'www', '/home/', '/root/', '%2e%2e%2f', '%2e%2e\\'
    ];

    // Combine all the keywords into a single array
    $allKeywords = array_merge($sqliKeywords, $xssKeywords, $commandInjectionKeywords, $pathTraversalKeywords);
    
    // Use str_ireplace to perform a case-insensitive replacement
    $sanitized = str_ireplace($allKeywords, $replacement, $input);

    // If $input is not the same as $sanitized, then $input contains at least a keyword
    if($sanitized !== $input){
        echo '
            <style>
                .blocked-message {
                    color: #ff4d4d; /* A softer red color */
                    background-color: #ffe6e6; /* Light red background for emphasis */
                    border: 1px solid #ff4d4d;
                    padding: 20px;
                    margin: 50px auto;
                    width: 60%;
                    font-family: Arial, sans-serif;
                    font-size: 1.5em;
                    text-align: center;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(255, 77, 77, 0.5);
                }
            </style>
            <div class="blocked-message">
                WAF has just blocked your potential web attacks.
            </div>
            <footer>
                <p> 🐈 Siam Thanat Hack Co., Ltd. (STH)</p>
            </footer>
       ';
        exit();
    }
    
    return $sanitized;
}

// @author Siam Thanat Hack Co., Ltd. (STH)
?>
