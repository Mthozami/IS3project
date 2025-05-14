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
    // Return HTTP 500 error
    http_response_code(500); 
    echo "Database connection failed.";
    // Stop script execution
    exit; 
}

// Check if the request method is POST and fineID is set
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["fineID"])) {
    // Convert fineID to integer to sanitize input
    $fineID = intval($_POST["fineID"]);

    // Step 1: Get current fine details using prepared statement (Prevents SQL Injection )
    $stmt = $conn->prepare("SELECT Amount, IsPaid FROM Fines WHERE FineID = ?");
    // Bind integer parameter
    $stmt->bind_param("i", $fineID); 
     // Execute the statement
    $stmt->execute();
    // Bind result variables
    $stmt->bind_result($amount, $isPaid); 

    // If no fine found
    if (!$stmt->fetch()) {
        // Return 404
        http_response_code(404); 
        echo "Fine not found.";
        // Close statement
        $stmt->close(); 
        exit;
    }
    // Close statement
    $stmt->close(); 

    // If the fine is already paid
    if ($isPaid) {
        echo "This fine has already been paid.";
        exit;
    }

    // Step 2: Update fine to mark it as paid (Safe via prepared statement ✅)
    $stmt = $conn->prepare("UPDATE Fines SET IsPaid = 1 WHERE FineID = ?");
    // Bind parameter
    $stmt->bind_param("i", $fineID); 
    if (!$stmt->execute()) {
        // Internal error
        http_response_code(500); 
        echo "Failed to mark fine as paid.";
        $stmt->close();
        exit;
    }
    $stmt->close();

    // Step 3: Re-fetch updated amount (in case of adjustments)
    $stmt = $conn->prepare("SELECT Amount FROM Fines WHERE FineID = ?");
    $stmt->bind_param("i", $fineID);
    $stmt->execute();
    $stmt->bind_result($amount);
    if (!$stmt->fetch()) {
        http_response_code(500);
        echo "Could not retrieve adjusted fine amount.";
        $stmt->close();
        exit;
    }
    $stmt->close();

    /* Step 4: Insert payment record in Transactions table (Safe via prepared statement )
     Generate a unique receipt number
    Get current datetime*/
    $receipt = uniqid("RCPT-"); 
    $paymentDate = date("Y-m-d H:i:s"); 

    $stmt = $conn->prepare("INSERT INTO Transactions (FineID, ReceiptNumber, PaymentDate, PaidAmount) VALUES (?, ?, ?, ?)");
    // Bind parameters
    $stmt->bind_param("issd", $fineID, $receipt, $paymentDate, $amount); 
    if ($stmt->execute()) {
        echo "Fine marked as paid and transaction recorded: R" . number_format($amount, 2);
    } else {
        // Error on insert
        http_response_code(500); 
        echo "Failed to record transaction.";
    }
 // Close the statement
    $stmt->close(); 
} else {
    // Bad request
    http_response_code(400); 
    echo "Invalid request.";
}

// Close DB connection
$conn->close();
?>
