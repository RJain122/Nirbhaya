<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voice_db";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["codeword"])) {
        $codeword = $_POST["codeword"];
        echo "Received codeword: " . $codeword . "<br>";

        // Prepare and Bind SQL Statement
        $stmt = $conn->prepare("INSERT INTO voice_records (codeword) VALUES (?)");
        $stmt->bind_param("s", $codeword);
        
        if ($stmt->execute()) {
            echo "Codeword saved successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        echo "No codeword received!";
    }
}

$conn->close();
?>
