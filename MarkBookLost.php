<?php
// Database credentials
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

// Connect to MySQL
$conn = new mysqli($host, $username, $password, $dbname);

// Handle DB connection error
if ($conn->connect_error) {
    http_response_code(500);
    echo "Database connection failed.";
    exit;
}

// Check if the method is POST and borrowingID is sent
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["borrowingID"])) {
    // Sanitize input to integer
    $borrowingID = intval($_POST["borrowingID"]);

    // Step 1: Check if a fine already exists (Safe via prepared statement )
    $check = $conn->prepare("SELECT FineID FROM Fines WHERE BorrowingID = ?");
    $check->bind_param("i", $borrowingID);
    $check->execute();
    // Needed for num_rows
    $check->store_result(); 

    // If fine already exists for this borrowing
    if ($check->num_rows > 0) {
        echo "This borrowing already has a fine.";
        $check->close();
        exit;
    }
    $check->close();

    $conn->begin_transaction(); // Begin transaction

    // Step 2: Add a new fine for R600 (Safe via prepared statement )
    $insert = $conn->prepare("INSERT INTO Fines (BorrowingID, Amount) VALUES (?, 600)");
    // Only bind the borrowingID, amount is fixed
    $insert->bind_param("i", $borrowingID);
    if ($insert->execute()) {
        $conn->commit(); // Commit transaction
        echo "Fine of R600 added for lost/damaged book.";
    } else {
        $conn->rollback(); // Rollback transaction on failure
        http_response_code(500);
        echo "Failed to insert fine.";
    }
    $insert->close();
} else {
    http_response_code(400);
    echo "Invalid request.";
}

// Close database
$conn->close();
?>
