<?php
// Set database connection variables
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

// Create a new MySQLi connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check if the connection failed
if ($conn->connect_error) {
    http_response_code(500); 
    echo "Database connection failed.";
    exit; 
}

// Check if the request method is POST and fineID is set
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["fineID"])) {
    $fineID = intval($_POST["fineID"]);

    // Step 1: Get current fine details
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

    $conn->begin_transaction(); // Begin transaction

    // Step 2: Update fine to mark it as paid
    $stmt = $conn->prepare("UPDATE Fines SET IsPaid = 1 WHERE FineID = ?");
    $stmt->bind_param("i", $fineID); 
    if (!$stmt->execute()) {
        $conn->rollback(); // Rollback on failure
        http_response_code(500); 
        echo "Failed to mark fine as paid.";
        $stmt->close();
        exit;
    }
    $stmt->close();

    // Step 3: Re-fetch updated amount
    $stmt = $conn->prepare("SELECT Amount FROM Fines WHERE FineID = ?");
    $stmt->bind_param("i", $fineID);
    $stmt->execute();
    $stmt->bind_result($amount);
    if (!$stmt->fetch()) {
        $conn->rollback(); // Rollback on failure
        http_response_code(500);
        echo "Could not retrieve adjusted fine amount.";
        $stmt->close();
        exit;
    }
    $stmt->close();

    // Step 4: Insert payment record
    $receipt = uniqid("RCPT-"); 
    $paymentDate = date("Y-m-d H:i:s"); 

    $stmt = $conn->prepare("INSERT INTO Transactions (FineID, ReceiptNumber, PaymentDate, PaidAmount) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issd", $fineID, $receipt, $paymentDate, $amount); 
    if ($stmt->execute()) {
        $conn->commit(); // Commit if all succeeded
        echo "Fine marked as paid and transaction recorded: R" . number_format($amount, 2);
    } else {
        $conn->rollback(); // Rollback on failure
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
