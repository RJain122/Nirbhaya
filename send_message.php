<?php
header('Content-Type: application/json');

// 1. Database Connection
$conn = new mysqli('localhost', 'root', '', 'help_db');
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Database connection failed"]));
}

// 2. Get the message from POST request
$data = json_decode(file_get_contents('php://input'), true);
$message = $data['message'] ?? '';

if (empty($message)) {
    echo json_encode(["success" => false, "error" => "Message is empty!"]);
    exit();
}

// 3. Save to database
$stmt = $conn->prepare("INSERT INTO messages (message) VALUES (?)");
$stmt->bind_param("s", $message);
$saved = $stmt->execute();
$stmt->close();

if (!$saved) {
    echo json_encode(["success" => false, "error" => "Failed to save message"]);
    exit();
}

// 4. Send with Brevo
$api_key = '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'; // Replace with your actual API key
$url = 'https://api.brevo.com/v3/smtp/email';

$email_data = [
    'sender' => ['name' => 'Safety Alert', 'email' => 'ritsjain2004@gmail.com'],
    'to' => [['email' => 'aasthajain590@gmail.com', 'name' => 'Admin']],
    'subject' => 'Emergency Alert!',
    'htmlContent' => "<p>Emergency Message: $message</p>"
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($email_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: application/json',
    'api-key: ' . $api_key,
    'content-type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

// 5. Return success
echo json_encode(["success" => true, "message" => "Alert sent successfully!"]);
$conn->close();
?>