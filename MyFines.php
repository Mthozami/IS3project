<?php
session_start();
header("Content-Type: application/json");

$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

if (!isset($_SESSION["UserID"])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$userID = $_SESSION["UserID"];

$sql = "
  SELECT f.CreatedAt, f.Amount, f.IsPaid, b.Title AS BookTitle
  FROM Fines f
  JOIN Borrowings br ON f.BorrowingID = br.BorrowingID
  JOIN Books b ON br.BookID = b.BookID
  WHERE br.UserID = ?
  ORDER BY f.CreatedAt DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = [
        "CreatedAt" => $row["CreatedAt"],
        "Amount" => $row["Amount"],
        "IsPaid" => $row["IsPaid"],
        "BookTitle" => $row["BookTitle"]
    ];
}

echo json_encode([
    "success" => true,
    "rows" => $rows
]);

$stmt->close();
$conn->close();
?>
