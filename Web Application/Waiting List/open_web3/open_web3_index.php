<?php
require_once 'config.php';
require_once 'functions.php';

$pdo = new PDO('sqlite:' . DB_PATH);

// Server-side validation functions
function isValidPhoneNumber($input) {
    // Checks if the input is numeric and exactly 10 digits long
    return preg_match('/^[0-9]{10}$/', $input);  
}

function isValidFullName($input) {
    // Checks if the input contains only letters and spaces, with a maximum length of 20 characters
    return preg_match('/^[a-zA-Z ]{1,20}$/', $input);  
}

// Check if this is a status check request (Feature 1)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'check_status') {
    $phone_number = $_POST['phone_number'];
    $queue_number = $_POST['queue_number'];

    // Server-side validation for phone number and queue number
    if (!isValidPhoneNumber($phone_number) || !is_numeric($queue_number)) {
        echo json_encode(["status" => "error", "message" => "Invalid input. Phone number (10 Digits Only) and queue number must be numeric."]);
        exit;
    }

    // Fetch the 'settings' column from 'waiting_list' for the given phone_number and queue_number
    $stmt = $pdo->prepare("SELECT settings FROM waiting_list WHERE phone_number = :phone_number AND queue_number = :queue_number");
    $stmt->execute([
        'phone_number' => $phone_number,
        'queue_number' => $queue_number
    ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Extract $priority_lane from settings
        list($remark, $priority_lane, $pick_up_date) = explode('|', $row['settings']);

        // Query 'queue_priority' to check if the priority queue is ready for a given phone_number
        $query = sprintf(
            "SELECT phone_number, priority_lane FROM queue_priority WHERE phone_number = '%s' AND priority_lane = '%s'",
            $phone_number,
            $priority_lane
        );

        // Execute the query with prepared statements
        $stmt_priority = $pdo->query($query);
        $priority_row = $stmt_priority->fetch(PDO::FETCH_ASSOC);

        // The queue is the highest priority (priority_lane = 1)
        if ($priority_row && $priority_row['priority_lane'] !== "0") {
            echo json_encode([
                "status" => "success",
                "message" => "You are V.I.P on our priority lane ! You are our {$priority_row['priority_lane']}st priority!. Please be patient. sthPhone 16 Pro Max is sending to you."
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "No matching record found in the priority queue. Please be patient and wait for 1337 more days."
            ]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No matching record found in the waiting list."]);
    }
    exit;
}

// Check if this is an add new user request (Feature 2)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_user') {
    $phone_number = $_POST['phone_number'];
    $full_name = $_POST['full_name'];

    // Server-side validation
    if (!isValidPhoneNumber($phone_number) || !isValidFullName($full_name)) {
        echo "Invalid input. Phone number must be numeric (10 digits), and full name must contain alphabetic characters and spaces only (Max: 20 chars).";
        exit;
    }

    $queue_number = getNextQueueNumber($pdo);
    $priority_lane = "0";
    $pick_up_date = date('Y-m-d', strtotime("+" . rand(10, 20) . " days"));
    $remark = "Midnight at Central Rama 9"; // Default initial remark
    $settings = "$remark|$priority_lane|$pick_up_date";

    // Insert or update record in waiting_list
    $stmt = $pdo->prepare("INSERT OR REPLACE INTO waiting_list (phone_number, full_name, queue_number, settings)
                           VALUES (:phone_number, :full_name, :queue_number, :settings)");
    $stmt->execute([
        'phone_number' => $phone_number,
        'full_name' => $full_name,
        'queue_number' => $queue_number,
        'settings' => $settings
    ]);

    // Insert the default priority_lane record into queue_priority
    $stmt = $pdo->prepare("INSERT OR REPLACE INTO queue_priority (phone_number, priority_lane) VALUES (:phone_number, :priority_lane)");
    $stmt->execute([
        'phone_number' => $phone_number,
        'priority_lane' => $priority_lane
    ]);

    // After the user is added successfully to the waiting list
    $sig = generateHMAC($remark, $phone_number, $queue_number);

    // Echo the link correctly with HTML tag
    echo "You have been added. Your queue URL is: <a href='queue.php?phone={$phone_number}&queue={$queue_number}&remark={$remark}&sig={$sig}'>View Queue</a>";

    exit;
}

function getNextQueueNumber($pdo) {
    $stmt = $pdo->query("SELECT MAX(queue_number) as max_queue FROM waiting_list");
    $row = $stmt->fetch();
    return $row['max_queue'] + 1;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.min.js"></script>
    <title>sthPhone 16 Pro Max Waiting List</title>
</head>
<body>
    <div id="app">
        <h1>sthPhone 16 Pro Max Waiting List</h1>
        
        <!-- Feature 1: Check status -->
        <div>
            <h2>Check Your Status</h2>
            <form @submit.prevent="checkStatus">
                <input type="text" v-model="check_phone_number" placeholder="Phone Number" required><br>
                <input type="text" v-model="queue_number" placeholder="Queue Number" required><br>
                <button type="submit">Check Status</button>
            </form>
        </div>
        
        <!-- Feature 2: Add new user -->
        <div>
            <h2>Add to Waiting List</h2>
            <form @submit.prevent="addUser">
                <input type="text" v-model="phone_number" placeholder="Phone Number" required><br>
                <input type="text" v-model="full_name" placeholder="Full Name" required><br>
                <button type="submit">Join the Waiting List</button>
            </form>
        </div>
        
        <p v-if="errorMessage" style="color: red;">{{ errorMessage }}</p>
        <p v-if="successMessage" class="successMessage" style="color: green;" v-html="successMessage"></p>
    </div>

    <script>
        new Vue({
            el: '#app',
            data: {
                check_phone_number: '',
                queue_number: '',
                phone_number: '',
                full_name: '',
                errorMessage: '',
                successMessage: ''
            },
            methods: {
                // Method to check status (Feature 1)
                checkStatus() {
                    if (!/^[0-9]+$/.test(this.check_phone_number) || !/^[0-9]+$/.test(this.queue_number)) {
                        this.errorMessage = "Invalid input. Phone number and queue number must be numeric.";
                        return;
                    }

                    this.errorMessage = ''; // Clear error message if validation passes
                    fetch('index.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `action=check_status&phone_number=${this.check_phone_number}&queue_number=${this.queue_number}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert(data.message);
                    });
                },

                // Method to add a new user (Feature 2)
                addUser() {
                    if (!/^[0-9]{10}$/.test(this.phone_number)) {
                        this.errorMessage = "Invalid phone number. It must be exactly 10 digits.";
                        return;
                    }
                    if (!/^[a-zA-Z ]+$/.test(this.full_name)) {
                        this.errorMessage = "Invalid full name. Only alphabetic characters and spaces are allowed. (Max 20 chars)";
                        return;
                    }

                    this.errorMessage = ''; 

                    fetch('index.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `action=add_user&phone_number=${this.phone_number}&full_name=${this.full_name}`
                    })
                    .then(response => response.text())
                    .then(data => {
                        this.successMessage = data;
                        this.$nextTick(() => {
                            document.querySelector('.successMessage').innerHTML = this.successMessage;
                        });
                    });
                }

            }
        });
    </script>
    <footer>
        <p> 🐈 Siam Thanat Hack Co., Ltd. (STH)</p>
    </footer>
    <!-- @author Siam Thanat Hack Co., Ltd. (STH) -->
</body>
</html>
