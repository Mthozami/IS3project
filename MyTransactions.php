<?php
session_start();
header("Content-Type: application/json");

$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

// DB Connection
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// Must be logged in
if (!isset($_SESSION["UserID"])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$userId = $_SESSION["UserID"];

$sql = "
  SELECT t.ReceiptNumber, t.PaymentDate, t.PaidAmount
  FROM Transactions t
  JOIN Fines f ON t.FineID = f.FineID
  JOIN Borrowings b ON f.BorrowingID = b.BorrowingID
  WHERE b.UserID = ?
  ORDER BY t.PaymentDate DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

echo json_encode([
  "success" => true,
  "rows" => $rows
]);

$stmt->close();
$conn->close();
?>
