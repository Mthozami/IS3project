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

// Fetch borrowings
$sql = "SELECT b.Title, br.BorrowedDate, br.ReturnDate
        FROM Borrowings br
        INNER JOIN Books b ON br.BookID = b.BookID
        WHERE br.UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$borrowings = [];
while ($row = $result->fetch_assoc()) {
    $borrowings[] = [
        "Title" => $row["Title"],
        "BorrowedDate" => $row["BorrowedDate"],
        "ReturnDate" => $row["ReturnDate"]
    ];
}
$stmt->close();

// Check for fines
$hasFine = false;
$fineQuery = $conn->prepare("SELECT COUNT(*) as fineCount FROM Fines f
                             INNER JOIN Borrowings b ON f.BorrowingID = b.BorrowingID
                             WHERE b.UserID = ?");
$fineQuery->bind_param("i", $userID);
$fineQuery->execute();
$fineResult = $fineQuery->get_result();
if ($fineRow = $fineResult->fetch_assoc()) {
    $hasFine = $fineRow["fineCount"] > 0;
}
$fineQuery->close();

$conn->close();

echo json_encode([
    "success" => true,
    "rows" => $borrowings,
    "hasFine" => $hasFine
]);
?>
