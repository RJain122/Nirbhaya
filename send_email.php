<?php
header('Content-Type: application/json');

// Brevo API configuration
$BREVO_API_KEY = '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'; // Replace with your actual Brevo API key

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (empty($data['recipient']) || empty($data['sender_email']) || empty($data['sender_name']) || empty($data['code_word'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

// Prepare email data for Brevo
$emailData = [
    'sender' => [
        'name' => $data['sender_name'],
        'email' => $data['sender_email']
    ],
    'to' => [
        [
            'email' => $data['recipient'],
            'name' => 'Recipient'
        ]
    ],
    'subject' => 'Code Word Detected: ' . $data['code_word'],
    'htmlContent' => '<p>The code word "<strong>' . htmlspecialchars($data['code_word']) . '</strong>" was detected by the voice activation system.</p>',
    'headers' => [
        'X-Mailin-custom' => 'Voice-Activation-System'
    ]
];

// Send email via Brevo API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.brevo.com/v3/smtp/email');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: application/json',
    'api-key: ' . $BREVO_API_KEY,
    'content-type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode >= 200 && $httpCode < 300) {
    echo json_encode(['success' => true]);
} else {
    $error = json_decode($response, true)['message'] ?? 'Unknown error';
    echo json_encode(['success' => false, 'error' => $error]);
}
?>