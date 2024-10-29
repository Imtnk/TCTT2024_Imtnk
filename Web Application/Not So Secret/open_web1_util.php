<?php

// Function to create the session token using a more complex secret key without a salt
function create_session_token($username, $timestamp){
    // Manipulate the date and timestamp components
    $date_component = date('Y-m-d', $timestamp); 
    $hour_component = date('H', $timestamp); // Hour component
    $minute_component = floor(date('i', $timestamp) / 10); // Segment minutes into blocks of 10

    // Combine all components into the secret key
    $secret_key = hash('sha256', $date_component . $hour_component . $minute_component);
    
    // Generate the session token using the more complex secret_key
    $session_token = hash_hmac('sha256', $username, $secret_key);

    return $session_token;
}

// Function to validate the session token
function validate_session(){
    global $users;
    
    // Check if the necessary cookies are set
    if (isset($_COOKIE['session_token']) && isset($_COOKIE['session_timestamp'])) {
        $session_token = $_COOKIE['session_token'];
        $timestamp = $_COOKIE['session_timestamp'];

        // Validate the timestamp to ensure it is a valid integer
        if (!is_numeric($timestamp) || strlen($timestamp) != 10) {
            return false;
        }
        
        // Check each user's session token to see if it matches
        foreach ($users as $username => $password) {
            if ($session_token === create_session_token($username, $timestamp)) {
                // Valid session token found, set the session variables
                $_SESSION['username'] = $username;
                return true;
            }
        }
    }
    return false;
}

// @author Siam Thanat Hack Co., Ltd. (STH)
?>