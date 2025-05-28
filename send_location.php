<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    if (empty($latitude) || empty($longitude)) {
        die("Location data not received.");
    }

    $mapLink = "https://www.google.com/maps?q=$latitude,$longitude";

    // Define multiple recipients correctly
    $toEmails = [
        ["email" => "aasthajain590@gmail.com", "name" => "Recipient 1"],
        ["email" => "shruti26cs123@satiengg.in", "name" => "Recipient 2"],
        ["email" => "vedanshi26cs151@satiengg.in", "name" => "Recipient 3"]
    ];

    $data = [
        "sender" => ["name" => "Your Name", "email" => "ritsjain2004@gmail.com"],  // Must be Brevo verified
        "to" => $toEmails,  // Corrected placement
        "subject" => "Live Location Shared",
        "htmlContent" => "
            <h3>Live Location Received</h3>
            <p><strong>Latitude:</strong> $latitude</p>
            <p><strong>Longitude:</strong> $longitude</p>
            <p><strong>Google Maps Link:</strong> <a href='$mapLink'> $mapLink </a></p>
        "
    ];

    $apiKey = "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";  // Replace with your real API key

    $ch = curl_init("https://api.brevo.com/v3/smtp/email");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "api-key: $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "<br>Brevo API Response: $response";
    echo "<br>HTTP Status Code: $http_code";
}
?>
