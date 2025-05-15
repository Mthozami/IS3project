<?php
// Start the session so we can know who the user is
session_start();

// Tell the browser we're sending back JSON data
header("Content-Type: application/json");

// These are the details needed to connect to the database
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);

// If connection fails, send a message and stop
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// If the user is not logged in, stop and send a message
if (!isset($_SESSION["UserID"])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

// Get the logged-in user's ID from the session
$userID = $_SESSION["UserID"];

try {
    // Start transaction
    $conn->begin_transaction();

    // SQL to get fines and book info for this user
    $sql = "
      SELECT f.CreatedAt, f.Amount, f.IsPaid, b.Title AS BookTitle
      FROM Fines f
      JOIN Borrowings br ON f.BorrowingID = br.BorrowingID
      JOIN Books b ON br.BookID = b.BookID
      WHERE br.UserID = ?
      ORDER BY f.CreatedAt DESC
    ";

    // Prepare the SQL safely to stop SQL injection
    $stmt = $conn->prepare($sql);

    // Add the user's ID into the query
    $stmt->bind_param("i", $userID);

    // Run the query
    $stmt->execute();

    // Get the result from the database
    $result = $stmt->get_result();

    // Create a list to hold each fine row
    $rows = [];

    // Go through each row and add it to the list
    while ($row = $result->fetch_assoc()) {
        $rows[] = [
            // When the fine was created
            "CreatedAt" => $row["CreatedAt"],
            // How much the fine is
            "Amount" => $row["Amount"],
            // If it's paid (1 = yes, 0 = no)
            "IsPaid" => $row["IsPaid"],
            // The title of the book
            "BookTitle" => $row["BookTitle"]
        ];
    }

    // Commit transaction
    $conn->commit();

    // Send the list of fines back as JSON
    echo json_encode([
        "success" => true,
        "rows" => $rows
    ]);
} catch (Exception $e) {
    // Roll back if something goes wrong
    $conn->rollback();
    echo json_encode(["success" => false, "message" => "Error retrieving fines"]);
}

// Close the SQL statement and database connection
$stmt->close();
$conn->close();
?>
