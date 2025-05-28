<?php
// Database configuration
$dbConfig = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'submit_db'
];

// Initialize message variables
$success = false;
$message = '';

try {
    // Create connection
    $conn = new mysqli(
        $dbConfig['host'],
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['database']
    );

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed");
    }

    // Validate required fields
    $required = ['username', 'email', 'message'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("The $field field is required");
        }
    }

    // Sanitize data
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $subject = isset($_POST['subject']) ? $conn->real_escape_string($_POST['subject']) : '';
    $message_content = $conn->real_escape_string($_POST['message']);

    // Insert into database
    $sql = "INSERT INTO form_submissions (username, email, subject, message) 
            VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Database preparation failed");
    }

    $stmt->bind_param("ssss", $username, $email, $subject, $message_content);
    
    if ($stmt->execute()) {
        $success = true;
        $message = 'Your message has been submitted successfully!';
    } else {
        throw new Exception("Database insertion failed");
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    $message = 'Error: ' . $e->getMessage();
}

// Display result to user
?>
<!DOCTYPE html>
<html>
<head>
    <title>Form Submission Result</title>
    <style>
        .message-box {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="message-box <?php echo $success ? 'success' : 'error'; ?>">
        <?php echo $message; ?>
        <br>
        <a href="contact.html" class="back-btn">← Back to Form</a>
    </div>
</body>
</html>