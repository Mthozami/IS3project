<?php
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo "Database connection failed.";
    exit;
}

$query = "
  SELECT 
    b.BorrowingID, b.ReturnDate,
    u.FullName, bk.Title AS BookTitle
  FROM Borrowings b
  JOIN Users u ON b.UserID = u.UserID
  JOIN Books bk ON b.BookID = bk.BookID
  LEFT JOIN Fines f ON b.BorrowingID = f.BorrowingID AND f.IsPaid = 0
  WHERE b.Status = 'borrowed' AND b.ReturnDate <= CURDATE()
";

$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $borrowingID = $row["BorrowingID"];
        $returnDate = new DateTime($row["ReturnDate"]);
        $today = new DateTime();
        $daysOverdue = $returnDate->diff($today)->days;

        if ($daysOverdue > 0) {
            $amount = 25 * $daysOverdue;

            // Check if a fine already exists and is unpaid
            $check = $conn->prepare("SELECT FineID FROM Fines WHERE BorrowingID = ? AND IsPaid = 0");
            $check->bind_param("i", $borrowingID);
            $check->execute();
            $resultCheck = $check->get_result();

            if ($resultCheck && $resultCheck->num_rows > 0) {
                // Update existing unpaid fine
                $update = $conn->prepare("UPDATE Fines SET Amount = ? WHERE BorrowingID = ? AND IsPaid = 0");
                $update->bind_param("di", $amount, $borrowingID);
                $update->execute();
            } else {
                // Insert new fine
                $insert = $conn->prepare("INSERT INTO Fines (BorrowingID, Amount, FullName, BookTitle) VALUES (?, ?, ?, ?)");
                $insert->bind_param("idss", $borrowingID, $amount, $row["FullName"], $row["BookTitle"]);
                $insert->execute();
            }
        }
    }
}

$conn->close();
echo "Fines updated.";
?>
