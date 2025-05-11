<?php
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo "Database connection failed.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["fineID"])) {
    $fineID = intval($_POST["fineID"]);

    // Step 1: Get the amount
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
        echo "This fine has already been paid.";
        exit;
    }

    // Step 2: Mark as paid
    $stmt = $conn->prepare("UPDATE Fines SET IsPaid = 1 WHERE FineID = ?");
    $stmt->bind_param("i", $fineID);
    if (!$stmt->execute()) {
        http_response_code(500);
        echo "Failed to mark fine as paid.";
        $stmt->close();
        exit;
    }
    $stmt->close();

    // Step 3: Record transaction
    $receipt = uniqid("RCPT-");
    $paymentDate = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("INSERT INTO Transactions (FineID, ReceiptNumber, PaymentDate, PaidAmount) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issd", $fineID, $receipt, $paymentDate, $amount);
    if ($stmt->execute()) {
        echo "Fine marked as paid and transaction recorded successfully.";
    } else {
        http_response_code(500);
        echo "Failed to record transaction.";
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo "Invalid request.";
}

$conn->close();
?>
