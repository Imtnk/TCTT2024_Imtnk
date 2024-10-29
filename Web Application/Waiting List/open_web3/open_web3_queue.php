<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="queue.css">
    <title>sthPhone 16 Pro Max Pickup</title>
</head>
<body>
    <div class="container">
        <h1>sthPhone 16 Pro Max Waiting List</h1>
        
        <?php
        require_once 'config.php';
        require_once 'functions.php';

        $pdo = new PDO('sqlite:' . DB_PATH);

        if (isset($_GET['phone'], $_GET['queue'], $_GET['remark'], $_GET['sig'])) {
            $phone = $_GET['phone'];
            $queue = $_GET['queue'];
            $remark = $_GET['remark'];
            $sig = $_GET['sig'];

            if (verifyHMAC($remark, $phone, $queue, $sig)) {
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remark'])) {
                    $newRemark = $_POST['remark'];
                    // Extract only the first 100 characters
                    $newRemark = substr($newRemark, 0, 100);
                    
                    // Fetch the current settings
                    $stmt = $pdo->prepare("SELECT settings FROM waiting_list WHERE phone_number = :phone AND queue_number = :queue");
                    $stmt->execute(['phone' => $phone, 'queue' => $queue]);
                    $row = $stmt->fetch();

                    list($existing_remark, $priority_lane, $pick_up_date) = explode('|', $row['settings'], 3);

                    // Update the remark in settings
                    $updated_settings = "$newRemark|$priority_lane|$pick_up_date";
                    // Security Validation 1 - Prevent all OWASP Top 10 Security Risks
                    $updated_settings = preventWebAttacks($updated_settings);
                    // Security Validation 2 - Advanced AI Protection
                    $updated_settings = removeBadChars($updated_settings);

                    $stmt = $pdo->prepare("UPDATE waiting_list SET settings = :settings WHERE phone_number = :phone");
                    $stmt->execute(['settings' => $updated_settings, 'phone' => $phone]);

                    echo "<p id='success-message'>Remark updated successfully!</p>";
                    exit;
                }

                // Display the current settings
                $stmt = $pdo->prepare("SELECT * FROM waiting_list WHERE phone_number = :phone AND queue_number = :queue");
                $stmt->execute(['phone' => $phone, 'queue' => $queue]);
                $row = $stmt->fetch();

                list($remark, $priority_lane, $pick_up_date) = explode('|', $row['settings'], 3);
                ?>

                <form method='POST'>
                    <label for='phone-number'>Phone Number:</label> 
                    <span id='phone-number'><?php echo htmlspecialchars($row['phone_number']); ?></span><br>
                    
                    <label for='full-name'>Full Name:</label>
                    <span id='full-name'><?php echo htmlspecialchars($row['full_name']); ?></span><br>
                    
                    <label for='queue-number'>Queue Number:</label> 
                    <span id='queue-number'><?php echo htmlspecialchars($row['queue_number']); ?></span><br>
                    
                    <!-- <label for='priority-lane'>Priority Lane:</label> -->
                    <!-- <span id='priority-lane'><?php echo htmlspecialchars($priority_lane); ?></span><br> -->
                    
                    <label for='pick-up-date'>Pick Up Date:</label>
                    <span id='pick-up-date'><?php echo htmlspecialchars($pick_up_date); ?></span><br>
                    
                    <label for='remark'>Remark for calling date/time:</label>
                    <input type='text' id='remark' name='remark' value='<?php echo htmlspecialchars($remark); ?>'><br>
                    
                    <button type='submit'>Edit Remark</button>
                </form>
                <?php
            } else {
                echo "<p id='invalid-signature'>Invalid signature!</p>";
            }
        }
        ?>
    </div>
    <footer>
        <p> 🐈 Siam Thanat Hack Co., Ltd. (STH)</p>
    </footer>
    <!-- @author Siam Thanat Hack Co., Ltd. (STH) -->
</body>
</html>
