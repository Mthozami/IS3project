<?php
// Start the session to keep track of who is logged in
session_start();

// Tell the browser that we will send JSON (not HTML)
header("Content-Type: application/json");

// Database login info
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

// Check if user is logged in
if (!isset($_SESSION["UserID"])) {
    // If not, send error message in JSON and stop
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

// Save the user's ID from the session
$userID = $_SESSION["UserID"];

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);

// If connection fails, stop and show error
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "DB connection failed"]);
    exit;
}

// SQL to get borrowings and check if there's any unpaid fine
$sql = "SELECT 
            b.Title,                 -- Book title
            br.BorrowedDate,         -- Date borrowed
            br.ReturnDate,           -- Due date
            EXISTS (                 -- Check if there is any unpaid fine
                SELECT 1 FROM Fines f 
                WHERE f.BorrowingID = br.BorrowingID AND f.IsPaid = 0
            ) AS HasUnpaidFine       -- Will be 1 if there’s an unpaid fine
        FROM Borrowings br
        INNER JOIN Books b ON br.BookID = b.BookID
        WHERE br.UserID = ?";

// Prepare the SQL query safely (to prevent SQL injection)
$stmt = $conn->prepare($sql);

// Put the user ID into the query
$stmt->bind_param("i", $userID);

// Run the query
$stmt->execute();

// Get the results back
$result = $stmt->get_result();

// Make an empty list to keep all borrowings
$borrowings = [];

// A flag to check if the user has any unpaid fines
$hasAnyUnpaidFine = false;

// Go through every row (book borrowing)
while ($row = $result->fetch_assoc()) {
    // If this book has an unpaid fine, set the flag to true
    if ($row["HasUnpaidFine"]) {
        $hasAnyUnpaidFine = true;
    }

    // Add this borrowing to the list
    $borrowings[] = [
        "Title" => $row["Title"],
        "BorrowedDate" => $row["BorrowedDate"],
        "ReturnDate" => $row["ReturnDate"],
        "HasUnpaidFine" => (bool)$row["HasUnpaidFine"] // Convert 1 or 0 to true/false
    ];
}

// Close the statement and database connection
$stmt->close();
$conn->close();

// Send the borrowings and fine info back as JSON
echo json_encode([
    "success" => true,
    "rows" => $borrowings,
    "hasFine" => $hasAnyUnpaidFine
]);
