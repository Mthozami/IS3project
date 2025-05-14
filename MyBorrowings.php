<?php
session_start();
header("Content-Type: application/json");

$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

// Check login
if (!isset($_SESSION["UserID"])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$userID = $_SESSION["UserID"];

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "DB connection failed"]);
    exit;
}

// Fetch borrowings and unpaid fine status per borrowing
$sql = "SELECT 
            b.Title, 
            br.BorrowedDate, 
            br.ReturnDate,
            EXISTS (
                SELECT 1 FROM Fines f 
                WHERE f.BorrowingID = br.BorrowingID AND f.IsPaid = 0
            ) AS HasUnpaidFine
        FROM Borrowings br
        INNER JOIN Books b ON br.BookID = b.BookID
        WHERE br.UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$borrowings = [];
$hasAnyUnpaidFine = false;

while ($row = $result->fetch_assoc()) {
    if ($row["HasUnpaidFine"]) {
        $hasAnyUnpaidFine = true;
    }

    $borrowings[] = [
        "Title" => $row["Title"],
        "BorrowedDate" => $row["BorrowedDate"],
        "ReturnDate" => $row["ReturnDate"],
        "HasUnpaidFine" => (bool)$row["HasUnpaidFine"]
    ];
}

$stmt->close();
$conn->close();

echo json_encode([
    "success" => true,
    "rows" => $borrowings,
    "hasFine" => $hasAnyUnpaidFine
]);
