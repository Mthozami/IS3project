<?php
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

// Connect to DB
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo "Database connection failed.";
    exit;
}

// Validate request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["fineID"])) {
    $fineID = intval($_POST["fineID"]);
    if ($fineID <= 0) {
        http_response_code(400);
        echo "Invalid fine ID.";
        exit;
    }

    // Step 1: Fetch existing unpaid fine
    $stmt = $conn->prepare("SELECT Amount, IsPaid FROM Fines WHERE FineID = ?");
    $stmt->bind_param("i", $fineID);
    $stmt->execute();
    $stmt->bind_result($amount, $isPaid);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo "Fine not found.";
        $stmt->close();
        exit;
    }
    $stmt->close();

    if ($isPaid) {
        echo "This fine is already paid and cannot be adjusted.";
        exit;
    }

    // Step 2: Add R600 adjustment (e.g., damage/loss penalty)
    $adjustedAmount = $amount + 600;

    $stmt = $conn->prepare("UPDATE Fines SET Amount = ? WHERE FineID = ?");
    $stmt->bind_param("di", $adjustedAmount, $fineID);
    if ($stmt->execute()) {
        echo "Fine adjusted for damage/loss. New amount: R" . number_format($adjustedAmount, 2);
    } else {
        http_response_code(500);
        echo "Failed to adjust fine.";
    }
    $stmt->close();
} else {
    http_response_code(400);
    echo "Invalid request.";
}

$conn->close();
?>
