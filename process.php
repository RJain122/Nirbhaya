<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "form_db");

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    $username = $_POST['username'];
    $location = $_POST['location'];
    $incident = $_POST['incident'];
    $message = $_POST['message'];
    $agree = isset($_POST['agree']) ? "Yes" : "No";

    // Store data in MySQL
    $sql = "INSERT INTO reports (username, location, incident, message, agree) VALUES ('$username', '$location', '$incident', '$message', '$agree')";
    if ($conn->query($sql) === TRUE) {
        
        // Send Email via Brevo API
        $api_key = "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"; // Replace with your Brevo API Key
        $email_data = [
            "sender" => ["email" => "ritsjain2004@gmail.com", "name" => "Your Website"],
            "to" => [
        ["email" => "ritsjain2004@gmail.com", "name" => "Rits Jain"],
        ["email" => "shruti26cs123@satiengg.in", "name" => "Shruti Jain"],
        
        ["email" => "tanish.rai2510@gmail.com", "name" => "Shruti Jain"],
        ["email" => "vedanshi26cs151@satiengg.in", "name" => "Shruti Jain"]
    ],
            "subject" => "New Incident Report",
            "htmlContent" => "<h3>New Report Received</h3>
                              <p><strong>Username:</strong> $username</p>
                              <p><strong>Location:</strong> $location</p>
                              <p><strong>Incident:</strong> $incident</p>
                              <p><strong>Message:</strong> $message</p>
                              <p><strong>Agreement:</strong> $agree</p>"
        ];

        $ch = curl_init("https://api.brevo.com/v3/smtp/email");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "accept: application/json",
            "api-key: $api_key",
            "content-type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($email_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 201) {
    echo "✅ Email sent successfully!";
} else {
    echo "❌ Email failed! Response: " . $response;
}


        if ($response) {
            echo "Data saved and email sent!";
        } else {
            echo "Data saved, but email failed!";
        }
    } else {
        echo "Database Error: " . $conn->error;
    }

    $conn->close();
}
?>
