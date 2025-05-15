<?php
// Start the session so we know which user is logged in
session_start();

// Tell the browser we're sending JSON data (not HTML)
header("Content-Type: application/json");

// These are the details we use to connect to the database
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

// Try to connect to the database
$conn = new mysqli($host, $username, $password, $dbname);

// If connection fails, send a message and stop the code
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// If no user is logged in, stop and show a message
if (!isset($_SESSION["UserID"])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

// Get the ID of the logged-in user
$userId = $_SESSION["UserID"];

try {
    // Start transaction
    $conn->begin_transaction();

    // This SQL gets the user's payments from the database
    $sql = "
      SELECT t.ReceiptNumber, t.PaymentDate, t.PaidAmount
      FROM Transactions t
      JOIN Fines f ON t.FineID = f.FineID
      JOIN Borrowings b ON f.BorrowingID = b.BorrowingID
      WHERE b.UserID = ?
      ORDER BY t.PaymentDate DESC
    ";

    // Prepare the SQL safely (prevents SQL injection)
    $stmt = $conn->prepare($sql);

    // Put the user ID into the SQL query
    $stmt->bind_param("i", $userId);

    // Run the query
    $stmt->execute();

    // Get all the rows (results) back
    $result = $stmt->get_result();

    // Make an empty list to hold each payment row
    $rows = [];

    // Go through each row and add it to the list
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    // Commit transaction
    $conn->commit();

    // Send all the rows back as JSON
    echo json_encode([
        "success" => true,
        "rows" => $rows
    ]);
} catch (Exception $e) {
    // Roll back if anything fails
    $conn->rollback();
    echo json_encode(["success" => false, "message" => "Error retrieving payments"]);
}

// Close the SQL statement and the database connection
$stmt->close();
$conn->close();
?>
